-- DATABSE DI PROVA | NON FINALE

CREATE TABLE user (
  username varchar(255) NOT NULL,
  email varchar(255) PRIMARY KEY NOT NULL,
  password varchar(255) NOT NULL,
  user_creation_date timestamp NOT NULL
);

CREATE TABLE mense (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    indirizzo VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    orari VARCHAR(100),
    descrizione TEXT
);

CREATE TABLE piatti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descrizione TEXT,
    prezzo DECIMAL(5,2) NOT NULL,
    mensa_id INT,
    FOREIGN KEY (mensa_id) REFERENCES mense(id)
);

CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    descrizione TEXT,
    data_inizio DATE,
    data_fine DATE,
    mensa_id INT,
    FOREIGN KEY (mensa_id) REFERENCES mense(id)
);

CREATE TABLE menu_piatto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    menu_id INT,
    piatto_id INT,
    FOREIGN KEY (menu_id) REFERENCES menu(id),
    FOREIGN KEY (piatto_id) REFERENCES piatti(id)
);

CREATE TABLE recensioni_mense (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mensa_id INT,
    utente VARCHAR(100) NOT NULL,
    valutazione TINYINT(1) NOT NULL CHECK (valutazione BETWEEN 1 AND 5),
    commento TEXT,
    data_recensione DATE,
    FOREIGN KEY (mensa_id) REFERENCES mense(id)
);

CREATE TABLE recensioni_piatti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    piatto_id INT,
    utente VARCHAR(100) NOT NULL,
    valutazione TINYINT(1) NOT NULL CHECK (valutazione BETWEEN 1 AND 5),
    commento TEXT,
    data_recensione DATE,
    FOREIGN KEY (piatto_id) REFERENCES piatti(id)
);

CREATE TABLE ranking_piatti (
    id INT PRIMARY KEY AUTO_INCREMENT,
    piatto_id INT,
    periodo ENUM('settimanale', 'mensile') NOT NULL,
    punteggio INT DEFAULT 0,
    data_inizio DATE,
    data_fine DATE,
    FOREIGN KEY (piatto_id) REFERENCES piatti(id)
);


-- Inserimento Dati
INSERT INTO mense (nome, indirizzo, telefono, orari, descrizione) VALUES
('Mensa Universitaria', 'Via degli Studenti, 1', '0123456789', 'Lun-Ven: 8:00-20:00', 'Mensa per studenti con piatti freschi e locali.'),
('Mensa del Lavoratore', 'Corso del Lavoro, 20', '0987654321', 'Lun-Ven: 7:00-15:00', 'Offre pasti nutrienti a prezzi accessibili per i lavoratori.'),
('Mensa Vegana', 'Via Verde, 15', '0112233445', 'Lun-Dom: 11:00-19:00', 'Mensa completamente vegana con opzioni gluten-free.');

INSERT INTO piatti (nome, descrizione, prezzo, mensa_id) VALUES
('Pasta al Pomodoro', 'Spaghetti con salsa di pomodoro fresco.', 5.00, 1),
('Insalata Mista', 'Insalata con verdure fresche e vinaigrette.', 4.50, 1),
('Riso Vegano', 'Riso con verdure e spezie, completamente vegano.', 6.00, 3),
('Pollo alla Griglia', 'Petto di pollo grigliato servito con contorno.', 8.00, 2),
('Zuppa di Lenticchie', "Zuppa calda di lenticchie, perfetta per l'inverno.", 4.00, 2);

INSERT INTO menu (nome, descrizione, data_inizio, data_fine, mensa_id) VALUES
('Menu Settimana 1', 'Menu settimanale con piatti vari.', '2024-10-01', '2024-10-07', 1),
('Menu Lavoratori', 'Menu speciale per i lavoratori.', '2024-10-01', '2024-10-31', 2),
('Menu Vegano', 'Menu con tutti piatti vegani.', '2024-10-01', '2024-10-31', 3);

INSERT INTO menu_piatto (menu_id, piatto_id) VALUES
(1, 1), -- Pasta al Pomodoro
(1, 2), -- Insalata Mista
(2, 4), -- Pollo alla Griglia
(2, 5), -- Zuppa di Lenticchie
(3, 3); -- Riso Vegano

INSERT INTO recensioni_mense (mensa_id, utente, valutazione, commento, data_recensione) VALUES
(1, 'Marco Rossi', 4, "Buona qualità dei piatti, ma a volte c'è attesa.", '2024-10-05'),
(2, 'Anna Bianchi', 5, 'Ottima mensa! Servizio veloce e cibo delizioso.', '2024-10-04'),
(3, 'Giuseppe Verdi', 3, "Buoni piatti, ma un po' costosi.', '2024-10-06");

INSERT INTO recensioni_piatti (piatto_id, utente, valutazione, commento, data_recensione) VALUES
(1, 'Luca Neri', 5, 'Ottima pasta! Salsa fresca e saporita.', '2024-10-02'),
(2, 'Sara Gallo', 4, 'Insalata buona, ma potrebbe avere più varietà.', '2024-10-03'),
(3, 'Carlo Blu', 5, 'Riso delizioso! Consigliato!', '2024-10-01'),
(4, 'Elena Rosa', 4, 'Pollo alla griglia ben cotto e saporito.', '2024-10-05');

INSERT INTO ranking_piatti (piatto_id, periodo, punteggio, data_inizio, data_fine) VALUES
(1, 'settimanale', 10, '2024-10-01', '2024-10-07'), -- Pasta al Pomodoro
(2, 'settimanale', 8, '2024-10-01', '2024-10-07'),  -- Insalata Mista
(3, 'mensile', 15, '2024-10-01', '2024-10-31');  -- Riso Vegano
