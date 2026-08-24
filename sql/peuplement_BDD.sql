-- on se place dans la BDD
USE chez_deb;

-- comme le fichier est disponible sur github les mots de passe sont changés
INSERT INTO utilisateur (nom, email, mot_de_passe, role) VALUES
    ('Olivier Debrabant', 'contactchezdeb@gmail.com', 'mdp', 'admin'),
    ('Paolina', 'paolina.info@gmail.com', 'mdp', 'user');

INSERT INTO reservation () VALUES
    ();