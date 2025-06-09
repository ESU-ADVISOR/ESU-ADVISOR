SET CHARACTER SET utf8mb4;

DROP VIEW IF EXISTS mensa_orari_apertura;
DROP VIEW IF EXISTS piatto_recensioni_foto;
DROP EVENT IF EXISTS crea_menu_settimanale_event;
DROP PROCEDURE IF EXISTS crea_menu_settimanale;
DROP TABLE IF EXISTS preferenze_utente;
DROP TABLE IF EXISTS allergeni_utente;
DROP TABLE IF EXISTS piatto_allergeni;
DROP TABLE IF EXISTS piatto_foto;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS recensione;
DROP TABLE IF EXISTS orarioapertura;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS utente;
DROP TABLE IF EXISTS piatto;
DROP TABLE IF EXISTS mensa;

CREATE TABLE mensa (
    nome VARCHAR(50) NOT NULL,
    indirizzo VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    maps_link TEXT NOT NULL,
    PRIMARY KEY (nome)
);

CREATE TABLE piatto (
    nome VARCHAR(100) NOT NULL,
    categoria ENUM ("Primo", "Secondo", "Contorno") NOT NULL,
    descrizione TEXT NOT NULL,
    PRIMARY KEY (nome),
    CHECK (LENGTH (descrizione) <= 500)
);

CREATE TABLE utente (
    id INT AUTO_INCREMENT,
    password VARCHAR(100) NOT NULL,
    dataNascita DATE NOT NULL,
    username VARCHAR(50) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE (username),
    CHECK (username REGEXP "^[a-zA-Z0-9_]+$")
);

CREATE TABLE orarioapertura (
    giornoSettimana INT NOT NULL,
    orainizio VARCHAR(5) NOT NULL,
    orafine VARCHAR(5) NOT NULL,
    mensa VARCHAR(50) NOT NULL,
    PRIMARY KEY (giornoSettimana, orainizio,  orafine, mensa),
    FOREIGN KEY (mensa) REFERENCES mensa (nome) ON UPDATE CASCADE ON DELETE CASCADE,
    CHECK (
        giornoSettimana >= 1
        AND giornoSettimana <= 7
    ),
    CHECK (orainizio REGEXP "^[0-2][0-9]:[0-5][0-9]$"),
    CHECK (orafine REGEXP "^[0-2][0-9]:[0-5][0-9]$")
);

