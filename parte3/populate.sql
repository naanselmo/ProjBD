INSERT INTO user (nif, nome, telefone) VALUES (1, 'Albino', 19221312);

INSERT INTO edificio (morada) VALUES ('Rua Sao Joao Bosco');
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua Sao Joao Bosco', 1, 1827712);
INSERT INTO espaco (morada, codigo) VALUES ('Rua Sao Joao Bosco', 1);

INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua Sao Joao Bosco', 2, 1827712);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua Sao Joao Bosco', 2, 1);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua Sao Joao Bosco', 3, 1827712);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua Sao Joao Bosco', 3, 1);

INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES
  ('Rua Sao Joao Bosco', 2, '2016-08-12', now(), 9.2);

INSERT INTO reserva (numero) VALUES (1);

INSERT INTO aluga (morada, codigo, data_inicio, nif, numero) VALUES
  ('Rua Sao Joao Bosco', 2, '2016-08-12', 1, 1);
