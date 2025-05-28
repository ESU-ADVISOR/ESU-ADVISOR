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
    password VARCHAR(100) NOT NULL,
    dataNascita DATE NOT NULL,
    username VARCHAR(50) NOT NULL,
    PRIMARY KEY (username),
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
    utente VARCHAR(50) NOT NULL,
    piatto VARCHAR(100) NOT NULL,
    data DATE DEFAULT CURRENT_DATE,
    CHECK (
        voto >= 1
        AND voto <= 5
    ),
    PRIMARY KEY (utente, piatto),
    FOREIGN KEY (utente) REFERENCES utente (username) ON UPDATE CASCADE ON DELETE CASCADE,
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
    username VARCHAR(50) NOT NULL,
    dimensione_testo ENUM ("piccolo", "medio", "grande") NOT NULL DEFAULT "medio",
    dimensione_icone ENUM ("piccolo", "medio", "grande") NOT NULL DEFAULT "medio",
    modifica_font ENUM ("normale", "dislessia") NOT NULL DEFAULT "normale",
    modifica_tema ENUM ("chiaro", "scuro", "sistema") NOT NULL DEFAULT "sistema",
    mensa_preferita VARCHAR(50) NULL DEFAULT NULL,
    PRIMARY KEY (username),
    FOREIGN KEY (username) REFERENCES utente (username) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (mensa_preferita) REFERENCES mensa (nome) ON UPDATE CASCADE ON DELETE SET NULL
);

