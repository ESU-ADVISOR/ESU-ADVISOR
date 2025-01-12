SET GLOBAL event_scheduler = "ON";

DROP VIEW IF EXISTS mensa_orari_apertura;
DROP VIEW IF EXISTS piatto_recensioni_foto;
DROP EVENT IF EXISTS crea_menu_settimanale;
DROP TABLE IF EXISTS preferenze_utente;
DROP TABLE IF EXISTS piatto_allergeni;
DROP TABLE IF EXISTS piatto_foto;
DROP TABLE IF EXISTS menu_piatto;
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
        descrizione TEXT NOT NULL,
        PRIMARY KEY (nome),
        CHECK (LENGTH (descrizione) <= 500)
    );

CREATE TABLE utente (
        email VARCHAR(50) NOT NULL,
        password VARCHAR(100) NOT NULL,
        dataNascita DATE NOT NULL,
        username VARCHAR(50) NOT NULL,
        PRIMARY KEY (email),
        UNIQUE (username),
        CHECK (email LIKE '%@%.%'),
        CHECK (username REGEXP '^[a-zA-Z0-9_]+$'),
        CHECK (
            email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
        ),
        INDEX (username),
        UNIQUE (username)
    );

CREATE TABLE menu (
        data DATE NOT NULL,
        mensa VARCHAR(50) NOT NULL,
        PRIMARY KEY (data, mensa),
        FOREIGN KEY (mensa) REFERENCES mensa (nome) ON UPDATE CASCADE ON DELETE CASCADE
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
        CHECK (orainizio REGEXP '^[0-2][0-9]:[0-5][0-9]$'),
        CHECK (orafine REGEXP '^[0-2][0-9]:[0-5][0-9]$')
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
        FOREIGN KEY (utente) REFERENCES utente (email) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
    );

CREATE TABLE menu_piatto (
        piatto VARCHAR(100) NOT NULL,
        data DATE NOT NULL,
        mensa VARCHAR(50) NOT NULL,
        PRIMARY KEY (piatto, data, mensa),
        FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE,
        FOREIGN KEY (data, mensa) REFERENCES menu (data, mensa) ON UPDATE CASCADE ON DELETE CASCADE
    );

CREATE TABLE piatto_foto (
        photoid INT AUTO_INCREMENT,
        foto    BLOB        NOT NULL,      
        piatto VARCHAR(100) NOT NULL,
        PRIMARY KEY (photoid, piatto),
        FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
    );

CREATE TABLE piatto_allergeni (
        allergeni VARCHAR(30) NOT NULL,
        piatto VARCHAR(100) NOT NULL,
        PRIMARY KEY (allergeni, piatto),
        FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
    );

