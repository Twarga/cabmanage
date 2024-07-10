-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 10, 2024 at 03:13 AM
-- Server version: 11.4.2-MariaDB
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cabmanage`
--
CREATE DATABASE IF NOT EXISTS `cabmanage` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `cabmanage2`;

-- --------------------------------------------------------

--
-- Table structure for table `docteurs_exterieurs`
--

DROP TABLE IF EXISTS `docteurs_exterieurs`;
CREATE TABLE `docteurs_exterieurs` (
  `docteur_id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `hospital_name` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `examens`
--

DROP TABLE IF EXISTS `examens`;
CREATE TABLE `examens` (
  `examen_id` int(11) NOT NULL,
  `sub_type` varchar(255) DEFAULT NULL,
  `prelevement_number` varchar(100) NOT NULL,
  `prix` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `examens`
--

INSERT INTO `examens` (`examen_id`, `sub_type`, `prelevement_number`, `prix`) VALUES
(9, '1 anticorps ', 'p279', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `factures`
--

DROP TABLE IF EXISTS `factures`;
CREATE TABLE `factures` (
  `facture_id` int(11) NOT NULL,
  `examen_id` int(11) NOT NULL,
  `prelevement_id` int(11) NOT NULL,
  `total_prix` float NOT NULL,
  `prix_reduit` float DEFAULT NULL,
  `avance` float DEFAULT NULL,
  `montant_du` float DEFAULT NULL,
  `rest` float DEFAULT NULL,
  `etat_paiement` enum('Non payé','Partiellement payé','Payé') NOT NULL,
  `date_paiement` date DEFAULT NULL,
  `date_creation` date DEFAULT NULL,
  `date_modification` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `barcode` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

DROP TABLE IF EXISTS `patients`;
CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `date_naissance` date NOT NULL,
  `age` int(11) NOT NULL,
  `type_identification` enum('Passeport','Carte d''identité','Permis de conduire','Carte de séjour') NOT NULL,
  `identification_number` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `situation_familiale` enum('Marié','Célibataire','Veuf') NOT NULL,
  `sexe` enum('Femme','Homme') NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `prelevement_history` text DEFAULT NULL,
  `type_assurance` enum('CNOPS','CNSS','MAFAR','SAHAM') NOT NULL,
  `numero_assurance` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prelevements`
--

DROP TABLE IF EXISTS `prelevements`;
CREATE TABLE `prelevements` (
  `prelevement_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `type_prelevement` enum('Biopsie','Cytologie','Pièce opératoire','Immuno Histochimique') NOT NULL,
  `date_reception` date NOT NULL,
  `date_creation` date NOT NULL,
  `nombre_flacons` int(11) NOT NULL,
  `ordonnance` blob DEFAULT NULL,
  `docteur_exterieur_id` int(11) DEFAULT NULL,
  `rapport_template` text DEFAULT NULL,
  `rapport_txt` text DEFAULT NULL,
  `examen_id` int(11) DEFAULT NULL,
  `facture_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

DROP TABLE IF EXISTS `templates`;
CREATE TABLE `templates` (
  `template_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `type_user` enum('Docteur','Assistant','Admin') NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `prenom`, `phone_number`, `email`, `type_user`, `password`) VALUES
(1, 'hamid', 'L3roui', '1234567890', 'hamid@example.com', 'Admin', '$2y$10$WTEZwsjwi.zsX9tAqHWoGujAlX/g33nAMY/OObZberoTDP9S2CrX6'),
(2, 'Docteur 1', 'L3roui', '1234567890', 'D1@example.com', 'Docteur', '$2y$10$om4BT3PhrmX1vovA4bPpi.44s9jq8RtYnFTGiKEpIYWTEd9yeXkgK'),
(3, 'Assitance1', 'L3roui', '1234567890', 'A1@example.com', 'Assistant', '$2y$10$8IjC.gwwZ9KuV4DOHOGpxupLM/vBx7jjrQKD3oNbjYy8TRLpf1hFG'),
(4, 'ad1', 'm', '4058235', 'admin@gmail.com', 'Admin', 'password123'),
(5, 'ad1', 'm', '4058235', 'admin@gmail.com', 'Admin', 'password123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `docteurs_exterieurs`
--
ALTER TABLE `docteurs_exterieurs`
  ADD PRIMARY KEY (`docteur_id`);

--
-- Indexes for table `examens`
--
ALTER TABLE `examens`
  ADD PRIMARY KEY (`examen_id`);

--
-- Indexes for table `factures`
--
ALTER TABLE `factures`
  ADD PRIMARY KEY (`facture_id`),
  ADD KEY `examen_id` (`examen_id`),
  ADD KEY `prelevement_id` (`prelevement_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `prelevements`
--
ALTER TABLE `prelevements`
  ADD PRIMARY KEY (`prelevement_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `docteur_exterieur_id` (`docteur_exterieur_id`),
  ADD KEY `examen_id` (`examen_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`template_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `docteurs_exterieurs`
--
ALTER TABLE `docteurs_exterieurs`
  MODIFY `docteur_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `examens`
--
ALTER TABLE `examens`
  MODIFY `examen_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `factures`
--
ALTER TABLE `factures`
  MODIFY `facture_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prelevements`
--
ALTER TABLE `prelevements`
  MODIFY `prelevement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `template_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `factures`
--
ALTER TABLE `factures`
  ADD CONSTRAINT `factures_ibfk_1` FOREIGN KEY (`examen_id`) REFERENCES `examens` (`examen_id`),
  ADD CONSTRAINT `factures_ibfk_2` FOREIGN KEY (`prelevement_id`) REFERENCES `prelevements` (`prelevement_id`);

--
-- Constraints for table `prelevements`
--
ALTER TABLE `prelevements`
  ADD CONSTRAINT `prelevements_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `prelevements_ibfk_2` FOREIGN KEY (`docteur_exterieur_id`) REFERENCES `docteurs_exterieurs` (`docteur_id`),
  ADD CONSTRAINT `prelevements_ibfk_3` FOREIGN KEY (`examen_id`) REFERENCES `examens` (`examen_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
