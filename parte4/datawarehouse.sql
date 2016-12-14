CREATE TABLE user(
  nif INTEGER,
  nome CHAR(20),
  telefone INTEGER,
  PRIMARY KEY (nif));

CREATE TABLE date_dimension (
  date_id INTEGER,
  dia INTEGER,
  semana INTEGER,
  mes INTEGER,
  semestre INTEGER,
  ano INTEGER,
  PRIMARY KEY(date_id) );

CREATE TABLE local_dimension(
  local_id INTEGER,
  cod_espaco VARCHAR(50),
  cod_posto VARCHAR(50),
  cod_edificio VARCHAR(50),
  PRIMARY KEY(local_id)
);

CREATE TABLE time_dimension(
  time_id INTEGER,
  minuto_do_dia INTEGER
  PRIMARY KEY(time_id)
);

CREATE TABLE reserva(
  nif INTEGER,
  date_id INTEGER,
  time_id INTEGER,
  local_id INTEGER,
  total_pago INTEGER,
  duracao_em_dias INTEGER,
  PRIMARY KEY (nif, date_id,time_id,local_id),
  FOREIGN KEY (date_id) REFERENCES date_dimension(date_id),
  FOREIGN KEY (time_id) REFERENCES time_dimension(time_id),
  FOREIGN KEY(nif) REFERENCES user(nif),
  FOREIGN KEY(local_id) REFERENCES local_dimension(local_id)
);