CREATE TABLE preferenze_utente (
    email VARCHAR(50) NOT NULL,
    dimensione_testo ENUM ('piccolo', 'medio', 'grande', 'molto grande') NOT NULL DEFAULT 'medio',
    dimensione_icone ENUM ('piccolo', 'medio', 'grande', 'molto grande') NOT NULL DEFAULT 'medio',
    modifica_font ENUM ('normale', 'dislessia') NOT NULL DEFAULT 'normale',
    dark_mode BOOLEAN NOT NULL DEFAULT FALSE,
    PRIMARY KEY (email),
    FOREIGN KEY (email) REFERENCES utente  (email) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE VIEW piatto_recensioni_foto AS
SELECT
    p.nome AS piatto,
    AVG(r.voto) AS media_stelle,
    GROUP_CONCAT(DISTINCT pf.foto ORDER BY RAND() SEPARATOR ', ') AS foto_casuali
FROM
    piatto p
    JOIN recensione r ON p.nome = r.piatto
    LEFT JOIN piatto_foto pf ON p.nome = pf.piatto
GROUP BY
    p.nome;

INSERT INTO utente (email, password, dataNascita, username)
VALUES ("roberto@example.com", "password", "1990-01-01", "roberto"),
       ("angela@example.com", "password", "1990-01-01", "angela"),
       ("jane.doe@example.com", "password123", "1985-05-20", "janedoe"),
       ("john.smith@example.com", "password456", "1988-08-15", "johnsmith"),
       ("alice.jones@example.com", "password789", "1992-11-30", "alicejones"),
       ("admin@example.com", "password", "1995-02-15", "admin"),
       ("user@example.com", "$2y$10$wxWPWc.4uvQrXY4lrTdqiudjxn8aVAB129PUW/f73KkZS.oknZqNu", "1970-01-01", "user"); -- password: user
     

INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ('RistorESU Agripolis', "Viale dell'Università, 6 - Legnaro (PD)", '04 97430607', 'https://www.google.com/maps/place/Mensa+Agripolis/@45.3474897,11.9577471,17z/data=!4m6!3m5!1s0x477ec378b59940cf:0x5b21dfbc8034b869!8m2!3d45.346961!4d11.9586004!16s%2Fg%2F11h9__56t4?entry=tts');
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ('RistorESU Nord Piovego', 'Viale Giuseppe Colombo, 1 - Padova', '049 7430811', 'https://www.google.com/maps/place/RistorEsu+Nord+Piovego/@45.4110432,11.887099,17z/data=!3m1!4b1!4m6!3m5!1s0x477edaf60d6b6371:0x2c00159331ead3d8!8m2!3d45.4110432!4d11.8896739!16s%2Fg%2F1pp2tjhxw?entry=tts');
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ('Mensa Murialdo', 'Via Antonio Grassi, 42 - Padova', '049 772011', 'https://www.google.com/maps/place/Mensa+Murialdo/@45.4130884,11.8994815,17z/data=!3m1!4b1!4m6!3m5!1s0x477edaed17825579:0x39ac780af76d258d!8m2!3d45.4130885!4d11.9043524!16s%2Fg%2F11g5zwxl4z?entry=tts');
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ('Mensa Azienda Ospedaliera di Padova', 'Via Nicolò Giustiniani, 1 - Padova', '049 8211111', 'https://www.google.com/maps/place/Azienda+Ospedale+Universit%C3%A0+Padova/@45.4029354,11.88911,19z/data=!4m6!3m5!1s0x477edaf91e846ae5:0x19313e029e7efd8a!8m2!3d45.4028873!4d11.8891995!16s%2Fg%2F11c6wm6888?entry=tts');
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ('Mensa Ciels', 'Via Sebastiano Venier, 200 - Padova', '049 774152', 'https://www.google.com/maps/place/Campus+CIELS+-+Sede+di+Padova/@45.3760046,11.8877834,17z/data=!3m2!4b1!5s0x477edb6d735b8e83:0xcc35839005059d33!4m6!3m5!1s0x477edb6d0ab7afc9:0xaef45488826e9515!8m2!3d45.3760046!4d11.8877834!16s%2Fg%2F1tgq5h7z?entry=ttu&g_ep=EgoyMDI0MTIwOS4wIKXMDSoASAFQAw%3D%3D');
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ('Casa del Fanciullo', 'Vicolo Santonini, 12 - Padova', '049 8751075', 'https://www.google.com/maps/place/Associazione+Casa+Del+Fanciullo/@45.3997459,11.879446,17z/data=!3m1!4b1!4m6!3m5!1s0x477eda55078d6023:0xf616c3a03d554e82!8m2!3d45.3997459!4d11.8820209!16s%2Fg%2F1pv5v58wj?entry=tts');
INSERT INTO mensa (nome, indirizzo, telefono, maps_link) VALUES ('Pio X', 'Via Bonporti, 20 - Padova', '049 6895862', 'https://www.google.com/maps/place/Mensa+Pio+X/@45.4053724,11.8688651,17z/data=!3m1!4b1!4m6!3m5!1s0x477eda4e563f1161:0x135b6ab250952049!8m2!3d45.4053724!4d11.87144!16s%2Fg%2F11cjk2k92p?entry=tts');

INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, '11:45', '14:30', 'RistorESU Agripolis');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, '11:45', '14:30', 'RistorESU Agripolis');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, '11:45', '14:30', 'RistorESU Agripolis');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, '11:45', '14:30', 'RistorESU Agripolis');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, '11:45', '14:30', 'RistorESU Agripolis');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, '11:30', '14:30', 'RistorESU Nord Piovego');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, '11:30', '14:30', 'RistorESU Nord Piovego');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, '11:30', '14:30', 'RistorESU Nord Piovego');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, '11:30', '14:30', 'RistorESU Nord Piovego');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, '11:30', '14:30', 'RistorESU Nord Piovego');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, '11:45', '14:30', 'Mensa Murialdo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, '11:45', '14:30', 'Mensa Murialdo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, '11:45', '14:30', 'Mensa Murialdo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, '11:45', '14:30', 'Mensa Murialdo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, '11:45', '14:30', 'Mensa Murialdo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, '12:00', '15:00', 'Mensa Azienda Ospedaliera di Padova');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, '12:00', '15:00', 'Mensa Azienda Ospedaliera di Padova');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, '12:00', '15:00', 'Mensa Azienda Ospedaliera di Padova');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, '12:00', '15:00', 'Mensa Azienda Ospedaliera di Padova');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, '12:00', '15:00', 'Mensa Azienda Ospedaliera di Padova');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, '11:45', '14:30', 'Mensa Ciels');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, '11:45', '14:30', 'Mensa Ciels');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, '11:45', '14:30', 'Mensa Ciels');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, '11:45', '14:30', 'Mensa Ciels');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, '11:45', '14:30', 'Mensa Ciels');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, '11:45', '14:30', 'Casa del Fanciullo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, '11:45', '14:30', 'Casa del Fanciullo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, '11:45', '14:30', 'Casa del Fanciullo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, '11:45', '14:30', 'Casa del Fanciullo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, '11:45', '14:30', 'Casa del Fanciullo');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (1, '11:45', '14:30', 'Pio X');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (2, '11:45', '14:30', 'Pio X');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (3, '11:45', '14:30', 'Pio X');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (4, '11:45', '14:30', 'Pio X');
INSERT INTO orarioapertura (giornoSettimana, orainizio, orafine, mensa) VALUES (5, '11:45', '14:30', 'Pio X');