CREATE TABLE allergeni_utente (
    username VARCHAR(50) NOT NULL,
    allergene ENUM ("Glutine", "Crostacei", "Uova", "Pesce", "Arachidi", "Soia", "Latte", "Frutta_a_guscio", "Sedano", "Senape", "Sesamo", "Anidride_solforosa", "Lupini", "Molluschi") NOT NULL,
    PRIMARY KEY (username, allergene),
    FOREIGN KEY (username) REFERENCES utente (username) ON UPDATE CASCADE ON DELETE CASCADE
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
VALUES ( "password", "1990-01-01", "roberto"),
       ( "password", "1990-01-01", "angela"),
       ( "password123", "1985-05-20", "janedoe"),
       ( "password456", "1988-08-15", "johnsmith"),
       ( "password789", "1992-11-30", "alicejones"),
       ( "password", "1995-02-15", "admin"),
       ( "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1970-01-01", "user"); -- password: user


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

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Fagioli in umido", "Contorno", "Fagioli cotti lentamente in umido con pomodoro e spezie.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Crema di piselli", "Primo", "Vellutata di piselli freschi con un tocco di menta.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Orzo con pomodorini e basilico", "Primo", "Orzo perlato condito con pomodorini freschi e basilico.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Patate al basilico", "Contorno", "Patate al forno aromatizzate con basilico fresco.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Carote al vapore", "Contorno", "Carote cotte al vapore, condite con un filo d’olio.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Fagiolini", "Contorno", "Fagiolini freschi cotti al vapore.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Polpettine vegane", "Secondo", "Polpettine a base di legumi e verdure.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta alla carbonara", "Primo", "Pasta condita con uova, guanciale e pecorino.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta all'arrabbiata", "Primo", "Pasta con sugo di pomodoro piccante.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta salmone e zucchine", "Primo", "Pasta condita con salmone affumicato e zucchine.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta e fagioli alla veneta", "Primo", "Pasta e fagioli preparata secondo la tradizione veneta.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Mozzarella alla romana", "Secondo", "Mozzarella impanata e fritta.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Minestra di verdure", "Primo", "Zuppa di verdure miste.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Frittata con verdure e formaggio", "Secondo", "Frittata con verdure miste e formaggio.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pollo al forno", "Secondo", "Pollo arrosto con erbe aromatiche.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pizza pomodorini, rucola e grana", "Secondo", "Pizza con pomodorini freschi, rucola e scaglie di grana.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta alla Norma (melanzane e ricotta)", "Primo", "Pasta con melanzane fritte e ricotta salata.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta al ragù", "Primo", "Pasta con ragù di carne.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Riso al curry", "Primo", "Riso basmati con curry e verdure.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Riso pilaw con piselli", "Primo", "Riso pilaw con piselli freschi.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Crema di funghi", "Primo", "Vellutata di funghi porcini.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Gnocchi al pomodoro", "Primo", "Gnocchi di patate con sugo di pomodoro.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Filetto di platessa alla marchigiana", "Secondo", "Filetto di platessa con pomodoro e olive.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Filetto di merluzzo", "Secondo", "Filetto di merluzzo impanato e fritto.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Melanzana con pomodoro e funghi", "Secondo", "Melanzana ripiena di pomodoro e funghi.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Arrosto di tacchino", "Secondo", "Arrosto di tacchino con erbe aromatiche.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Tortino ricotta e spinaci", "Secondo", "Tortino di pasta sfoglia ripieno di ricotta e spinaci.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalatona vegetariana", "Secondo", "Insalata mista con verdure fresche.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalata vegana con ceci, patate, carote e melanzane", "Secondo", "Insalata vegana con ceci, patate, carote e melanzane grigliate.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Bis di cereali con verdure", "Primo", "Mix di cereali con verdure di stagione.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Peperoni alla partenopea", "Contorno", "Peperoni ripieni alla napoletana.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Melanzana con pomodoro, capperi e olive", "Secondo", "Melanzana condita con pomodoro, capperi e olive.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Roast beef con funghi", "Secondo", "Roast beef con funghi trifolati.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Trancio di pizza margherita", "Secondo", "Trancio di pizza margherita con mozzarella e pomodoro.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Insalata vegana con carote, zucchine, fagioli e mais", "Secondo", "Insalata vegana con carote, zucchine, fagioli e mais.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Melanzana alla siciliana", "Secondo", "Melanzana ripiena alla siciliana.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Hamburger di manzo BIO con cipolle caramellate", "Secondo", "Hamburger di manzo biologico con cipolle caramellate.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Patate fritte", "Contorno", "Patate fritte croccanti.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Tris di verdure", "Contorno", "Mix di verdure cotte al vapore.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Patate all'olio extravergine", "Contorno", "Patate condite con olio extravergine di oliva.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Carote e piselli al vapore", "Contorno", "Carote e piselli cotti al vapore.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta zucca e funghi", "Primo", "Pasta con zucca e funghi.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta al tonno e olive", "Primo", "Pasta con tonno e olive.");

INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Pasta pomodoro e piselli", "Primo", "Pasta con sugo di pomodoro e piselli.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Arrosto di maiale", "Secondo", "Arrosto di maiale con erbe aromatiche.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Coscette di pollo", "Secondo", "Coscette di pollo arrosto.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Hamburger vegano", "Secondo", "Hamburger vegano a base di legumi.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Falafel", "Secondo", "Polpette di ceci speziate.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Kebab di pollo", "Secondo", "Kebab di pollo con spezie orientali.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Piselli", "Contorno", "Piselli freschi cotti al vapore.");
INSERT INTO piatto (nome, categoria, descrizione) VALUES ("Ceci", "Contorno", "Ceci lessati.");

-- ===============================================

-- PIATTI SENZA ALLERGENI (solo verdure/frutta/legumi semplici)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Fagioli in umido");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Crema di piselli");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Patate al basilico");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Carote al vapore");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Fagiolini");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Melanzana con pomodoro e funghi");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalatona vegetariana");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalata vegana con ceci, patate, carote e melanzane");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Melanzana con pomodoro, capperi e olive");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Insalata vegana con carote, zucchine, fagioli e mais");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Patate fritte");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Tris di verdure");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Patate all'olio extravergine");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Carote e piselli al vapore");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Piselli");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Ceci");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Riso pilaw con piselli");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Nessuno", "Crema di funghi");

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
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta pomodoro e piselli");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Kebab di pollo");

-- PIATTI CON GLUTINE + LATTE (pasta con formaggi)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pizza pomodorini, rucola e grana");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Pizza pomodorini, rucola e grana");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta alla Norma (melanzane e ricotta)");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Pasta alla Norma (melanzane e ricotta)");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Trancio di pizza margherita");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Trancio di pizza margherita");

