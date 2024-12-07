-- Creazione del database
CREATE DATABASE IF NOT EXISTS ristorante;

-- Utilizzo del database
USE ristorante;

-- Creazione della tabella piatti
CREATE TABLE piatti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

-- Creazione della tabella voti
CREATE TABLE voti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    piatto_id INT,
    voto INT CHECK (voto >= 1 AND voto <= 5),
    FOREIGN KEY (piatto_id) REFERENCES piatti(id)
);

-- Inserimento dei piatti
INSERT INTO piatti (nome) VALUES ('Pizza Margherita');
INSERT INTO piatti (nome) VALUES ('Lasagna');
INSERT INTO piatti (nome) VALUES ('Risotto alla Milanese');

-- Inserimento dei voti
INSERT INTO voti (piatto_id, voto) VALUES (1, 5);
INSERT INTO voti (piatto_id, voto) VALUES (1, 4);
INSERT INTO voti (piatto_id, voto) VALUES (2, 3);
INSERT INTO voti (piatto_id, voto) VALUES (2, 5);
INSERT INTO voti (piatto_id, voto) VALUES (3, 4);
INSERT INTO voti (piatto_id, voto) VALUES (3, 5);