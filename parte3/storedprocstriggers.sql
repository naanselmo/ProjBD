/* Não podem existir ofertas com datas sobrepostas */

delimiter //
CREATE TRIGGER insertOffer BEFORE INSERT ON oferta
FOR EACH ROW
	BEGIN
	DECLARE msg VARCHAR(255);

	IF (new.data_inicio <= old.data_fim) and (new.data_fim >= old.data_inicio) then
		set msg = "intervalo de datas inválido";
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
	END IF

END
	
 /* A data de pagamento de uma reserva paga tem de ser superior ao timestamp do
último estado dessa reserva */

delimiter //
CREATE TRIGGER insertPay BEFORE INSERT ON paga
FOR EACH ROW
	BEGIN
	DECLARE msg VARCHAR(255);
	DECLARE last timestamp 

	set last = SELECT timestamp 
			   FROM paga NATURAL JOIN estado
			   WHERE numero = new.numero
	           ORDER BY timestamp DESC
	           LIMIT 1 ;

	if (last >= new.timestamp) then
		set msg = "data de pagamento inválida";
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
	END IF

END