INSERT INTO menu (DATA, mensa)VALUES ('2024-12-10', 'RistorESU Agripolis');
INSERT INTO menu (DATA, mensa)VALUES ('2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu (DATA, mensa)VALUES ('2024-12-10', 'Mensa Murialdo');
INSERT INTO menu (DATA, mensa)VALUES ('2024-12-10', 'Mensa Azienda Ospedaliera di Padova');
INSERT INTO menu (DATA, mensa)VALUES ('2024-12-10', 'Mensa Ciels');
INSERT INTO menu (DATA, mensa)VALUES ('2024-12-10', 'Casa del Fanciullo');
INSERT INTO menu (DATA, mensa)VALUES ('2024-12-10', 'Pio X');

INSERT INTO piatto (nome, descrizione) VALUES ('Fagioli in umido', 'Fagioli cotti lentamente in umido con pomodoro e spezie.');
INSERT INTO piatto (nome, descrizione) VALUES ('Crema di piselli', 'Vellutata di piselli freschi con un tocco di menta.');
INSERT INTO piatto (nome, descrizione) VALUES ('Orzo con pomodorini e basilico', 'Orzo perlato condito con pomodorini freschi e basilico.');
INSERT INTO piatto (nome, descrizione) VALUES ('Patate al basilico', 'Patate al forno aromatizzate con basilico fresco.');
INSERT INTO piatto (nome, descrizione) VALUES ('Carote al vapore', 'Carote cotte al vapore, condite con un filo d’olio.');
INSERT INTO piatto (nome, descrizione) VALUES ('Fagiolini', 'Fagiolini freschi cotti al vapore.');
INSERT INTO piatto (nome, descrizione) VALUES ('Polpettine vegane', 'Polpettine a base di legumi e verdure.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pasta alla carbonara', 'Pasta condita con uova, guanciale e pecorino.');
INSERT INTO piatto (nome, descrizione) VALUES ("Pasta all'arrabbiata", 'Pasta con sugo di pomodoro piccante.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pasta salmone e zucchine', 'Pasta condita con salmone affumicato e zucchine.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pasta e fagioli alla veneta', 'Pasta e fagioli preparata secondo la tradizione veneta.');
INSERT INTO piatto (nome, descrizione) VALUES ('Mozzarella alla romana', 'Mozzarella impanata e fritta.');
INSERT INTO piatto (nome, descrizione) VALUES ('Minestra di verdure', 'Zuppa di verdure miste.');
INSERT INTO piatto (nome, descrizione) VALUES ('Frittata con verdure e formaggio', 'Frittata con verdure miste e formaggio.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pollo al forno', 'Pollo arrosto con erbe aromatiche.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pizza pomodorini, rucola e grana', 'Pizza con pomodorini freschi, rucola e scaglie di grana.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pasta alla Norma (melanzane e ricotta)', 'Pasta con melanzane fritte e ricotta salata.');
INSERT INTO piatto (nome, descrizione) VALUES ("Pasta al ragù", "Pasta con ragù di carne.");
INSERT INTO piatto (nome, descrizione) VALUES ('Riso al curry', 'Riso basmati con curry e verdure.');
INSERT INTO piatto (nome, descrizione) VALUES ('Riso pilaw con piselli', 'Riso pilaw con piselli freschi.');
INSERT INTO piatto (nome, descrizione) VALUES ('Crema di funghi', 'Vellutata di funghi porcini.');
INSERT INTO piatto (nome, descrizione) VALUES ('Gnocchi al pomodoro', 'Gnocchi di patate con sugo di pomodoro.');
INSERT INTO piatto (nome, descrizione) VALUES ('Filetto di platessa alla marchigiana', 'Filetto di platessa con pomodoro e olive.');
INSERT INTO piatto (nome, descrizione) VALUES ('Filetto di merluzzo', 'Filetto di merluzzo impanato e fritto.');
INSERT INTO piatto (nome, descrizione) VALUES ('Melanzana con pomodoro e funghi', 'Melanzana ripiena di pomodoro e funghi.');
INSERT INTO piatto (nome, descrizione) VALUES ('Arrosto di tacchino', 'Arrosto di tacchino con erbe aromatiche.');
INSERT INTO piatto (nome, descrizione) VALUES ('Tortino ricotta e spinaci', 'Tortino di pasta sfoglia ripieno di ricotta e spinaci.');
INSERT INTO piatto (nome, descrizione) VALUES ('Insalatona vegetariana', 'Insalata mista con verdure fresche.');
INSERT INTO piatto (nome, descrizione) VALUES ('Insalata vegana con ceci, patate, carote e melanzane', 'Insalata vegana con ceci, patate, carote e melanzane grigliate.');
INSERT INTO piatto (nome, descrizione) VALUES ('Bis di cereali con verdure', 'Mix di cereali con verdure di stagione.');
INSERT INTO piatto (nome, descrizione) VALUES ('Peperoni alla partenopea', 'Peperoni ripieni alla napoletana.');
INSERT INTO piatto (nome, descrizione) VALUES ('Melanzana con pomodoro, capperi e olive', 'Melanzana condita con pomodoro, capperi e olive.');
INSERT INTO piatto (nome, descrizione) VALUES ('Roast beef con funghi', 'Roast beef con funghi trifolati.');
INSERT INTO piatto (nome, descrizione) VALUES ('Trancio di pizza margherita', 'Trancio di pizza margherita con mozzarella e pomodoro.');
INSERT INTO piatto (nome, descrizione) VALUES ('Insalata vegana con carote, zucchine, fagioli e mais', 'Insalata vegana con carote, zucchine, fagioli e mais.');
INSERT INTO piatto (nome, descrizione) VALUES ('Melanzana alla siciliana', 'Melanzana ripiena alla siciliana.');
INSERT INTO piatto (nome, descrizione) VALUES ('Hamburger di manzo BIO con cipolle caramellate', 'Hamburger di manzo biologico con cipolle caramellate.');
INSERT INTO piatto (nome, descrizione) VALUES ('Patate fritte', 'Patate fritte croccanti.');
INSERT INTO piatto (nome, descrizione) VALUES ('Tris di verdure', 'Mix di verdure cotte al vapore.');
INSERT INTO piatto (nome, descrizione) VALUES ("Patate all'olio extravergine", 'Patate condite con olio extravergine di oliva.');
INSERT INTO piatto (nome, descrizione) VALUES ('Carote e piselli al vapore', 'Carote e piselli cotti al vapore.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pasta zucca e funghi', 'Pasta con zucca e funghi.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pasta al tonno e olive', 'Pasta con tonno e olive.');
INSERT INTO piatto (nome, descrizione) VALUES ('Pasta pomodoro e piselli', 'Pasta con sugo di pomodoro e piselli.');
INSERT INTO piatto (nome, descrizione) VALUES ('Arrosto di maiale', 'Arrosto di maiale con erbe aromatiche.');
INSERT INTO piatto (nome, descrizione) VALUES ('Coscette di pollo', 'Coscette di pollo arrosto.');
INSERT INTO piatto (nome, descrizione) VALUES ('Hamburger vegano', 'Hamburger vegano a base di legumi.');
INSERT INTO piatto (nome, descrizione) VALUES ('Falafel', 'Polpette di ceci speziate.');
INSERT INTO piatto (nome, descrizione) VALUES ('Kebab di pollo', 'Kebab di pollo con spezie orientali.');
INSERT INTO piatto (nome, descrizione) VALUES ('Piselli', 'Piselli freschi cotti al vapore.');
INSERT INTO piatto (nome, descrizione) VALUES ('Ceci', 'Ceci lessati.');

INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Fagioli in umido', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Crema di piselli', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Orzo con pomodorini e basilico', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Patate al basilico', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Carote al vapore', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Fagiolini', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Polpettine vegane', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta alla carbonara', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ("Pasta all'arrabbiata", '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta salmone e zucchine', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta e fagioli alla veneta', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Mozzarella alla romana', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Minestra di verdure', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Frittata con verdure e formaggio', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pollo al forno', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pizza pomodorini, rucola e grana', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta alla Norma (melanzane e ricotta)', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta al ragù', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Riso al curry', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Riso pilaw con piselli', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Crema di funghi', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Gnocchi al pomodoro', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Filetto di platessa alla marchigiana', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Filetto di merluzzo', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Melanzana con pomodoro e funghi', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Arrosto di tacchino', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Tortino ricotta e spinaci', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Insalatona vegetariana', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Insalata vegana con ceci, patate, carote e melanzane', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Bis di cereali con verdure', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Peperoni alla partenopea', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Melanzana con pomodoro, capperi e olive', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Roast beef con funghi', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Trancio di pizza margherita', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Insalata vegana con carote, zucchine, fagioli e mais', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Melanzana alla siciliana', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Hamburger di manzo BIO con cipolle caramellate', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Patate fritte', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Tris di verdure', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ("Patate all'olio extravergine", '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Carote e piselli al vapore', '2024-12-10', 'RistorESU Nord Piovego');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta zucca e funghi', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Polpettine vegane', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta salmone e zucchine', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Falafel', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta al tonno e olive', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Pasta pomodoro e piselli', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Arrosto di maiale', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Coscette di pollo', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Kebab di pollo', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Piselli', '2024-12-10', 'Pio X');
INSERT INTO menu_piatto (piatto, DATA, mensa) VALUES ('Ceci', '2024-12-10', 'Pio X');

INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Alto assorbimento delle radiazioni elettromagnetiche, quasi al pari di un corpo nero. Densità alta ma non esagerata, conforme agli standard UNI ISO. Sapore buono, leggermente salato, temperatura leggermente al di sopra di quella ambientale. Frazione in massa di pasta infima ma ciò è possibile che sia dovuto al fatto che il piatto servito fosse solo una delle prime mestolate. Pro: sapore, senso di sazietà raggiunto facilmente, ottimo materiale edile Contro: mancanza di nota piccante, produzione incontrollabile di metano gassoso da orifizi corporei.', 'roberto@example.com', 'Pasta e fagioli alla veneta');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, 'Il sapore è buono ma la texture non mi è piaciuta. Ci sono troppi pezzi grossi, non è stata tritata bene.', 'angela@example.com', 'Crema di piselli');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, 'Fredda e non frullata bene.', 'jane.doe@example.com', 'Crema di piselli');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buona, ma calda sarebbe stata meglio.', 'john.smith@example.com', 'Pasta alla Norma (melanzane e ricotta)');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Mangiabile.', 'alice.jones@example.com', 'Pasta alla Norma (melanzane e ricotta)');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Melanzaneun po' acide ma buona.", 'roberto@example.com', 'Pasta alla Norma (melanzane e ricotta)');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Nulla da dire, è buona. Magari calda sarebbe stata più gradita.', 'angela@example.com', 'Pasta alla Norma (melanzane e ricotta)');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Pasta troppo cotta, il sugo però era buono.', 'jane.doe@example.com', 'Pasta al ragù');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Pasta cotta giusta e ragù molto saporito, classica ma molto buona.', 'john.smith@example.com', 'Pasta al ragù');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buono, riso non stracotto,un po' al dente.", 'alice.jones@example.com', 'Riso al curry');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Riso un po' al dente ma rimane comunque buono.", 'roberto@example.com', 'Riso pilaw con piselli');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Molto buono, riso al dente ma non crudo.', 'angela@example.com', 'Riso pilaw con piselli');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Personalmente preferisco le minestre frullate, però il gusto era buono.', 'jane.doe@example.com', 'Minestra di verdure');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, 'Eccellente, nulla da dire se non buonissima.', 'john.smith@example.com', 'Crema di funghi');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, 'Sugo molto saporito.', 'alice.jones@example.com', "Pasta all'arrabbiata");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, 'Molto buona, il sugo è piccante al punto giusto.', 'roberto@example.com', "Pasta all'arrabbiata");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Troppo salato, cereali un po' troppo al dente e basilico inesistente.", 'angela@example.com', 'Orzo con pomodorini e basilico');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buona e gustosa, il sugo era abbondante e saporito, si mangia molto volentieri. Peccato per la pasta un po' al dente.", 'jane.doe@example.com', 'Pasta alla carbonara');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Dose buona, condimento abbondante e quantità di pancetta giusta, ma il sugo me lo aspettavo un po' più saporito.", 'john.smith@example.com', 'Pasta alla carbonara');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, 'Gli gnocchi erano della consistenza giusta e non molli, e il sugo molto saporito, nel complesso davvero buoni.', 'alice.jones@example.com', 'Gnocchi al pomodoro');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Ottimo considerando anche la mancanza di spine. Tramite analisi olfattiva e degustativa è possibile affermare l'assenza di olio di palma e di cadmio.", 'roberto@example.com', 'Filetto di platessa alla marchigiana');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buono, la panatura è croccante e asciutta.', 'angela@example.com', 'Filetto di merluzzo');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buono, la panatura è asciutta e croccante.', 'jane.doe@example.com', 'Filetto di merluzzo');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Il gusto era buono ma c'era troppo olio sulla melanzana.", 'john.smith@example.com', 'Melanzana con pomodoro e funghi');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Il sapore è buono, la carne non è secca.', 'alice.jones@example.com', 'Arrosto di tacchino');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buono il ripieno, la pasta sfoglia però è un po' dura e difficile da tagliare.", 'roberto@example.com', 'Tortino ricotta e spinaci');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto saporito, forse un po' salato ma molto buono.", 'angela@example.com', 'Tortino ricotta e spinaci');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buona ma impasta un po' in bocca.", 'jane.doe@example.com', 'Mozzarella alla romana');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buona, il pomodoro le dà quel tocco in più che ci sta. Arrivi alla fine che sei sazio.', 'john.smith@example.com', 'Mozzarella alla romana');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, 'Cotto bene, aromatizzato bene, buono.', 'alice.jones@example.com', 'Pollo al forno');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Si lascia mangiare, bene.', 'roberto@example.com', 'Pollo al forno');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buono, non c'è tanto da dire, il nome coincide con il prodotto, l'unica pecca è che secondo me sono assenti totalmente le proteine, ci sono solo verdure.", 'angela@example.com', 'Hamburger vegano');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Tutto buono tranne i pomodorini che erano amari.', 'jane.doe@example.com', 'Insalatona vegetariana');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Le patate erano crude, le verdure fredde e i ceci erano pochi. Apporto proteico molto basso per essere un secondo.', 'john.smith@example.com', 'Insalata vegana con ceci, patate, carote e melanzane');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Saporito, i cereali cotti al punto giusto. Ottimo anche per variare la fonte di carboidrati e non mangiare sempre grano.', 'alice.jones@example.com', 'Bis di cereali con verdure');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Il gusto di per sé era buono, anche se erano zuppi di acqua. Inoltre, la mozzarella sopra ai peperoni era palesemente la mozzarella alla romana avanzata dai giorni scorsi.', 'roberto@example.com', 'Peperoni alla partenopea');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, "Più bagnata dell'oceano, ha la consistenza di una spugna. Nel complesso il sapore è orribile.", 'angela@example.com', 'Frittata con verdure e formaggio');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Buona, ma il rapporto capperi/topping è troppo alto.', 'jane.doe@example.com', 'Melanzana con pomodoro, capperi e olive');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Molto buono, i funghi ci stanno molto bene.', 'john.smith@example.com', 'Roast beef con funghi');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buono, riempie molto, il problema è la distribuzione di mozzarella tra i vari tranci che non è per nulla uniforme.', 'alice.jones@example.com', 'Trancio di pizza margherita');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, "Molto buonO, l'impasto era morbiDo e soffice e nel complesso molto gustosa.", 'roberto@example.com', 'Trancio di pizza margherita');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Molto buona anche da fredda, buon apporto di legumi.', 'angela@example.com', 'Insalata vegana con carote, zucchine, fagioli e mais');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, 'Letteralmente il pezzo di formaggio più duro che io abbia mangiato, impossibile da tagliare, pesante. Nel complesso non è stata un’esperienza piacevole mangiarla.', 'jane.doe@example.com', 'Melanzana alla siciliana');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, 'La consistenza della carne faceva invidia al cartongesso e mi viene sinceramente da chiedermi con quale parte del manzo abbiano fatto questo hamburger, ma scommetterei sulle corna. Per fortuna le cipolle caramellate facevano un ottimo lavoro di distrazione al gusto ambiguo del prodotto animale. Punti bonus per le cose di consistenza strana che ogni tanto ti trovavi in bocca a sorpresa dopo un boccone. Citando yotobi “è come aprire un uovo di Pasqua e trovarci dentro il virus del vaiolo.”', 'john.smith@example.com', 'Hamburger di manzo BIO con cipolle caramellate');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, 'Buonissimi, poco da aggiungere.', 'alice.jones@example.com', 'Fagiolini');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Buoni ma freddi.', 'roberto@example.com', 'Fagiolini');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Buoni, ma leggermente crudi.', 'angela@example.com', 'Fagiolini');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buoni, nulla da dire.', 'jane.doe@example.com', 'Fagiolini');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Gusto medio, abbastanza buoni ma sempre freddi.', 'john.smith@example.com', 'Fagiolini');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Non sono croccanti, sono infatti un po' molli, però almeno non sono troppo salate.", 'alice.jones@example.com', 'Patate fritte');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone, peccato siano un po' fredde e molli.", 'roberto@example.com', 'Patate fritte');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Sono patate fritte, sono standard, nulla da aggiungere.', 'angela@example.com', 'Patate fritte');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buono anche se avrei preferito mangiarlo caldo.', 'jane.doe@example.com', 'Tris di verdure');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Un buon contorno, forse le verdure erano un po' troppo bagnate.", 'john.smith@example.com', 'Tris di verdure');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone, l'unica pecca sono i cavolfiori poco cotti.", 'alice.jones@example.com', 'Tris di verdure');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, 'Mix tra crude e cotte, non si riescono a mangiare.', 'roberto@example.com', "Patate all'olio extravergine");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Cotte, aromatizzate poco, due erbette potevano metterle (non basilico, grazie).', 'angela@example.com', "Patate all'olio extravergine");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, "Metà dei bocconi sono crudi e l'altra metà cotti, non hanno tutti la stessa consistenza. Sanno di poco e sono completamente scondite, consiglio di prendere sale e olio.", 'jane.doe@example.com', "Patate all'olio extravergine");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Spesso alcune sono crude, ma in questo caso erano tutte apposto.', 'john.smith@example.com', "Patate all'olio extravergine");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Incredibilmente quasi tutti i bocconi erano cotti, un miglioramento.', 'alice.jones@example.com', "Patate all'olio extravergine");
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (1, 'Completamente crude, non si salvava nemmeno un boccone.', 'roberto@example.com', 'Patate al basilico');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Le carote sono abbastanza bagnate e non caldissime, comunque rimangono un buon contorno.', 'angela@example.com', 'Carote e piselli al vapore');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, "Buone ma un po' secche.", 'jane.doe@example.com', 'Carote al vapore');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buone, un po' secche.", 'john.smith@example.com', 'Carote al vapore');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Buoni, rappresentativi della cultura veneta.', 'alice.jones@example.com', 'Fagioli in umido');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (5, 'Molto gustosa, impasto sottile ma buono, alcune volte poco cotto in qualche punto. Farcitura buona e leggera.', 'roberto@example.com', 'Pizza pomodorini, rucola e grana');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "I funghi sovrastano un po' la zucca, che si sente meno, ma nel complesso molto buona e soddisfacente.", 'angela@example.com', 'Pasta zucca e funghi');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'La pasta era discretamente buona e di buona cottura, il pomodoro non è troppo acido (cosa rara).', 'jane.doe@example.com', 'Pasta al tonno e olive');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, 'Solita, acida e insapore, pasta cotta troppo.', 'john.smith@example.com', 'Pasta pomodoro e piselli');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, 'Sugo molto buono, si mangia di gusto. Peccato che le zucchine si sentano poco, ma nel complesso buono.', 'alice.jones@example.com', 'Pasta salmone e zucchine');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Arrosto di buona cottura e consistenza, unica pecca l'eccessiva salinità (troppo sale).", 'roberto@example.com', 'Arrosto di maiale');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Cotte bene.', 'angela@example.com', 'Coscette di pollo');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, 'Insapori.', 'jane.doe@example.com', 'Polpettine vegane');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buone e saporite, accompagnate poi dal sugo di pomodoro si risolve anche il fatto che sono un po' secchi. In generale buonissime, ne mangerei tantissime.", 'john.smith@example.com', 'Polpettine vegane');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Molto buoni e saporiti, si mangiano volentieri. Unica pecca, sono un po'granulosi all'interno, per il resto ottimi.", 'alice.jones@example.com', 'Falafel');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (4, "Buoni e ben speziati, sanno proprio di falafel. Sono un po' asciutte, ma mangiati con la maionese sono top.", 'roberto@example.com', 'Falafel');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Normale, nulla da dire.', 'angela@example.com', 'Kebab di pollo');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (3, 'Nulla da dire, buoni, standard.', 'jane.doe@example.com', 'Piselli');
INSERT INTO recensione (voto, descrizione, utente, piatto) VALUES (2, 'Ceci di scarsa qualità, granulosi e di consistenza inadeguata.', 'john.smith@example.com', 'Ceci');