CREATE TABLE recensione (
    voto INT NOT NULL,
    descrizione TEXT,
    utente INT NOT NULL,
    piatto VARCHAR(100) NOT NULL,
    data DATE DEFAULT CURRENT_DATE,
    modificato BOOLEAN DEFAULT FALSE,
    CHECK (
        voto >= 1
        AND voto <= 5
    ),
    PRIMARY KEY (utente, piatto),
    FOREIGN KEY (utente) REFERENCES utente (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE menu (
    piatto VARCHAR(100) NOT NULL,
    mensa VARCHAR(50) NOT NULL,
    PRIMARY KEY (piatto, mensa),
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (mensa) REFERENCES mensa (nome) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE piatto_foto (
    photoid INT AUTO_INCREMENT,
    foto BLOB NOT NULL,
    piatto VARCHAR(100) NOT NULL,
    PRIMARY KEY (photoid, piatto),
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
);

-- Lista allergene dall'EU https://www.salute.gov.it/imgs/C_17_pagineAree_1460_0_file.pdf
CREATE TABLE piatto_allergeni (
    allergene ENUM ("Nessuno", "Glutine", "Crostacei", "Uova", "Pesce", "Arachidi", "Soia", "Latte", "Frutta_a_guscio", "Sedano", "Senape", "Sesamo", "Anidride_solforosa", "Lupini", "Molluschi"
    ) NOT NULL DEFAULT "Nessuno",
    piatto VARCHAR(100) NOT NULL,
    PRIMARY KEY (allergene, piatto),
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE preferenze_utente (
    utente INT NOT NULL,
    dimensione_testo ENUM ("piccolo", "medio", "grande") NOT NULL DEFAULT "medio",
    modifica_font ENUM ("normale", "dislessia") NOT NULL DEFAULT "normale",
    modifica_tema ENUM ("chiaro", "scuro", "sistema") NOT NULL DEFAULT "sistema",
    mensa_preferita VARCHAR(50) NULL DEFAULT NULL,
    PRIMARY KEY (utente),
    FOREIGN KEY (utente) REFERENCES utente (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (mensa_preferita) REFERENCES mensa (nome) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE allergeni_utente (
    utente int NOT NULL,
    allergene ENUM ("Glutine", "Crostacei", "Uova", "Pesce", "Arachidi", "Soia", "Latte", "Frutta_a_guscio", "Sedano", "Senape", "Sesamo", "Anidride_solforosa", "Lupini", "Molluschi") NOT NULL,
    PRIMARY KEY (utente, allergene),
    FOREIGN KEY (utente) REFERENCES utente (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE VIEW piatto_recensioni_foto AS
SELECT
    p.nome AS piatto,
    AVG(r.voto) AS media_stelle,
    GROUP_CONCAT(DISTINCT pf.foto ORDER BY RAND() SEPARATOR ", ") AS foto_casuali
FROM
    piatto p
    JOIN recensione r ON p.nome = r.piatto
    LEFT JOIN piatto_foto pf ON p.nome = pf.piatto
GROUP BY
    p.nome;

INSERT INTO utente (password, dataNascita, username)
VALUES ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1990-01-01", "Malik"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1990-01-01", "Giacomo"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1985-05-20", "Gulio"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1988-08-15", "Manuel"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1992-11-30", "Andrea"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1995-02-15", "Martin"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1970-01-01", "user"); -- password: user

INSERT INTO preferenze_utente(utente) VALUES (1);
INSERT INTO preferenze_utente(utente) VALUES (2);
INSERT INTO preferenze_utente(utente) VALUES (3);
INSERT INTO preferenze_utente(utente) VALUES (4);
INSERT INTO preferenze_utente(utente) VALUES (5);
INSERT INTO preferenze_utente(utente) VALUES (6);
INSERT INTO preferenze_utente(utente) VALUES (7);

INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ("RistorESU Agripolis", "Viale dell\'Università, 6 - Legnaro (PD)", "04 97430607", "https://www.google.com/maps/place/Mensa+Agripolis/@45.3474897,11.9577471,17z/data=!4m6!3m5!1s0x477ec378b59940cf:0x5b21dfbc8034b869!8m2!3d45.346961!4d11.9586004!16s%2Fg%2F11h9__56t4?entry=tts");
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ("RistorESU Nord Piovego", "Viale Giuseppe Colombo, 1 - Padova", "049 7430811", "https://www.google.com/maps/place/RistorEsu+Nord+Piovego/@45.4110432,11.8896739,1675m/data=!3m2!1e3!4b1!4m6!3m5!1s0x477edaf60d6b6371:0x2c00159331ead3d8!8m2!3d45.4110432!4d11.8896739!16s%2Fg%2F1pp2tjhxw?entry=tts");
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ("Mensa Murialdo", "Via Antonio Grassi, 42 - Padova", "049 772011", "https://www.google.com/maps/place/Mensa+Murialdo/@45.4130884,11.8994815,17z/data=!3m1!4b1!4m6!3m5!1s0x477edaed17825579:0x39ac780af76d258d!8m2!3d45.4130885!4d11.9043524!16s%2Fg%2F11g5zwxl4z?entry=tts");
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ("Mensa Azienda Ospedaliera di Padova", "Via Nicolò Giustiniani, 1 - Padova", "049 8211111", "https://www.google.com/maps/place/Azienda+Ospedale+Universit%C3%A0+Padova/@45.4029354,11.88911,19z/data=!4m6!3m5!1s0x477edaf91e846ae5:0x19313e029e7efd8a!8m2!3d45.4028873!4d11.8891995!16s%2Fg%2F11c6wm6888?entry=tts");
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ("Mensa Ciels", "Via Sebastiano Venier, 200 - Padova", "049 774152", "https://www.google.com/maps/place/Campus+CIELS+-+Sede+di+Padova/@45.3760046,11.8877834,17z/data=!3m2!4b1!5s0x477edb6d735b8e83:0xcc35839005059d33!4m6!3m5!1s0x477edb6d0ab7afc9:0xaef45488826e9515!8m2!3d45.3760046!4d11.8877834!16s%2Fg%2F1tgq5h7z?entry=ttu&g_ep=EgoyMDI0MTIwOS4wIKXMDSoASAFQAw%3D%3D");
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ("Casa del Fanciullo", "Vicolo Santonini, 12 - Padova", "049 8751075", "https://www.google.com/maps/place/Associazione+Casa+Del+Fanciullo/@45.3997459,11.879446,17z/data=!3m1!4b1!4m6!3m5!1s0x477eda55078d6023:0xf616c3a03d554e82!8m2!3d45.3997459!4d11.8820209!16s%2Fg%2F1pv5v58wj?entry=tts");
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ("Pio X", "Via Bonporti, 20 - Padova", "049 6895862", "https://www.google.com/maps/place/Mensa+Pio+X/@45.4053724,11.8688651,17z/data=!3m1!4b1!4m6!3m5!1s0x477eda4e563f1161:0x135b6ab250952049!8m2!3d45.4053724!4d11.87144!16s%2Fg%2F11cjk2k92p?entry=tts");

INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "11:45", "14:30", "RistorESU Agripolis");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "11:45", "14:30", "RistorESU Agripolis");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "11:45", "14:30", "RistorESU Agripolis");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "11:45", "14:30", "RistorESU Agripolis");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "11:45", "14:30", "RistorESU Agripolis");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "11:30", "14:30", "RistorESU Nord Piovego");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "11:30", "14:30", "RistorESU Nord Piovego");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "11:30", "14:30", "RistorESU Nord Piovego");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "11:30", "14:30", "RistorESU Nord Piovego");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "11:30", "14:30", "RistorESU Nord Piovego");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "11:45", "14:30", "Mensa Murialdo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "11:45", "14:30", "Mensa Murialdo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "11:45", "14:30", "Mensa Murialdo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "11:45", "14:30", "Mensa Murialdo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "11:45", "14:30", "Mensa Murialdo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "12:00", "15:00", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "12:00", "15:00", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "12:00", "15:00", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "12:00", "15:00", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "12:00", "15:00", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "11:45", "14:30", "Mensa Ciels");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "11:45", "14:30", "Mensa Ciels");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "11:45", "14:30", "Mensa Ciels");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "11:45", "14:30", "Mensa Ciels");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "11:45", "14:30", "Mensa Ciels");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "08:00", "19:00", "Casa del Fanciullo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "08:00", "19:00", "Casa del Fanciullo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "08:00", "19:00", "Casa del Fanciullo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "08:00", "19:00", "Casa del Fanciullo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "08:00", "19:00", "Casa del Fanciullo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (6, "08:00", "12:30", "Casa del Fanciullo");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "11:45", "14:30", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "11:45", "14:30", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "11:45", "14:30", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "11:45", "14:30", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "11:45", "14:30", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, "18:45", "21:00", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, "18:45", "21:00", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, "18:45", "21:00", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, "18:45", "21:00", "Pio X");
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, "18:45", "21:00", "Pio X");

