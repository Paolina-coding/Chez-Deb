-- création de la base de données
DROP DATABASE IF EXISTS chez_deb;
CREATE DATABASE chez_deb;

-- création de l'utilisateur responsable avec tous les droits sur cette base de données (ici le mot de passe a été modifié car le fichier est sur GitHub)
DROP USER IF EXISTS 'responsable'@'localhost';
CREATE USER 'responsable'@'localhost' IDENTIFIED BY '***'; 
GRANT ALL PRIVILEGES ON chez_deb.* TO 'responsable'@'localhost' WITH GRANT OPTION;
FLUSH PRIVILEGES;

-- on se place dans la BDD
USE chez_deb;

-- création des tables
CREATE TABLE utilisateur (
  id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(255) NOT NULL,
  email VARCHAR(191) NOT NULL UNIQUE,
  mot_de_passe VARCHAR(255) NOT NULL,
  role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
);

CREATE TABLE reservation (
  id_reservation INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NULL,
  nom_client VARCHAR(255) NOT NULL,
  email_client VARCHAR(255),
  telephone_client VARCHAR(30),
  date_reservation DATE NOT NULL,
  heure_reservation TIME NOT NULL,
  nb_personnes INT NOT NULL CHECK (nb_personnes > 0),
  date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);

CREATE TABLE photo (
  id_photo INT AUTO_INCREMENT PRIMARY KEY,
  id_utilisateur INT NOT NULL,
  chemin_fichier VARCHAR(255) NOT NULL,
  description VARCHAR(255),
  statut ENUM('en_attente', 'acceptee', 'refusee') DEFAULT 'en_attente',
  date_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur)
);
