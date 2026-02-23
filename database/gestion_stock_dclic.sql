-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 23 fév. 2026 à 15:14
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_stock_dclic`
--

-- --------------------------------------------------------

--
-- Structure de la table `article`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom_article` varchar(50) NOT NULL,
  `id_categorie` int NOT NULL,
  `quantite` int NOT NULL,
  `prix_unitaire` int NOT NULL,
  `date_fabrication` datetime NOT NULL,
  `date_expiration` date DEFAULT NULL,
  `seuil_alerte` int DEFAULT '5',
  `statut` enum('actif','archive') NOT NULL DEFAULT 'actif',
  PRIMARY KEY (`id`),
  KEY `id_categorie` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `categorie_article`
--

DROP TABLE IF EXISTS `categorie_article`;
CREATE TABLE IF NOT EXISTS `categorie_article` (
  `id` int NOT NULL AUTO_INCREMENT,
  `libelle_categorie` varchar(60) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `telephone` varchar(30) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `statut` enum('actif','archive') DEFAULT 'actif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `commande`
--

DROP TABLE IF EXISTS `commande`;
CREATE TABLE IF NOT EXISTS `commande` (
  `id` int NOT NULL AUTO_INCREMENT,
  `reference` varchar(20) DEFAULT NULL,
  `id_fournisseur` int NOT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_livraison_prevue` date DEFAULT NULL,
  `date_livraison_reelle` date DEFAULT NULL,
  `statut` enum('en_attente','validee','livree','annulee') NOT NULL DEFAULT 'en_attente',
  `notes` text,
  `total_global` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `id_fournisseur` (`id_fournisseur`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `commande_detail`
--

DROP TABLE IF EXISTS `commande_detail`;
CREATE TABLE IF NOT EXISTS `commande_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_commande` int NOT NULL,
  `id_article` int NOT NULL,
  `quantite` int NOT NULL,
  `prix_unitaire` int NOT NULL,
  `total_ligne` int NOT NULL,
  `quantite_recue` int DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `id_commande` (`id_commande`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `configuration`
--

DROP TABLE IF EXISTS `configuration`;
CREATE TABLE IF NOT EXISTS `configuration` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cle` varchar(100) NOT NULL,
  `valeur` text,
  `type` varchar(50) DEFAULT 'text',
  `date_modif` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cle` (`cle`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `configuration`
--

INSERT INTO `configuration` (`id`, `cle`, `valeur`, `type`, `date_modif`) VALUES
(9, 'entreprise_nom', 'EasyStock', 'text', '2026-02-14 10:56:56'),
(10, 'entreprise_email', 'contact@easystock.com', 'email', '2026-02-14 10:56:56'),
(11, 'entreprise_telephone', '54846780', 'text', '2026-02-13 13:27:25'),
(12, 'entreprise_adresse', 'Bobo, Burkina Fasso', 'textarea', '2026-02-13 13:22:04'),
(13, 'devise', 'FCFA', 'text', '2026-02-14 12:26:13'),
(14, 'format_recu', 'ESP-{annee}-{numero}', 'text', '2026-02-13 13:22:04');

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

DROP TABLE IF EXISTS `fournisseur`;
CREATE TABLE IF NOT EXISTS `fournisseur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `telephone` varchar(15) NOT NULL,
  `adresse` varchar(50) NOT NULL,
  `statut` enum('actif','archive') NOT NULL DEFAULT 'actif',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `mouvement_stock`
--

DROP TABLE IF EXISTS `mouvement_stock`;
CREATE TABLE IF NOT EXISTS `mouvement_stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_article` int NOT NULL,
  `type` enum('entree','sortie','ajustement') NOT NULL,
  `quantite` int NOT NULL,
  `stock_avant` int NOT NULL,
  `stock_apres` int NOT NULL,
  `motif` varchar(255) DEFAULT NULL,
  `date_mouvement` datetime DEFAULT CURRENT_TIMESTAMP,
  `id_utilisateur` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
CREATE TABLE IF NOT EXISTS `password_reset` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(150) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expire` datetime NOT NULL,
  `utilise` tinyint(1) DEFAULT '0',
  `date_demande` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','gestionnaire','caissier') NOT NULL DEFAULT 'caissier',
  `statut` enum('actif','archive') NOT NULL DEFAULT 'actif',
  `date_creation` datetime DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` datetime DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `login` (`login`),
  KEY `created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `vente`
--

DROP TABLE IF EXISTS `vente`;
CREATE TABLE IF NOT EXISTS `vente` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_client` int NOT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `date_vente` datetime DEFAULT CURRENT_TIMESTAMP,
  `total_global` int NOT NULL DEFAULT '0',
  `etat` enum('0','1') NOT NULL DEFAULT '1',
  `imprime` tinyint(1) DEFAULT '0',
  `date_impression` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_client` (`id_client`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Structure de la table `vente_detail`
--

DROP TABLE IF EXISTS `vente_detail`;
CREATE TABLE IF NOT EXISTS `vente_detail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_vente` int NOT NULL,
  `id_article` int NOT NULL,
  `quantite` int NOT NULL,
  `prix_unitaire` int NOT NULL,
  `total_ligne` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_vente` (`id_vente`),
  KEY `id_article` (`id_article`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
