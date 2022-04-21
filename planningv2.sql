-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 21 avr. 2022 à 14:45
-- Version du serveur : 10.4.22-MariaDB
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `planningv2`
--
CREATE DATABASE IF NOT EXISTS `planningv2` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `planningv2`;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client`
(
    `Id_Client` int(11) NOT NULL AUTO_INCREMENT,
    PRIMARY KEY (`Id_Client`)
) ENGINE = MyISAM
  AUTO_INCREMENT = 5
  DEFAULT CHARSET = latin1;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`Id_Client`)
VALUES (1),
       (2),
       (3),
       (4);

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
CREATE TABLE IF NOT EXISTS `evenement`
(
    `Id_Evenement`        int(11) NOT NULL AUTO_INCREMENT,
    `Objet_Evenement`     varchar(50) DEFAULT NULL,
    `Contenu_Evenement`   text        DEFAULT NULL,
    `Url_Evenement`       text        DEFAULT NULL,
    `Datedebut_Evenement` datetime    DEFAULT NULL,
    `Datefin_Evenement`   datetime    DEFAULT NULL,
    `Id_TypeEvenement`    int(11)     DEFAULT NULL,
    PRIMARY KEY (`Id_Evenement`),
    KEY `Id_TypeEvenement` (`Id_TypeEvenement`)
) ENGINE = MyISAM
  AUTO_INCREMENT = 7
  DEFAULT CHARSET = latin1;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`Id_Evenement`, `Objet_Evenement`, `Contenu_Evenement`, `Url_Evenement`, `Datedebut_Evenement`,
                         `Datefin_Evenement`, `Id_TypeEvenement`)
VALUES (1, 'rdv simple', 'teste rdv', '', '2022-04-21 11:34:00', '2022-04-21 12:37:00', 1),
       (2, 'rdv pour mesurer ', 'ceci est un teste de rdv métré', '', '2022-04-21 13:40:00', '2022-04-21 15:42:00', 2),
       (3, 'jerome', 'en congé', '', '2022-04-18 09:00:00', '2022-04-22 18:00:00', 4),
       (6, 'livraison Café', 'teste livraison de café', '', '2022-04-21 15:05:00', '2022-04-21 15:05:00', 5);

-- --------------------------------------------------------

--
-- Structure de la table `typeevenement`
--

DROP TABLE IF EXISTS `typeevenement`;
CREATE TABLE IF NOT EXISTS `typeevenement`
(
    `Id_TypeEvenement`      int(11) NOT NULL AUTO_INCREMENT,
    `Nom_TypeEvenement`     varchar(50) DEFAULT NULL,
    `Couleur_TypeEvenement` varchar(50) DEFAULT NULL,
    `Id_Client`             int(11)     DEFAULT NULL,
    PRIMARY KEY (`Id_TypeEvenement`),
    KEY `Id_Client` (`Id_Client`)
) ENGINE = MyISAM
  AUTO_INCREMENT = 9
  DEFAULT CHARSET = latin1;

--
-- Déchargement des données de la table `typeevenement`
--

INSERT INTO `typeevenement` (`Id_TypeEvenement`, `Nom_TypeEvenement`, `Couleur_TypeEvenement`, `Id_Client`)
VALUES (1, 'Rdv', '#cf2a2a', 1),
       (2, 'Rdv métré', '#1dcacd', 1),
       (3, 'Rdv pose', '#4bb724', 1),
       (4, 'congé', '#cbe411', 1),
       (5, 'Livraison', '#ec8d09', 1),
       (6, 'Commande', '#2115c1', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
