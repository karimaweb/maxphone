-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 03, 2025 at 09:50 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `maxphone`
--

-- --------------------------------------------------------

--
-- Table structure for table `activation_code`
--

CREATE TABLE `activation_code` (
  `id` int NOT NULL,
  `utilisateur_id` int NOT NULL,
  `code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activation_code`
--

INSERT INTO `activation_code` (`id`, `utilisateur_id`, `code`, `expires_at`) VALUES
(7, 188, '989531', '2025-02-28 14:54:30'),
(8, 189, '300673', '2025-02-28 14:55:23'),
(18, 201, '510215', '2025-02-28 20:21:12'),
(19, 209, '498057', '2025-02-28 21:02:36'),
(20, 210, '757010', '2025-03-03 09:39:28');

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

CREATE TABLE `categorie` (
  `id` int NOT NULL,
  `nom_categorie` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`id`, `nom_categorie`, `parent_id`) VALUES
(1, 'Téléphones', NULL),
(2, 'Accessoires', NULL),
(3, 'Smartphones', 1),
(4, 'Écrans', 284),
(256, 'Téléphones classiques', 1),
(257, 'Écouteurs', 2),
(258, 'Chargeurs', 2),
(259, 'Coques', 2),
(260, 'Housses', 2),
(261, 'Supports téléphones', 2),
(284, 'Pièces de réparation', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250206114530', '2025-02-06 11:45:45', 63),
('DoctrineMigrations\\Version20250206120033', '2025-02-06 12:00:41', 301),
('DoctrineMigrations\\Version20250206124850', '2025-02-06 12:49:00', 148),
('DoctrineMigrations\\Version20250206125747', '2025-02-06 12:57:58', 92),
('DoctrineMigrations\\Version20250206131133', '2025-02-06 13:11:42', 200),
('DoctrineMigrations\\Version20250206132307', '2025-02-06 13:23:17', 181),
('DoctrineMigrations\\Version20250207132411', '2025-02-07 13:24:28', 208),
('DoctrineMigrations\\Version20250207135155', '2025-02-07 13:52:04', 380),
('DoctrineMigrations\\Version20250207135653', '2025-02-07 13:57:02', 89),
('DoctrineMigrations\\Version20250207142231', '2025-02-07 14:22:38', 84),
('DoctrineMigrations\\Version20250207142845', '2025-02-07 14:28:53', 118),
('DoctrineMigrations\\Version20250207171635', '2025-02-07 17:16:40', 76),
('DoctrineMigrations\\Version20250207174905', '2025-02-07 17:49:17', 67),
('DoctrineMigrations\\Version20250213154327', '2025-02-13 15:43:56', 97),
('DoctrineMigrations\\Version20250214135441', '2025-02-14 13:55:02', 192),
('DoctrineMigrations\\Version20250214140846', '2025-02-14 14:08:51', 53),
('DoctrineMigrations\\Version20250216114022', '2025-02-16 11:40:28', 443),
('DoctrineMigrations\\Version20250218152857', '2025-02-19 09:36:11', 305),
('DoctrineMigrations\\Version20250219092934', '2025-02-19 09:36:12', 148),
('DoctrineMigrations\\Version20250219132425', '2025-02-19 13:24:41', 164),
('DoctrineMigrations\\Version20250219151241', '2025-02-19 15:12:45', 236),
('DoctrineMigrations\\Version20250220095230', '2025-02-20 09:52:43', 138),
('DoctrineMigrations\\Version20250220125512', '2025-02-20 12:55:22', 224),
('DoctrineMigrations\\Version20250227213943', '2025-02-27 21:39:52', 148);

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

CREATE TABLE `image` (
  `id` int NOT NULL,
  `nom_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `produit_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `image`
--

INSERT INTO `image` (`id`, `nom_image`, `produit_id`) VALUES
(5, 'iphone14Black.jpg', 66),
(6, 'coqueV.png', 67),
(7, 'coqueIphone.jpg', 68),
(9, 'ecouteurBleutoth_.jpg', 71),
(10, 'iphone15_blue.jpg', 71),
(11, 'samsung_s24_front.png', 72),
(12, 'samsung_s24_back.png', 72),
(13, 'google_pixel8_white.jpg', 73),
(14, 'google_pixel8_black.jpg', 73),
(15, 'oneplus_11_red.png', 74),
(16, 'chargeur_usb_c.jpg', 75),
(17, 'ecouteurs_bt.jpg', 76),
(18, 'iphone14.jpg', 77),
(22, 'coqueV.jpg', 66);

-- --------------------------------------------------------

--
-- Table structure for table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `produit`
--

CREATE TABLE `produit` (
  `id` int NOT NULL,
  `categorie_id` int DEFAULT NULL,
  `libelle_produit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prix_unitaire` double DEFAULT NULL,
  `type_produit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qte_stock` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produit`
--

INSERT INTO `produit` (`id`, `categorie_id`, `libelle_produit`, `prix_unitaire`, `type_produit`, `qte_stock`, `utilisateur_id`) VALUES
(66, 1, 'IPhone 14', NULL, 'réparation', 0, 108),
(67, 1, 'Samsung Galaxy S23', 999.99, 'vente', 15, 108),
(68, 2, 'Coque iphone', 29.99, 'vente', 50, 108),
(71, 1, 'IPhone 15', 1199.99, 'vente', 8, 108),
(72, 1, 'Samsung Galaxy S24', 1099.99, 'vente', 0, 108),
(73, 1, 'Google Pixel 8', 899.99, 'vente', 10, 108),
(74, 1, 'OnePlus 11', 749.99, 'vente', 2, 108),
(75, 2, 'Chargeur rapide USB-C', 39.99, 'vente', 40, 108),
(76, 2, 'Écouteurs Bluetooth', 59.99, 'vente', 30, 108),
(77, 2, 'Coque transparente iPhone 15', 19.99, 'vente', 1, 108),
(79, 2, 'Support téléphone voiture', 24.99, 'vente', 0, 108),
(81, 1, 'Samsung S12', 555, 'vente', 2, 108),
(82, 1, 'Samsung S15', 677, 'vente', 3, NULL),
(101, 1, 'Samsung S12', 677, 'vente', 12, NULL),
(107, 1, 'tablette', NULL, 'réparation', NULL, NULL),
(108, 1, 'HONOR X9', NULL, 'réparation', NULL, NULL),
(109, 1, 'samsung S14', NULL, 'réparation', NULL, NULL),
(110, 1, 'RADMI8', NULL, 'réparation', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int NOT NULL,
  `date_heure_rendez_vous` datetime NOT NULL,
  `statut_rendez_vous` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `utilisateur_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `date_heure_rendez_vous`, `statut_rendez_vous`, `description`, `utilisateur_id`) VALUES
(1, '2025-02-10 09:00:00', 'confirmé', 'Remplacement batterie', 109),
(2, '2025-02-11 14:00:00', 'confirmé', 'Changement écran', 110),
(3, '2025-02-13 16:52:00', 'confirmé', 'pour une réparation et un diagnostic', 109),
(4, '2025-02-18 14:00:00', 'confirmé', 'Mon téléphone ne charge plus, le connecteur semble défectueux.', 164),
(5, '2025-02-21 11:06:00', 'confirmé', 'ecran cassé', 165),
(6, '2025-02-20 12:13:00', 'en attente', 'ecran cassé', 158),
(7, '2025-02-20 13:43:00', 'confirmé', 'ecran cassé', 165),
(8, '2025-02-21 13:49:00', 'confirmé', 'problème de son', 112),
(9, '2025-02-27 10:37:00', 'confirmé', 'problème de son', 157),
(10, '2025-02-24 10:38:00', 'confirmé', 'Afficheur', 158),
(15, '2025-02-25 11:56:00', 'annulé', 'Afficheur', 165),
(16, '2025-03-08 11:58:00', 'confirmé', 'Afficheur', 171);

-- --------------------------------------------------------

--
-- Table structure for table `reparation`
--

CREATE TABLE `reparation` (
  `id` int NOT NULL,
  `diagnostic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_heure_reparation` datetime NOT NULL,
  `statut_reparation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rendez_vous_id` int DEFAULT NULL,
  `produit_id` int DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reparation`
--

INSERT INTO `reparation` (`id`, `diagnostic`, `date_heure_reparation`, `statut_reparation`, `rendez_vous_id`, `produit_id`, `utilisateur_id`) VALUES
(17, 'changement botton volume', '2025-02-20 13:53:00', 'terminé', NULL, 107, 157),
(18, 'changement botton volume', '2025-02-20 14:06:00', 'terminé', NULL, 66, 112),
(19, 'changement afficheur', '2025-02-20 14:34:00', 'terminé', NULL, 107, 163),
(20, 'probléme son', '2025-02-20 15:04:00', 'terminé', NULL, 67, 164),
(21, 'Ecran cassé', '2025-02-20 15:06:00', 'terminé', 7, 67, 165),
(22, 'changement carte mère', '2025-02-20 19:29:00', 'terminé', NULL, 74, 163),
(23, 'changement afficheur', '2025-02-23 14:49:00', 'terminé', 8, 71, 156),
(25, 'probléme son', '2025-02-24 19:42:00', 'en cours', NULL, 71, 174),
(27, 'changement connecteur', '2025-02-24 11:01:00', 'en cours', NULL, 82, 181);

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `id` int NOT NULL,
  `reparation_id` int NOT NULL,
  `objet_ticket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_ticket` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_ticket` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_creation_ticket` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_maj_ticket` datetime DEFAULT NULL,
  `utilisateur_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ticket`
--

INSERT INTO `ticket` (`id`, `reparation_id`, `objet_ticket`, `description_ticket`, `statut_ticket`, `date_creation_ticket`, `date_maj_ticket`, `utilisateur_id`) VALUES
(7, 17, 'Réparation sans RDV', 'Réparation ajoutée en magasin.', 'Résolu', '2025-02-20 12:55:36', NULL, 157),
(8, 18, 'Réparation sans RDV', 'Réparation ajoutée en magasin.', 'Résolu', '2025-02-20 13:06:38', NULL, 112),
(9, 19, 'Réparation sans RDV', 'Réparation ajoutée en magasin.', 'En cours', '2025-02-20 13:35:51', NULL, 163),
(10, 20, 'Réparation sans RDV', 'Réparation ajoutée en magasin.', 'Résolu', '2025-02-20 14:04:32', NULL, 164),
(11, 22, 'Réparation sans RDV', 'Réparation ajoutée en magasin.', 'Résolu', '2025-02-20 18:29:41', NULL, 163),
(13, 25, 'Réparation sans RDV', 'Réparation ajoutée en magasin.', 'En cours', '2025-02-24 18:42:26', NULL, 174),
(15, 27, 'Réparation sans RDV', 'Réparation ajoutée en magasin.', 'En cours', '2025-02-25 10:02:10', NULL, 181);

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom_utilisateur` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom_utilisateur` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_telephone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `email`, `roles`, `password`, `nom_utilisateur`, `prenom_utilisateur`, `adresse`, `num_telephone`) VALUES
(108, 'jean.dupont@hotmail.com', '[\"ROLE_ADMIN\"]', '$2y$13$Bos6iAOWRVQzaK79fo17Fu2ZnM5DZT.iwptJNMQHzu0/5I.AIhfzG', 'Dupont', 'Jean', '10 Rue des Lilas, Paris', '0102030405'),
(109, 'marie.durand@hotmail.fr', '[\"ROLE_ADMIN\"]', '$2y$13$phN4mkYyJihnC2/56PwgUuYiNwPCGh30om1zdDsji5eagcwGu0puK', 'Durand', 'Marie', '15 Avenue des Champs, Lyon', '0607080910'),
(110, 'paul.martin@gmail.com', '[\"ROLE_USER\"]', '$2y$13$lHAI.Qzwu6HlVWsY7AUDx.tr12wiwg/Bl1/hPoUkdB8IsJImtSWx6', 'Martin', 'Paul', '5 Impasse des Roses, Marseille', '0708091011'),
(111, 'client3@yahoo.fr', '[\"ROLE_USER\"]', 'motdepassehashé', 'Martin', 'Paul', '5 Impasse des Roses, Marseille', '0708091011'),
(112, 'client4@example.com', '[\"ROLE_USER\"]', 'motdepassehashé', 'Bernard', 'Sophie', '20 Boulevard Haussmann, Paris', '0809101112'),
(156, 'client4@hotmail.com', '[\"ROLE_USER\"]', 'motdepassehashé', 'Bernard', 'Sophie', '20 Boulevard Haussmann, Paris', '0809101112'),
(157, 'ouchenekarima2008@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$vls4kdWXE8qH75iVS1.PvuoSr/2NM8HZXAC/fIY3YbkzelNxQrUeC', 'Douane', 'Karima', '24', '0641885380'),
(158, 'djugo@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$Gnu3zcDcN2IJAfumloOT6Os3g4HAXZmg06nV5jaTKL/YCEs9t/a6C', 'Douane', 'djugo', '24', '0641885322'),
(163, 'jojo@gmail.fr', '[\"ROLE_USER\"]', '$2y$13$5An9xdElztlyCBrNaB72c.7s76LD1TFGsNXB4YF8oTHYOAwwAZQ3e', 'F', 'F', '63 Rue De La Forêt Konacker', '0641885333'),
(164, 'ouchene2008@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$QWt9UsYFGl9IPtcCAe83/.2A7Q3D4uJj6ayzSZLHhV6eihcgig/eu', 'karima', 'joujou', '24 rue des grands bois', '0641885323'),
(165, 'soso@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$OlzkYQApDo/0g/vOZ1/zQOBLPdr/Gr06pT9ZyAwM3DKbI08UcnZZm', 'ouchene', 'soso', '24', '0641885380'),
(171, 'linaloal@gmail.com', '[]', '$2y$13$8/sraMMs0tgahWY/251gbO7hrbWR5ouZH305LJ7tKHnX/1SP0INj2', 'daci', 'lina', '24', '0641885380'),
(174, 'bila@hotmail.fr', '[]', '$2y$13$9t4AESrZcLuPSoARrEV0s.dovuZ2iEFbHLkI6fj6B.YrQqSliyJ.K', 'bila', 'bola', '68 Rue de mine', '0641885380'),
(179, 'moda@gmail.com', '[]', '$2y$13$EEnyN0qoJUkJ7gYN0sykGuOJdKBZejAYPDvniqoGzpoAcrYFQKT6C', 'MODA', 'midi', '77 rue grand espace', '074188538'),
(180, 'massikarima2008@hotmail.fr', '[]', '$2y$13$Kh2cEGJzfBtXyYNsfiJ6Nuz5VYPCxMa.S31xYE8adIMqtNx7DChVO', 'massi', 'moussa', '24 rue de la marne', '0641885380'),
(181, 'ouchenemoussa@hotmail.fr', '[]', '$2y$13$O25PvuDQlqzI0xjPKJKRxezAujzJiPG51uleFIHgo7Ro3jtFhsihW', 'massi', 'moussa', '63 Rue De La Forêt Konacker', '0641885380'),
(188, 'sesspoir@gmail.com', '[\"ROLE_USER\"]', '$2y$13$rdEjSDxWABuJBKQCSPlkQ.F8oiwR1iUVu/abCJuaT0kNoXN91Qe9W', 'Douane', 'Karima', '24 hhhhhhhhhh', '0641885380'),
(189, 'karima2008@hotmail.com', '[\"ROLE_USER\"]', '$2y$13$WjtLyipohhJtJ2gnQyeW5.djOH7/mEeqJPwp3rVqhl5aVFxjKRguq', 'Douane', 'Karima', '24 bbbbbbbbbbb', '0641885380'),
(201, 'damienbin@gmail.com', '[\"ROLE_USER\"]', '$2y$13$3NhyqybcT8tY9wHxs2fgcOXX/Je49SY68Uhl24nt/9m7Gxgxk/GVm', 'bin', 'damien', '63 Rue De La Forêt Konacker', '0641885380'),
(202, 'madina2008@hotmail.fr', '[]', '$2y$13$wRD6BBhyc4Jf3WLusJjq9.3BqL3tka6KoPg2OhM1TKgqdjFhQLbs2', 'Douane', 'madina', '24 ggggggggg', '0641885380'),
(203, 'MMMMMM2008@hotmail.fr', '[]', '$2y$13$cBdPyWx2ncoiMITxJsmQKOFlmG7Z7Wyl7yV2LMmXaC9lCP1EkzDEm', 'Douane', 'Karima', '24MMMMM', '0641885380'),
(204, 'zako@hotmail.fr', '[]', '$2y$13$LWC3WhcoxY.qT5a2tkmn0uzZqrPYRTIyn4WgznOphXnepxjBT/hWi', 'zakaria', 'zako', '24 ggg', '0641885380'),
(205, 'zako2008@hotmail.fr', '[]', '$2y$13$vlu47XuHjorCcjGARnah5uBPkBosQpeNXNChC35SovGupdDtSJqOG', 'Douane', 'zakaria', '24hhhhhhhh', '0641885380'),
(206, 'mama2008@hotmail.fr', '[]', '$2y$13$xqUpRwqlFsjWjXrfsl8QYusztcg3jS.3A3zQ343gFMTv1kfFc.7mm', 'Douane', 'Karima', '24 fff', '0641885380'),
(207, 'sespoir@hotmail.fr', '[]', '$2y$13$s.Jv4iguqpxh2sjnquzWg.YD8Ew3pSOw/m6BzZceEcdjwS01jIL1a', 'Douane', 'Karima', '24 gg', '0641885380'),
(208, 'damien@gmail.com', '[]', '$2y$13$EidpBU.wVJ5SsU4luSfG2e.VKEnN.ppNmpCV57mqcFQRbLDnDpIKy', 'Douane', 'Karima', '24 fff', '0641885380'),
(209, 'aminamina@gmail.com', '[]', '$2y$13$v41lQfgHNw/ILbqYI/QqVus2CD0h/pSQG3Z4zlOycIhz2iCRF2SmW', 'amina', 'mina', '24   ggg', '0641885380'),
(210, 'david2008@hotmail.fr', '[]', '$2y$13$3Xg9.XD5qb6SFVxMMmeX7uA/zsBKMJba2gBhCKHFt4tC9Fc/1cdGS', 'david', 'dodod', '24 rerer', '0641885380');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activation_code`
--
ALTER TABLE `activation_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_FA574C9AFB88E14F` (`utilisateur_id`);

--
-- Indexes for table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_497DD634727ACA70` (`parent_id`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `image`
--
ALTER TABLE `image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C53D045FF347EFB` (`produit_id`);

--
-- Indexes for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Indexes for table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_29A5EC27BCF5E72D` (`categorie_id`),
  ADD KEY `IDX_29A5EC27FB88E14F` (`utilisateur_id`);

--
-- Indexes for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_65E8AA0AFB88E14F` (`utilisateur_id`);

--
-- Indexes for table `reparation`
--
ALTER TABLE `reparation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8FDF219D91EF7EAA` (`rendez_vous_id`),
  ADD KEY `IDX_8FDF219DF347EFB` (`produit_id`),
  ADD KEY `IDX_8FDF219DFB88E14F` (`utilisateur_id`);

--
-- Indexes for table `ticket`
--
ALTER TABLE `ticket`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_97A0ADA397934BA` (`reparation_id`),
  ADD KEY `IDX_97A0ADA3FB88E14F` (`utilisateur_id`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activation_code`
--
ALTER TABLE `activation_code`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `reparation`
--
ALTER TABLE `reparation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activation_code`
--
ALTER TABLE `activation_code`
  ADD CONSTRAINT `FK_FA574C9AFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categorie`
--
ALTER TABLE `categorie`
  ADD CONSTRAINT `FK_497DD634727ACA70` FOREIGN KEY (`parent_id`) REFERENCES `categorie` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `image`
--
ALTER TABLE `image`
  ADD CONSTRAINT `FK_C53D045FF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`);

--
-- Constraints for table `produit`
--
ALTER TABLE `produit`
  ADD CONSTRAINT `FK_29A5EC27BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_29A5EC27FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `FK_65E8AA0AFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `reparation`
--
ALTER TABLE `reparation`
  ADD CONSTRAINT `FK_8FDF219D91EF7EAA` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `FK_8FDF219DF347EFB` FOREIGN KEY (`produit_id`) REFERENCES `produit` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_8FDF219DFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA397934BA` FOREIGN KEY (`reparation_id`) REFERENCES `reparation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_97A0ADA3FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
