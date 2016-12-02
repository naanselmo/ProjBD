DROP TRIGGER IF EXISTS insertOffer;
DELIMITER //
  CREATE TRIGGER insertOffer BEFORE INSERT ON oferta
  FOR EACH row
  begin
    DECLARE registers INTEGER;

    SELECT Count(*)
    INTO   registers
    FROM   oferta
    WHERE  codigo = new.codigo
           AND morada = new.morada
           AND new.data_inicio <= data_fim
           AND new.data_fim >= data_inicio;

    IF registers > 0 THEN
      CALL raise_error;
    END IF;
  END//
delimiter ;

/* A data de pagamento de uma reserva paga tem de ser superior ao timestamp do
último estado dessa reserva */
DROP TRIGGER IF EXISTS insertPay;
DELIMITER //
  CREATE TRIGGER insertPay BEFORE INSERT ON paga
  FOR EACH row
  begin
    DECLARE last TIMESTAMP;

    SELECT time_stamp
    INTO   last
    FROM   paga
           NATURAL JOIN estado
    WHERE  numero = new.numero
    ORDER  BY time_stamp DESC
    LIMIT  1;

    IF (last >= new.data) THEN
      CALL raise_error;
    END IF;
  END//
delimiter ;
