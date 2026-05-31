CREATE DATABASE gyakorikerdesek;
USE gyakorikerdesek;

CREATE TABLE felhasznalok (
    id INT PRIMARY KEY AUTO_INCREMENT,
    felhasznalonev VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    jelszo VARCHAR(255) NOT NULL,
    szerepkor VARCHAR(20) DEFAULT 'user', -- 'user', 'moderator', 'admin'
    regisztralva TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE kerdesek (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cim VARCHAR(200) NOT NULL,
    tartalom TEXT NOT NULL,
    felhasznalo_id INT NOT NULL,
    letrehozva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (felhasznalo_id) REFERENCES felhasznalok(id) ON DELETE CASCADE
);

CREATE TABLE hozzaszolasok (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tartalom TEXT NOT NULL,
    felhasznalo_id INT NOT NULL,
    kerdes_id INT NOT NULL,
    letrehozva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (felhasznalo_id) REFERENCES felhasznalok(id) ON DELETE CASCADE,
    FOREIGN KEY (kerdes_id) REFERENCES kerdesek(id) ON DELETE CASCADE
);

CREATE TABLE ai_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    felhasznalo_id INT NULL,
    esemeny_tipus VARCHAR(50) NOT NULL,
    adatok JSON NOT NULL,
    idopont TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);