/* Não podem existir ofertas com datas sobrepostas */

DELIMITER //
CREATE TRIGGER insertOffer BEFORE INSERT ON oferta
FOR EACH ROW
  BEGIN
    DECLARE msg VARCHAR(255);

    IF (new.data_inicio <= old.data_fim) AND (new.data_fim >= old.data_inicio)
    THEN
      SET msg = 'intervalo de datas inválido';
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = msg;
    END IF;

  END//

/* A data de pagamento de uma reserva paga tem de ser superior ao timestamp do
último estado dessa reserva */
CREATE TRIGGER insertPay BEFORE INSERT ON paga
FOR EACH ROW
  BEGIN
    DECLARE msg VARCHAR(255);
    DECLARE last TIMESTAMP;

    SELECT time_stamp
    INTO last
    FROM paga
      NATURAL JOIN estado
    WHERE numero = new.numero
    ORDER BY time_stamp DESC
    LIMIT 1;

    IF (last >= new.data)
    THEN
      SET msg = 'data de pagamento inválida';
      SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = msg;
    END IF;

  END//
DELIMITER ;