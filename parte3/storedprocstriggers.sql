DROP TRIGGER IF EXISTS 'insertOffer';
delimiter //
CREATE TRIGGER insertOffer BEFORE INSERT ON oferta
FOR EACH ROW
	BEGIN
		DECLARE msg VARCHAR(255);
		DECLARE registers INTEGER ;

		SELECT count(*) into registers
		FROM oferta
		WHERE codigo = new.codigo
		AND morada = new.morada
		AND (new.data_inicio BETWEEN data_inicio AND data_fim
		OR new.data_fim BETWEEN data_inicio AND data_fim
	    OR data_inicio BETWEEN new.data_inicio AND new.data_fim
	    OR data_fim BETWEEN  new.data_inicio AND new.data_fim);

		IF new.datainicio>new.data_fim OR registers>0 THEN
	    IF timestampdiff(day,new.data_inicio,new.data_fim) < 0 AND registers > 0 THEN
	  		set msg = 'intervalo de datas inválido';
	    END IF;
	END
//
delimiter ;
/* A data de pagamento de uma reserva paga tem de ser superior ao timestamp do
último estado dessa reserva */
DROP TRIGGER IF EXISTS 'insertPay';
delimiter //
CREATE TRIGGER insertPay BEFORE INSERT ON paga
FOR EACH ROW
	BEGIN
		DECLARE msg VARCHAR(255);
		DECLARE last timestamp;

		SELECT time_stamp INTO last
		FROM paga NATURAL JOIN estado
		WHERE numero = new.numero
		ORDER BY time_stamp DESC
		LIMIT 1 ;

		if (last >= new.time_stamp) then
			set msg = 'data de pagamento inválida';
	        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
		END IF;
	END
//