-- ========== PRIMI PIATTI (ordinati alfabeticamente) ==========
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Bis di cereali con verdure", "Primo", "Mix di cereali con verdure di stagione.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Crema di funghi", "Primo", "Vellutata di funghi porcini.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Crema di piselli", "Primo", "Vellutata di piselli freschi con un tocco di menta.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Gnocchi al pomodoro", "Primo", "Gnocchi di patate con sugo di pomodoro.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Minestra di verdure", "Primo", "Zuppa di verdure miste.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Orzo con pomodorini e basilico", "Primo", "Orzo perlato condito con pomodorini freschi e basilico.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta al ragù", "Primo", "Pasta con ragù di carne.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta al tonno e olive", "Primo", "Pasta con tonno e olive.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta all'arrabbiata", "Primo", "Pasta con sugo di pomodoro piccante.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta alla carbonara", "Primo", "Pasta condita con uova, guanciale e pecorino.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta alla Norma", "Primo", "Pasta con melanzane fritte e ricotta salata.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta e fagioli alla veneta", "Primo", "Pasta e fagioli preparata secondo la tradizione veneta.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta salmone e zucchine", "Primo", "Pasta condita con salmone affumicato e zucchine.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta zucca e funghi", "Primo", "Pasta con zucca e funghi.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Riso al curry", "Primo", "Riso basmati con curry e verdure.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Riso pilaw con piselli", "Primo", "Riso pilaw con piselli freschi.");

