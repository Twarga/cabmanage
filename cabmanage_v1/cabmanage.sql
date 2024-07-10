-- Create database
CREATE DATABASE IF NOT EXISTS cabmanage;
USE cabmanage;

-- Table for users
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    email VARCHAR(100) NOT NULL,
    type_user ENUM('Docteur', 'Assistant', 'Admin') NOT NULL
);

-- Table for patients
CREATE TABLE IF NOT EXISTS patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_naissance DATE NOT NULL,
    age INT NOT NULL,
    type_identification ENUM('Passeport', 'Carte d\'identité', 'Permis de conduire', 'Carte de séjour') NOT NULL,
    identification_number VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone_number VARCHAR(15),
    situation_familiale ENUM('Marié', 'Célibataire', 'Veuf') NOT NULL,
    sexe ENUM('Femme', 'Homme') NOT NULL,
    adresse VARCHAR(255),
    prelevement_history TEXT,
    type_assurance ENUM('CNOPS', 'CNSS', 'MAFAR', 'SAHAM') NOT NULL,
    numero_assurance VARCHAR(100) NOT NULL
);

-- Table for external doctors
CREATE TABLE IF NOT EXISTS docteurs_exterieurs (
    docteur_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    phone_number VARCHAR(15) NOT NULL,
    hospital_name VARCHAR(100) NOT NULL
);

-- Table for examens
CREATE TABLE IF NOT EXISTS examens (
    examen_id INT AUTO_INCREMENT PRIMARY KEY,
    sub_type ENUM('Type1', 'Type2', 'Type3') NOT NULL,
    prelevement_number VARCHAR(100) NOT NULL,
    prix FLOAT NOT NULL
);

-- Table for prélèvements
CREATE TABLE IF NOT EXISTS prelevements (
    prelevement_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    type_prelevement ENUM('Biopsie', 'Cytologie', 'Pièce opératoire', 'Immuno Histochimique') NOT NULL,
    date_reception DATE NOT NULL,
    date_creation DATE NOT NULL,
    nombre_flacons INT NOT NULL,
    ordonnance BLOB,
    docteur_exterieur_id INT,
    rapport_template TEXT,
    rapport_txt TEXT,
    examen_id INT,
    facture_id INT,
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id),
    FOREIGN KEY (docteur_exterieur_id) REFERENCES docteurs_exterieurs(docteur_id),
    FOREIGN KEY (examen_id) REFERENCES examens(examen_id)
);

-- Table for factures
CREATE TABLE IF NOT EXISTS factures (
    facture_id INT AUTO_INCREMENT PRIMARY KEY,
    examen_id INT NOT NULL,
    prelevement_id INT NOT NULL,
    total_prix FLOAT NOT NULL,
    prix_reduit FLOAT,
    avance FLOAT,
    montant_du FLOAT,
    rest FLOAT,
    etat_paiement ENUM('Non payé', 'Partiellement payé', 'Payé') NOT NULL,
    FOREIGN KEY (examen_id) REFERENCES examens(examen_id),
    FOREIGN KEY (prelevement_id) REFERENCES prelevements(prelevement_id)
);
