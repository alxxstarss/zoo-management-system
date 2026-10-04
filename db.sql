-- db.sql
-- Script de création de la structure de la base de données du zoo.
-- Il définit les tables de visiteurs, personnel, animaux, etc.

CREATE TABLE Contribution (
    id_contribution INT PRIMARY KEY,
    niveau_contribution VARCHAR(30)
);

CREATE TABLE Prestation (
    num_prest INT PRIMARY KEY,
    nom_prest VARCHAR(30)
);

CREATE TABLE Possede (
    num_prest INT REFERENCES Prestation(num_prest),
    id_contribution INT REFERENCES Contribution(id_contribution),
    PRIMARY KEY (num_prest, id_contribution)
);

CREATE TABLE Visiteur (
    id_visiteur INT PRIMARY KEY,
    nom_visiteur VARCHAR(30),
    prenom_visiteur VARCHAR(30),
    tel_visiteur CHAR(10),
    email_visiteur VARCHAR(30)
);

CREATE TABLE Espece_Animale (
    id_espece INT PRIMARY KEY,
    nom_latin_esp VARCHAR(30),
    nom_usuel_espece VARCHAR(30)
);

CREATE TABLE Cohabite (
    id_espece1 INT REFERENCES Espece_Animale(id_espece),
    id_espece2 INT REFERENCES Espece_Animale(id_espece),
    PRIMARY KEY (id_espece1, id_espece2)
);

CREATE TABLE Zone (
    id_zone INT PRIMARY KEY,
    nom_zone VARCHAR(30)
);

CREATE TABLE Enclos (
    id_enclos INT PRIMARY KEY,
    latitude_enclos FLOAT,
    longitude_enclos FLOAT,
    particularite VARCHAR(30),
    superficie_enclos FLOAT,
    id_zone INT REFERENCES Zone(id_zone)
);

CREATE TABLE Animal (
    id_animal INT PRIMARY KEY,
    nom_animal VARCHAR(30),
    menace CHAR(3),
    date_animal DATE,
    poids_animal FLOAT,
    regime_animal VARCHAR(30),
    id_espece INT REFERENCES Espece_Animale(id_espece),
    idpere INT REFERENCES Animal(id_animal),
    idmere INT REFERENCES Animal(id_animal),
    id_enclos INT REFERENCES Enclos(id_enclos)
);

CREATE TABLE Parraine (
    id_contribution INT REFERENCES Contribution(id_contribution),
    id_visiteur INT REFERENCES Visiteur(id_visiteur),
    id_animal INT REFERENCES Animal(id_animal),
    PRIMARY KEY (id_contribution, id_visiteur, id_animal)
);

CREATE TABLE Personnel (
    id_personnel INT PRIMARY KEY,
    nom_personnel VARCHAR(30),
    prenom_personnel VARCHAR(30),
    date_debut DATE,
    mot_de_passe VARCHAR(255),
    salaire FLOAT,
    type_personnel VARCHAR(30)
);

CREATE TABLE Emplois (
    id_emplois INT,
    id_personnel INT REFERENCES Personnel(id_personnel),
    date_debut DATE,
    date_fin DATE,
    contrat VARCHAR(30),
    PRIMARY KEY(id_emplois, id_personnel)
);

CREATE TABLE Entretien (
    id_personnel INT REFERENCES Personnel(id_personnel),
    id_zone INT REFERENCES Zone(id_zone),
    PRIMARY KEY (id_personnel, id_zone)
);

CREATE TABLE Soigneur (
    id_soigneur INT PRIMARY KEY,
    specialite_soigneur VARCHAR(30),
    grade_soigneur VARCHAR(30),
    id_personnel INT REFERENCES Personnel(id_personnel),
    id_manager INT REFERENCES Soigneur(id_soigneur),
    id_remplacent INT REFERENCES Soigneur(id_soigneur),
    constraint uq_soigneur_personnel unique (id_personnel)
);

CREATE TABLE Soigne (
    id_soigneur INT REFERENCES Soigneur(id_soigneur),
    id_animal INT REFERENCES Animal(id_animal),
    dose_soins FLOAT,
    type_soins VARCHAR(30),
    PRIMARY KEY (id_soigneur, id_animal)
);

CREATE TABLE Nourrir (
    id_soigneur INT REFERENCES Soigneur(id_soigneur),
    id_animal INT REFERENCES Animal(id_animal),
    dose_nourriture FLOAT,
    PRIMARY KEY (id_soigneur, id_animal)
);

CREATE TABLE Boutique (
    id_boutique INT PRIMARY KEY,
    nom_boutique VARCHAR(30),
    type_boutique VARCHAR(30),
    id_responsable INT REFERENCES Personnel(id_personnel),
    id_zone INT REFERENCES Zone(id_zone)
);

CREATE TABLE Vend (
    id_vendeur INT REFERENCES Personnel(id_personnel),
    id_boutique INT REFERENCES Boutique(id_boutique),
    PRIMARY KEY (id_vendeur, id_boutique)
);