-- ========== SECONDI PIATTI (ordinati alfabeticamente) ==========
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Arrosto di maiale", "Secondo", "Arrosto di maiale con erbe aromatiche.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Arrosto di tacchino", "Secondo", "Arrosto di tacchino con erbe aromatiche.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Coscette di pollo", "Secondo", "Coscette di pollo arrosto.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Falafel", "Secondo", "Polpette di ceci speziate.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Filetto di merluzzo", "Secondo", "Filetto di merluzzo impanato e fritto.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Filetto di platessa alla marchigiana", "Secondo", "Filetto di platessa con pomodoro e olive.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Frittata con verdure e formaggio", "Secondo", "Frittata con verdure miste e formaggio.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Hamburger vegano", "Secondo", "Hamburger vegano a base di legumi.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalata vegana con carote, zucchine, fagioli e mais", "Secondo", "Insalata vegana con carote, zucchine, fagioli e mais.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalata vegana con ceci, patate, carote e melanzane", "Secondo", "Insalata vegana con ceci, patate, carote e melanzane grigliate.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Kebab di pollo", "Secondo", "Kebab di pollo con spezie orientali.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Melanzana alla siciliana", "Secondo", "Melanzana ripiena alla siciliana.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Melanzana con pomodoro, capperi e olive", "Secondo", "Melanzana condita con pomodoro, capperi e olive.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Mozzarella alla romana", "Secondo", "Mozzarella impanata e fritta.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Peperoni alla partenopea", "Secondo", "Peperoni ripieni alla napoletana.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Roast beef con funghi", "Secondo", "Roast beef con funghi trifolati.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Tortino ricotta e spinaci", "Secondo", "Tortino di pasta sfoglia ripieno di ricotta e spinaci.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Trancio di pizza margherita", "Secondo", "Trancio di pizza margherita con mozzarella e pomodoro.");

-- ========== CONTORNI (ordinati alfabeticamente) ==========
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Carote al vapore", "Contorno", "Carote cotte al vapore, condite con un filo d'olio.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Carote e piselli al vapore", "Contorno", "Carote e piselli cotti al vapore.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Ceci", "Contorno", "Ceci lessati.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Cavoli lessati", "Contorno", "Cavoli cotti al vapore.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Fagioli in umido", "Contorno", "Fagioli cotti lentamente in umido con pomodoro e spezie.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Fagiolini", "Contorno", "Fagiolini freschi cotti al vapore.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalata", "Contorno", "Insalata mista con verdure fresche.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Patate al basilico", "Contorno", "Patate al forno aromatizzate con basilico fresco.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Patate fritte", "Contorno", "Patate fritte croccanti.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Piselli", "Contorno", "Piselli freschi cotti al vapore.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Tris di verdure", "Contorno", "Mix di verdure cotte al vapore.");

-- ========== INSERIMENTO ALLERGENI ORGANIZZATO ==========

-- PIATTI SENZA ALLERGENI (solo verdure/frutta/legumi semplici)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Fagioli in umido");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Crema di piselli");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Carote al vapore");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Fagiolini");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalata vegana con ceci, patate, carote e melanzane");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Melanzana con pomodoro, capperi e olive");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalata vegana con carote, zucchine, fagioli e mais");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Carote e piselli al vapore");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Piselli");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Ceci");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Riso pilaw con piselli");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Crema di funghi");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Cavoli lessati");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalata");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Arrosto di maiale");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Arrosto di tacchino");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Coscette di pollo");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Roast beef con funghi");

-- PIATTI CON SOLO GLUTINE (paste e cereali)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Orzo con pomodorini e basilico");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta all'arrabbiata");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta e fagioli alla veneta");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta al ragù");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Gnocchi al pomodoro");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Bis di cereali con verdure");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Peperoni alla partenopea");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Melanzana alla siciliana");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta zucca e funghi");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Kebab di pollo");