-- PIATTI CON GLUTINE + PESCE (paste con pesce)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta salmone e zucchine");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Pasta salmone e zucchine");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Pasta al tonno e olive");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Pasta al tonno e olive");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Filetto di merluzzo");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Filetto di merluzzo");

-- PIATTI CON GLUTINE + UOVA + LATTE (tortini e impanati)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Tortino ricotta e spinaci");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Tortino ricotta e spinaci");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Uova", "Tortino ricotta e spinaci");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Mozzarella alla romana");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Mozzarella alla romana");


-- PIATTI CON UOVA + LATTE (frittate)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Uova", "Frittata con verdure e formaggio");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Latte", "Frittata con verdure e formaggio");

-- PIATTI CON SOLO PESCE
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Pesce", "Filetto di platessa alla marchigiana");

-- PIATTI CON GLUTINE + SOIA (prodotti vegani)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Polpettine vegane");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Soia", "Polpettine vegane");

INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Hamburger vegano");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Soia", "Hamburger vegano");

-- PIATTI CON GLUTINE + SESAMO (falafel)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Glutine", "Falafel");
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Sesamo", "Falafel");

-- PIATTI CON SENAPE (curry e spezie)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Senape", "Riso al curry");

-- PIATTI CON SEDANO (minestre e zuppe)
INSERT INTO piatto_allergeni (allergene, piatto) VALUES ("Sedano", "Minestra di verdure");

-- PIATTI CON GLUTINE + UOVA + LATTE + CARNE SUINA (carbonara con guanciale)

