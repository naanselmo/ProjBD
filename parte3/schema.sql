DROP TABLE IF EXISTS estado;
DROP TABLE IF EXISTS paga;
DROP TABLE IF EXISTS aluga;
DROP TABLE IF EXISTS reserva;
DROP TABLE IF EXISTS oferta;
DROP TABLE IF EXISTS posto;
DROP TABLE IF EXISTS espaco;
DROP TABLE IF EXISTS fiscaliza;
DROP TABLE IF EXISTS arrenda;
DROP TABLE IF EXISTS alugavel;
DROP TABLE IF EXISTS edificio;
DROP TABLE IF EXISTS fiscal;
DROP TABLE IF EXISTS user;

CREATE TABLE user
  (
     nif      VARCHAR(9) NOT NULL UNIQUE,
     nome     VARCHAR(80) NOT NULL,
     telefone VARCHAR(26) NOT NULL,
     PRIMARY KEY(nif)
  );

CREATE TABLE fiscal
  (
     id      INT NOT NULL UNIQUE,
     empresa VARCHAR(255) NOT NULL,
     PRIMARY KEY(id)
  );

CREATE TABLE edificio
  (
     morada VARCHAR(255) NOT NULL UNIQUE,
     PRIMARY KEY(morada)
  );

CREATE TABLE alugavel
  (
     morada VARCHAR(255) NOT NULL,
     codigo VARCHAR(255) NOT NULL,
     foto   VARCHAR(255) NOT NULL,
     PRIMARY KEY(morada, codigo),
     FOREIGN KEY(morada) REFERENCES edificio(morada)
  );

CREATE TABLE arrenda
  (
     morada VARCHAR(255) NOT NULL,
     codigo VARCHAR(255) NOT NULL,
     nif    VARCHAR(9) NOT NULL,
     PRIMARY KEY(morada, codigo),
     FOREIGN KEY(morada, codigo) REFERENCES alugavel(morada, codigo),
     FOREIGN KEY(nif) REFERENCES user(nif)
  );

CREATE TABLE fiscaliza
  (
     id     INT NOT NULL,
     morada VARCHAR(255) NOT NULL,
     codigo VARCHAR(255) NOT NULL,
     PRIMARY KEY(id, morada, codigo),
     FOREIGN KEY(morada, codigo) REFERENCES arrenda(morada, codigo),
     FOREIGN KEY(id) REFERENCES fiscal(id)
  );

CREATE TABLE espaco
  (
     morada VARCHAR(255) NOT NULL,
     codigo VARCHAR(255) NOT NULL,
     PRIMARY KEY(morada, codigo),
     FOREIGN KEY(morada, codigo) REFERENCES alugavel(morada, codigo)
  );

CREATE TABLE posto
  (
     morada        VARCHAR(255) NOT NULL,
     codigo        VARCHAR(255) NOT NULL,
     codigo_espaco VARCHAR(255) NOT NULL,
     PRIMARY KEY(morada, codigo),
     FOREIGN KEY(morada, codigo) REFERENCES alugavel(morada, codigo),
     FOREIGN KEY(morada, codigo_espaco) REFERENCES espaco(morada, codigo)
  );

CREATE TABLE oferta
  (
     morada      VARCHAR(255) NOT NULL,
     codigo      VARCHAR(255) NOT NULL,
     data_inicio DATE NOT NULL,
     data_fim    DATE NOT NULL,
     tarifa      NUMERIC(19, 4) NOT NULL,
     PRIMARY KEY(morada, codigo, data_inicio),
     FOREIGN KEY(morada, codigo) REFERENCES alugavel(morada, codigo)
  );

CREATE TABLE reserva
  (
     numero VARCHAR(255) NOT NULL UNIQUE,
     PRIMARY KEY(numero)
  );

CREATE TABLE aluga
  (
     morada      VARCHAR(255) NOT NULL,
     codigo      VARCHAR(255) NOT NULL,
     data_inicio DATE NOT NULL,
     nif         VARCHAR(9) NOT NULL,
     numero      VARCHAR(255) NOT NULL,
     PRIMARY KEY(morada, codigo, data_inicio, nif, numero),
     FOREIGN KEY(morada, codigo, data_inicio) REFERENCES oferta(morada, codigo,
     data_inicio),
     FOREIGN KEY(nif) REFERENCES user(nif),
     FOREIGN KEY(numero) REFERENCES reserva(numero)
  );

CREATE TABLE paga
  (
     numero VARCHAR(255) NOT NULL UNIQUE,
     data   TIMESTAMP NOT NULL,
     metodo VARCHAR(255) NOT NULL,
     PRIMARY KEY(numero),
     FOREIGN KEY(numero) REFERENCES reserva(numero)
  );

CREATE TABLE estado
  (
     numero     VARCHAR(255) NOT NULL,
     time_stamp TIMESTAMP NOT NULL,
     estado     VARCHAR(255) NOT NULL,
     PRIMARY KEY(numero, time_stamp),
     FOREIGN KEY(numero) REFERENCES reserva(numero)
  );