-- PIATTI CON GLUTINE + LATTE
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta alla Norma");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Pasta alla Norma");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Trancio di pizza margherita");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Trancio di pizza margherita");

-- PIATTI CON GLUTINE + PESCE
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta salmone e zucchine");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Pasta salmone e zucchine");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta al tonno e olive");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Pasta al tonno e olive");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Filetto di merluzzo");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Filetto di merluzzo");

-- PIATTI CON GLUTINE + UOVA + LATTE
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Tortino ricotta e spinaci");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Tortino ricotta e spinaci");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Uova", "Tortino ricotta e spinaci");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Mozzarella alla romana");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Mozzarella alla romana");

-- PIATTI CON UOVA + LATTE
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Uova", "Frittata con verdure e formaggio");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Frittata con verdure e formaggio");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta alla carbonara");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Uova", "Pasta alla carbonara");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Pasta alla carbonara");

-- PIATTI CON SOLO PESCE
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Filetto di platessa alla marchigiana");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Hamburger vegano");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Soia", "Hamburger vegano");

-- PIATTI CON GLUTINE + SESAMO
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Falafel");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Sesamo", "Falafel");

-- PIATTI CON SENAPE
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Senape", "Riso al curry");

-- PIATTI CON SEDANO
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Sedano", "Minestra di verdure");

-- ===============================================
-- INSERIMENTO RECENSIONI PIATTI
-- ===============================================

