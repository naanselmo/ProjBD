DELIMITER //

# Parses a day, month and year to a mysql date type.
DROP FUNCTION IF EXISTS to_date //
CREATE FUNCTION to_date(day INT, month INT, year INT)
  RETURNS DATE
  BEGIN
    RETURN DATE(CONCAT(year, '-', month, '-', day));
  END //

# Generates the date dimension, with all the days of the years 2016 and 2017.
DROP PROCEDURE IF EXISTS load_date_dimension //
CREATE PROCEDURE load_date_dimension()
  BEGIN
    DECLARE full_date DATETIME;
    SET full_date = '2016-01-01 00:00:00';
    WHILE full_date < '2018-01-01 00:00:00' DO
      INSERT INTO date_dimension (date_id, dia, semana, mes, semestre, ano) VALUES (
        YEAR(full_date) * 10000 + MONTH(full_date) * 100 + DAY(full_date),
        DAY(full_date),
        # Week starting with a sunday and range from 1-52
        WEEK(full_date, 2),
        MONTH(full_date),
        # If the month is lesser then the 7th month, its first semester, otherwise second.
        IF(MONTH(full_date) < 7, 1, 2),
        YEAR(full_date)
      );
      SET full_date = DATE_ADD(full_date, INTERVAL 1 DAY);
    END WHILE;
  END;
//

# Generates the time dimension, with all minutes of the day, since 00:00 until 23:59
DROP PROCEDURE IF EXISTS load_time_dimension //
CREATE PROCEDURE load_time_dimension()
  BEGIN
    DECLARE full_day DATETIME;
    SET full_day = '2016-01-01 00:00:00';
    WHILE full_day < '2016-01-01 23:59:59' DO
      INSERT INTO time_dimension (time_id, hora, minuto) VALUES (
        HOUR(full_day) * 100 + MINUTE(full_day),
        HOUR(full_day),
        MINUTE(full_day)
      );
      SET full_day = DATE_ADD(full_day, INTERVAL 1 MINUTE);
    END WHILE;
  END //

# Loads all users into the user dimension.
DROP PROCEDURE IF EXISTS load_user_dimension //
CREATE PROCEDURE load_user_dimension()
  BEGIN
    INSERT INTO user
      SELECT
        nif,
        nome,
        telefone
      FROM proj.user;
  END //

# Loads all the locations into the local dimension.
# All workspaces will have the cod_posto as null.
DROP PROCEDURE IF EXISTS load_local_dimension //
CREATE PROCEDURE load_local_dimension()
  BEGIN
    # Unions all workspaces and workstations.
    INSERT INTO local_dimension
      SELECT
        CONCAT(morada, codigo_espaco, IFNULL(codigo_posto, '')) AS local_id,
        codigo_espaco                                           AS cod_espaco,
        codigo_posto                                            AS cod_posto,
        morada                                                  AS cod_edificio
      FROM ((SELECT
               morada,
               codigo AS codigo_espaco,
               NULL      codigo_posto
             FROM proj.espaco)
            UNION ALL (SELECT
                         morada,
                         codigo_espaco,
                         codigo AS codigo_posto
                       FROM proj.posto)) AS local;
  END //

