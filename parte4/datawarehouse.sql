USE proj_dw;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS user;
CREATE TABLE user (
  nif      INTEGER,
  nome     CHAR(80),
  telefone INTEGER,
  PRIMARY KEY (nif)
);

DROP TABLE IF EXISTS date_dimension;
CREATE TABLE date_dimension (
  date_id  INTEGER,
  dia      INTEGER,
  semana   INTEGER,
  mes      INTEGER,
  semestre INTEGER,
  ano      INTEGER,
  PRIMARY KEY (date_id)
);

DROP TABLE IF EXISTS local_dimension;
CREATE TABLE local_dimension (
  local_id     VARCHAR(765),
  cod_espaco   VARCHAR(255),
  cod_posto    VARCHAR(255),
  cod_edificio VARCHAR(255),
  PRIMARY KEY (local_id)
);

DROP TABLE IF EXISTS time_dimension;
CREATE TABLE time_dimension (
  time_id INTEGER,
  hora    INTEGER,
  minuto  INTEGER,
  PRIMARY KEY (time_id)
);

DROP TABLE IF EXISTS reserva;
CREATE TABLE reserva (
  nif             INTEGER,
  date_id         INTEGER,
  time_id         INTEGER,
  local_id        VARCHAR(765),
  total_pago      FLOAT,
  duracao_em_dias INTEGER,
  PRIMARY KEY (nif, date_id, time_id, local_id),
  FOREIGN KEY (date_id) REFERENCES date_dimension (date_id),
  FOREIGN KEY (time_id) REFERENCES time_dimension (time_id),
  FOREIGN KEY (nif) REFERENCES user (nif),
  FOREIGN KEY (local_id) REFERENCES local_dimension (local_id)
);

SET FOREIGN_KEY_CHECKS = 1;