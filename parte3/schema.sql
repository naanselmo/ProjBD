SET FOREIGN_KEY_CHECKS=0;

CREATE TABLE reserva
(
  numero INTEGER,
  PRIMARY KEY (numero)
);

CREATE TABLE user
(
  nif      INTEGER,
  nome     CHAR(25),
  telefone INTEGER,
  PRIMARY KEY (nif)
);

CREATE TABLE fiscal
(
  id      INTEGER,
  empresa CHAR(25),
  PRIMARY KEY (id)
);

CREATE TABLE edificio
(
  morada CHAR(50),
  PRIMARY KEY (morada)
);

CREATE TABLE alugavel
(
  morada CHAR(50),
  codigo INTEGER,
  foto   LONGBLOB,
  PRIMARY KEY (morada, codigo),
  FOREIGN KEY (morada) REFERENCES edificio (morada)
);

CREATE TABLE arrenda
(
  morada CHAR(50),
  codigo INTEGER,
  nif    INTEGER,
  PRIMARY KEY (morada, codigo),
  FOREIGN KEY (morada, codigo) REFERENCES alugavel (morada, codigo),
  FOREIGN KEY (nif) REFERENCES user (nif)
);

CREATE TABLE fiscaliza
(
  id     INTEGER,
  morada CHAR(50),
  codigo INTEGER,
  PRIMARY KEY (id, morada, codigo),
  FOREIGN KEY (id) REFERENCES fiscal (id),
  FOREIGN KEY (morada, codigo) REFERENCES arrenda (morada, codigo)
);

CREATE TABLE espaco
(
  morada CHAR(50),
  codigo INTEGER,
  PRIMARY KEY (morada, codigo),
  FOREIGN KEY (morada, codigo) REFERENCES alugavel (morada, codigo)
);

CREATE TABLE posto
(
  morada        CHAR(50),
  codigo        INTEGER,
  codigo_espaco INTEGER,
  PRIMARY KEY (morada, codigo),
  FOREIGN KEY (morada, codigo) REFERENCES alugavel (morada, codigo),
  FOREIGN KEY (morada, codigo_espaco) REFERENCES espaco (morada, codigo)
);

CREATE TABLE oferta
(
  morada      CHAR(50),
  codigo      INTEGER,
  data_inicio DATE,
  data_fim    DATE,
  tarifa      NUMERIC(8, 2),
  PRIMARY KEY (morada, codigo, data_inicio),
  FOREIGN KEY (morada, codigo) REFERENCES alugavel (morada, codigo)
);

CREATE TABLE aluga
(
  morada      CHAR(50),
  codigo      INTEGER,
  data_inicio DATE,
  nif         INTEGER,
  numero      INTEGER,
  PRIMARY KEY (morada, codigo, data_inicio, nif, numero),
  FOREIGN KEY (morada, codigo, data_inicio) REFERENCES oferta (morada, codigo, data_inicio),
  FOREIGN KEY (nif) REFERENCES user (nif),
  FOREIGN KEY (numero) REFERENCES reserva (numero)
);

CREATE TABLE paga
(
  numero INTEGER,
  data   DATE,
  metodo CHAR(10),
  PRIMARY KEY (numero),
  FOREIGN KEY (numero) REFERENCES reserva (numero)
);

CREATE TABLE estado
(
  numero    INTEGER,
  timestamp TIMESTAMP,
  estado    CHAR(10),
  PRIMARY KEY (numero, timestamp),
  FOREIGN KEY (numero) REFERENCES reserva (numero)
);

SET FOREIGN_KEY_CHECKS=1;