CREATE TABLE Chiffre_Af_j (
    id_responsable INT REFERENCES Personnel(id_personnel),
    id_boutique INT REFERENCES Boutique(id_boutique),
    date_j DATE,
    montantca NUMBER(10,2),
    PRIMARY KEY (id_responsable, id_boutique, date_j)
);

CREATE TABLE Prestataire (
    id_prestataire INT PRIMARY KEY,
    nom_prestataire VARCHAR(30),
    prenom_prestataire VARCHAR(30),
    specialite_prestataire VARCHAR(30)
);

CREATE TABLE Reparation (
    num_reparation INT PRIMARY KEY,
    nature_reparation VARCHAR(30),
    type_reparation VARCHAR(30)
);

CREATE TABLE Intervient (
    id_prestataire INT REFERENCES Prestataire(id_prestataire),
    num_reparation INT REFERENCES Reparation(num_reparation),
    PRIMARY KEY (id_prestataire, num_reparation)
);

CREATE TABLE Intervient2 (
    id_personnel_teq INT REFERENCES Personnel(id_personnel),
    num_reparation INT REFERENCES Reparation(num_reparation),
    PRIMARY KEY (id_personnel_teq, num_reparation)
);

CREATE TABLE Reparer (
    id_enclos INT REFERENCES Enclos(id_enclos),
    num_reparation INT REFERENCES Reparation(num_reparation),
    PRIMARY KEY (id_enclos, num_reparation)
);

INSERT INTO Contribution VALUES (1, 'Bronze');
INSERT INTO Contribution VALUES (2, 'Argent');
INSERT INTO Contribution VALUES (3, 'Or');

INSERT INTO Prestation VALUES (1, 'Photo du filleul');
INSERT INTO Prestation VALUES (2, 'Fond d’écran');
INSERT INTO Prestation VALUES (3, 'Visite guidée gratuite');
INSERT INTO Prestation VALUES (4, 'Accès VIP');

INSERT INTO Possede VALUES (1, 1);
INSERT INTO Possede VALUES (2, 1);
INSERT INTO Possede VALUES (2, 2);
INSERT INTO Possede VALUES (3, 1);
INSERT INTO Possede VALUES (3, 2);
INSERT INTO Possede VALUES (3, 3);
INSERT INTO Possede VALUES (4, 3);

INSERT INTO Visiteur VALUES (101, 'Martin', 'Luc', '0601020304', 'luc@mail.com');
INSERT INTO Visiteur VALUES (102, 'Dupont', 'Sarah', '0605060708', 'sarah@mail.com');
INSERT INTO Visiteur VALUES (103, 'Nguyen', 'Bao', '0609091011', 'bao@mail.com');

INSERT INTO Espece_Animale VALUES (1, 'Panthera leo', 'Lion');
INSERT INTO Espece_Animale VALUES (2, 'Panthera tigris', 'Tigre');
INSERT INTO Espece_Animale VALUES (3, 'Ailuropoda melanoleuca', 'Panda');
INSERT INTO Espece_Animale VALUES (4, 'Falco peregrinus', 'Faucon pèlerin');

INSERT INTO Cohabite VALUES (1, 2);
INSERT INTO Cohabite VALUES (3, 4);
INSERT INTO Cohabite VALUES (1, 3);
INSERT INTO Cohabite VALUES (1, 4);

INSERT INTO Zone VALUES (10, 'Félins');
INSERT INTO Zone VALUES (11, 'Rapaces');
INSERT INTO Zone VALUES (12, 'Herbivores');

INSERT INTO Enclos VALUES (100, 48.123, 2.456, 'Roche volcanique', 300, 10);
INSERT INTO Enclos VALUES (101, 48.124, 2.457, 'Perchoirs', 200, 11);
INSERT INTO Enclos VALUES (102, 48.125, 2.458, 'Bassin eau douce', 500, 12);

INSERT INTO Animal VALUES (2001, 'Simba', 'Oui', DATE '2018-05-12', 190, 'Carnivore', 1, NULL, NULL, 100);
INSERT INTO Animal VALUES (2002, 'Nala', 'Non', DATE '2017-07-20', 150, 'Carnivore', 1, NULL, NULL, 100);
INSERT INTO Animal VALUES (2003, 'Kovu', 'Non', DATE '2022-06-01', 80, 'Carnivore', 1, 2001, 2002, 100);
INSERT INTO Animal VALUES (2004, 'Kiara', 'Non', DATE '2023-08-15', 60, 'Carnivore', 1, 2001, 2002, 100);
INSERT INTO Animal VALUES (2005, 'Bamboo', 'Non', DATE '2020-10-10', 100, 'Herbivore', 3, NULL, NULL, 102);
INSERT INTO Animal VALUES (2006, 'Aquila', 'Non', DATE '2019-01-01', 5, 'Carnivore', 4, NULL, NULL, 101);
INSERT INTO Animal VALUES (2007, 'Vautour', 'Non', DATE '2023-01-01', 10, 'Carnivore', 4,NULL,NULL, 101);

INSERT INTO Parraine VALUES (1, 101, 2005);
INSERT INTO Parraine VALUES (2, 102, 2001);
INSERT INTO Parraine VALUES (3, 103, 2006);

