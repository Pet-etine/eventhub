-- ============================================================
--  EventHub - SQL-rakenne (kevyt esimerkkiversio)
--  Tämä tiedosto ei sisällä täydellistä tuotantotietokantaa,
--  mutta riittää sovelluksen esittelyyn ja testaukseen.
-- ============================================================

DROP DATABASE IF EXISTS eventhub;
CREATE DATABASE eventhub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eventhub;

-- ============================================================
--  Käyttäjätilit
-- ============================================================

CREATE TABLE henkilo (
    idhenkilo INT AUTO_INCREMENT PRIMARY KEY,
    nimi VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    salasana VARCHAR(255) NOT NULL,
    rooli ENUM('kayttaja', 'admin') DEFAULT 'kayttaja',
    vahvistusavain VARCHAR(100),
    vahvistettu TINYINT(1) DEFAULT 0,
    reset_avain VARCHAR(100),
    reset_voimassa TIMESTAMP NULL,
    luotu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
--  Tapahtumat
-- ============================================================

CREATE TABLE tapahtuma (
    idtapahtuma INT AUTO_INCREMENT PRIMARY KEY,
    nimi VARCHAR(200) NOT NULL,
    kuvaus TEXT,
    paikkakunta VARCHAR(120) NOT NULL,
    tap_alkaa DATE NOT NULL,
    tap_loppuu DATE NOT NULL
);

-- Esimerkkitapahtumia (voit poistaa nämä)
INSERT INTO tapahtuma (nimi, paikkakunta, tap_alkaa, tap_loppuu) VALUES
('Desucon Frostbite 2025', 'Lahti', '2025-01-24', '2025-01-26'),
('Assembly Winter 2025', 'Helsinki', '2025-02-20', '2025-02-23'),
('Kummacon 2025', 'Oulu', '2025-05-17', '2025-05-18');

-- ============================================================
--  Ilmoittautumiset
-- ============================================================

CREATE TABLE ilmoittautuminen (
    idilmoittautuminen INT AUTO_INCREMENT PRIMARY KEY,
    idhenkilo INT NOT NULL,
    idtapahtuma INT NOT NULL,
    rooli ENUM('kavija','esiintyja','myyja','vapaaehtoinen','cosplayer') DEFAULT 'kavija',
    muistiinpanot TEXT,
    aika TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_ilmo_henkilo FOREIGN KEY(idhenkilo)
        REFERENCES henkilo(idhenkilo) ON DELETE CASCADE,

    CONSTRAINT fk_ilmo_tapahtuma FOREIGN KEY(idtapahtuma)
        REFERENCES tapahtuma(idtapahtuma) ON DELETE CASCADE
);

-- ============================================================
--  Admin-tilin luonti (esimerkkikäyttöön)
-- ============================================================

-- HUOM! Salasana tässä on hashattu versiolla: salasana123
INSERT INTO henkilo (nimi, email, salasana, rooli, vahvistettu)
VALUES (
    'Admin',
    'admin@example.com',
    '$2y$10$1KFDlFdKO1qN3rmC2PHoM.qSqesB6i9Ta4shZxuTqNiMVXZOFmB/q',
    'admin',
    1
);

-- ============================================================
--  Valmis.
-- ============================================================