-- INSERT INTO menu (piatto, mensa) VALUES ("Fagioli in umido", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Crema di piselli", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Orzo con pomodorini e basilico", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Patate al basilico", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Carote al vapore", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Fagiolini", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Polpettine vegane", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla carbonara", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta all'arrabbiata", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta salmone e zucchine", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta e fagioli alla veneta", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Mozzarella alla romana", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Minestra di verdure", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Frittata con verdure e formaggio", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pollo al forno", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pizza pomodorini, rucola e grana", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta alla Norma (melanzane e ricotta)", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta al ragù", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Riso al curry", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Riso pilaw con piselli", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Crema di funghi", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Gnocchi al pomodoro", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Filetto di platessa alla marchigiana", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Filetto di merluzzo", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Melanzana con pomodoro e funghi", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Arrosto di tacchino", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Tortino ricotta e spinaci", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Insalatona vegetariana", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Insalata vegana con ceci, patate, carote e melanzane", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Bis di cereali con verdure", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Peperoni alla partenopea", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Melanzana con pomodoro, capperi e olive", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Roast beef con funghi", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Trancio di pizza margherita", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Insalata vegana con carote, zucchine, fagioli e mais", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Melanzana alla siciliana", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Hamburger di manzo BIO con cipolle caramellate", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Patate fritte", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Tris di verdure", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Patate all'olio extravergine", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Carote e piselli al vapore", "RistorESU Nord Piovego");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta zucca e funghi", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Polpettine vegane", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta salmone e zucchine", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Falafel", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta al tonno e olive", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Pasta pomodoro e piselli", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Arrosto di maiale", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Coscette di pollo", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Kebab di pollo", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Piselli", "Pio X");
-- INSERT INTO menu (piatto, mensa) VALUES ("Ceci", "Pio X");

-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Alto assorbimento delle radiazioni elettromagnetiche, quasi al pari di un corpo nero. Densità alta ma non esagerata, conforme agli standard UNI ISO. Sapore buono, leggermente salato, temperatura leggermente al di sopra di quella ambientale. Frazione in massa di pasta infima ma ciò è possibile che sia dovuto al fatto che il piatto servito fosse solo una delle prime mestolate. Pro: sapore, senso di sazietà raggiunto facilmente, ottimo materiale edile Contro: mancanza di nota piccante, produzione incontrollabile di metano gassoso da orifizi corporei.", "roberto", "Pasta e fagioli alla veneta");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Il sapore è buono ma la texture non mi è piaciuta. Ci sono troppi pezzi grossi, non è stata tritata bene.", "angela", "Crema di piselli");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Fredda e non frullata bene.", "janedoe", "Crema di piselli");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buona, ma calda sarebbe stata meglio.", "johnsmith", "Pasta alla Norma (melanzane e ricotta)");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Mangiabile.", "alicejones", "Pasta alla Norma (melanzane e ricotta)");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Melanzaneun po' acide ma buona.", "roberto", "Pasta alla Norma (melanzane e ricotta)");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Nulla da dire, è buona. Magari calda sarebbe stata più gradita.", "angela", "Pasta alla Norma (melanzane e ricotta)");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Pasta troppo cotta, il sugo però era buono.", "janedoe", "Pasta al ragù");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Pasta cotta giusta e ragù molto saporito, classica ma molto buona.", "johnsmith", "Pasta al ragù");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, riso non stracotto,un po' al dente.", "alicejones", "Riso al curry");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Riso un po' al dente ma rimane comunque buono.", "roberto", "Riso pilaw con piselli");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buono, riso al dente ma non crudo.", "angela", "Riso pilaw con piselli");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Personalmente preferisco le minestre frullate, però il gusto era buono.", "janedoe", "Minestra di verdure");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Eccellente, nulla da dire se non buonissima.", "johnsmith", "Crema di funghi");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Sugo molto saporito.", "alicejones", "Pasta all'arrabbiata");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buona, il sugo è piccante al punto giusto.", "roberto", "Pasta all'arrabbiata");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Troppo salato, cereali un po' troppo al dente e basilico inesistente.", "angela", "Orzo con pomodorini e basilico");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buona e gustosa, il sugo era abbondante e saporito, si mangia molto volentieri. Peccato per la pasta un po' al dente.", "janedoe", "Pasta alla carbonara");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Dose buona, condimento abbondante e quantità di pancetta giusta, ma il sugo me lo aspettavo un po' più saporito.", "johnsmith", "Pasta alla carbonara");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Gli gnocchi erano della consistenza giusta e non molli, e il sugo molto saporito, nel complesso davvero buoni.", "alicejones", "Gnocchi al pomodoro");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Ottimo considerando anche la mancanza di spine. Tramite analisi olfattiva e degustativa è possibile affermare l'assenza di olio di palma e di cadmio.", "roberto", "Filetto di platessa alla marchigiana");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, la panatura è croccante e asciutta.", "angela", "Filetto di merluzzo");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, la panatura è asciutta e croccante.", "janedoe", "Filetto di merluzzo");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Il gusto era buono ma c'era troppo olio sulla melanzana.", "johnsmith", "Melanzana con pomodoro e funghi");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Il sapore è buono, la carne non è secca.", "alicejones", "Arrosto di tacchino");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buono il ripieno, la pasta sfoglia però è un po' dura e difficile da tagliare.", "roberto", "Tortino ricotta e spinaci");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto saporito, forse un po' salato ma molto buono.", "angela", "Tortino ricotta e spinaci");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buona ma impasta un po' in bocca.", "janedoe", "Mozzarella alla romana");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buona, il pomodoro le dà quel tocco in più che ci sta. Arrivi alla fine che sei sazio.", "johnsmith", "Mozzarella alla romana");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Cotto bene, aromatizzato bene, buono.", "alicejones", "Pollo al forno");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Si lascia mangiare, bene.", "roberto", "Pollo al forno");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buono, non c'è tanto da dire, il nome coincide con il prodotto, l'unica pecca è che secondo me sono assenti totalmente le proteine, ci sono solo verdure.", "angela", "Hamburger vegano");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Tutto buono tranne i pomodorini che erano amari.", "janedoe", "Insalatona vegetariana");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Le patate erano crude, le verdure fredde e i ceci erano pochi. Apporto proteico molto basso per essere un secondo.", "johnsmith", "Insalata vegana con ceci, patate, carote e melanzane");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Saporito, i cereali cotti al punto giusto. Ottimo anche per variare la fonte di carboidrati e non mangiare sempre grano.", "alicejones", "Bis di cereali con verdure");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Il gusto di per sé era buono, anche se erano zuppi di acqua. Inoltre, la mozzarella sopra ai peperoni era palesemente la mozzarella alla romana avanzata dai giorni scorsi.", "roberto", "Peperoni alla partenopea");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, "Più bagnata dell'oceano, ha la consistenza di una spugna. Nel complesso il sapore è orribile.", "angela", "Frittata con verdure e formaggio");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buona, ma il rapporto capperi/topping è troppo alto.", "janedoe", "Melanzana con pomodoro, capperi e olive");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buono, i funghi ci stanno molto bene.", "johnsmith", "Roast beef con funghi");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, riempie molto, il problema è la distribuzione di mozzarella tra i vari tranci che non è per nulla uniforme.", "alicejones", "Trancio di pizza margherita");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buonO, l'impasto era morbiDo e soffice e nel complesso molto gustosa.", "roberto", "Trancio di pizza margherita");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buona anche da fredda, buon apporto di legumi.", "angela", "Insalata vegana con carote, zucchine, fagioli e mais");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Letteralmente il pezzo di formaggio più duro che io abbia mangiato, impossibile da tagliare, pesante. Nel complesso non è stata un’esperienza piacevole mangiarla.", "janedoe", "Melanzana alla siciliana");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "La consistenza della carne faceva invidia al cartongesso e mi viene sinceramente da chiedermi con quale parte del manzo abbiano fatto questo hamburger, ma scommetterei sulle corna. Per fortuna le cipolle caramellate facevano un ottimo lavoro di distrazione al gusto ambiguo del prodotto animale. Punti bonus per le cose di consistenza strana che ogni tanto ti trovavi in bocca a sorpresa dopo un boccone. Citando yotobi “è come aprire un uovo di Pasqua e trovarci dentro il virus del vaiolo.”", "johnsmith", "Hamburger di manzo BIO con cipolle caramellate");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Buonissimi, poco da aggiungere.", "alicejones", "Fagiolini");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buoni ma freddi.", "roberto", "Fagiolini");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buoni, ma leggermente crudi.", "angela", "Fagiolini");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buoni, nulla da dire.", "janedoe", "Fagiolini");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Gusto medio, abbastanza buoni ma sempre freddi.", "johnsmith", "Fagiolini");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Non sono croccanti, sono infatti un po' molli, però almeno non sono troppo salate.", "alicejones", "Patate fritte");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone, peccato siano un po' fredde e molli.", "roberto", "Patate fritte");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Sono patate fritte, sono standard, nulla da aggiungere.", "angela", "Patate fritte");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono anche se avrei preferito mangiarlo caldo.", "janedoe", "Tris di verdure");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Un buon contorno, forse le verdure erano un po' troppo bagnate.", "johnsmith", "Tris di verdure");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone, l'unica pecca sono i cavolfiori poco cotti.", "alicejones", "Tris di verdure");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, "Mix tra crude e cotte, non si riescono a mangiare.", "roberto", "Patate all'olio extravergine");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Cotte, aromatizzate poco, due erbette potevano metterle (non basilico, grazie).", "angela", "Patate all'olio extravergine");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Metà dei bocconi sono crudi e l'altra metà cotti, non hanno tutti la stessa consistenza. Sanno di poco e sono completamente scondite, consiglio di prendere sale e olio.", "janedoe", "Patate all'olio extravergine");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Spesso alcune sono crude, ma in questo caso erano tutte apposto.", "johnsmith", "Patate all'olio extravergine");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Incredibilmente quasi tutti i bocconi erano cotti, un miglioramento.", "alicejones", "Patate all'olio extravergine");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, "Completamente crude, non si salvava nemmeno un boccone.", "roberto", "Patate al basilico");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Le carote sono abbastanza bagnate e non caldissime, comunque rimangono un buon contorno.", "angela", "Carote e piselli al vapore");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone ma un po' secche.", "janedoe", "Carote al vapore");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buone, un po' secche.", "johnsmith", "Carote al vapore");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buoni, rappresentativi della cultura veneta.", "alicejones", "Fagioli in umido");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto gustosa, impasto sottile ma buono, alcune volte poco cotto in qualche punto. Farcitura buona e leggera.", "roberto", "Pizza pomodorini, rucola e grana");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "I funghi sovrastano un po' la zucca, che si sente meno, ma nel complesso molto buona e soddisfacente.", "angela", "Pasta zucca e funghi");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "La pasta era discretamente buona e di buona cottura, il pomodoro non è troppo acido (cosa rara).", "janedoe", "Pasta al tonno e olive");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Solita, acida e insapore, pasta cotta troppo.", "johnsmith", "Pasta pomodoro e piselli");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Sugo molto buono, si mangia di gusto. Peccato che le zucchine si sentano poco, ma nel complesso buono.", "alicejones", "Pasta salmone e zucchine");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Arrosto di buona cottura e consistenza, unica pecca l'eccessiva salinità (troppo sale).", "roberto", "Arrosto di maiale");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Cotte bene.", "angela", "Coscette di pollo");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Insapori.", "janedoe", "Polpettine vegane");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buone e saporite, accompagnate poi dal sugo di pomodoro si risolve anche il fatto che sono un po' secchi. In generale buonissime, ne mangerei tantissime.", "johnsmith", "Polpettine vegane");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buoni e saporiti, si mangiano volentieri. Unica pecca, sono un po'granulosi all'interno, per il resto ottimi.", "alicejones", "Falafel");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buoni e ben speziati, sanno proprio di falafel. Sono un po' asciutte, ma mangiati con la maionese sono top.", "roberto", "Falafel");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Normale, nulla da dire.", "angela", "Kebab di pollo");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Nulla da dire, buoni, standard.", "janedoe", "Piselli");
-- INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Ceci di scarsa qualità, granulosi e di consistenza inadeguata.", "johnsmith", "Ceci");