INSERT INTO Personnel VALUES (500, 'Durand', 'Paul', DATE '2020-01-01', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 2000, 'Soigneur');
INSERT INTO Personnel VALUES (501, 'Lemoine', 'Claire', DATE '2019-06-01', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 2300, 'Soigneur');
INSERT INTO Personnel VALUES (502, 'Roux', 'Marc', DATE '2021-03-01', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 2100, 'Soigneur');
INSERT INTO Personnel VALUES (503, 'Petit', 'Anna', DATE '2022-09-01', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 1800, 'Soigneur');
INSERT INTO Personnel VALUES (504, 'Moreau', 'Jean', DATE '2018-02-15', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 2200, 'Soigneur');
INSERT INTO Personnel VALUES (505, 'Bernard', 'Luc', DATE '2017-11-20', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 1900, 'Entretien');
INSERT INTO Personnel VALUES (506, 'Martin', 'Sophie', DATE '2016-05-10', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 2500, 'Responsable boutique');
INSERT INTO Personnel VALUES (507, 'Leroy', 'Nina', DATE '2015-04-01', '$2y$10$MLVuCBSwvu9H9KHxB7SQ0.Hx0NF6SIvOZqMUcDbXIFCbVAR6XVwTu', 2600, 'Comptable');

INSERT INTO Emplois VALUES (1, 500, DATE '2020-01-01', DATE '2021-12-31', 'CDD');
INSERT INTO Emplois VALUES (2, 500, DATE '2022-01-01', NULL, 'CDI');
INSERT INTO Emplois VALUES (3, 501, DATE '2019-06-01', NULL, 'CDI');
INSERT INTO Emplois VALUES (4, 502, DATE '2021-03-01', NULL, 'CDI');
INSERT INTO Emplois VALUES (5, 503, DATE '2022-09-01', NULL, 'CDI');
INSERT INTO Emplois VALUES (6, 504, DATE '2018-02-15', DATE '2020-02-15', 'CDD');
INSERT INTO Emplois VALUES (7, 504, DATE '2020-02-16', NULL, 'CDI');

INSERT INTO Entretien VALUES (505, 10);
INSERT INTO Entretien VALUES (505, 11);
INSERT INTO Entretien VALUES (505, 12);
INSERT INTO Entretien VALUES (504, 11);
INSERT INTO Entretien VALUES (502, 12);

INSERT INTO Soigneur VALUES (600, 'Félins', 'manager', 500, null, null);
INSERT INTO Soigneur VALUES (603, 'Félins', 'soigneur', 503, 600, null);
INSERT INTO Soigneur VALUES (604, 'Rapaces', 'soigneur', 504, 600, null);
INSERT INTO Soigneur VALUES (601, 'Rapaces', 'soigneur', 501, 600, 604);
INSERT INTO Soigneur VALUES (602, 'Herbivores', 'soigneur', 502, 600, 603);

INSERT INTO Soigne VALUES (600, 2001, 10, 'Vermifuge');
INSERT INTO Soigne VALUES (600, 2002, 5, 'Antibiotique');
INSERT INTO Soigne VALUES (601, 2006, 2, 'Désinfection aile');
INSERT INTO Soigne VALUES (602, 2005, 3, 'Traitement digestion');
INSERT INTO Soigne VALUES (603, 2004, 2, 'Vaccin rappel');

INSERT INTO Nourrir VALUES (600, 2001, 6);
INSERT INTO Nourrir VALUES (600, 2002, 5);
INSERT INTO Nourrir VALUES (602, 2005, 8);
INSERT INTO Nourrir VALUES (601, 2006, 0.3);
INSERT INTO Nourrir VALUES (602, 2003, 4);
INSERT INTO Nourrir VALUES (603, 2004, 3);

INSERT INTO Boutique VALUES (700, 'Souvenirs Félins', 'Souvenir', 506, 10);
INSERT INTO Boutique VALUES (701, 'Snack Herbivores', 'Snack', 506, 12);

INSERT INTO Vend VALUES (503, 700);
INSERT INTO Vend VALUES (503, 701);

INSERT INTO Chiffre_Af_j VALUES (506, 700, DATE '2026-03-10', 450.00);
INSERT INTO Chiffre_Af_j VALUES (506, 700, DATE '2026-03-11', 389.50);
INSERT INTO Chiffre_Af_j VALUES (506, 701, DATE '2026-03-10', 780.25);
INSERT INTO Chiffre_Af_j VALUES (506, 701, DATE '2026-03-11', 920.90);

INSERT INTO Prestataire VALUES (800, 'Durand', 'Hugo', 'Électricien');
INSERT INTO Prestataire VALUES (801, 'Morel', 'Julie', 'Maçonnerie');

INSERT INTO Reparation VALUES (900, 'Réparation clôture', 'Technique');
INSERT INTO Reparation VALUES (901, 'Installation éclairage', 'Électrique');

INSERT INTO Intervient VALUES (800, 901);
INSERT INTO Intervient VALUES (801, 900);

INSERT INTO Intervient2 VALUES (504, 900);

INSERT INTO Reparer VALUES (100, 900);
INSERT INTO Reparer VALUES (102, 901);