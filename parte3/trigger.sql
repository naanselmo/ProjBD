delimiter //
CREATE TRIGGER insertOffer BEFORE INSERT ON oferta
FOR EACH ROW
	BEGIN
	DECLARE msg VARCHAR(255);
	DECLARE dataIni DATE;
	DECLARE dataFim DATE;
	DECLARE registers INTEGER ;

	SELECT count(*) into registers, data_inicio as dataIni, data_fim as dataFim
	FROM oferta
	WHERE codigo = new.codigo
	AND morada = new.morada
	AND (new.data_inicio BETWEEN dataIni AND dataFim
	OR new.data_fim BETWEEN dataIni AND dataFim
  OR dataIni BETWEEN new.data_inicio AND new.data_fim
  OR dataFim BETWEEN  new.data_inicio AND new.data_fim);

  IF timestampdiff(day,new.data_inicio,new.data_fim) < 0
  AND registers > 0 THEN
  		  set msg = 'intervalo de datas inválido';
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = msg;
	END IF

END //