INSERT INTO piatto_foto (piatto, foto) VALUES ("Bis di cereali con verdure", "images/uploads/bis-di-cereali-con-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Crema di funghi", "images/uploads/crema-di-funghi.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Crema di piselli", "images/uploads/crema-di-piselli2.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Filetto di merluzzo", "images/uploads/filetto-di-merluzzo-e-carote-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Carote al vapore", "images/uploads/filetto-di-merluzzo-e-carote-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Filetto di platessa alla Marchigiana", "images/uploads/filetto-di-platessa-alla-marchigiana+patate-al-basilico.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Patate al basilico", "images/uploads/filetto-di-platessa-alla-marchigiana+patate-al-basilico.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Frittata con verdure e formaggio", "images/uploads/frittata-con-verdure-e-formaggio-+-tris-di-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Tris di verdure", "images/uploads/frittata-con-verdure-e-formaggio-+-tris-di-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Gnocchi al pomodoro", "images/uploads/gnocchi-al-pomodoro.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Insalata vegana con ceci, patate, carote e melanzane", "images/uploads/insalata-vegana-con-ceci-patate-carote-e-melanzare-+-tris-di-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Tris di verdure", "images/uploads/insalata-vegana-con-ceci-patate-carote-e-melanzare-+-tris-di-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Insalata vegana con carote, zucchine, fagioli e mais", "images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais-+-carote-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Carote al vapore", "images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais-+-carote-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Melanzana alla siciliana", "images/uploads/melanzana-alla-siciliana.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Melanzana con pomodoro, capperi e olive", "images/uploads/melanzana-con-pomodoro-capperi-e-olive-+-fagioli-in-umido.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Fagioli in umido", "images/uploads/melanzana-con-pomodoro-capperi-e-olive-+-fagioli-in-umido.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Minestra di verdure", "images/uploads/minestra-di-verdure.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Orzo con pomodorini e basilico", "images/uploads/orzo-con-pomodorini-e-basilico.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta al ragù", "images/uploads/pasta-al-ragu.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta all'arrabbiata", "images/uploads/pasta-all'arrabbiata.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta all'arrabbiata", "images/uploads/pasta-all'arrabbiata-+-roast-beef-con-funghi-+-fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Fagiolini", "images/uploads/pasta-all'arrabbiata-+-roast-beef-con-funghi-+-fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Roast beef con funghi", "images/uploads/pasta-all'arrabbiata-+-roast-beef-con-funghi-+-fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta alla Carbonara", "images/uploads/pasta-alla-carbonara.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta al tonno e olive", "images/uploads/pasta-tonno-e-olive.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Patate fritte", "images/uploads/patatine-fritte.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Peperoni alla partenopea", "images/uploads/peperoni-alla-partenopea-e-fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Fagiolini", "images/uploads/peperoni-alla-partenopea-e-fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Riso pilaw con piselli", "images/uploads/riso-pilaw-con-piselli-1.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Riso pilaw con piselli", "images/uploads/riso-pilaw-con-piselli-2.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Tortino ricotta e spinaci", "images/uploads/tortino-ricotta-e-spinaci-+-carote-e-piselli-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Carote e piselli al vapore", "images/uploads/tortino-ricotta-e-spinaci-+-carote-e-piselli-al-vapore.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Trancio di pizza margherita", "images/uploads/trancio-di-pizza-margherita+patate-al-basilico.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Trancio di pizza margherita", "images/uploads/trancio-di-pizza-margherita+patate-al-basilico+fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Patate al basilico", "images/uploads/trancio-di-pizza-margherita+patate-al-basilico.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Patate al basilico", "images/uploads/trancio-di-pizza-margherita+patate-al-basilico+fagiolini.webp");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Fagiolini", "images/uploads/trancio-di-pizza-margherita+patate-al-basilico+fagiolini.webp");

