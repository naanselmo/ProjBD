CREATE TABLE User
  ( 
     nif      INTEGER, 
     nome     CHAR(25), 
     telefone INTEGER, 
     CONSTRAINT userKey PRIMARY KEY (nif) 
  ) ;

CREATE TABLE Fiscal 
  ( 
     id      INTEGER, 
     empresa CHAR(25), 
     CONSTRAINT fiscalKey PRIMARY KEY (id) 
  ) ;

CREATE TABLE Edificio 
  ( 
     morada CHAR(50), 
     CONSTRAINT edificioKey PRIMARY KEY (morada) 
  ) ;

CREATE TABLE Alugavel 
  ( 
     morada CHAR(50), 
     codigo INTEGER, 
     foto   LONGBLOB, 
     CONSTRAINT alugavelKey PRIMARY KEY (morada, codigo), 
     FOREIGN KEY (morada) REFERENCES Edificio(morada) 
  ) ;

CREATE TABLE Arrenda 
  ( 
     morada  CHAR(50), 
     codigo INTEGER, 
     nif     INTEGER, 
     CONSTRAINT arrendaKey PRIMARY KEY (morada, codigo), 
     FOREIGN KEY (morada, codigo) REFERENCES Alugavel(morada, codigo), 
     FOREIGN KEY (nif) REFERENCES User(nif) 
  ) ;

CREATE TABLE Fiscaliza 
  ( 
     id     INTEGER, 
     morada CHAR(50), 
     codigo INTEGER, 
     CONSTRAINT fiscalizaKey PRIMARY KEY (id, morada, codigo), 
     FOREIGN KEY (id) REFERENCES Fiscal(id) 
     FOREIGN KEY (morada, codigo) REFERENCES Arrenda(morada, codigo) 
  ) ;

CREATE TABLE Espaco 
  ( 
     morada CHAR(50), 
     codigo INTEGER, 
     CONSTRAINT espacoKey PRIMARY KEY (morada, codigo), 
     FOREIGN KEY (morada, codigo) REFERENCES Alugavel(morada, codigo) 
  ) ;

CREATE TABLE Posto 
  ( 
     morada        CHAR(50), 
     codigo        INTEGER, 
     codigo_espaco INTEGER, 
     CONSTRAINT postoKey PRIMARY KEY (morada, codigo), 
     FOREIGN KEY (morada, codigo) REFERENCES Alugavel(morada, codigo) 
     FOREIGN KEY (morada, codigo) REFERENCES Espaco(morada, codigo) 
  ) ;

CREATE TABLE Oferta 
  ( 
     morada      CHAR(50), 
     codigo      INTEGER, 
     data_inicio DATE, 
     data_fim    DATE, 
     tarifa      NUMERIC(8, 2), 
     CONSTRAINT ofertaKey PRIMARY KEY (morada, codigo, data_inicio), 
     FOREIGN KEY (morada, codigo) REFERENCES Alugavel(morada, codigo) 
  ) ;

CREATE TABLE Aluga 
  ( 
     morada      CHAR(50), 
     codigo      INTEGER, 
     data_inicio DATE, 
     nif         INTEGER, 
     numero      INTEGER, 
     CONSTRAINT alugaKey PRIMARY KEY(morada, codigo, data_inicio, nif, numero), 
     FOREIGN KEY (morada, codigo, data_inicio) REFERENCES Oferta(morada, codigo, 
     data_inicio) 
     FOREIGN KEY (nif) REFERENCES User(nif), 
     FOREIGN KEY (numero) REFERENCES Reserva(numero) 
  ) ;

CREATE TABLE Paga 
  ( 
     numero INTEGER, 
     data   DATE, 
     metodo CHAR(50), 
     CONSTRAINT pagaKey PRIMARY KEY (numero), 
     FOREIGN KEY (numero) REFERENCES Reserva (numero) 
  ) ;

CREATE TABLE Estado 
  ( 
     numero    INTEGER, 
     timestamp TIMESTAMP, 
     estado    CHAR(50), 
     CONSTRAINT estadoKey PRIMARY KEY (numero, timestamp), 
     FOREIGN KEY(numero) REFERENCES Reserva(numero) 
  ) ;

CREATE TABLE Reserva 
  ( 
     numero INTEGER, 
     CONSTRAINT reservaKey PRIMARY KEY(numero) 
  ) ;