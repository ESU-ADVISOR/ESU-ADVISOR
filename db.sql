SET CHARACTER SET utf8mb4;

DROP VIEW IF EXISTS mensa_orari_apertura;
DROP VIEW IF EXISTS piatto_recensioni_foto;
DROP TABLE IF EXISTS preferenze_utente;
DROP TABLE IF EXISTS allergeni_utente;
DROP TABLE IF EXISTS piatto_allergeni;
DROP TABLE IF EXISTS piatto_foto;
DROP TABLE IF EXISTS recensione;
DROP TABLE IF EXISTS menu;
DROP TABLE IF EXISTS orarioapertura;
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
    username VARCHAR(50) NOT NULL,
    password VARCHAR(100) NOT NULL,
    dataNascita DATE NOT NULL,
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
        giornoSettimana >=1
        AND giornoSettimana <= 7
    ),
    CHECK (orainizio REGEXP "^[0-2][0-9]:[0-5][0-9]$"),
    CHECK (orafine REGEXP "^[0-2][0-9]:[0-5][0-9]$")
);

CREATE TABLE menu (
    piatto VARCHAR(100) NOT NULL,
    mensa VARCHAR(50) NOT NULL,
    PRIMARY KEY (piatto, mensa),
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (mensa) REFERENCES mensa (nome) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE recensione (
    voto INT NOT NULL,
    descrizione TEXT,
    idUtente INT NOT NULL,
    piatto VARCHAR(100) NOT NULL,
    mensa VARCHAR(50) NOT NULL,
    data DATE DEFAULT CURRENT_DATE,
    modificato BOOLEAN DEFAULT FALSE,
    CHECK (
        voto >= 1
        AND voto <= 5
    ),
    PRIMARY KEY (idUtente, piatto, mensa),
    FOREIGN KEY (idUtente) REFERENCES utente (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (piatto, mensa) REFERENCES menu (piatto, mensa) ON UPDATE CASCADE ON DELETE CASCADE
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
    idUtente INT NOT NULL,
    dimensione_testo ENUM ("piccolo", "medio", "grande") NOT NULL DEFAULT "medio",
    modifica_font ENUM ("normale", "dislessia") NOT NULL DEFAULT "normale",
    modifica_tema ENUM ("chiaro", "scuro", "sistema") NOT NULL DEFAULT "sistema",
    mensa_preferita VARCHAR(50) NULL DEFAULT NULL,
    PRIMARY KEY (idUtente),
    FOREIGN KEY (idUtente) REFERENCES utente (id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (mensa_preferita) REFERENCES mensa (nome) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE allergeni_utente (
    idUtente INT NOT NULL,
    allergene ENUM ("Glutine", "Crostacei", "Uova", "Pesce", "Arachidi", "Soia", "Latte", "Frutta_a_guscio", "Sedano", "Senape", "Sesamo", "Anidride_solforosa", "Lupini", "Molluschi") NOT NULL,
    PRIMARY KEY (idUtente, allergene),
    FOREIGN KEY (idUtente) REFERENCES utente (id) ON UPDATE CASCADE ON DELETE CASCADE
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
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1970-01-01", "user"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1999-03-12", "Sofia"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1997-07-08", "Lorenzo"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1993-11-22", "Chiara"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1991-05-14", "Marco"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1996-09-03", "Giulia"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1989-12-17", "Alessandro"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1994-04-29", "Francesca"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1998-08-11", "Matteo"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1992-01-25", "Valentina"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1987-06-18", "Davide"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1995-10-07", "Elena"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1990-02-13", "Luca"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1988-11-09", "Federico"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1997-03-26", "Martina"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1991-09-14", "Simone");

INSERT INTO preferenze_utente(idUtente) VALUES (1);
INSERT INTO preferenze_utente(idUtente) VALUES (2);
INSERT INTO preferenze_utente(idUtente) VALUES (3);
INSERT INTO preferenze_utente(idUtente) VALUES (4);
INSERT INTO preferenze_utente(idUtente) VALUES (5);
INSERT INTO preferenze_utente(idUtente) VALUES (6);
INSERT INTO preferenze_utente(idUtente) VALUES (7);
INSERT INTO preferenze_utente(idUtente) VALUES (8);
INSERT INTO preferenze_utente(idUtente) VALUES (9);
INSERT INTO preferenze_utente(idUtente) VALUES (10);
INSERT INTO preferenze_utente(idUtente) VALUES (11);
INSERT INTO preferenze_utente(idUtente) VALUES (12);
INSERT INTO preferenze_utente(idUtente) VALUES (13);
INSERT INTO preferenze_utente(idUtente) VALUES (14);
INSERT INTO preferenze_utente(idUtente) VALUES (15);
INSERT INTO preferenze_utente(idUtente) VALUES (16);
INSERT INTO preferenze_utente(idUtente) VALUES (17);
INSERT INTO preferenze_utente(idUtente) VALUES (18);
INSERT INTO preferenze_utente(idUtente) VALUES (19);
INSERT INTO preferenze_utente(idUtente) VALUES (20);
INSERT INTO preferenze_utente(idUtente) VALUES (21);
INSERT INTO preferenze_utente(idUtente) VALUES (22);

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
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalata vegana con carote zucchine fagioli e mais", "Secondo", "Insalata vegana con carote zucchine fagioli e mais.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalata vegana con ceci patate carote e melanzane", "Secondo", "Insalata vegana con ceci patate carote e melanzane grigliate.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Kebab di pollo", "Secondo", "Kebab di pollo con spezie orientali.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Melanzana alla siciliana", "Secondo", "Melanzana ripiena alla siciliana.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Melanzana con pomodoro capperi e olive", "Secondo", "Melanzana condita con pomodoro, capperi e olive.");
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
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalata vegana con ceci patate carote e melanzane");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Melanzana con pomodoro capperi e olive");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalata vegana con carote zucchine fagioli e mais");
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
-- FOTO PIATTI (mantiene la sezione originale)
-- ===============================================

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

INSERT INTO piatto_foto (piatto, foto) VALUES ("Arrosto di maiale", "images/uploads/arrosto-di-maiale.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Arrosto di tacchino", "images/uploads/arrosto-di-tacchino.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Coscette di pollo", "images/uploads/coscette-di-pollo.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Falafel", "images/uploads/falafel.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Filetto di merluzzo", "images/uploads/filetto-di-merluzzo.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Filetto di platessa alla marchigiana", "images/uploads/filetto-di-platessa-alla-marchigiana.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Frittata con verdure e formaggio", "images/uploads/frittata-con-verdure-e-formaggio.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Hamburger vegano", "images/uploads/hamburger-vegano.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Insalata vegana con carote zucchine fagioli e mais", "images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Insalata vegana con ceci patate carote e melanzane", "images/uploads/insalata-vegana-con-ceci-patate-carote-e-melanzane.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Kebab di pollo", "images/uploads/kebab-di-pollo.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Melanzana alla siciliana", "images/uploads/melanzana-alla-siciliana.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Melanzana con pomodoro capperi e olive", "images/uploads/melanzana-con-pomodoro-capperi-e-olive.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Mozzarella alla romana", "images/uploads/mozzarella-alla-romana.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Peperoni alla partenopea", "images/uploads/peperoni-alla-partenopea.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Roast beef con funghi", "images/uploads/roast-beef-con-funghi.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Tortino ricotta e spinaci", "images/uploads/tortino-ricotta-e-spinaci.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Trancio di pizza margherita", "images/uploads/trancio-di-pizza-margherita.webp");

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

-- ===============================================
-- MENU BILANCIATI PER OGNI MENSA
-- ===============================================

-- RistorESU Agripolis (menu completo - 5 primi, 6 secondi, 4 contorni)
INSERT INTO menu (piatto, mensa) VALUES ("Pasta e fagioli alla veneta", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Crema di piselli", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla Norma", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta al ragù", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Riso al curry", "RistorESU Agripolis");

INSERT INTO menu (piatto, mensa) VALUES ("Filetto di platessa alla marchigiana", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Filetto di merluzzo", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Melanzana con pomodoro capperi e olive", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Arrosto di tacchino", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Tortino ricotta e spinaci", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Mozzarella alla romana", "RistorESU Agripolis");

INSERT INTO menu (piatto, mensa) VALUES ("Insalata", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Fagiolini", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Patate fritte", "RistorESU Agripolis");
INSERT INTO menu (piatto, mensa) VALUES ("Tris di verdure", "RistorESU Agripolis");

-- RistorESU Nord Piovego (5 primi, 6 secondi, 4 contorni)
INSERT INTO menu (piatto, mensa) VALUES ("Pasta al ragù", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla carbonara", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Gnocchi al pomodoro", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Riso pilaw con piselli", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Crema di funghi", "RistorESU Nord Piovego");

INSERT INTO menu (piatto, mensa) VALUES ("Coscette di pollo", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Filetto di merluzzo", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Hamburger vegano", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Arrosto di tacchino", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Falafel", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Trancio di pizza margherita", "RistorESU Nord Piovego");

INSERT INTO menu (piatto, mensa) VALUES ("Insalata", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Patate fritte", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Fagiolini", "RistorESU Nord Piovego");
INSERT INTO menu (piatto, mensa) VALUES ("Carote al vapore", "RistorESU Nord Piovego");

-- Mensa Murialdo (5 primi, 6 secondi, 4 contorni)
INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla Norma", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta all'arrabbiata", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Minestra di verdure", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Orzo con pomodorini e basilico", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta zucca e funghi", "Mensa Murialdo");

INSERT INTO menu (piatto, mensa) VALUES ("Hamburger vegano", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Falafel", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Trancio di pizza margherita", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Peperoni alla partenopea", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Melanzana alla siciliana", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Frittata con verdure e formaggio", "Mensa Murialdo");

INSERT INTO menu (piatto, mensa) VALUES ("Insalata", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Tris di verdure", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Piselli", "Mensa Murialdo");
INSERT INTO menu (piatto, mensa) VALUES ("Ceci", "Mensa Murialdo");

-- Mensa Azienda Ospedaliera di Padova (5 primi, 6 secondi, 4 contorni - menu salutare)
INSERT INTO menu (piatto, mensa) VALUES ("Crema di piselli", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Minestra di verdure", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Riso pilaw con piselli", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta al tonno e olive", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta salmone e zucchine", "Mensa Azienda Ospedaliera di Padova");

INSERT INTO menu (piatto, mensa) VALUES ("Filetto di platessa alla marchigiana", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Arrosto di tacchino", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Frittata con verdure e formaggio", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Insalata vegana con ceci patate carote e melanzane", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Bis di cereali con verdure", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Insalata vegana con carote zucchine fagioli e mais", "Mensa Azienda Ospedaliera di Padova");

INSERT INTO menu (piatto, mensa) VALUES ("Insalata", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Carote al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Fagiolini", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO menu (piatto, mensa) VALUES ("Carote e piselli al vapore", "Mensa Azienda Ospedaliera di Padova");

-- Mensa Ciels (5 primi, 6 secondi, 4 contorni)
INSERT INTO menu (piatto, mensa) VALUES ("Pasta al ragù", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Gnocchi al pomodoro", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla carbonara", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Riso al curry", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Crema di funghi", "Mensa Ciels");

INSERT INTO menu (piatto, mensa) VALUES ("Coscette di pollo", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Mozzarella alla romana", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Arrosto di maiale", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Roast beef con funghi", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Kebab di pollo", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Tortino ricotta e spinaci", "Mensa Ciels");

INSERT INTO menu (piatto, mensa) VALUES ("Insalata", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Patate fritte", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Fagioli in umido", "Mensa Ciels");
INSERT INTO menu (piatto, mensa) VALUES ("Patate al basilico", "Mensa Ciels");

-- Casa del Fanciullo (5 primi, 6 secondi, 4 contorni)
INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla carbonara", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Minestra di verdure", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta e fagioli alla veneta", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta all'arrabbiata", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Orzo con pomodorini e basilico", "Casa del Fanciullo");

INSERT INTO menu (piatto, mensa) VALUES ("Arrosto di tacchino", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Filetto di merluzzo", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Coscette di pollo", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Melanzana con pomodoro capperi e olive", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Peperoni alla partenopea", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Hamburger vegano", "Casa del Fanciullo");

INSERT INTO menu (piatto, mensa) VALUES ("Insalata", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Fagiolini", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Carote al vapore", "Casa del Fanciullo");
INSERT INTO menu (piatto, mensa) VALUES ("Piselli", "Casa del Fanciullo");

-- Pio X (5 primi, 6 secondi, 4 contorni)
INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla Norma", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Crema di funghi", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Gnocchi al pomodoro", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta zucca e funghi", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Pasta salmone e zucchine", "Pio X");

INSERT INTO menu (piatto, mensa) VALUES ("Hamburger vegano", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Trancio di pizza margherita", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Falafel", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Melanzana alla siciliana", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Frittata con verdure e formaggio", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Bis di cereali con verdure", "Pio X");

INSERT INTO menu (piatto, mensa) VALUES ("Insalata", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Carote al vapore", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Ceci", "Pio X");
INSERT INTO menu (piatto, mensa) VALUES ("Cavoli lessati", "Pio X");

-- ===============================================
-- RECENSIONI ORIGINALI + NUOVE RECENSIONI
-- ===============================================

INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Un classico della tradizione veneta preparato alla perfezione. I fagioli sono cremosi e la pasta cotta al punto giusto.", 8, "Pasta e fagioli alla veneta", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Piatto sostanzioso ma un po' pesante. Il sapore è autentico ma la presentazione potrebbe essere migliore.", 12, "Pasta e fagioli alla veneta", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buono e caldo, perfetto per una giornata fredda. I fagioli sono ben cotti e saporiti.", 17, "Pasta e fagioli alla veneta", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Saporito e nutriente, anche se un po' salato per i miei gusti. Nel complesso soddisfacente.", 21, "Pasta e fagioli alla veneta", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Il sapore è buono ma la texture non mi è piaciuta. Ci sono troppi pezzi grossi, non è stata tritata bene.",2, "Crema di piselli", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Fredda e non frullata bene.",3, "Crema di piselli", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Delicata e vellutata, con un bel colore verde. Solo un po' fredda quando servita.", 9, "Crema di piselli", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Cremosa e ben speziata, il tocco di menta si sente al punto giusto. Davvero buona!", 14, "Crema di piselli", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona, ma calda sarebbe stata meglio.",4, "Pasta alla Norma", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Mangiabile.",5, "Pasta alla Norma", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Melanzane un po' acide ma buona.", 10, "Pasta alla Norma", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Nulla da dire, è buona. Magari calda sarebbe stata più gradita.", 15, "Pasta alla Norma", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Pasta perfetta con melanzane fritte croccanti e ricotta salata di qualità. Un piatto siciliano fatto bene!", 18, "Pasta alla Norma", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Le melanzane erano un po' oleose e la ricotta scarsa. Il sugo di pomodoro comunque era buono.", 22, "Pasta alla Norma", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Pasta troppo cotta, il sugo però era buono.",3, "Pasta al ragù", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Pasta cotta giusta e ragù molto saporito, classica ma molto buona.", 6, "Pasta al ragù", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Ragù ricco e saporito, la pasta era cotta alla perfezione. Un classico sempre vincente.", 11, "Pasta al ragù", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Il ragù sapeva di poco e la pasta era scotta. Non all'altezza delle aspettative.", 16, "Pasta al ragù", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buon sapore tradizionale, porzione generosa. Solo un po' oleoso.", 20, "Pasta al ragù", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Nella media, niente di eccezionale ma comunque mangiabile.",1, "Pasta al ragù", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buono, riso non stracotto, un po' al dente.",5, "Riso al curry", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Il curry non era abbastanza piccante, sapore un po' blando. Il riso era cotto bene però.", 7, "Riso al curry", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona combinazione di spezie, riso basmati profumato. Un'alternativa interessante.", 13, "Riso al curry", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo piccante per i miei gusti e le verdure erano troppo cotte.", 8, "Riso al curry", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Perfetto! Spezie bilanciate e verdure croccanti. Il miglior piatto etnico della mensa.", 12, "Riso al curry", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Saporito e colorato, mi ha ricordato i sapori orientali. Buona la presentazione.", 18, "Riso al curry", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Riso un po' al dente ma rimane comunque buono.",1, "Riso pilaw con piselli", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Molto buono, riso al dente ma non crudo.",2, "Riso pilaw con piselli", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Riso ben condito ma i piselli erano un po' duri. Nel complesso accettabile.", 9, "Riso pilaw con piselli", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Delizioso! Riso profumato e piselli dolci. Un piatto semplice ma ben eseguito.", 14, "Riso pilaw con piselli", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Insipido e senza personalità. Aveva bisogno di più condimenti.", 22, "Riso pilaw con piselli", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Personalmente preferisco le minestre frullate, però il gusto era buono.",3, "Minestra di verdure", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Calda e nutriente, piena di verdure fresche. Perfetta per scaldarsi.", 10, "Minestra di verdure", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo acquosa e le verdure erano scotte. Mancava sapore.", 15, "Minestra di verdure", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Onesta minestra casalinga, niente di speciale ma fa il suo dovere.", 17, "Minestra di verdure", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Ricca di ingredienti genuini, sapore autentico. Mi ha ricordato casa.", 21, "Minestra di verdure", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona varietà di verdure ma un po' salata. Il brodo era saporito.",4, "Minestra di verdure", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Eccellente, nulla da dire se non buonissima.",4, "Crema di funghi", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Cremosa e profumata, si sentiva il sapore autentico dei funghi porcini.", 11, "Crema di funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Vellutata perfetta, texture liscia e sapore intenso. Una delle migliori!", 16, "Crema di funghi", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona ma un po' densa, avrei preferito più liquida. Il sapore comunque c'era.", 20, "Crema di funghi", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Saporita e ben preparata, i funghi erano di qualità. Solo un po' salata.", 6, "Crema di funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Straordinaria! Il profumo riempiva tutta la zona pranzo. Complimenti al cuoco.", 13, "Crema di funghi", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Sugo molto saporito.",5, "Pasta all'arrabbiata", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Molto buona, il sugo è piccante al punto giusto.",1, "Pasta all'arrabbiata", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Piccante al punto giusto ma la pasta era un po' troppo cotta.", 8, "Pasta all'arrabbiata", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buon sapore piccante, sugo ben fatto. Una classica ben riuscita.", 12, "Pasta all'arrabbiata", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo piccante per me, non riuscivo a sentire altri sapori.", 17, "Pasta all'arrabbiata", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Perfetta per chi ama il piccante, sugo rosso saporito e denso.", 7, "Pasta all'arrabbiata", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Troppo salato, cereali un po' troppo al dente e basilico inesistente.",2, "Orzo con pomodorini e basilico", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Fresco e saporito, i pomodorini erano dolci e il basilico profumato.", 9, "Orzo con pomodorini e basilico", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Piatto estivo perfetto! Orzo al dente e ingredienti freschi. Ottimo!", 14, "Orzo con pomodorini e basilico", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "L'orzo era troppo al dente e i pomodorini erano acidi.", 18, "Orzo con pomodorini e basilico", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Leggero e digeribile, buona alternativa alla pasta tradizionale.", 21, "Orzo con pomodorini e basilico", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Carino ma niente di speciale. Il condimento era scarso.", 11, "Orzo con pomodorini e basilico", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Molto buona e gustosa, il sugo era abbondante e saporito, si mangia molto volentieri. Peccato per la pasta un po' al dente.",3, "Pasta alla carbonara", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Dose buona, condimento abbondante e quantità di pancetta giusta, ma il sugo me lo aspettavo un po' più saporito.",4, "Pasta alla carbonara", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Cremosa e saporita, anche se non proprio come quella romana originale.", 10, "Pasta alla carbonara", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Le uova erano troppo cotte e si sentivano i grumi. Deludente.", 15, "Pasta alla carbonara", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona ma un po' pesante. La porzione era generosa.", 22, "Pasta alla carbonara", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Gli gnocchi erano della consistenza giusta e non molli, e il sugo molto saporito, nel complesso davvero buoni.",5, "Gnocchi al pomodoro", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Gnocchi fatti in casa e sugo di pomodoro fresco. Molto buoni!", 16, "Gnocchi al pomodoro", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Gli gnocchi erano un po' pesanti ma il sugo era saporito.", 20, "Gnocchi al pomodoro", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Perfetti! Gnocchi leggeri e sugo con basilico fresco. Eccellenti!", 6, "Gnocchi al pomodoro", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Gnocchi di patate troppo compatti, difficili da digerire.", 13, "Gnocchi al pomodoro", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buon piatto tradizionale, mi ha fatto pensare alla nonna.",1, "Gnocchi al pomodoro", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Ottimo considerando anche la mancanza di spine. Tramite analisi olfattiva e degustativa è possibile affermare l'assenza di olio di palma e di cadmio.",1, "Filetto di platessa alla marchigiana", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Pesce fresco e ben cucinato, le olive davano sapore. Buono!", 8, "Filetto di platessa alla marchigiana", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Il pesce era buono ma il condimento troppo salato per i miei gusti.", 12, "Filetto di platessa alla marchigiana", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Eccellente preparazione! Il pesce si sfaldava e i sapori erano bilanciati.", 17, "Filetto di platessa alla marchigiana", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Il pesce sapeva di poco e le olive erano troppo salate.", 21, "Filetto di platessa alla marchigiana", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Piatto leggero e saporito, perfetto per chi è a dieta.",4, "Filetto di platessa alla marchigiana", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buono, la panatura è croccante e asciutta.",2, "Filetto di merluzzo", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buono, la panatura è asciutta e croccante.",3, "Filetto di merluzzo", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Panatura dorata e croccante, pesce tenero dentro. Perfetto!", 9, "Filetto di merluzzo", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono ma un po' oleoso, la panatura assorbiva troppo olio.", 14, "Filetto di merluzzo", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Pesce fresco e ben cucinato, porzione giusta.", 18, "Filetto di merluzzo", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Secco e la panatura si staccava. Non il massimo.", 7, "Filetto di merluzzo", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Il gusto era buono ma c'era troppo olio sulla melanzana.",4, "Melanzana con pomodoro capperi e olive", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona, ma il rapporto capperi/topping è troppo alto.",3, "Melanzana con pomodoro capperi e olive", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Mediterraneo e saporito, le melanzane erano ben cotte.", 10, "Melanzana con pomodoro capperi e olive", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo salato per via dei capperi e delle olive. Inmangiabile.", 15, "Melanzana con pomodoro capperi e olive", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono ma le melanzane erano un po' troppo oleose.", 22, "Melanzana con pomodoro capperi e olive", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Il sapore è buono, la carne non è secca.",5, "Arrosto di tacchino", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Carne tenera e ben speziata, cottura perfetta. Molto soddisfacente.", 11, "Arrosto di tacchino", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono ma un po' secco, avrebbe avuto bisogno di più sugo.", 16, "Arrosto di tacchino", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Eccellente! Carne succulenta e aromatizzata benissimo. Top!", 20, "Arrosto di tacchino", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo asciutto e insapore, difficile da masticare.", 6, "Arrosto di tacchino", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona alternativa al pollo, leggero e digeribile.", 13, "Arrosto di tacchino", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Molto buono il ripieno, la pasta sfoglia però è un po' dura e difficile da tagliare.",1, "Tortino ricotta e spinaci", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Molto saporito, forse un po' salato ma molto buono.",2, "Tortino ricotta e spinaci", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Pasta sfoglia croccante e ripieno cremoso. Davvero delizioso!", 8, "Tortino ricotta e spinaci", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono ma un po' pesante, il ripieno era abbondante.", 12, "Tortino ricotta e spinaci", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Saporito e ben fatto, gli spinaci erano freschi.", 17, "Tortino ricotta e spinaci", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "La pasta sfoglia era troppo dura e il ripieno troppo salato.", 21, "Tortino ricotta e spinaci", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona ma impasta un po' in bocca.",3, "Mozzarella alla romana", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona, il pomodoro le dà quel tocco in più che ci sta. Arrivi alla fine che sei sazio.",4, "Mozzarella alla romana", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Croccante fuori e filante dentro, panatura perfetta!", 9, "Mozzarella alla romana", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo oleosa e la mozzarella era gommosa.", 14, "Mozzarella alla romana", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona e sfiziosa, anche se un po' pesante.", 18, "Mozzarella alla romana", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Cotto bene, aromatizzato bene, buono.",5, "Coscette di pollo", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Si lascia mangiare, bene.",1, "Coscette di pollo", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Pollo succoso e ben speziato, pelle croccante.", 10, "Coscette di pollo", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buone ma un po' secche, la cottura era giusta però.", 15, "Coscette di pollo", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo salate e un po' bruciate, non le ho finite.", 22, "Coscette di pollo", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono, non c'è tanto da dire, il nome coincide con il prodotto, l'unica pecca è che secondo me sono assenti totalmente le proteine, ci sono solo verdure.",2, "Hamburger vegano", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sorprendentemente buono! Sapore ricco nonostante sia vegano.", 11, "Hamburger vegano", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Interessante alternativa, anche se non sa di carne ovviamente.", 16, "Hamburger vegano", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Secco e insapore, mi aspettavo di più da un hamburger vegano.", 20, "Hamburger vegano", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Eccellente opzione vegana! Ben speziato e saporito.", 6, "Hamburger vegano", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Accettabile ma niente di speciale. Fa il suo dovere.", 13, "Hamburger vegano", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Tutto buono tranne i pomodorini che erano amari.",3, "Insalata", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Fresca e croccante, verdure di qualità. Perfetta d'estate!", 8, "Insalata", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Verdure appassite e pomodori non maturi. Deludente.", 12, "Insalata", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Mix perfetto di verdure fresche, croccante e colorata!", 17, "Insalata", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Standard, niente di speciale ma sempre utile come contorno.", 21, "Insalata", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona varietà di ingredienti, fresca e leggera.",4, "Insalata", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Le patate erano crude, le verdure fredde e i ceci erano pochi. Apporto proteico molto basso per essere un secondo.",4, "Insalata vegana con ceci patate carote e melanzane", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Completa e nutriente, ottima combinazione di ingredienti!", 9, "Insalata vegana con ceci patate carote e melanzane", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona idea ma le verdure erano un po' fredde.", 14, "Insalata vegana con ceci patate carote e melanzane", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sana e colorata, buon apporto proteico dai ceci.", 18, "Insalata vegana con ceci patate carote e melanzane", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Le patate erano dure e le melanzane oleose. Non mi è piaciuta.", 7, "Insalata vegana con ceci patate carote e melanzane", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Piatto vegano ben bilanciato, saporito e nutriente.", 22, "Insalata vegana con ceci patate carote e melanzane", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Saporito, i cereali cotti al punto giusto. Ottimo anche per variare la fonte di carboidrati e non mangiare sempre grano.",5, "Bis di cereali con verdure", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Originale e salutare, buona varietà di cereali e verdure fresche.", 10, "Bis di cereali con verdure", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Interessante ma un po' secco, avrebbe avuto bisogno di più condimento.", 15, "Bis di cereali con verdure", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "I cereali erano troppo al dente e il sapore troppo blando.", 11, "Bis di cereali con verdure", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona alternativa ai primi tradizionali, leggero e digeribile.",1, "Bis di cereali con verdure", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Il gusto di per sé era buono, anche se erano zuppi di acqua. Inoltre, la mozzarella sopra ai peperoni era palesemente la mozzarella alla romana avanzata dai giorni scorsi.",1, "Peperoni alla partenopea", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sapore autentico napoletano, peperoni dolci e ben cotti.", 16, "Peperoni alla partenopea", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo oleosi e il ripieno era scarso. Non all'altezza.", 20, "Peperoni alla partenopea", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Deliziosi! Ripieno saporito e peperoni teneri. Ottimi!", 6, "Peperoni alla partenopea", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buoni ma un po' pesanti, il ripieno era abbondante.", 13, "Peperoni alla partenopea", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Piatto tradizionale ben preparato, mi ha ricordato Napoli.",2, "Peperoni alla partenopea", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (1, "Più bagnata dell'oceano, ha la consistenza di una spugna. Nel complesso il sapore è orribile.",2, "Frittata con verdure e formaggio", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Le verdure erano fresche ma la frittata un po' troppo cotta.", 8, "Frittata con verdure e formaggio", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Saporita e nutriente, buona varietà di verdure.", 12, "Frittata con verdure e formaggio", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Perfetta! Soffice e piena di ingredienti genuini.", 17, "Frittata con verdure e formaggio", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo oleosa e il formaggio non si sentiva. Deludente.", 21, "Frittata con verdure e formaggio", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Accettabile come piatto veloce, niente di speciale.",3, "Frittata con verdure e formaggio", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Molto buono, i funghi ci stanno molto bene.",4, "Roast beef con funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Carne tenera e funghi saporiti, cottura perfetta!", 9, "Roast beef con funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono ma la carne era un po' secca per i miei gusti.", 14, "Roast beef con funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Ottima combinazione di sapori, ben presentato.", 18, "Roast beef con funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "La carne era dura e i funghi troppo salati.", 7, "Roast beef con funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Eccellente! Carne di qualità e funghi freschi. Complimenti!", 22, "Roast beef con funghi", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buono, riempie molto, il problema è la distribuzione di mozzarella tra i vari tranci che non è per nulla uniforme.",5, "Trancio di pizza margherita", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Molto buono, l'impasto era morbido e soffice e nel complesso molto gustosa.",1, "Trancio di pizza margherita", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Pizza nella media, impasto un po' secco ma mozzarella buona.", 10, "Trancio di pizza margherita", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona pizza, anche se non come in pizzeria. Soddisfacente.", 15, "Trancio di pizza margherita", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Classica pizza margherita, buona per una mensa.", 11, "Trancio di pizza margherita", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Molto buona anche da fredda, buon apporto di legumi.",2, "Insalata vegana con carote zucchine fagioli e mais", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Colorata e nutriente, ottimo apporto di fibre e proteine.", 16, "Insalata vegana con carote zucchine fagioli e mais", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Freschissima e ben condita, perfetta per l'estate!", 20, "Insalata vegana con carote zucchine fagioli e mais", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona combinazione ma le zucchine erano un po' molli.", 6, "Insalata vegana con carote zucchine fagioli e mais", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Insipida e senza condimento adeguato. Troppo semplice.", 13, "Insalata vegana con carote zucchine fagioli e mais", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sana e leggera, ideale per chi è a dieta.",3, "Insalata vegana con carote zucchine fagioli e mais", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Letteralmente il pezzo di formaggio più duro che io abbia mangiato, impossibile da tagliare, pesante. Nel complesso non è stata un'esperienza piacevole mangiarla.",3, "Melanzana alla siciliana", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sapore autentico siciliano, melanzane ben cotte e formaggio filante.", 8, "Melanzana alla siciliana", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Eccellente preparazione! Il ripieno era ricco e saporito.", 12, "Melanzana alla siciliana", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo oleosa e il formaggio era troppo salato.", 17, "Melanzana alla siciliana", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona ma pesante, tipico piatto del sud ben fatto.", 21, "Melanzana alla siciliana", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Gustosa e ben condita, mi ha ricordato la Sicilia.",4, "Melanzana alla siciliana", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Buonissimi, poco da aggiungere.",5, "Fagiolini", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buoni ma freddi.",1, "Fagiolini", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buoni, ma leggermente crudi.",2, "Fagiolini", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buoni, nulla da dire.",3, "Fagiolini", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Gusto medio, abbastanza buoni ma sempre freddi.",4, "Fagiolini", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Non sono croccanti, sono infatti un po' molli, però almeno non sono troppo salate.",5, "Patate fritte", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buone, peccato siano un po' fredde e molli.",1, "Patate fritte", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Sono patate fritte, sono standard, nulla da aggiungere.",2, "Patate fritte", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Croccanti e ben dorate, perfette come contorno!", 9, "Patate fritte", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Molli e oleose, chiaramente scaldate più volte.", 14, "Patate fritte", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Standard da mensa, niente di speciale ma vanno bene.", 18, "Patate fritte", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buono anche se avrei preferito mangiarlo caldo.",3, "Tris di verdure", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Un buon contorno, forse le verdure erano un po' troppo bagnate.",4, "Tris di verdure", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buone, l'unica pecca sono i cavolfiori poco cotti.",5, "Tris di verdure", "RistorESU Agripolis");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (1, "Mix tra crude e cotte, non si riescono a mangiare.",1, "Patate al basilico", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Cotte, aromatizzate poco, due erbette potevano metterle.",2, "Patate al basilico", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Metà dei bocconi sono crudi e l'altra metà cotti, non hanno tutti la stessa consistenza. Sanno di poco e sono completamente scondite, consiglio di prendere sale e olio.",3, "Patate al basilico", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Spesso alcune sono crude, ma in questo caso erano tutte apposto.",4, "Patate al basilico", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Incredibilmente quasi tutti i bocconi erano cotti, un miglioramento.",5, "Patate al basilico", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Le carote sono abbastanza bagnate e non caldissime, comunque rimangono un buon contorno.",2, "Carote e piselli al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Verdure fresche e ben cotte, leggere e colorate.", 8, "Carote e piselli al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buone ma un po' scondite, avevano bisogno di sale.", 12, "Carote e piselli al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Perfette! Croccanti al punto giusto e molto saporite.", 17, "Carote e piselli al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Le carote erano dure e i piselli troppo cotti. Combinazione sbagliata.", 21, "Carote e piselli al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Semplici ma genuine, contorno leggero e salutare.", 6, "Carote e piselli al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buone ma un po' secche.",3, "Carote al vapore", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buone, un po' secche.",4, "Carote al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Dolci e tenere, cotte alla perfezione. Semplici ma buone!", 10, "Carote al vapore", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo secche e senza sapore, avevano bisogno di condimento.", 15, "Carote al vapore", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Standard, niente di eccezionale ma fanno il loro dovere.",1, "Carote al vapore", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buoni, rappresentativi della cultura veneta.",5, "Fagioli in umido", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Cremosi e saporiti, preparazione tradizionale perfetta!", 11, "Fagioli in umido", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buoni ma un po' scotti, il sugo era saporito però.", 16, "Fagioli in umido", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sostanziosi e nutrienti, ideali per scaldarsi.", 20, "Fagioli in umido", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo acquosi e senza consistenza. Deludenti.", 7, "Fagioli in umido", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buon sapore casalingo, mi hanno ricordato casa.", 13, "Fagioli in umido", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "I funghi sovrastano un po' la zucca, che si sente meno, ma nel complesso molto buona e soddisfacente.",2, "Pasta zucca e funghi", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Combinazione perfetta! Zucca dolce e funghi saporiti.", 9, "Pasta zucca e funghi", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buona ma la zucca si sentiva poco rispetto ai funghi.", 14, "Pasta zucca e funghi", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Autunnale e cremosa, pasta cotta al punto giusto.", 18, "Pasta zucca e funghi", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo oleosa e i funghi erano troppo salati.", 22, "Pasta zucca e funghi", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Deliziosa! Sapori bilanciati e presentazione curata.",3, "Pasta zucca e funghi", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "La pasta era discretamente buona e di buona cottura, il pomodoro non è troppo acido (cosa rara).",3, "Pasta al tonno e olive", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Classica pasta veloce, tonno di qualità e olive saporite.", 8, "Pasta al tonno e olive", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Il tonno era secco e le olive troppo salate per me.", 12, "Pasta al tonno e olive", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Perfetta! Semplicità che funziona sempre.", 17, "Pasta al tonno e olive", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Discreta ma niente di speciale, pasta nella media.", 21, "Pasta al tonno e olive", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona alternativa al solito sugo, veloce e saporita.",4, "Pasta al tonno e olive", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sugo molto buono, si mangia di gusto. Peccato che le zucchine si sentano poco, ma nel complesso buono.",5, "Pasta salmone e zucchine", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Elegante e raffinata, salmone di buona qualità.", 10, "Pasta salmone e zucchine", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Le zucchine erano un po' troppo cotte ma il salmone buono.", 15, "Pasta salmone e zucchine", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo salata e il salmone sapeva di poco.", 11, "Pasta salmone e zucchine", "Mensa Azienda Ospedaliera di Padova");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Sofisticata per una mensa, mi è piaciuta molto.",1, "Pasta salmone e zucchine", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Arrosto di buona cottura e consistenza, unica pecca l'eccessiva salinità (troppo sale).",1, "Arrosto di maiale", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Tenero e succoso, speziatura perfetta. Ottimo arrosto!", 16, "Arrosto di maiale", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono ma un po' secco, avrebbe avuto bisogno di più sugo.", 20, "Arrosto di maiale", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Carne di qualità e ben cotta, soddisfacente.", 6, "Arrosto di maiale", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo salato e un po' duro da masticare.", 13, "Arrosto di maiale", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buon secondo piatto, porzione generosa.",2, "Arrosto di maiale", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Cotte bene.",2, "Coscette di pollo", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Molto buoni e saporiti, si mangiano volentieri. Unica pecca, sono un po'granulosi all'interno, per il resto ottimi.",5, "Falafel", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buoni e ben speziati, sanno proprio di falafel. Sono un po' asciutte, ma mangiati con la maionese sono top.",1, "Falafel", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Interessanti ma un po' secchi, meglio con una salsa.", 9, "Falafel", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Ben speziati e croccanti fuori, buona alternativa vegana.", 14, "Falafel", "RistorESU Nord Piovego");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo asciutti e granulosi, difficili da mangiare.", 18, "Falafel", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Perfetti! Sapore medio-orientale autentico e ben fatto.", 7, "Falafel", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Normale, nulla da dire.",2, "Kebab di pollo", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Speziato e saporito, pollo tenero e ben marinato.", 11, "Kebab di pollo", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buono ma un po' secco, le spezie si sentivano bene.", 16, "Kebab di pollo", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Eccellente! Sapori orientali perfetti, molto gustoso.", 20, "Kebab di pollo", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo piccante per me e la carne era dura.", 6, "Kebab di pollo", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Interessante variante etnica, ben preparata.", 13, "Kebab di pollo", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Nulla da dire, buoni, standard.",3, "Piselli", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Dolci e teneri, cotti al vapore perfettamente.", 8, "Piselli", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo scotti e acquosi, senza sapore.", 12, "Piselli", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Freschi e saporiti, si sentiva la qualità!", 7, "Piselli", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Standard, niente di speciale ma vanno bene.", 21, "Piselli", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buon contorno leggero, ben conditi.",4, "Piselli", "Casa del Fanciullo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Ceci di scarsa qualità, granulosi e di consistenza inadeguata.",4, "Ceci", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Buoni ma un po' duri, cottura insufficiente.", 9, "Ceci", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Perfetti! Cremosi e ben conditi, ottima fonte proteica.", 14, "Ceci", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Nutrienti e saporiti, ideali per vegetariani.", 18, "Ceci", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Granulosi e senza sapore, preparazione scadente.", 7, "Ceci", "Mensa Murialdo");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Nella media, niente di eccezionale ma proteici.", 22, "Ceci", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Semplici e genuini, anche se un po' acquosi.", 10, "Cavoli lessati", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (2, "Troppo cotti e senza sapore, deludenti.", 15, "Cavoli lessati", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (5, "Preparati benissimo! Croccanti e saporiti.", 11, "Cavoli lessati", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (1, "Orribili, sapore amaro e consistenza molle.",1, "Cavoli lessati", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Accettabili come contorno, niente di più.",5, "Cavoli lessati", "Pio X");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (4, "Buona e sfiziosa, anche se un po' pesante.", 6, "Mozzarella alla romana", "Mensa Ciels");
INSERT INTO recensione (voto, descrizione, idUtente, piatto, mensa) VALUES (3, "Nella media, niente di eccezionale ma mangiabile.", 7, "Mozzarella alla romana", "RistorESU Agripolis");
