
CREATE DATABASE IF NOT EXISTS ecoride;
USE ecoride;

-- Désactivation des vérifications de clés étrangères pour les drops si nécessaire
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS participation;
DROP TABLE IF EXISTS covoiturage;
DROP TABLE IF EXISTS voiture;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS marque;
DROP TABLE IF EXISTS role;
DROP TABLE IF EXISTS parametre;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE parametre (
    id_parametre INT AUTO_INCREMENT PRIMARY KEY,
    propriete VARCHAR(100) NOT NULL UNIQUE,
    valeur VARCHAR(255) NOT NULL,
    description VARCHAR(255)
);

CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE utilisateur (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    pseudo VARCHAR(50) NOT NULL,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    telephone VARCHAR(20),
    adresse VARCHAR(255),
    date_naissance DATE,
    photo VARCHAR(255),
    bio TEXT,
    pref_fumeur BOOLEAN DEFAULT FALSE,
    pref_animaux BOOLEAN DEFAULT FALSE,
    pref_voyage VARCHAR(255),
    credits INT DEFAULT 20,
    is_suspended BOOLEAN DEFAULT FALSE,
    id_role INT NOT NULL,
    FOREIGN KEY (id_role) REFERENCES role(id_role)
);

CREATE TABLE marque (
    id_marque INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(50) NOT NULL
);

CREATE TABLE voiture (
    id_voiture INT AUTO_INCREMENT PRIMARY KEY,
    modele VARCHAR(50) NOT NULL,
    immatriculation VARCHAR(20) NOT NULL UNIQUE,
    energie VARCHAR(50) NOT NULL,
    couleur VARCHAR(50),
    date_premiere_immatriculation DATE,
    nombre_places INT DEFAULT 4,
    id_marque INT NOT NULL,
    id_utilisateur INT NOT NULL,
    FOREIGN KEY (id_marque) REFERENCES marque(id_marque),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE covoiturage (
    id_covoiturage INT AUTO_INCREMENT PRIMARY KEY,
    date_depart DATE NOT NULL,
    heure_depart TIME NOT NULL,
    date_arrivee DATE NOT NULL,
    heure_arrivee TIME NOT NULL,
    lieu_depart VARCHAR(100) NOT NULL,
    lieu_arrivee VARCHAR(100) NOT NULL,
    statut VARCHAR(50) DEFAULT 'PLANIFIÉ',
    nb_place INT NOT NULL,
    prix_personne FLOAT NOT NULL,
    est_ecologique BOOLEAN DEFAULT FALSE,
    id_conducteur INT NOT NULL,
    id_voiture INT NOT NULL,
    FOREIGN KEY (id_conducteur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_voiture) REFERENCES voiture(id_voiture)
);

CREATE TABLE participation (
    id_participation INT AUTO_INCREMENT PRIMARY KEY,
    id_covoiturage INT NOT NULL,
    id_passager INT NOT NULL,
    statut VARCHAR(50) DEFAULT 'CONFIRMÉ',
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage),
    FOREIGN KEY (id_passager) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE avis (
    id_avis INT AUTO_INCREMENT PRIMARY KEY,
    commentaire TEXT,
    note INT NOT NULL,
    statut VARCHAR(50) DEFAULT 'EN_ATTENTE',
    id_covoiturage INT NOT NULL,
    id_auteur INT NOT NULL,
    id_destinataire INT NOT NULL,
    FOREIGN KEY (id_covoiturage) REFERENCES covoiturage(id_covoiturage),
    FOREIGN KEY (id_auteur) REFERENCES utilisateur(id_utilisateur),
    FOREIGN KEY (id_destinataire) REFERENCES utilisateur(id_utilisateur)
);

INSERT INTO role (libelle) VALUES ('Visiteur'), ('Utilisateur'), ('Employe'), ('Administrateur');
INSERT INTO marque (libelle) VALUES ('Tesla'), ('Renault'), ('Peugeot'), ('BMW'), ('Toyota');

/* Utilisateur test pour seed.php (EcoDriver) */
/* Mot de passe hashé (mot de passe : 'test') */
INSERT INTO utilisateur (email, password, pseudo, id_role, credits) VALUES 
('test@test.com', '$2y$10$xRXr3xYe.GPMj0R/nFTWEeyoafsJvaJ0npAbH.9hJ3UAtE8Z9Xy66', 'EcoDriver', 2, 50),
('employe@test.com', '$2y$10$xRXr3xYe.GPMj0R/nFTWEeyoafsJvaJ0npAbH.9hJ3UAtE8Z9Xy66', 'EmployeTest', 3, 999),
('admin@test.com', '$2y$10$xRXr3xYe.GPMj0R/nFTWEeyoafsJvaJ0npAbH.9hJ3UAtE8Z9Xy66', 'AdminTest', 4, 999);

INSERT INTO parametre (propriete, valeur, description) VALUES 
('credits_inscription', '20', 'Nombre de crédits offerts à l\'inscription'),
('commission_trajet', '2', 'Nombre de crédits prélevés par EcoRide par trajet');
