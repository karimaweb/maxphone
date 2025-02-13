-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 13, 2025 at 06:52 AM
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
(4, 'Écrans', 1),
(5, 'Batteries', 1),
(6, 'Coques', 2),
(7, 'Chargeurs', 2),
(8, 'Écouteurs', 2);

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
('DoctrineMigrations\\Version20250207174905', '2025-02-07 17:49:17', 67);

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
(8, 'galaxy_s23_back.jpg', 69),
(9, 'iphone15_gold.jpg', 71),
(10, 'iphone15_blue.jpg', 71),
(11, 'samsung_s24_front.png', 72),
(12, 'samsung_s24_back.png', 72),
(13, 'google_pixel8_white.jpg', 73),
(14, 'google_pixel8_black.jpg', 73),
(15, 'oneplus_11_red.png', 74),
(16, 'chargeur_usb_c.jpg', 75),
(17, 'ecouteurs_bt.jpg', 76),
(18, 'coque_iphone15.png', 77),
(19, 'verre_trempe_s24.jpg', 78),
(20, 'support_voiture.jpg', 79);

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
  `prix_unitaire` double NOT NULL,
  `type_produit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qte_stock` int NOT NULL,
  `utilisateur_id` int DEFAULT NULL,
  `attribuer_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produit`
--

INSERT INTO `produit` (`id`, `categorie_id`, `libelle_produit`, `prix_unitaire`, `type_produit`, `qte_stock`, `utilisateur_id`, `attribuer_id`) VALUES
(66, 1, 'iPhone 14', 1099.99, 'vente', 10, 108, NULL),
(67, 1, 'Samsung Galaxy S23', 999.99, 'vente', 15, 108, NULL),
(68, 2, 'Coque iphone', 29.99, 'vente', 50, 109, NULL),
(69, 3, 'Écran LCD iPhone', 89.99, 'réparation', 5, 110, 1),
(70, 3, 'Batterie Samsung', 49.99, 'réparation', 20, 110, 2),
(71, 1, 'iPhone 15', 1199.99, 'vente', 8, 108, NULL),
(72, 1, 'Samsung Galaxy S24', 1099.99, 'vente', 12, 109, NULL),
(73, 1, 'Google Pixel 8', 899.99, 'vente', 10, 110, NULL),
(74, 1, 'OnePlus 11', 749.99, 'vente', 15, 109, NULL),
(75, 2, 'Chargeur rapide USB-C', 39.99, 'vente', 40, 108, NULL),
(76, 2, 'Écouteurs Bluetooth', 59.99, 'vente', 30, 110, NULL),
(77, 2, 'Coque transparente iPhone 15', 19.99, 'vente', 50, 109, NULL),
(78, 2, 'Verre trempé Samsung S24', 14.99, 'vente', 60, 110, NULL),
(79, 2, 'Support téléphone voiture', 24.99, 'vente', 25, 108, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int NOT NULL,
  `date_heure_rendez_vous` datetime NOT NULL,
  `statut_rendez_vous` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `utilisateur_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `date_heure_rendez_vous`, `statut_rendez_vous`, `description`, `utilisateur_id`) VALUES
(1, '2025-02-10 09:00:00', 'confirmé', 'Remplacement batterie', 109),
(2, '2025-02-11 14:00:00', 'en attente', 'Changement écran', 110);

-- --------------------------------------------------------

--
-- Table structure for table `reparation`
--

CREATE TABLE `reparation` (
  `id` int NOT NULL,
  `diagnostic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_heure_reparation` datetime NOT NULL,
  `statut_reparation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rendez_vous_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reparation`
--

INSERT INTO `reparation` (`id`, `diagnostic`, `date_heure_reparation`, `statut_reparation`, `rendez_vous_id`) VALUES
(1, 'Changement écran cassé', '2025-02-01 10:00:00', 'terminé', 1),
(2, 'Remplacement batterie', '2025-02-02 14:30:00', 'en cours', 2),
(3, 'Réparation connecteur de charge', '2025-02-03 09:00:00', 'en attente', 2);

-- --------------------------------------------------------

--
-- Table structure for table `ticket`
--

CREATE TABLE `ticket` (
  `id` int NOT NULL,
  `reparation_id` int DEFAULT NULL,
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
(1, 1, 'Demande de réparation pour écran', 'L\'écran est fissuré après une chute.', 'en cours', '2025-02-10 09:00:00', NULL, 108),
(2, 2, 'Problème de batterie', 'La batterie se décharge rapidement.', 'en attente', '2025-02-11 10:00:00', NULL, 109),
(3, 3, 'Problème de charge', 'Le téléphone ne charge plus.', 'terminé', '2025-02-12 11:00:00', NULL, 110);

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
(108, 'jean.dupont@example.com', '[\"ROLE_ADMIN\"]', '$2y$13$Bos6iAOWRVQzaK79fo17Fu2ZnM5DZT.iwptJNMQHzu0/5I.AIhfzG', 'Dupont', 'Jean', '10 Rue des Lilas, Paris', '0102030405'),
(109, 'marie.durand@example.com', '[\"ROLE_USER\"]', '$2y$13$phN4mkYyJihnC2/56PwgUuYiNwPCGh30om1zdDsji5eagcwGu0puK', 'Durand', 'Marie', '15 Avenue des Champs, Lyon', '0607080910'),
(110, 'paul.martin@example.com', '[\"ROLE_USER\"]', '$2y$13$lHAI.Qzwu6HlVWsY7AUDx.tr12wiwg/Bl1/hPoUkdB8IsJImtSWx6', 'Martin', 'Paul', '5 Impasse des Roses, Marseille', '0708091011');

--
-- Indexes for dumped tables
--

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
  ADD KEY `IDX_29A5EC27FB88E14F` (`utilisateur_id`),
  ADD KEY `IDX_29A5EC2726A8FFEE` (`attribuer_id`);

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
  ADD KEY `IDX_8FDF219D91EF7EAA` (`rendez_vous_id`);

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
-- AUTO_INCREMENT for table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=247;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reparation`
--
ALTER TABLE `reparation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- Constraints for dumped tables
--

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
  ADD CONSTRAINT `FK_29A5EC2726A8FFEE` FOREIGN KEY (`attribuer_id`) REFERENCES `reparation` (`id`),
  ADD CONSTRAINT `FK_29A5EC27BCF5E72D` FOREIGN KEY (`categorie_id`) REFERENCES `categorie` (`id`),
  ADD CONSTRAINT `FK_29A5EC27FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `FK_65E8AA0AFB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);

--
-- Constraints for table `reparation`
--
ALTER TABLE `reparation`
  ADD CONSTRAINT `FK_8FDF219D91EF7EAA` FOREIGN KEY (`rendez_vous_id`) REFERENCES `rendez_vous` (`id`);

--
-- Constraints for table `ticket`
--
ALTER TABLE `ticket`
  ADD CONSTRAINT `FK_97A0ADA397934BA` FOREIGN KEY (`reparation_id`) REFERENCES `reparation` (`id`),
  ADD CONSTRAINT `FK_97A0ADA3FB88E14F` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