-- INSERT recensioni with exact piatto names that match the database
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Alto assorbimento delle radiazioni elettromagnetiche, quasi al pari di un corpo nero. Densità alta ma non esagerata, conforme agli standard UNI ISO. Sapore buono, leggermente salato, temperatura leggermente al di sopra di quella ambientale. Frazione in massa di pasta infima ma ciò è possibile che sia dovuto al fatto che il piatto servito fosse solo una delle prime mestolate. Pro: sapore, senso di sazietà raggiunto facilmente, ottimo materiale edile Contro: mancanza di nota piccante, produzione incontrollabile di metano gassoso da orifizi corporei.", 1, "Pasta e fagioli alla veneta");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Il sapore è buono ma la texture non mi è piaciuta. Ci sono troppi pezzi grossi, non è stata tritata bene.", 2, "Crema di piselli");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Fredda e non frullata bene.", 3, "Crema di piselli");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buona, ma calda sarebbe stata meglio.", 4, "Pasta alla Norma");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Mangiabile.", 5, "Pasta alla Norma");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Melanzane un po' acide ma buona.", 1, "Pasta alla Norma");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Nulla da dire, è buona. Magari calda sarebbe stata più gradita.", 2, "Pasta alla Norma");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Pasta troppo cotta, il sugo però era buono.", 3, "Pasta al ragù");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Pasta cotta giusta e ragù molto saporito, classica ma molto buona.", 4, "Pasta al ragù");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, riso non stracotto, un po' al dente.", 5, "Riso al curry");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Riso un po' al dente ma rimane comunque buono.", 1, "Riso pilaw con piselli");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buono, riso al dente ma non crudo.", 2, "Riso pilaw con piselli");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Personalmente preferisco le minestre frullate, però il gusto era buono.", 3, "Minestra di verdure");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Eccellente, nulla da dire se non buonissima.", 4, "Crema di funghi");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Sugo molto saporito.", 5, "Pasta all'arrabbiata");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buona, il sugo è piccante al punto giusto.", 1, "Pasta all'arrabbiata");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Troppo salato, cereali un po' troppo al dente e basilico inesistente.", 2, "Orzo con pomodorini e basilico");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buona e gustosa, il sugo era abbondante e saporito, si mangia molto volentieri. Peccato per la pasta un po' al dente.", 3, "Pasta alla carbonara");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Dose buona, condimento abbondante e quantità di pancetta giusta, ma il sugo me lo aspettavo un po' più saporito.", 4, "Pasta alla carbonara");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Gli gnocchi erano della consistenza giusta e non molli, e il sugo molto saporito, nel complesso davvero buoni.", 5, "Gnocchi al pomodoro");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Ottimo considerando anche la mancanza di spine. Tramite analisi olfattiva e degustativa è possibile affermare l'assenza di olio di palma e di cadmio.", 1, "Filetto di platessa alla marchigiana");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, la panatura è croccante e asciutta.", 2, "Filetto di merluzzo");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, la panatura è asciutta e croccante.", 3, "Filetto di merluzzo");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Il gusto era buono ma c'era troppo olio sulla melanzana.", 4, "Melanzana con pomodoro, capperi e olive");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Il sapore è buono, la carne non è secca.", 5, "Arrosto di tacchino");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buono il ripieno, la pasta sfoglia però è un po' dura e difficile da tagliare.", 1, "Tortino ricotta e spinaci");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto saporito, forse un po' salato ma molto buono.", 2, "Tortino ricotta e spinaci");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buona ma impasta un po' in bocca.", 3, "Mozzarella alla romana");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buona, il pomodoro le dà quel tocco in più che ci sta. Arrivi alla fine che sei sazio.", 4, "Mozzarella alla romana");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Cotto bene, aromatizzato bene, buono.", 5, "Coscette di pollo");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Si lascia mangiare, bene.", 1, "Coscette di pollo");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buono, non c'è tanto da dire, il nome coincide con il prodotto, l'unica pecca è che secondo me sono assenti totalmente le proteine, ci sono solo verdure.", 2, "Hamburger vegano");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Tutto buono tranne i pomodorini che erano amari.", 3, "Insalata");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Le patate erano crude, le verdure fredde e i ceci erano pochi. Apporto proteico molto basso per essere un secondo.", 4, "Insalata vegana con ceci, patate, carote e melanzane");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Saporito, i cereali cotti al punto giusto. Ottimo anche per variare la fonte di carboidrati e non mangiare sempre grano.", 5, "Bis di cereali con verdure");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Il gusto di per sé era buono, anche se erano zuppi di acqua. Inoltre, la mozzarella sopra ai peperoni era palesemente la mozzarella alla romana avanzata dai giorni scorsi.", 1, "Peperoni alla partenopea");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, "Più bagnata dell'oceano, ha la consistenza di una spugna. Nel complesso il sapore è orribile.", 2, "Frittata con verdure e formaggio");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buona, ma il rapporto capperi/topping è troppo alto.", 3, "Melanzana con pomodoro, capperi e olive");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buono, i funghi ci stanno molto bene.", 4, "Roast beef con funghi");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, riempie molto, il problema è la distribuzione di mozzarella tra i vari tranci che non è per nulla uniforme.", 5, "Trancio di pizza margherita");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buono, l'impasto era morbido e soffice e nel complesso molto gustosa.", 1, "Trancio di pizza margherita");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buona anche da fredda, buon apporto di legumi.", 2, "Insalata vegana con carote, zucchine, fagioli e mais");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Letteralmente il pezzo di formaggio più duro che io abbia mangiato, impossibile da tagliare, pesante. Nel complesso non è stata un'esperienza piacevole mangiarla.", 3, "Melanzana alla siciliana");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Buonissimi, poco da aggiungere.", 5, "Fagiolini");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buoni ma freddi.", 1, "Fagiolini");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buoni, ma leggermente crudi.", 2, "Fagiolini");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buoni, nulla da dire.", 3, "Fagiolini");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Gusto medio, abbastanza buoni ma sempre freddi.", 4, "Fagiolini");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Non sono croccanti, sono infatti un po' molli, però almeno non sono troppo salate.", 5, "Patate fritte");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone, peccato siano un po' fredde e molli.", 1, "Patate fritte");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Sono patate fritte, sono standard, nulla da aggiungere.", 2, "Patate fritte");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono anche se avrei preferito mangiarlo caldo.", 3, "Tris di verdure");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Un buon contorno, forse le verdure erano un po' troppo bagnate.", 4, "Tris di verdure");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone, l'unica pecca sono i cavolfiori poco cotti.", 5, "Tris di verdure");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, "Mix tra crude e cotte, non si riescono a mangiare.", 1, "Patate al basilico");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Cotte, aromatizzate poco, due erbette potevano metterle.", 2, "Patate al basilico");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Metà dei bocconi sono crudi e l'altra metà cotti, non hanno tutti la stessa consistenza. Sanno di poco e sono completamente scondite, consiglio di prendere sale e olio.", 3, "Patate al basilico");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Spesso alcune sono crude, ma in questo caso erano tutte apposto.", 4, "Patate al basilico");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Incredibilmente quasi tutti i bocconi erano cotti, un miglioramento.", 5, "Patate al basilico");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Le carote sono abbastanza bagnate e non caldissime, comunque rimangono un buon contorno.", 2, "Carote e piselli al vapore");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone ma un po' secche.", 3, "Carote al vapore");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buone, un po' secche.", 4, "Carote al vapore");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buoni, rappresentativi della cultura veneta.", 5, "Fagioli in umido");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "I funghi sovrastano un po' la zucca, che si sente meno, ma nel complesso molto buona e soddisfacente.", 2, "Pasta zucca e funghi");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "La pasta era discretamente buona e di buona cottura, il pomodoro non è troppo acido (cosa rara).", 3, "Pasta al tonno e olive");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Sugo molto buono, si mangia di gusto. Peccato che le zucchine si sentano poco, ma nel complesso buono.", 5, "Pasta salmone e zucchine");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Arrosto di buona cottura e consistenza, unica pecca l'eccessiva salinità (troppo sale).", 1, "Arrosto di maiale");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Cotte bene.", 2, "Coscette di pollo");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buoni e saporiti, si mangiano volentieri. Unica pecca, sono un po'granulosi all'interno, per il resto ottimi.", 5, "Falafel");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buoni e ben speziati, sanno proprio di falafel. Sono un po' asciutte, ma mangiati con la maionese sono top.", 1, "Falafel");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Normale, nulla da dire.", 2, "Kebab di pollo");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Nulla da dire, buoni, standard.", 3, "Piselli");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Ceci di scarsa qualità, granulosi e di consistenza inadeguata.", 4, "Ceci");

