-- --------------------------------------------------------
-- Hôte :                        127.0.0.1
-- Version du serveur:           5.7.24 - MySQL Community Server (GPL)
-- SE du serveur:                Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Listage de la structure de la base pour e-gestock
DROP DATABASE IF EXISTS `e-gestock`;
CREATE DATABASE IF NOT EXISTS `e-gestock` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `e-gestock`;

-- Listage de la structure de la table e-gestock. adjudication_commandes
DROP TABLE IF EXISTS `adjudication_commandes`;
CREATE TABLE IF NOT EXISTS `adjudication_commandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `selection_adjudications_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `adjudication_commandes_selection_adjudications_id_index` (`selection_adjudications_id`),
  KEY `adjudication_commandes_profils_id_index` (`profils_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. agents
DROP TABLE IF EXISTS `agents`;
CREATE TABLE IF NOT EXISTS `agents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mle` int(11) NOT NULL,
  `nom_prenoms` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_nais` date DEFAULT NULL,
  `genres_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `agents_mle_unique` (`mle`),
  KEY `agents_genres_id_index` (`genres_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. agent_sections
DROP TABLE IF EXISTS `agent_sections`;
CREATE TABLE IF NOT EXISTS `agent_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agents_id` bigint(20) unsigned NOT NULL,
  `sections_id` bigint(20) unsigned NOT NULL,
  `exercice` year(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `agent_sections_agents_id_index` (`agents_id`),
  KEY `agent_sections_sections_id_index` (`sections_id`),
  KEY `agent_sections_exercice_index` (`exercice`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. articles
DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_articles` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `design_article` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_fam` bigint(20) unsigned DEFAULT NULL,
  `type_articles_id` bigint(20) unsigned DEFAULT NULL,
  `code_unite` bigint(20) unsigned DEFAULT NULL,
  `ref_taxe` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_ref_articles_unique` (`ref_articles`),
  KEY `articles_ref_fam_index` (`ref_fam`),
  KEY `articles_type_articles_id_index` (`type_articles_id`),
  KEY `articles_code_unite_index` (`code_unite`),
  KEY `articles_ref_taxe_index` (`ref_taxe`)
) ENGINE=InnoDB AUTO_INCREMENT=5327 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. commandes
DROP TABLE IF EXISTS `commandes`;
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `num_bc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `delai` int(11) DEFAULT NULL,
  `demande_achats_id` bigint(20) unsigned NOT NULL,
  `periodes_id` bigint(20) unsigned NOT NULL,
  `date_echeance` date NOT NULL,
  `date_livraison_prevue` date DEFAULT NULL,
  `date_livraison_effective` date DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `commandes_demande_achats_id_index` (`demande_achats_id`),
  KEY `commandes_periodes_id_index` (`periodes_id`),
  KEY `profils_id` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. cotation_fournisseurs
DROP TABLE IF EXISTS `cotation_fournisseurs`;
CREATE TABLE IF NOT EXISTS `cotation_fournisseurs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organisations_id` bigint(20) unsigned NOT NULL,
  `demande_achats_id` bigint(20) unsigned NOT NULL,
  `montant_total_brut` double DEFAULT NULL,
  `remise_generale` double DEFAULT NULL,
  `montant_total_net` double DEFAULT NULL,
  `tva` double DEFAULT NULL,
  `montant_total_ttc` double DEFAULT NULL,
  `acompte` tinyint(1) DEFAULT '0',
  `taux_acompte` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant_acompte` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assiete_bnc` double DEFAULT NULL,
  `taux_bnc` double DEFAULT NULL,
  `net_a_payer` double DEFAULT NULL,
  `delai` double NOT NULL,
  `periodes_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `date_echeance` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cotation_fournisseurs_organisations_id_index` (`organisations_id`),
  KEY `cotation_fournisseurs_demande_achats_id_index` (`demande_achats_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. credit_budgetaires
DROP TABLE IF EXISTS `credit_budgetaires`;
CREATE TABLE IF NOT EXISTS `credit_budgetaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `depots_id` bigint(20) unsigned NOT NULL,
  `code_structure` bigint(20) unsigned NOT NULL,
  `ref_fam` bigint(20) unsigned NOT NULL,
  `exercice` bigint(20) unsigned NOT NULL,
  `credit_initiale` int(11) NOT NULL,
  `consommation` int(11) NOT NULL,
  `credit` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `credit_budgetaires_depots_id_index` (`depots_id`),
  KEY `credit_budgetaires_structures_id_index` (`code_structure`),
  KEY `credit_budgetaires_ref_fam_index` (`ref_fam`),
  KEY `credit_budgetaires_exercice_index` (`exercice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. criteres
DROP TABLE IF EXISTS `criteres`;
CREATE TABLE IF NOT EXISTS `criteres` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mesure` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. critere_adjudications
DROP TABLE IF EXISTS `critere_adjudications`;
CREATE TABLE IF NOT EXISTS `critere_adjudications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `criteres_id` bigint(20) unsigned NOT NULL,
  `demande_achats_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `critere_adjudications_criteres_id_index` (`criteres_id`),
  KEY `critere_adjudications_demande_achats_id_index` (`demande_achats_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. dashboards
DROP TABLE IF EXISTS `dashboards`;
CREATE TABLE IF NOT EXISTS `dashboards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dashboards_position_unique` (`position`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. demandes
DROP TABLE IF EXISTS `demandes`;
CREATE TABLE IF NOT EXISTS `demandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qte_demandee` int(11) NOT NULL,
  `prixu_demande` bigint(20) NOT NULL DEFAULT '0',
  `montant_demande` bigint(20) NOT NULL DEFAULT '0',
  `requisitions_id` bigint(20) unsigned NOT NULL,
  `magasin_stocks_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `requisitions_intitule` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `demandes_requisitions_id_index` (`requisitions_id`),
  KEY `demandes_magasin_stocks_id_index` (`magasin_stocks_id`),
  KEY `profils_id` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. demande_achats
DROP TABLE IF EXISTS `demande_achats`;
CREATE TABLE IF NOT EXISTS `demande_achats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `num_bc` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_fam` bigint(20) unsigned NOT NULL,
  `intitule` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_gestion` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exercice` year(4) NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `num_bc` (`num_bc`),
  KEY `demande_achats_profils_id_index` (`profils_id`),
  KEY `ref_fam` (`ref_fam`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. demande_achat_credit_budgetaires
DROP TABLE IF EXISTS `demande_achat_credit_budgetaires`;
CREATE TABLE IF NOT EXISTS `demande_achat_credit_budgetaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `demande_achats_id` bigint(20) unsigned NOT NULL,
  `credit_budgetaires_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `demande_achat_credit_budgetaires_demande_achats_id_index` (`demande_achats_id`),
  KEY `demande_achat_credit_budgetaires_credit_budgetaires_id_index` (`credit_budgetaires_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. demande_fonds
DROP TABLE IF EXISTS `demande_fonds`;
CREATE TABLE IF NOT EXISTS `demande_fonds` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `num_dem` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_section` bigint(20) unsigned NOT NULL,
  `profils_id_emetteur` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `ref_fam` bigint(20) unsigned NOT NULL,
  `credit_budgetaires_id` bigint(20) unsigned NOT NULL,
  `exercice` bigint(20) unsigned NOT NULL,
  `solde_avant_op` bigint(20) unsigned NOT NULL,
  `intitule` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `montant` bigint(20) NOT NULL,
  `observation` text COLLATE utf8mb4_unicode_ci,
  `profils_id_signataire` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `demande_fonds_num_dem_unique` (`num_dem`),
  KEY `demande_fonds_sections_id_index` (`code_section`),
  KEY `demande_fonds_profils_id_index` (`profils_id`),
  KEY `demande_fonds_ref_fam_index` (`ref_fam`),
  KEY `demande_fonds_exercice_index` (`exercice`),
  KEY `demande_fonds_profils_id_signataire_index` (`profils_id_signataire`),
  KEY `profils_id_emetteur` (`profils_id_emetteur`),
  KEY `credit_budgetaires_id` (`credit_budgetaires_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. depots
DROP TABLE IF EXISTS `depots`;
CREATE TABLE IF NOT EXISTS `depots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_depot` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `design_dep` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tel_dep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adr_dep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ville_dep` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profils_id` bigint(20) unsigned DEFAULT NULL,
  `principal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_ville` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depots_ref_depot_unique` (`ref_depot`),
  KEY `depots_profils_id_index` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. detail_adjudications
DROP TABLE IF EXISTS `detail_adjudications`;
CREATE TABLE IF NOT EXISTS `detail_adjudications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cotation_fournisseurs_id` bigint(20) unsigned NOT NULL,
  `critere_adjudications_id` bigint(20) unsigned NOT NULL,
  `valeur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_adjudications_cotation_fournisseurs_id_index` (`cotation_fournisseurs_id`),
  KEY `detail_adjudications_criteres_id_index` (`critere_adjudications_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. detail_cotations
DROP TABLE IF EXISTS `detail_cotations`;
CREATE TABLE IF NOT EXISTS `detail_cotations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cotation_fournisseurs_id` bigint(20) unsigned NOT NULL,
  `ref_articles` bigint(20) unsigned NOT NULL,
  `qte` int(11) NOT NULL,
  `prix_unit` int(11) NOT NULL,
  `remise` double DEFAULT '0',
  `montant_ht` double NOT NULL DEFAULT '0',
  `montant_ttc` double DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_cotations_cotation_fournisseurs_id_index` (`cotation_fournisseurs_id`),
  KEY `detail_cotations_ref_articles_index` (`ref_articles`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. detail_demande_achats
DROP TABLE IF EXISTS `detail_demande_achats`;
CREATE TABLE IF NOT EXISTS `detail_demande_achats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `demande_achats_id` bigint(20) unsigned NOT NULL,
  `ref_articles` bigint(20) unsigned NOT NULL,
  `qte_demandee` int(11) NOT NULL,
  `qte_accordee` int(11) DEFAULT NULL,
  `flag_valide` tinyint(1) DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_demande_achats_demande_achats_id_index` (`demande_achats_id`),
  KEY `detail_demande_achats_ref_articles_index` (`ref_articles`),
  KEY `profils_id` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. detail_factures
DROP TABLE IF EXISTS `detail_factures`;
CREATE TABLE IF NOT EXISTS `detail_factures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `factures_id` bigint(20) unsigned NOT NULL,
  `ref_articles` bigint(20) unsigned NOT NULL,
  `qte` int(11) NOT NULL,
  `prix_unit` int(11) NOT NULL,
  `remise` double DEFAULT NULL,
  `montant_ht` double DEFAULT NULL,
  `montant_ttc` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_factures_factures_id_index` (`factures_id`),
  KEY `detail_factures_ref_articles_index` (`ref_articles`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. detail_livraisons
DROP TABLE IF EXISTS `detail_livraisons`;
CREATE TABLE IF NOT EXISTS `detail_livraisons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `livraison_commandes_id` bigint(20) unsigned NOT NULL,
  `detail_cotations_id` bigint(20) unsigned NOT NULL,
  `qte` int(11) NOT NULL,
  `prix_unit` int(11) NOT NULL,
  `remise` double DEFAULT '0',
  `montant_ht` double NOT NULL DEFAULT '0',
  `montant_ttc` double DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `detail_cotations_ref_articles_index` (`detail_cotations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. dotation_budgetaires
DROP TABLE IF EXISTS `dotation_budgetaires`;
CREATE TABLE IF NOT EXISTS `dotation_budgetaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `depots_id` bigint(20) unsigned NOT NULL,
  `ref_fam` bigint(20) unsigned NOT NULL,
  `exercice` bigint(20) unsigned NOT NULL,
  `dotation_initiale` int(11) NOT NULL,
  `consommation` int(11) NOT NULL,
  `dotation` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dotation_budgetaires_depots_id_index` (`depots_id`),
  KEY `dotation_budgetaires_ref_fam_index` (`ref_fam`),
  KEY `dotation_budgetaires_exercice_index` (`exercice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. exercices
DROP TABLE IF EXISTS `exercices`;
CREATE TABLE IF NOT EXISTS `exercices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `exercice` year(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exercices_exercice_unique` (`exercice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. factures
DROP TABLE IF EXISTS `factures`;
CREATE TABLE IF NOT EXISTS `factures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `selection_adjudications_id` bigint(20) unsigned NOT NULL,
  `organisations_id` bigint(20) unsigned NOT NULL,
  `demande_achats_id` bigint(20) unsigned NOT NULL,
  `montant_total_brut` double DEFAULT NULL,
  `remise_generale` double DEFAULT NULL,
  `montant_total_net` double DEFAULT NULL,
  `tva` double DEFAULT NULL,
  `montant_total_ttc` double DEFAULT NULL,
  `assiete_bnc` double DEFAULT NULL,
  `taux_bnc` double DEFAULT NULL,
  `net_a_payer` double DEFAULT NULL,
  `acompte` tinyint(1) DEFAULT NULL,
  `taux_acompte` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant_acompte` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delai` double NOT NULL DEFAULT '0',
  `periodes_id` bigint(20) unsigned NOT NULL,
  `date_echeance` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `factures_selection_adjudications_id_index` (`selection_adjudications_id`),
  KEY `factures_organisations_id_index` (`organisations_id`),
  KEY `factures_demande_achats_id_index` (`demande_achats_id`),
  KEY `factures_periodes_id_index` (`periodes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. familles
DROP TABLE IF EXISTS `familles`;
CREATE TABLE IF NOT EXISTS `familles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_fam` int(11) NOT NULL,
  `design_fam` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `familles_ref_fam_unique` (`ref_fam`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. genres
DROP TABLE IF EXISTS `genres`;
CREATE TABLE IF NOT EXISTS `genres` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `genres_libelle_unique` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. gestions
DROP TABLE IF EXISTS `gestions`;
CREATE TABLE IF NOT EXISTS `gestions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code_gestion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `libelle_gestion` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gestions_code_gestion_unique` (`code_gestion`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. inventaires
DROP TABLE IF EXISTS `inventaires`;
CREATE TABLE IF NOT EXISTS `inventaires` (
  `num_inv` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `debut_per` date NOT NULL,
  `fin_per` date NOT NULL,
  `flag_valide` tinyint(1) DEFAULT '0',
  `flag_integre` tinyint(1) DEFAULT '0',
  `ref_depot` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`num_inv`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. inventaire_articles
DROP TABLE IF EXISTS `inventaire_articles`;
CREATE TABLE IF NOT EXISTS `inventaire_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `inventaires_id` bigint(20) unsigned NOT NULL,
  `magasin_stocks_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `intergre_profils_id` bigint(20) unsigned DEFAULT NULL,
  `qte_theo` int(11) NOT NULL,
  `qte_phys` int(11) NOT NULL,
  `ecart` int(11) NOT NULL,
  `justificatif` text COLLATE utf8mb4_unicode_ci,
  `flag_valide` tinyint(1) DEFAULT '0',
  `flag_integre` tinyint(1) DEFAULT '0',
  `integre_created_at` datetime DEFAULT NULL,
  `integre_updated_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `inventaire_articles_inventaires_id_index` (`inventaires_id`),
  KEY `inventaire_articles_magasin_stocks_id_index` (`magasin_stocks_id`),
  KEY `inventaire_articles_profils_id_index` (`profils_id`),
  KEY `inventaire_articles_intergre_profils_id_index` (`intergre_profils_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. livraisons
DROP TABLE IF EXISTS `livraisons`;
CREATE TABLE IF NOT EXISTS `livraisons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qte` int(11) NOT NULL,
  `prixu_livre` bigint(20) NOT NULL,
  `montant_livre` bigint(20) NOT NULL,
  `statut` tinyint(1) DEFAULT NULL,
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `demandes_id` bigint(20) unsigned NOT NULL,
  `mouvements_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livraisons_profils_id_index` (`profils_id`),
  KEY `livraisons_demandes_id_index` (`demandes_id`),
  KEY `mouvements_id` (`mouvements_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. livraison_commandes
DROP TABLE IF EXISTS `livraison_commandes`;
CREATE TABLE IF NOT EXISTS `livraison_commandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cotation_fournisseurs_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livraison_commandes_commandes_id_index` (`cotation_fournisseurs_id`),
  KEY `livraison_commandes_profils_id_livreur_index` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. livraison_retours
DROP TABLE IF EXISTS `livraison_retours`;
CREATE TABLE IF NOT EXISTS `livraison_retours` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qte` int(11) NOT NULL,
  `prixu` bigint(20) NOT NULL,
  `montant` bigint(20) NOT NULL,
  `statut` tinyint(1) DEFAULT NULL,
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `retours_id` bigint(20) unsigned NOT NULL,
  `mouvements_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livraison_retours_profils_id_index` (`profils_id`),
  KEY `livraison_retours_retours_id_index` (`retours_id`),
  KEY `mouvements_id` (`mouvements_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. livraison_validers
DROP TABLE IF EXISTS `livraison_validers`;
CREATE TABLE IF NOT EXISTS `livraison_validers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profils_id` bigint(20) unsigned NOT NULL,
  `livraison_commandes_id` bigint(20) unsigned NOT NULL,
  `detail_livraisons_id` bigint(20) unsigned NOT NULL,
  `mouvements_id` bigint(20) unsigned DEFAULT NULL,
  `qte` int(11) NOT NULL,
  `prix_unit` int(11) NOT NULL,
  `remise` double DEFAULT '0',
  `montant_ht` double DEFAULT NULL,
  `montant_ttc` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `livraison_validers_livraison_commandes_id_index` (`livraison_commandes_id`),
  KEY `livraison_validers_detail_livraisons_id_index` (`detail_livraisons_id`),
  KEY `profils_id` (`profils_id`),
  KEY `mouvements_id` (`mouvements_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. magasins
DROP TABLE IF EXISTS `magasins`;
CREATE TABLE IF NOT EXISTS `magasins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_magasin` int(11) NOT NULL,
  `design_magasin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `depots_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `magasins_ref_magasin_unique` (`ref_magasin`),
  KEY `magasins_depots_id_index` (`depots_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. magasin_stocks
DROP TABLE IF EXISTS `magasin_stocks`;
CREATE TABLE IF NOT EXISTS `magasin_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qte` int(11) NOT NULL,
  `cmup` bigint(20) DEFAULT NULL,
  `montant` bigint(20) DEFAULT NULL,
  `stock_securite` int(11) DEFAULT NULL,
  `stock_alert` int(11) DEFAULT NULL,
  `stock_mini` int(11) DEFAULT NULL,
  `ref_articles` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_magasin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `magasin_stocks_ref_articles_index` (`ref_articles`),
  KEY `magasin_stocks_ref_magasin_index` (`ref_magasin`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. maitrise_stocks
DROP TABLE IF EXISTS `maitrise_stocks`;
CREATE TABLE IF NOT EXISTS `maitrise_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `magasin_stocks_id` bigint(20) unsigned NOT NULL,
  `type_maitrise_stocks_id` bigint(20) unsigned NOT NULL,
  `periodes_id` bigint(20) unsigned NOT NULL,
  `valeur` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maitrise_stocks_magasin_stocks_id_index` (`magasin_stocks_id`),
  KEY `maitrise_stocks_type_maitrise_stocks_id_index` (`type_maitrise_stocks_id`),
  KEY `maitrise_stocks_periodes_id_index` (`periodes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. mouvements
DROP TABLE IF EXISTS `mouvements`;
CREATE TABLE IF NOT EXISTS `mouvements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_mouvements_id` bigint(20) unsigned NOT NULL,
  `magasin_stocks_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `qte` int(11) NOT NULL,
  `prix_unit` int(11) DEFAULT NULL,
  `montant_ht` int(11) DEFAULT NULL,
  `taxe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `montant_ttc` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mouvements_magasin_stocks_id_index` (`magasin_stocks_id`),
  KEY `mouvements_profils_id_index` (`profils_id`),
  KEY `type_mouvements_id` (`type_mouvements_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. organisations
DROP TABLE IF EXISTS `organisations`;
CREATE TABLE IF NOT EXISTS `organisations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entnum` int(10) unsigned DEFAULT NULL,
  `denomination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_organisations_id` bigint(20) unsigned NOT NULL,
  `contacts` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `organisations_denomination_unique` (`denomination`),
  UNIQUE KEY `entnum` (`entnum`),
  KEY `organisations_type_organisations_id_index` (`type_organisations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. organisation_articles
DROP TABLE IF EXISTS `organisation_articles`;
CREATE TABLE IF NOT EXISTS `organisation_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organisations_id` bigint(20) unsigned NOT NULL,
  `ref_fam` bigint(20) unsigned NOT NULL,
  `flag_actif` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_articles_organisations_id_index` (`organisations_id`),
  KEY `organisation_articles_ref_fam_index` (`ref_fam`),
  KEY `organisation_articles_flag_actif_index` (`flag_actif`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. organisation_depots
DROP TABLE IF EXISTS `organisation_depots`;
CREATE TABLE IF NOT EXISTS `organisation_depots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_depot` bigint(20) unsigned NOT NULL,
  `organisations_id` bigint(20) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `flag_actif` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `organisation_depots_depots_id_index` (`ref_depot`),
  KEY `organisation_depots_organisations_id_index` (`organisations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. pays
DROP TABLE IF EXISTS `pays`;
CREATE TABLE IF NOT EXISTS `pays` (
  `code_pays` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom_pays` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_pays`),
  UNIQUE KEY `pays_nom_pays_unique` (`nom_pays`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. periodes
DROP TABLE IF EXISTS `periodes`;
CREATE TABLE IF NOT EXISTS `periodes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle_periode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valeur` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. preselection_soumissionnaires
DROP TABLE IF EXISTS `preselection_soumissionnaires`;
CREATE TABLE IF NOT EXISTS `preselection_soumissionnaires` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `critere_adjudications_id` bigint(20) unsigned NOT NULL,
  `organisations_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `preselection_soumissionnaires_critere_adjudications_id_index` (`critere_adjudications_id`),
  KEY `preselection_soumissionnaires_organisations_id_index` (`organisations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. profils
DROP TABLE IF EXISTS `profils`;
CREATE TABLE IF NOT EXISTS `profils` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `users_id` bigint(20) unsigned NOT NULL,
  `type_profils_id` bigint(20) unsigned NOT NULL,
  `flag_actif` tinyint(1) DEFAULT NULL,
  `flag_connexion` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profils_users_id_index` (`users_id`),
  KEY `profils_type_profils_id_index` (`type_profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. requisitions
DROP TABLE IF EXISTS `requisitions`;
CREATE TABLE IF NOT EXISTS `requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `num_dem` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_bc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exercice` year(4) NOT NULL,
  `intitule` text COLLATE utf8mb4_unicode_ci,
  `code_gestion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requisitions_exercice_index` (`exercice`),
  KEY `requisitions_code_gestion_index` (`code_gestion`),
  KEY `requisitions_profils_id_index` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. responsable_depots
DROP TABLE IF EXISTS `responsable_depots`;
CREATE TABLE IF NOT EXISTS `responsable_depots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profils_id` bigint(20) unsigned NOT NULL,
  `ref_depot` bigint(20) unsigned NOT NULL,
  `flag_actif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `responsable_depots_profils_id_index` (`profils_id`),
  KEY `responsable_depots_depots_id_index` (`ref_depot`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. retours
DROP TABLE IF EXISTS `retours`;
CREATE TABLE IF NOT EXISTS `retours` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `qte_retour` int(11) NOT NULL,
  `prixu_retour` bigint(20) NOT NULL,
  `montant_retour` bigint(20) NOT NULL,
  `observation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `livraisons_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `retours_livraisons_id_index` (`livraisons_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. sections
DROP TABLE IF EXISTS `sections`;
CREATE TABLE IF NOT EXISTS `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code_section` int(11) NOT NULL,
  `code_structure` bigint(20) unsigned NOT NULL,
  `nom_section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_section` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_gestion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sections_code_section_index` (`code_section`),
  KEY `sections_code_structure_index` (`code_structure`),
  KEY `sections_code_gestion_index` (`code_gestion`)
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. selection_adjudications
DROP TABLE IF EXISTS `selection_adjudications`;
CREATE TABLE IF NOT EXISTS `selection_adjudications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cotation_fournisseurs_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `selection_adjudications_cotation_fournisseurs_id_index` (`cotation_fournisseurs_id`),
  KEY `selection_adjudications_critere_adjudications_id_index` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. send_mails
DROP TABLE IF EXISTS `send_mails`;
CREATE TABLE IF NOT EXISTS `send_mails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_send_mails_id` bigint(20) unsigned NOT NULL,
  `elements_id` bigint(20) unsigned NOT NULL,
  `email` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `send_mails_type_send_mails_id_index` (`type_send_mails_id`),
  KEY `send_mails_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_commandes
DROP TABLE IF EXISTS `statut_commandes`;
CREATE TABLE IF NOT EXISTS `statut_commandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `commandes_id` bigint(20) unsigned NOT NULL,
  `type_statut_commandes_id` bigint(20) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_commandes_commandes_id_index` (`commandes_id`),
  KEY `statut_commandes_type_statut_commandes_id_index` (`type_statut_commandes_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_demande_achats
DROP TABLE IF EXISTS `statut_demande_achats`;
CREATE TABLE IF NOT EXISTS `statut_demande_achats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `demande_achats_id` bigint(20) unsigned NOT NULL,
  `type_statut_demande_achats_id` bigint(20) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_demande_achats_demande_achats_id_index` (`demande_achats_id`),
  KEY `statut_demande_achats_type_statut_demande_achats_id_index` (`type_statut_demande_achats_id`),
  KEY `profils_id` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_exercices
DROP TABLE IF EXISTS `statut_exercices`;
CREATE TABLE IF NOT EXISTS `statut_exercices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `exercice` bigint(20) unsigned NOT NULL,
  `type_statut_exercices_id` bigint(20) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_exercices_exercice_index` (`exercice`),
  KEY `statut_exercices_type_statut_exercices_id_index` (`type_statut_exercices_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_livraison_commandes
DROP TABLE IF EXISTS `statut_livraison_commandes`;
CREATE TABLE IF NOT EXISTS `statut_livraison_commandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `livraison_commandes_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `type_statut_livraisons_id` bigint(20) unsigned NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_livraison_commandes_livraison_commandes_id_index` (`livraison_commandes_id`),
  KEY `statut_livraison_commandes_profils_id_index` (`profils_id`),
  KEY `statut_livraison_commandes_type_statut_livraisons_id_index` (`type_statut_livraisons_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_livraison_requisitions
DROP TABLE IF EXISTS `statut_livraison_requisitions`;
CREATE TABLE IF NOT EXISTS `statut_livraison_requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `livraisons_id` bigint(20) unsigned NOT NULL,
  `type_statut_livraisons_id` bigint(20) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_livraison_requisitions_livraisons_id_index` (`livraisons_id`),
  KEY `statut_livraison_requisitions_type_statut_livraisons_id_index` (`type_statut_livraisons_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_organisations
DROP TABLE IF EXISTS `statut_organisations`;
CREATE TABLE IF NOT EXISTS `statut_organisations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `organisations_id` bigint(20) unsigned NOT NULL,
  `type_statut_organisations_id` bigint(20) unsigned NOT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `profils_id_statut` bigint(20) unsigned NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_organisations_type_statut_organisations_id_index` (`type_statut_organisations_id`),
  KEY `statut_organisations_profils_id_index` (`profils_id`),
  KEY `organisations_id` (`organisations_id`),
  KEY `profils_id_statut` (`profils_id_statut`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_requisitions
DROP TABLE IF EXISTS `statut_requisitions`;
CREATE TABLE IF NOT EXISTS `statut_requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requisitions_id` bigint(20) unsigned NOT NULL,
  `type_statut_requisitions_id` bigint(20) unsigned NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `commentaire` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_demande_achats_demande_achats_id_index` (`requisitions_id`),
  KEY `statut_demande_achats_type_statut_demande_achats_id_index` (`type_statut_requisitions_id`),
  KEY `profils_id` (`profils_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. statut_responsable_depots
DROP TABLE IF EXISTS `statut_responsable_depots`;
CREATE TABLE IF NOT EXISTS `statut_responsable_depots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `responsable_depots_id` bigint(20) unsigned NOT NULL,
  `type_statut_r_dep_id` bigint(20) unsigned NOT NULL,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `statut_responsable_depots_responsable_depots_id_index` (`responsable_depots_id`),
  KEY `statut_responsable_depots_type_statut_r_dep_id_index` (`type_statut_r_dep_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. structures
DROP TABLE IF EXISTS `structures`;
CREATE TABLE IF NOT EXISTS `structures` (
  `code_structure` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom_structure` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ref_depot` int(11) NOT NULL,
  `num_structure` int(11) NOT NULL,
  `organisations_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_structure`),
  KEY `structures_organisations_id_index` (`organisations_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9234 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. sub_dashboards
DROP TABLE IF EXISTS `sub_dashboards`;
CREATE TABLE IF NOT EXISTS `sub_dashboards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `dashboards_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_dashboards_dashboards_id_index` (`dashboards_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. sub_sub_dashboards
DROP TABLE IF EXISTS `sub_sub_dashboards`;
CREATE TABLE IF NOT EXISTS `sub_sub_dashboards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `sub_dashboards_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_sub_dashboards_sub_dashboards_id_index` (`sub_dashboards_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. sub_sub_sub_dashboards
DROP TABLE IF EXISTS `sub_sub_sub_dashboards`;
CREATE TABLE IF NOT EXISTS `sub_sub_sub_dashboards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  `sub_sub_dashboards_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sub_sub_sub_dashboards_sub_sub_dashboards_id_index` (`sub_sub_dashboards_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. taxes
DROP TABLE IF EXISTS `taxes`;
CREATE TABLE IF NOT EXISTS `taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ref_taxe` int(11) NOT NULL,
  `nom_taxe` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `taux` decimal(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `taxes_ref_taxe_unique` (`ref_taxe`),
  UNIQUE KEY `taxes_nom_taxe_unique` (`nom_taxe`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_articles
DROP TABLE IF EXISTS `type_articles`;
CREATE TABLE IF NOT EXISTS `type_articles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `design_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_articles_design_type_unique` (`design_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_commandes
DROP TABLE IF EXISTS `type_commandes`;
CREATE TABLE IF NOT EXISTS `type_commandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle_type_commande` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_maitrise_stocks
DROP TABLE IF EXISTS `type_maitrise_stocks`;
CREATE TABLE IF NOT EXISTS `type_maitrise_stocks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_mouvements
DROP TABLE IF EXISTS `type_mouvements`;
CREATE TABLE IF NOT EXISTS `type_mouvements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_organisations
DROP TABLE IF EXISTS `type_organisations`;
CREATE TABLE IF NOT EXISTS `type_organisations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_organisations_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_profils
DROP TABLE IF EXISTS `type_profils`;
CREATE TABLE IF NOT EXISTS `type_profils` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_profils_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_send_mails
DROP TABLE IF EXISTS `type_send_mails`;
CREATE TABLE IF NOT EXISTS `type_send_mails` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_statut_commandes
DROP TABLE IF EXISTS `type_statut_commandes`;
CREATE TABLE IF NOT EXISTS `type_statut_commandes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_statut_demande_achats
DROP TABLE IF EXISTS `type_statut_demande_achats`;
CREATE TABLE IF NOT EXISTS `type_statut_demande_achats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_statut_exercices
DROP TABLE IF EXISTS `type_statut_exercices`;
CREATE TABLE IF NOT EXISTS `type_statut_exercices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle_exercice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_statut_livraisons
DROP TABLE IF EXISTS `type_statut_livraisons`;
CREATE TABLE IF NOT EXISTS `type_statut_livraisons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_statut_organisations
DROP TABLE IF EXISTS `type_statut_organisations`;
CREATE TABLE IF NOT EXISTS `type_statut_organisations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_statut_organisations_libelle_unique` (`libelle`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_statut_requisitions
DROP TABLE IF EXISTS `type_statut_requisitions`;
CREATE TABLE IF NOT EXISTS `type_statut_requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. type_statut_responsable_depots
DROP TABLE IF EXISTS `type_statut_responsable_depots`;
CREATE TABLE IF NOT EXISTS `type_statut_responsable_depots` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_statut_responsable_depots_libelle_unique` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. unites
DROP TABLE IF EXISTS `unites`;
CREATE TABLE IF NOT EXISTS `unites` (
  `code_unite` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `unite` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_unite`)
) ENGINE=InnoDB AUTO_INCREMENT=1191 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `agents_id` bigint(20) unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `flag_actif` tinyint(1) DEFAULT NULL,
  `reset` tinyint(1) DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_agents_id_unique` (`agents_id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. valider_demande_achats
DROP TABLE IF EXISTS `valider_demande_achats`;
CREATE TABLE IF NOT EXISTS `valider_demande_achats` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profils_id` bigint(20) unsigned NOT NULL,
  `detail_demande_achats_id` bigint(20) unsigned NOT NULL,
  `qte_validee` int(11) NOT NULL,
  `flag_valide` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `valider_demande_achats_profils_id_index` (`profils_id`),
  KEY `valider_demande_achats_detail_demande_achats_id_index` (`detail_demande_achats_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. valider_requisitions
DROP TABLE IF EXISTS `valider_requisitions`;
CREATE TABLE IF NOT EXISTS `valider_requisitions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `demandes_id` bigint(20) unsigned NOT NULL,
  `qte_validee` int(11) NOT NULL,
  `prixu_valide` bigint(20) unsigned NOT NULL,
  `montant_valide` bigint(20) unsigned NOT NULL,
  `flag_valide` tinyint(1) DEFAULT NULL,
  `profils_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `valider_requisitions_profils_id_index` (`profils_id`),
  KEY `valider_requisitions_demandes_id_index` (`demandes_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. valider_retours
DROP TABLE IF EXISTS `valider_retours`;
CREATE TABLE IF NOT EXISTS `valider_retours` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `profils_id` bigint(20) unsigned NOT NULL,
  `retours_id` bigint(20) unsigned NOT NULL,
  `qte_validee` int(11) NOT NULL,
  `prixu_retour_valide` bigint(20) NOT NULL,
  `montant_retour_valide` bigint(20) NOT NULL,
  `flag_valide` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `valider_retours_profils_id_index` (`profils_id`),
  KEY `valider_retours_retours_id_index` (`retours_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

-- Listage de la structure de la table e-gestock. villes
DROP TABLE IF EXISTS `villes`;
CREATE TABLE IF NOT EXISTS `villes` (
  `code_ville` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nom_ville` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_pays` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`code_ville`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Les données exportées n'étaient pas sélectionnées.

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
