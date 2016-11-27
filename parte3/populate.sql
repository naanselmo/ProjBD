-- Users
INSERT INTO user (nif, nome, telefone) VALUES (1, 'Alice', 123456789);
INSERT INTO user (nif, nome, telefone) VALUES (2, 'Bob', 1357924680);
INSERT INTO user (nif, nome, telefone) VALUES (3, 'Charlie', 0987654321);
INSERT INTO user (nif, nome, telefone) VALUES (4, 'Dan', 0864297531);
INSERT INTO user (nif, nome, telefone) VALUES (5, 'Eve', 1029384756);

-- Edificio com alugáveis
INSERT INTO edificio (morada) VALUES ('Rua A');
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua A', 1, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua A', 2, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua A', 3, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua A', 4, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua A', 5, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua A', 6, 000000);
-- Espaço com 2 postos, 1 posto alugado
INSERT INTO espaco (morada, codigo) VALUES ('Rua A', 1);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua A', 2, 1);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua A', 3, 1);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua A', 2, now() - INTERVAL 2 DAY, now(), 5.2);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua A', 3, now() - INTERVAL 3 DAY, now(), 5.2);
INSERT INTO reserva (numero) VALUES (1);
INSERT INTO aluga (morada, codigo, data_inicio, nif, numero) VALUES ('Rua A', 2, now() - INTERVAL 2 DAY, 1, 1);
-- Espaço com 2 postos, 2 postos alugados
INSERT INTO espaco (morada, codigo) VALUES ('Rua A', 4);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua A', 5, 4);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua A', 6, 4);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua A', 4, now() - INTERVAL 4 DAY, now(), 1.4);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua A', 5, now() - INTERVAL 5 DAY, now(), 5.4);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua A', 6, now() - INTERVAL 6 DAY, now(), 9.5);
INSERT INTO reserva (numero) VALUES (2);
INSERT INTO reserva (numero) VALUES (3);
INSERT INTO aluga (morada, codigo, data_inicio, nif, numero) VALUES ('Rua A', 5, now() - INTERVAL 5 DAY, 1, 2);
INSERT INTO aluga (morada, codigo, data_inicio, nif, numero) VALUES ('Rua A', 6, now() - INTERVAL 6 DAY, 2, 3);

-- Edificio com alugáveis
INSERT INTO edificio (morada) VALUES ('Rua B');
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua B', 7, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua B', 8, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua B', 9, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua B', 10, 000000);
-- Espaço com 0 postos, espaço alugado
INSERT INTO espaco (morada, codigo) VALUES ('Rua B', 7);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua B', 7, now() - INTERVAL 7 DAY, now(), 9.2);
INSERT INTO reserva (numero) VALUES (4);
INSERT INTO aluga (morada, codigo, data_inicio, nif, numero) VALUES ('Rua B', 7, now() - INTERVAL 7 DAY, 2, 4);
-- Espaço com 2 postos, espaço alugado
INSERT INTO espaco (morada, codigo) VALUES ('Rua B', 8);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua B', 9, 8);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua B', 10, 8);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua B', 8, now() - INTERVAL 8 DAY, now(), 2.3);
INSERT INTO reserva (numero) VALUES (5);
INSERT INTO aluga (morada, codigo, data_inicio, nif, numero) VALUES ('Rua B', 8, now() - INTERVAL 8 DAY, 4, 5);

-- Edificio com alugáveis
INSERT INTO edificio (morada) VALUES ('Rua C');
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua C', 11, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua C', 12, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua C', 13, 000000);
INSERT INTO alugavel (morada, codigo, foto) VALUES ('Rua C', 14, 000000);
-- Espaço com 2 postos, 0 postos alugados
INSERT INTO espaco (morada, codigo) VALUES ('Rua C', 11);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua C', 12, 11);
INSERT INTO posto (morada, codigo, codigo_espaco) VALUES ('Rua C', 13, 11);
-- Espaço com 0 postos, 0 postos alugados
INSERT INTO espaco (morada, codigo) VALUES ('Rua C', 14);
INSERT INTO oferta (morada, codigo, data_inicio, data_fim, tarifa) VALUES ('Rua C', 14, now() - INTERVAL 14 DAY, now(), 7.5);