DROP PROCEDURE IF EXISTS load_reserva //
CREATE PROCEDURE load_reserva()
  BEGIN
    # Declare all variables for the cursor fetching.
    DECLARE fetched_nif VARCHAR(9);
    DECLARE fetched_morada VARCHAR(255);
    DECLARE fetched_codigo_espaco VARCHAR(255);
    DECLARE fetched_codigo_posto VARCHAR(255);
    DECLARE fetched_data_inicio DATE;
    DECLARE fetched_data_fim DATE;
    DECLARE fetched_data_pagamento DATETIME;
    DECLARE fetched_tarifa DECIMAL(19, 4);

    # Declare all variables that will be used to create the reserva entry.
    DECLARE fetched_date_id INT;
    DECLARE fetched_time_id INT;
    DECLARE fetched_local_id VARCHAR(765);

    # Declare the cursor related variables.
    # The query will give all the reservas that are rented by someone, even if they didn't pay
    # for it yet.
    DECLARE cursorDone INT DEFAULT FALSE;
    DECLARE cursorReserva CURSOR FOR SELECT
                                       nif,
                                       morada,
                                       codigo AS codigo_espaco,
                                       NULL   AS codigo_posto,
                                       data_inicio,
                                       data_fim,
                                       data   AS data_pagamento,
                                       tarifa
                                     FROM proj.aluga
                                       NATURAL JOIN proj.oferta
                                       NATURAL JOIN proj.espaco
                                       LEFT JOIN proj.paga ON paga.numero = aluga.numero
                                     UNION ALL
                                     SELECT
                                       nif,
                                       morada,
                                       codigo_espaco,
                                       codigo AS codigo_posto,
                                       data_inicio,
                                       data_fim,
                                       data   AS data_pagamento,
                                       tarifa
                                     FROM proj.aluga
                                       NATURAL JOIN proj.oferta
                                       NATURAL JOIN proj.posto
                                       LEFT JOIN proj.paga ON paga.numero = aluga.numero;

    # Make the cursorDone variable go false once the cursor goes through all the records.
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET cursorDone = TRUE;

    # Start looping.
    OPEN cursorReserva;
    reservaLoop: LOOP
      # Set the cursor done false, since any SELECT INTO will activate the handler and turn the variable true.
      SET cursorDone = FALSE;
      # Fetch the next record.
      FETCH cursorReserva
      INTO fetched_nif, fetched_morada, fetched_codigo_espaco, fetched_codigo_posto, fetched_data_inicio, fetched_data_fim, fetched_data_pagamento, fetched_tarifa;

      # If there are no more records close the cursor and leave the loop.
      IF cursorDone
      THEN
        CLOSE cursorReserva;
        LEAVE reservaLoop;
      END IF;

      # Fetch the local dimension of the current record.
      IF fetched_codigo_posto IS NULL
      THEN
        SELECT local_id
        INTO fetched_local_id
        FROM local_dimension
        WHERE cod_edificio = fetched_morada AND cod_espaco = fetched_codigo_espaco AND cod_posto IS NULL;
      ELSE
        SELECT local_id
        INTO fetched_local_id
        FROM local_dimension
        WHERE cod_edificio = fetched_morada AND cod_espaco = fetched_codigo_espaco AND cod_posto = fetched_codigo_posto;
      END IF;

      # Fill the whole date and time data for this reserva, since the start_date until the end_date.
      # Also fill the remaining days and total_pago as 0.
      INSERT INTO reserva
        SELECT
          fetched_nif                                          AS nif,
          date_id,
          time_id,
          fetched_local_id                                     AS local_id,
          0                                                    AS total_pago,
          (Datediff(fetched_data_fim, to_date(dia, mes, ano))) AS duracao_em_dias
        FROM time_dimension, date_dimension
        WHERE to_date(dia, mes, ano) BETWEEN fetched_data_inicio AND fetched_data_fim;

      # If it was paid then update the total_pago in the day and minute that it was paid.
      IF fetched_data_pagamento IS NOT NULL
      THEN

        # Fetch the time dimension of when the reserva was paid.
        SELECT time_id
        INTO fetched_time_id
        FROM time_dimension
        WHERE hora = HOUR(fetched_data_pagamento) AND minuto = MINUTE(fetched_data_pagamento);

        # Fetch the date dimension of when the reserva was paid.
        SELECT date_id
        INTO fetched_date_id
        FROM date_dimension
        WHERE
          ano = YEAR(fetched_data_pagamento) AND mes = MONTH(fetched_data_pagamento) AND
          dia = DAY(fetched_data_pagamento);

        # Update the payment time, using the fields that we fetched.
        UPDATE reserva
        SET total_pago = (Datediff(fetched_data_fim, fetched_data_inicio) + 1) * fetched_tarifa
        WHERE
          nif = fetched_nif AND time_id = fetched_time_id AND date_id = fetched_date_id AND
          local_id = fetched_local_id;
      END IF;
    END LOOP;
  END //

# Loads the data warehouse.
DROP PROCEDURE IF EXISTS load_data_warehouse //
CREATE PROCEDURE load_data_warehouse()
  BEGIN
    CALL load_time_dimension();
    CALL load_date_dimension();
    CALL load_user_dimension();
    CALL load_local_dimension();
    CALL load_reserva();
  END //

DELIMITER ;