INSERT INTO piatto_foto (piatto, foto) VALUES ('Bis di cereali con verdure', 'images/uploads/bis-di-cereali-con-verdure.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Crema di funghi', 'images/uploads/crema-di-funghi.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Crema di piselli', 'images/uploads/crema-di-piselli2.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Filetto di merluzzo', 'images/uploads/filetto-di-merluzzo-e-carote-al-vapore.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Carote al vapore', 'images/uploads/filetto-di-merluzzo-e-carote-al-vapore.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Filetto di platessa alla Marchigiana', 'images/uploads/filetto-di-platessa-alla-marchigiana+patate-al-basilico.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Patate al basilico', 'images/uploads/filetto-di-platessa-alla-marchigiana+patate-al-basilico.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Frittata con verdure e formaggio', 'images/uploads/frittata-con-verdure-e-formaggio-+-tris-di-verdure.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Tris di verdure', 'images/uploads/frittata-con-verdure-e-formaggio-+-tris-di-verdure.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Gnocchi al pomodoro', 'images/uploads/gnocchi-al-pomodoro.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Insalata vegana con ceci, patate, carote e melanzane', 'images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais-+-tris-di-verdure.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Tris di verdure', 'images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais-+-tris-di-verdure.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Insalata vegana con carote, zucchine, fagioli e mais', 'images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais-+-carote-al-vapore.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Carote al vapore', 'images/uploads/insalata-vegana-con-fagioli-carote-zucchine-e-mais-+-carote-al-vapore.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Melanzana alla siciliana', 'images/uploads/melanzana-alla-siciliana.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Melanzana con pomodoro, capperi e olive', 'images/uploads/melanzana-con-pomodoro-capperi-e-olive-+-fagioli-in-umido.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Fagioli in umido', 'images/uploads/melanzana-con-pomodoro-capperi-e-olive-+-fagioli-in-umido.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Minestra di verdure', 'images/uploads/minestra-di-verdure.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Orzo con pomodorini e basilico', 'images/uploads/orzo-con-pomodorini-e-basilico.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Pasta al ragù', 'images/uploads/pasta-al-ragu.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta all'arrabbiata", "images/uploads/pasta-all'arrabbiata.jpg");
INSERT INTO piatto_foto (piatto, foto) VALUES ("Pasta all'arrabbiata", "images/uploads/pasta-all'arrabbiata-+-roast-beef-con-funghi-+-fagiolini.jpg");
INSERT INTO piatto_foto (piatto, foto) VALUES ('Fagiolini', "images/uploads/pasta-all'arrabbiata-+-roast-beef-con-funghi-+-fagiolini.jpg");
INSERT INTO piatto_foto (piatto, foto) VALUES ('Roast beef con funghi', "images/uploads/pasta-all'arrabbiata-+-roast-beef-con-funghi-+-fagiolini.jpg");
INSERT INTO piatto_foto (piatto, foto) VALUES ('Pasta alla Carbonara', 'images/uploads/pasta-alla-carbonara.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Pasta al tonno e olive', 'images/uploads/pasta-tonno-e-olive.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Patate fritte', 'images/uploads/patatine-fritte.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Peperoni alla partenopea', 'images/uploads/peperoni-alla-partenopea-e-fagiolini.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Fagiolini', 'images/uploads/peperoni-alla-partenopea-e-fagiolini.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Riso pilaw con piselli', 'images/uploads/riso-pilaw-con-piselli-1.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Riso pilaw con piselli', 'images/uploads/riso-pilaw-con-piselli-2.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Tortino ricotta e spinaci', 'images/uploads/tortino-ricotta-e-spinaci-+-carote-e-piselli-al-vapore.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Carote e piselli al vapore', 'images/uploads/tortino-ricotta-e-spinaci-+-carote-e-piselli-al-vapore.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Trancio di pizza margherita', 'images/uploads/trancio-di-pizza-margherita+patate-al-basilico.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Trancio di pizza margherita', 'images/uploads/trancio-di-pizza-margherita+patate-al-basilico+fagiolini.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Patate al basilico', 'images/uploads/trancio-di-pizza-margherita+patate-al-basilico.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Patate al basilico', 'images/uploads/trancio-di-pizza-margherita+patate-al-basilico+fagiolini.jpg');
INSERT INTO piatto_foto (piatto, foto) VALUES ('Fagiolini', 'images/uploads/trancio-di-pizza-margherita+patate-al-basilico+fagiolini.jpg');

-- DA QUA SOTTO NON TOCCARE ED ESCLUDERE DALLA FORMATTAZIONE DEL DOCUMENTO

DELIMITER //

CREATE EVENT IF NOT EXISTS crea_menu_settimanale
ON SCHEDULE EVERY 1 WEEK
STARTS CURRENT_TIMESTAMP
DO
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE mensa_nome VARCHAR(50);
    DECLARE data_corrente DATE;
    DECLARE i INT DEFAULT 0;
    DECLARE data_increment DATE;

    DECLARE mensa_cursor CURSOR FOR 
        SELECT nome FROM mensa;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    SET done = 0;
    SET data_corrente = CURDATE();

    OPEN mensa_cursor;

    menu_loop: LOOP
        FETCH mensa_cursor INTO mensa_nome;
        
        IF done = 1 THEN
            LEAVE menu_loop;
        END IF;
        
        SET i = 0;
        SET data_increment = 0;
        
        WHILE i < 7 DO
            SET data_increment = DATE_ADD(data_corrente, INTERVAL i DAY);
            INSERT IGNORE INTO menu (data, mensa) VALUES (data_increment, mensa_nome);
            

            INSERT IGNORE INTO menu_piatto (piatto, data, mensa)
            SELECT p.nome, data_increment, mensa_nome
            FROM piatto p
            ORDER BY RAND()
            LIMIT 6;
            
            SET i = i + 1;
        END WHILE;
    END LOOP menu_loop;
    
        

    SET done = 1;

    CLOSE mensa_cursor;
END
//

DELIMITER ;