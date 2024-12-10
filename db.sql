-- DATABSE DI PROVA | NON FINALE
DROP TABLE IF EXISTS mensa;

DROP TABLE IF EXISTS piatto;

DROP TABLE IF EXISTS utente;

DROP TABLE IF EXISTS menu;

DROP TABLE IF EXISTS orarioapertura;

DROP TABLE IF EXISTS recensione;

DROP TABLE IF EXISTS menu_piatto;

DROP TABLE IF EXISTS piatto_foto;

DROP TABLE IF EXISTS piatto_allergeni;

DROP VIEW IF EXISTS piatto_recensioni_foto;

DROP VIEW IF EXISTS mensa_orari_apertura;

CREATE TABLE mensa (
    nome VARCHAR(50) NOT NULL,
    indirizzo VARCHAR(100) NOT NULL,
    PRIMARY KEY (nome)
);

CREATE TABLE piatto (
    nome VARCHAR(50) NOT NULL,
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
    -- CHECK (
    --     LENGTH (password) <= 12
    --     AND LENGTH (password) >= 8
    -- ),
    CHECK (username REGEXP '^[a-zA-Z0-9_]+$'),
    CHECK (
        email REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$'
    ),
    INDEX (username)
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
    PRIMARY KEY (giornoSettimana, mensa),
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
    piatto VARCHAR(50) NOT NULL,
    CHECK (
        voto >= 1
        AND voto <= 5
    ),
    PRIMARY KEY (utente, piatto),
    FOREIGN KEY (utente) REFERENCES utente (email) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE menu_piatto (
    piatto VARCHAR(50) NOT NULL,
    data DATE NOT NULL,
    mensa VARCHAR(50) NOT NULL,
    PRIMARY KEY (piatto, data, mensa),
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (data, mensa) REFERENCES menu (data, mensa) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE piatto_foto (
    photoid INT AUTO_INCREMENT,
    foto TEXT NOT NULL,
    piatto VARCHAR(50) NOT NULL,
    PRIMARY KEY (photoid, piatto),
    UNIQUE (foto),
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
);

DELIMITER / / CREATE TRIGGER check_menu_date BEFORE INSERT ON menu FOR EACH ROW BEGIN IF NEW.data > CURDATE () THEN SIGNAL SQLSTATE '45000'
SET
    MESSAGE_TEXT = 'Date cannot be in the future';

END IF;

END / / DELIMITER;

CREATE TABLE piatto_allergeni (
    allergeni VARCHAR(30) NOT NULL,
    piatto VARCHAR(50) NOT NULL,
    PRIMARY KEY (allergeni, piatto),
    FOREIGN KEY (piatto) REFERENCES piatto (nome) ON UPDATE CASCADE ON DELETE CASCADE
);


CREATE TABLE preferenze_utente
(
    email VARCHAR(50) NOT NULL,
    mensa_preferita VARCHAR(50),
    dark_mode BOOLEAN,
    PRIMARY KEY (email),
    FOREIGN KEY (email) REFERENCES utente(email) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (mensa_preferita) REFERENCES mensa(nome) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE VIEW piatto_recensioni_foto AS
SELECT
    p.nome AS piatto,
    AVG(r.voto) AS media_stelle,
    GROUP_CONCAT (
        DISTINCT pf.foto
        ORDER BY
            RAND () SEPARATOR ', '
    ) AS foto_casuali
FROM
    piatto p
    JOIN recensione r ON p.nome = r.piatto
    LEFT JOIN piatto_foto pf ON p.nome = pf.piatto
GROUP BY
    p.nome;

CREATE VIEW mensa_orari_apertura AS
SELECT
    m.nome AS mensa,
    m.indirizzo,
    oa.giornoSettimana,
    oa.orainizio,
    oa.orafine
FROM
    mensa m
    JOIN orarioapertura oa ON m.nome = oa.mensa;

-- Inserimento dati di esempio
INSERT into
    utente (email, password, dataNascita, username)
VALUES
    (
        "user@example.com",
        "password",
        "1990-01-01",
        "user"
    ),
    (
        "user1@example.com",
        "password",
        "1990-01-01",
        "user1"
    ),
    (
        "user2@example.com",
        "password",
        "1990-01-01",
        "user2"
    ),
    (
        "admin@example.com",
        "password",
        "1995-02-15",
        "admin"
    );

INSERT INTO
    mensa (nome, indirizzo)
VALUES
    ("Mensa Universitaria", "Via degli Studenti, 1"),
    ("Mensa del Lavoratore", "Corso del Lavoro, 20"),
    ("Mensa Vegana", "Via Verde, 15");

INSERT INTO
    piatto (nome, descrizione)
VALUES
    (
        "Pasta al Pomodoro",
        "Spaghetti con salsa di pomodoro fresco."
    ),
    (
        "Insalata Mista",
        "Insalata con verdure fresche e vinaigrette."
    ),
    (
        "Riso Vegano",
        "Riso con verdure e spezie, completamente vegano."
    ),
    (
        "Pollo alla Griglia",
        "Petto di pollo grigliato servito con contorno."
    ),
    (
        "Zuppa di Lenticchie",
        "Zuppa calda di lenticchie, perfetta per l'inverno."
    );

INSERT INTO
    menu (data, mensa)
VALUES
    ("2024-10-01", "Mensa Universitaria"),
    ("2024-10-01", "Mensa del Lavoratore"),
    ("2024-10-01", "Mensa Vegana");

INSERT INTO
    menu_piatto (piatto, data, mensa)
VALUES
    (
        "Pasta al Pomodoro",
        "2024-10-01",
        "Mensa Universitaria"
    ),
    (
        "Insalata Mista",
        "2024-10-01",
        "Mensa Universitaria"
    ),
    (
        "Pollo alla Griglia",
        "2024-10-01",
        "Mensa del Lavoratore"
    ),
    (
        "Zuppa di Lenticchie",
        "2024-10-01",
        "Mensa del Lavoratore"
    ),
    ("Riso Vegano", "2024-10-01", "Mensa Vegana");

INSERT INTO
    recensione (voto, descrizione, utente, piatto)
VALUES
    (
        4,
        "Buona qualità dei piatti, ma a volte c'è attesa.",
        "user@example.com",
        "Pasta al Pomodoro"
    ),
    (
        5,
        "Ottima mensa! Servizio veloce e cibo delizioso.",
        "user1@example.com",
        "Pollo alla Griglia"
    ),
    (
        3,
        "Buoni piatti, ma un po' costosi.",
        "user2@example.com",
        "Riso Vegano"
    ),
    (
        5,
        "Ottima pasta! Salsa fresca e saporita.",
        "user2@example.com",
        "Pasta al Pomodoro"
    ),
    (
        4,
        "Insalata buona, ma potrebbe avere più varietà.",
        "user1@example.com",
        "Insalata Mista"
    ),
    (
        5,
        "Riso delizioso! Consigliato!",
        "user1@example.com",
        "Riso Vegano"
    ),
    (
        4,
        "Pollo alla griglia ben cotto e saporito.",
        "user2@example.com",
        "Pollo alla Griglia"
    );

INSERT into
    orarioapertura (giornoSettimana, orainizio, orafine, mensa)
VALUES
    (1, "08:00", "14:00", "Mensa Universitaria"),
    (2, "08:00", "14:00", "Mensa Universitaria"),
    (3, "08:00", "14:00", "Mensa Universitaria"),
    (4, "08:00", "14:00", "Mensa Universitaria"),
    (5, "08:00", "14:00", "Mensa Universitaria"),
    (6, "08:00", "14:00", "Mensa Universitaria"),
    (7, "08:00", "14:00", "Mensa Universitaria"),
    (1, "08:00", "14:00", "Mensa del Lavoratore"),
    (2, "08:00", "14:00", "Mensa del Lavoratore"),
    (3, "08:00", "14:00", "Mensa del Lavoratore"),
    (4, "08:00", "14:00", "Mensa del Lavoratore"),
    (5, "08:00", "14:00", "Mensa del Lavoratore"),
    (6, "08:00", "14:00", "Mensa del Lavoratore"),
    (7, "08:00", "14:00", "Mensa del Lavoratore"),
    (1, "08:00", "14:00", "Mensa Vegana"),
    (2, "08:00", "14:00", "Mensa Vegana"),
    (3, "08:00", "14:00", "Mensa Vegana"),
    (4, "08:00", "14:00", "Mensa Vegana"),
    (5, "08:00", "14:00", "Mensa Vegana"),
    (6, "08:00", "14:00", "Mensa Vegana"),
    (7, "08:00", "14:00", "Mensa Vegana");