-- ===============================================
-- INSERIMENTO FOTO PIATTI AGGIORNATA
-- ===============================================

-- ========== PRIMI PIATTI (16 totali - ordinati alfabeticamente) ==========
INSERT INTO piatto_foto (piatto, foto) VALUES ("Bis di cereali con verdure", "images/uploads/bis-di-cereali-con-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Crema di funghi", "images/uploads/crema-di-funghi.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Crema di piselli", "images/uploads/crema-di-piselli2.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Gnocchi al pomodoro", "images/uploads/gnocchi-al-pomodoro.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Minestra di verdure", "images/uploads/minestra-di-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Orzo con pomodorini e basilico", "images/uploads/orzo-con-pomodorini-e-basilico.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta al ragù", "images/uploads/pasta-al-ragu.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta al tonno e olive", "images/uploads/pasta-tonno-e-olive.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta all'arrabbiata", "images/uploads/pasta-all'arrabbiata.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta alla carbonara", "images/uploads/pasta-alla-carbonara.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta alla Norma", "images/uploads/pasta-alla-norma.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta e fagioli alla veneta", "images/uploads/pasta-e-fagioli-alla-veneta.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta salmone e zucchine", "images/uploads/pasta-salmone-e-zucchine.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta zucca e funghi", "images/uploads/pasta-zucca-e-funghi.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Riso al curry", "images/uploads/riso-al-curry.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Riso pilaw con piselli", "images/uploads/riso-pilaw-con-piselli-1.webp");