DELIMITER //

CREATE PROCEDURE crea_menu_settimanale()
-- 11 contorni 24 secondi e 17 primi
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

    REPEAT
        FETCH mensa_cursor INTO mensa_nome;
        IF NOT done THEN

            INSERT IGNORE INTO menu (piatto, mensa)
            SELECT p.nome, mensa_nome
            FROM piatto p
            WHERE p.categoria = 'Primo'
            ORDER BY RAND()
            LIMIT 3;

            -- 3 secondi
            INSERT IGNORE INTO menu (piatto, mensa)
            SELECT p.nome, mensa_nome
            FROM piatto p
            WHERE p.categoria = 'Secondo'
            ORDER BY RAND()
            LIMIT 3;

            -- 2 contorni
            INSERT IGNORE INTO menu (piatto, mensa)
            SELECT p.nome, mensa_nome
            FROM piatto p
            WHERE p.categoria = 'Contorno'
            ORDER BY RAND()
            LIMIT 2;
        END IF;
    UNTIL done END REPEAT;
    COMMIT;

    CLOSE mensa_cursor;
END //

CREATE PROCEDURE crea_recensioni_casuali()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE piatto_nome VARCHAR(100);
    DECLARE num_recensioni INT;
    DECLARE i INT;
    DECLARE utente_nome VARCHAR(50);
    DECLARE voto INT;

    DECLARE piatti_cursor CURSOR FOR
        SELECT nome FROM piatto;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    OPEN piatti_cursor;

    REPEAT
        FETCH piatti_cursor INTO piatto_nome;
        IF NOT done THEN
            SET num_recensioni = FLOOR(4 + (RAND() * 4)); -- 4 to 7 recensioni
            SET i = 0;
            WHILE i < num_recensioni DO
                -- Scegli utente casuale
                SELECT username INTO utente_nome
                FROM utente
                ORDER BY RAND()
                LIMIT 1;

                -- Scegli voto casuale
                SET voto = FLOOR(1 + (RAND() * 5)); -- 1 to 5

                -- Inserisci recensione solo se non esiste già per quell'utente e piatto
                INSERT IGNORE INTO recensione (voto, descrizione, utente, piatto)
                VALUES (voto, 'recensione generata automaticamente', utente_nome, piatto_nome);

                SET i = i + 1;
            END WHILE;
        END IF;
    UNTIL done END REPEAT;

    CLOSE piatti_cursor;
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
    CALL crea_recensioni_casuali();
END //



CALL crea_menu_settimanale();
CALL crea_recensioni_casuali();