-- ========== SECONDI PIATTI (19 totali - ordinati alfabeticamente) ==========
INSERT INTO piatto_foto (piatto, foto) VALUES ("Arrosto di maiale", "images/uploads/arrosto-di-maiale.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Arrosto di tacchino", "images/uploads/arrosto-di-tacchino.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Coscette di pollo", "images/uploads/coscette-di-pollo.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Falafel", "images/uploads/falafel.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Filetto di merluzzo", "images/uploads/filetto-di-merluzzo.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Filetto di platessa alla marchigiana", "images/uploads/filetto-di-platessa-alla-marchigiana.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Frittata con verdure e formaggio", "images/uploads/frittata-con-verdure-e-formaggio.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Hamburger vegano", "images/uploads/hamburger-vegano.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Insalata vegana con carote, zucchine, fagioli e mais", "images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Insalata vegana con ceci, patate, carote e melanzane", "images/uploads/insalata-vegana-con-ceci-patate-carote-e-melanzane.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Kebab di pollo", "images/uploads/kebab-di-pollo.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Melanzana alla siciliana", "images/uploads/melanzana-alla-siciliana.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Melanzana con pomodoro, capperi e olive", "images/uploads/melanzana-con-pomodoro-capperi-e-olive.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Mozzarella alla romana", "images/uploads/mozzarella-alla-romana.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Peperoni alla partenopea", "images/uploads/peperoni-alla-partenopea.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Roast beef con funghi", "images/uploads/roast-beef-con-funghi.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Tortino ricotta e spinaci", "images/uploads/tortino-ricotta-e-spinaci.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Trancio di pizza margherita", "images/uploads/trancio-di-pizza-margherita.webp");

-- ========== CONTORNI (11 totali - ordinati alfabeticamente) ==========
INSERT INTO piatto_foto (piatto, foto) VALUES ("Carote al vapore", "images/uploads/carote-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Carote e piselli al vapore", "images/uploads/carote-e-piselli-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Cavoli lessati", "images/uploads/cavoli-lessati.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Ceci", "images/uploads/ceci.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Fagioli in umido", "images/uploads/fagioli-in-umido.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Fagiolini", "images/uploads/fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Insalata", "images/uploads/insalata.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Patate al basilico", "images/uploads/patate-al-basilico.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Patate fritte", "images/uploads/patatine-fritte.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Piselli", "images/uploads/piselli.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Tris di verdure", "images/uploads/tris-di-verdure.webp");

DELIMITER //

CREATE PROCEDURE crea_menu_settimanale()
-- 11 contorni 24 secondi e 17 primi + insalata fissa
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE mensa_nome VARCHAR(50);

    DECLARE mensa_cursor CURSOR FOR 
        SELECT nome FROM mensa;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Errore durante la creazione del menu settimanale';
    END;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;
    
    OPEN mensa_cursor;
    START TRANSACTION;

    DELETE FROM menu;

    REPEAT
        FETCH mensa_cursor INTO mensa_nome;
        IF NOT done THEN

            -- 3 primi casuali
            INSERT IGNORE INTO menu (piatto, mensa)
            SELECT p.nome, mensa_nome
            FROM piatto p
            WHERE p.categoria = 'Primo'
            ORDER BY RAND()
            LIMIT 3;

            -- 3 secondi casuali
            INSERT IGNORE INTO menu (piatto, mensa)
            SELECT p.nome, mensa_nome
            FROM piatto p
            WHERE p.categoria = 'Secondo'
            ORDER BY RAND()
            LIMIT 3;

            -- 2 contorni casuali
            INSERT IGNORE INTO menu (piatto, mensa)
            SELECT p.nome, mensa_nome
            FROM piatto p
            WHERE p.categoria = 'Contorno'
            AND p.nome != 'Insalata' 
            ORDER BY RAND()
            LIMIT 2;

            -- Insalata fissa per ogni mensa
            INSERT IGNORE INTO menu (piatto, mensa)
            VALUES ('Insalata', mensa_nome);

        END IF;
    UNTIL done END REPEAT;
    COMMIT;

    CLOSE mensa_cursor;
END //

-- necessita di event_scheduler = ON nel file di configurazione mariadb;
CREATE EVENT IF NOT EXISTS crea_menu_settimanale_event
ON SCHEDULE 
    EVERY 1 WEEK
    STARTS CURRENT_TIMESTAMP + INTERVAL 5 SECOND
    ON COMPLETION PRESERVE
ENABLE
DO
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    END;
    
    CALL crea_menu_settimanale();
END //

CREATE TRIGGER after_utente_insert
AFTER INSERT ON utente
FOR EACH ROW
BEGIN
    INSERT INTO preferenze_utente (utente) VALUES (NEW.id);
END //

DELIMITER ;

CALL crea_menu_settimanale();