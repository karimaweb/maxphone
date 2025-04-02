-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 26, 2025 at 10:54 AM
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
(33, 228, '215065', '2025-03-12 19:38:31'),
(34, 229, '947746', '2025-03-19 16:36:32');

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
('DoctrineMigrations\\Version20250227213943', '2025-02-27 21:39:52', 148),
('DoctrineMigrations\\Version20250307131959', '2025-03-07 13:20:15', 128),
('DoctrineMigrations\\Version20250307150829', '2025-03-07 15:08:37', 115),
('DoctrineMigrations\\Version20250314083011', '2025-03-14 08:30:52', 117),
('DoctrineMigrations\\Version20250314114642', '2025-03-14 11:46:48', 36),
('DoctrineMigrations\\Version20250318091710', '2025-03-18 09:17:30', 65),
('DoctrineMigrations\\Version20250318145054', '2025-03-18 14:51:09', 87),
('DoctrineMigrations\\Version20250318145348', '2025-03-18 14:53:54', 109);

-- --------------------------------------------------------

--
-- Table structure for table `historique_reparation`
--

CREATE TABLE `historique_reparation` (
  `id` int NOT NULL,
  `reparation_id` int NOT NULL,
  `statut_historique_reparation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `commentaire` longtext COLLATE utf8mb4_unicode_ci,
  `date_maj_reparation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `historique_reparation`
--

INSERT INTO `historique_reparation` (`id`, `reparation_id`, `statut_historique_reparation`, `commentaire`, `date_maj_reparation`) VALUES
(171, 56, 'Test final en cours → Terminé', ' Mise à jour du statut : \"Test final en cours\" → \"Terminé\"', '2025-03-12 07:40:31'),
(172, 56, 'Terminé → En attente', ' Mise à jour du statut : \"Terminé\" → \"En attente\"', '2025-03-12 07:40:37'),
(173, 56, 'En attente → Pièce commandée', ' Mise à jour du statut : \"En attente\" → \"Pièce commandée\"', '2025-03-12 07:40:54'),
(174, 56, 'Pièce commandée → Pièce reçue', ' Mise à jour du statut : \"Pièce commandée\" → \"Pièce reçue\"', '2025-03-12 08:10:04'),
(175, 56, 'Pièce reçue → Début de réparation', ' Mise à jour du statut : \"Pièce reçue\" → \"Début de réparation\"', '2025-03-12 08:10:10'),
(176, 56, 'Début de réparation → Test final en cours', ' Mise à jour du statut : \"Début de réparation\" → \"Test final en cours\"', '2025-03-12 08:10:15'),
(188, 56, 'Test final en cours → Diagnostic en cours', 'Mise à jour du statut : \"Test final en cours\" → \"Diagnostic en cours\"', '2025-03-19 13:43:48'),
(199, 58, 'Terminé → En attente', 'Mise à jour du statut : \"Terminé\" → \"En attente\"', '2025-03-20 14:23:14'),
(217, 73, 'En attente', NULL, '2025-03-25 10:30:24'),
(218, 73, 'En attente → Diagnostic en cours', 'Mise à jour du statut : \"En attente\" → \"Diagnostic en cours\"', '2025-03-25 10:35:24'),
(219, 73, 'Diagnostic en cours → Pièce commandée', 'Mise à jour du statut : \"Diagnostic en cours\" → \"Pièce commandée\"', '2025-03-25 10:35:32'),
(220, 73, 'Pièce commandée → Pièce reçue', 'Mise à jour du statut : \"Pièce commandée\" → \"Pièce reçue\"', '2025-03-25 10:35:39'),
(221, 73, 'Pièce reçue → Terminé', 'Mise à jour du statut : \"Pièce reçue\" → \"Terminé\"', '2025-03-25 10:35:49'),
(223, 75, 'En attente', NULL, '2025-03-25 16:25:48'),
(224, 56, 'Diagnostic en cours → Pièce commandée', 'Mise à jour du statut : \"Diagnostic en cours\" → \"Pièce commandée\"', '2025-03-25 17:06:08'),
(225, 56, 'Pièce commandée → Test final en cours', 'Mise à jour du statut : \"Pièce commandée\" → \"Test final en cours\"', '2025-03-25 17:06:16'),
(226, 56, 'Test final en cours → Terminé', 'Mise à jour du statut : \"Test final en cours\" → \"Terminé\"', '2025-03-25 17:06:25'),
(227, 58, 'En attente → Diagnostic en cours', 'Mise à jour du statut : \"En attente\" → \"Diagnostic en cours\"', '2025-03-25 17:15:04');

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
(10, 'Samsung galaxy s23.jpg', 67),
(11, 'Amasung Galaxy A05s_.jpg', 72),
(12, 'Ecouteur Bluetooth AC_SX679_.jpg', 72),
(13, 'Gamme Ecouteur filaire_.jpg', 73),
(14, 'Coque iphone.jpg', 73),
(15, 'galaxyA16_.jpg', 74),
(16, 'Cable de chargeur_.jpg', 75),
(17, 'Cahrgeur usb.jpg', 76),
(18, 'Coque Iphone13.jpg', 77),
(22, 'coques.jpg', 66),
(26, 'Xiami Redmi 14C jpg.jpg', 66);

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

--
-- Dumping data for table `messenger_messages`
--

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
(67, 1, 'Samsung Galaxy S23', 999.99, 'vente', 4, 108),
(68, 2, 'Coque iphone', 29.99, 'vente', 50, 108),
(71, 1, 'IPhone 15', 1199.99, 'vente', 8, 108),
(72, 1, 'Samsung Galaxy S24', 1099.99, 'vente', 0, 108),
(73, 1, 'Google Pixel 8', 899.99, 'vente', 10, 108),
(74, 1, 'OnePlus 11', 749.99, 'vente', 2, 108),
(75, 2, 'Chargeur rapide USB-C', 39.99, 'vente', 40, 108),
(76, 2, 'Écouteurs Bluetooth', 59.99, 'vente', 30, 108),
(77, 2, 'Coque transparente iPhone 15', 19.99, 'vente', 1, 108),
(112, 1, 'iphone11', NULL, 'réparation', NULL, NULL),
(113, 1, 'HONOR X11', NULL, 'réparation', NULL, NULL),
(114, 1, 'iphone10', NULL, 'réparation', NULL, NULL),
(116, 1, 'IPhone 6', NULL, 'réparation', NULL, NULL),
(127, 1, 'samsung AMEL', NULL, 'réparation', NULL, NULL),
(128, 1, 'Iphone16', NULL, 'réparation', NULL, NULL),
(131, 1, 'Iphone6', NULL, 'réparation', NULL, NULL),
(132, 1, 'samsung S1244', NULL, 'réparation', NULL, NULL),
(136, 1, 'samsung S12111', NULL, 'réparation', NULL, NULL),
(137, 1, 'IPhone 14 NOIR', NULL, 'réparation', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id` int NOT NULL,
  `date_heure_rendez_vous` datetime NOT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut_rendez_vous` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disponible',
  `utilisateur_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id`, `date_heure_rendez_vous`, `description`, `statut_rendez_vous`, `utilisateur_id`) VALUES
(378, '2025-03-21 18:00:00', 'Créneau libre', 'disponible', NULL),
(382, '2025-03-26 15:00:00', 'Créneau libre', 'disponible', 109),
(383, '2025-03-26 15:20:00', 'Créneau libre', 'disponible', 229),
(384, '2025-03-26 15:40:00', 'Créneau libre', 'disponible', NULL),
(385, '2025-03-26 16:00:00', 'Créneau libre', 'disponible', NULL),
(386, '2025-03-26 16:20:00', 'Créneau libre', 'disponible', NULL),
(387, '2025-03-26 16:40:00', 'Créneau libre', 'disponible', 108),
(388, '2025-03-26 17:00:00', 'Créneau libre', 'disponible', NULL),
(389, '2025-03-26 17:20:00', 'Créneau libre', 'disponible', NULL),
(390, '2025-03-26 17:40:00', 'Créneau libre', 'disponible', NULL),
(391, '2025-03-26 18:00:00', 'Créneau libre', 'disponible', NULL),
(392, '2025-03-28 14:00:00', 'Créneau libre', 'disponible', 108),
(393, '2025-03-28 14:20:00', 'Créneau libre', 'disponible', NULL),
(394, '2025-03-28 14:40:00', 'Créneau libre', 'disponible', NULL),
(395, '2025-03-28 15:00:00', 'Créneau libre', 'disponible', NULL),
(396, '2025-03-28 15:20:00', 'Créneau libre', 'disponible', NULL),
(397, '2025-03-28 15:40:00', 'Créneau libre', 'disponible', NULL),
(398, '2025-03-28 16:00:00', 'Créneau libre', 'réservé', 229),
(399, '2025-03-28 16:20:00', 'Créneau libre', 'disponible', NULL),
(400, '2025-03-28 16:40:00', 'Créneau libre', 'disponible', NULL),
(401, '2025-03-28 17:00:00', 'Créneau libre', 'disponible', NULL),
(402, '2025-03-28 17:20:00', 'Créneau libre', 'disponible', NULL),
(403, '2025-03-28 17:40:00', 'Créneau libre', 'disponible', NULL),
(404, '2025-03-28 18:00:00', 'Créneau libre', 'disponible', NULL),
(405, '2025-04-02 14:00:00', 'Créneau libre', 'disponible', 108),
(406, '2025-04-02 14:20:00', 'Créneau libre', 'disponible', NULL),
(407, '2025-04-02 14:40:00', 'Créneau libre', 'disponible', NULL),
(408, '2025-04-02 15:00:00', 'Créneau libre', 'disponible', 108),
(409, '2025-04-02 15:20:00', 'Créneau libre', 'disponible', NULL),
(410, '2025-04-02 15:40:00', 'Créneau libre', 'disponible', NULL),
(411, '2025-04-02 16:00:00', 'Créneau libre', 'disponible', NULL),
(412, '2025-04-02 16:20:00', 'Créneau libre', 'disponible', NULL),
(413, '2025-04-02 16:40:00', 'Créneau libre', 'disponible', NULL),
(414, '2025-04-02 17:00:00', 'Créneau libre', 'disponible', NULL),
(415, '2025-04-02 17:20:00', 'Créneau libre', 'disponible', NULL),
(416, '2025-04-02 17:40:00', 'Créneau libre', 'disponible', NULL),
(417, '2025-04-02 18:00:00', 'Créneau libre', 'disponible', NULL),
(418, '2025-04-04 14:00:00', 'Créneau libre', 'réservé', 108),
(419, '2025-04-04 14:20:00', 'Créneau libre', 'disponible', NULL),
(420, '2025-04-04 14:40:00', 'Créneau libre', 'disponible', NULL),
(421, '2025-04-04 15:00:00', 'Créneau libre', 'disponible', NULL),
(422, '2025-04-04 15:20:00', 'Créneau libre', 'disponible', NULL),
(423, '2025-04-04 15:40:00', 'Créneau libre', 'disponible', NULL),
(424, '2025-04-04 16:00:00', 'Créneau libre', 'disponible', NULL),
(425, '2025-04-04 16:20:00', 'Créneau libre', 'disponible', NULL),
(426, '2025-04-04 16:40:00', 'Créneau libre', 'disponible', NULL),
(427, '2025-04-04 17:00:00', 'Créneau libre', 'disponible', NULL),
(428, '2025-04-04 17:20:00', 'Créneau libre', 'disponible', NULL),
(429, '2025-04-04 17:40:00', 'Créneau libre', 'disponible', NULL),
(430, '2025-04-04 18:00:00', 'Créneau libre', 'disponible', NULL),
(431, '2025-04-09 14:00:00', 'Créneau libre', 'disponible', NULL),
(432, '2025-04-09 14:20:00', 'Créneau libre', 'disponible', NULL),
(433, '2025-04-09 14:40:00', 'Créneau libre', 'disponible', NULL),
(434, '2025-04-09 15:00:00', 'Créneau libre', 'disponible', NULL),
(435, '2025-04-09 15:20:00', 'Créneau libre', 'disponible', NULL),
(436, '2025-04-09 15:40:00', 'Créneau libre', 'disponible', NULL),
(437, '2025-04-09 16:00:00', 'Créneau libre', 'disponible', NULL),
(438, '2025-04-09 16:20:00', 'Créneau libre', 'disponible', NULL),
(439, '2025-04-09 16:40:00', 'Créneau libre', 'disponible', NULL),
(440, '2025-04-09 17:00:00', 'Créneau libre', 'disponible', NULL),
(441, '2025-04-09 17:20:00', 'Créneau libre', 'disponible', NULL),
(442, '2025-04-09 17:40:00', 'Créneau libre', 'disponible', NULL),
(443, '2025-04-09 18:00:00', 'Créneau libre', 'disponible', NULL),
(444, '2025-04-11 14:00:00', 'Créneau libre', 'disponible', NULL),
(445, '2025-04-11 14:20:00', 'Créneau libre', 'disponible', NULL),
(446, '2025-04-11 14:40:00', 'Créneau libre', 'disponible', NULL),
(447, '2025-04-11 15:00:00', 'Créneau libre', 'disponible', NULL),
(448, '2025-04-11 15:20:00', 'Créneau libre', 'disponible', NULL),
(449, '2025-04-11 15:40:00', 'Créneau libre', 'disponible', NULL),
(450, '2025-04-11 16:00:00', 'Créneau libre', 'disponible', NULL),
(451, '2025-04-11 16:20:00', 'Créneau libre', 'disponible', NULL),
(452, '2025-04-11 16:40:00', 'Créneau libre', 'disponible', NULL),
(453, '2025-04-11 17:00:00', 'Créneau libre', 'disponible', NULL),
(454, '2025-04-11 17:20:00', 'Créneau libre', 'disponible', NULL),
(455, '2025-04-11 17:40:00', 'Créneau libre', 'disponible', NULL),
(456, '2025-04-11 18:00:00', 'Créneau libre', 'disponible', NULL),
(457, '2025-04-16 14:00:00', 'Créneau libre', 'disponible', NULL),
(458, '2025-04-16 14:20:00', 'Créneau libre', 'disponible', NULL),
(459, '2025-04-16 14:40:00', 'Créneau libre', 'disponible', NULL),
(460, '2025-04-16 15:00:00', 'Créneau libre', 'disponible', NULL),
(461, '2025-04-16 15:20:00', 'Créneau libre', 'disponible', NULL),
(462, '2025-04-16 15:40:00', 'Créneau libre', 'disponible', NULL),
(463, '2025-04-16 16:00:00', 'Créneau libre', 'disponible', NULL),
(464, '2025-04-16 16:20:00', 'Créneau libre', 'disponible', NULL),
(465, '2025-04-16 16:40:00', 'Créneau libre', 'disponible', NULL),
(466, '2025-04-16 17:00:00', 'Créneau libre', 'disponible', NULL),
(467, '2025-04-16 17:20:00', 'Créneau libre', 'disponible', NULL),
(468, '2025-04-16 17:40:00', 'Créneau libre', 'disponible', NULL),
(469, '2025-04-16 18:00:00', 'Créneau libre', 'disponible', NULL),
(470, '2025-04-18 14:00:00', 'Créneau libre', 'disponible', NULL),
(471, '2025-04-18 14:20:00', 'Créneau libre', 'disponible', NULL),
(472, '2025-04-18 14:40:00', 'Créneau libre', 'disponible', NULL),
(473, '2025-04-18 15:00:00', 'Créneau libre', 'disponible', NULL),
(474, '2025-04-18 15:20:00', 'Créneau libre', 'disponible', NULL),
(475, '2025-04-18 15:40:00', 'Créneau libre', 'disponible', NULL),
(476, '2025-04-18 16:00:00', 'Créneau libre', 'disponible', NULL),
(477, '2025-04-18 16:20:00', 'Créneau libre', 'disponible', NULL),
(478, '2025-04-18 16:40:00', 'Créneau libre', 'disponible', NULL),
(479, '2025-04-18 17:00:00', 'Créneau libre', 'disponible', NULL),
(480, '2025-04-18 17:20:00', 'Créneau libre', 'disponible', NULL),
(481, '2025-04-18 17:40:00', 'Créneau libre', 'disponible', NULL),
(482, '2025-04-18 18:00:00', 'Créneau libre', 'disponible', NULL),
(483, '2025-04-23 14:00:00', 'Créneau libre', 'disponible', NULL),
(484, '2025-04-23 14:20:00', 'Créneau libre', 'disponible', NULL),
(485, '2025-04-23 14:40:00', 'Créneau libre', 'disponible', NULL),
(486, '2025-04-23 15:00:00', 'Créneau libre', 'disponible', NULL),
(487, '2025-04-23 15:20:00', 'Créneau libre', 'disponible', NULL),
(488, '2025-04-23 15:40:00', 'Créneau libre', 'disponible', NULL),
(489, '2025-04-23 16:00:00', 'Créneau libre', 'disponible', NULL),
(490, '2025-04-23 16:20:00', 'Créneau libre', 'disponible', NULL),
(491, '2025-04-23 16:40:00', 'Créneau libre', 'disponible', NULL),
(492, '2025-04-23 17:00:00', 'Créneau libre', 'disponible', NULL),
(493, '2025-04-23 17:20:00', 'Créneau libre', 'disponible', NULL),
(494, '2025-04-23 17:40:00', 'Créneau libre', 'disponible', NULL),
(495, '2025-04-23 18:00:00', 'Créneau libre', 'disponible', NULL),
(496, '2025-04-25 14:00:00', 'Créneau libre', 'disponible', NULL),
(497, '2025-04-25 14:20:00', 'Créneau libre', 'disponible', NULL),
(498, '2025-04-25 14:40:00', 'Créneau libre', 'disponible', NULL),
(499, '2025-04-25 15:00:00', 'Créneau libre', 'disponible', NULL),
(500, '2025-04-25 15:20:00', 'Créneau libre', 'disponible', NULL),
(501, '2025-04-25 15:40:00', 'Créneau libre', 'disponible', NULL),
(502, '2025-04-25 16:00:00', 'Créneau libre', 'disponible', NULL),
(503, '2025-04-25 16:20:00', 'Créneau libre', 'disponible', NULL),
(504, '2025-04-25 16:40:00', 'Créneau libre', 'disponible', NULL),
(505, '2025-04-25 17:00:00', 'Créneau libre', 'disponible', NULL),
(506, '2025-04-25 17:20:00', 'Créneau libre', 'disponible', NULL),
(507, '2025-04-25 17:40:00', 'Créneau libre', 'disponible', NULL),
(508, '2025-04-25 18:00:00', 'Créneau libre', 'disponible', NULL),
(509, '2025-04-30 14:00:00', 'Créneau libre', 'disponible', NULL),
(510, '2025-04-30 14:20:00', 'Créneau libre', 'disponible', NULL),
(511, '2025-04-30 14:40:00', 'Créneau libre', 'disponible', NULL),
(512, '2025-04-30 15:00:00', 'Créneau libre', 'disponible', NULL),
(513, '2025-04-30 15:20:00', 'Créneau libre', 'disponible', NULL),
(514, '2025-04-30 15:40:00', 'Créneau libre', 'disponible', NULL),
(515, '2025-04-30 16:00:00', 'Créneau libre', 'disponible', NULL),
(516, '2025-04-30 16:20:00', 'Créneau libre', 'disponible', NULL),
(517, '2025-04-30 16:40:00', 'Créneau libre', 'disponible', NULL),
(518, '2025-04-30 17:00:00', 'Créneau libre', 'disponible', NULL),
(519, '2025-04-30 17:20:00', 'Créneau libre', 'disponible', NULL),
(520, '2025-04-30 17:40:00', 'Créneau libre', 'disponible', NULL),
(521, '2025-04-30 18:00:00', 'Créneau libre', 'disponible', NULL);

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
(56, 'changement afficheur', '2025-02-25 15:42:00', 'terminé', NULL, 114, 110),
(58, 'changement afficheur', '2025-03-11 16:07:00', 'diagnostic en cours', NULL, 66, 108),
(73, 'probléme son', '2025-03-25 11:29:00', 'terminé', NULL, 112, 229),
(75, 'Problème  de batterie', '2025-03-25 17:25:00', 'en attente', NULL, 128, 165);

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
(70, 73, 'Réparation avec rdv', 'Réparation ajoutée en magasin.', 'Résolu', '2025-03-25 10:30:24', NULL, 229),
(73, 75, 'Réparation avec rdv', 'Réparation ajoutée en magasin.', 'En cours', '2025-03-25 16:25:48', NULL, 165);

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
(108, 'samir.rahoul@hotmail.com', '[\"ROLE_ADMIN\", \"ROLE_USER\"]', '$2y$13$Bos6iAOWRVQzaK79fo17Fu2ZnM5DZT.iwptJNMQHzu0/5I.AIhfzG', 'SAMIR', 'KAHOUL', '10 Rue des Lilas, Paris', '0102030405'),
(109, 'marie.durand@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$phN4mkYyJihnC2/56PwgUuYiNwPCGh30om1zdDsji5eagcwGu0puK', 'Durand', 'Marie', '15 Avenue des Champs, Lyon', '0607080910'),
(110, 'paul.martin@gmail.com', '[\"ROLE_USER\"]', '$2y$13$lHAI.Qzwu6HlVWsY7AUDx.tr12wiwg/Bl1/hPoUkdB8IsJImtSWx6', 'Martin', 'Paul', '5 Impasse des Roses, Marseille', '0708091011'),
(163, 'jojo@gmail.fr', '[\"ROLE_USER\"]', '$2y$13$5An9xdElztlyCBrNaB72c.7s76LD1TFGsNXB4YF8oTHYOAwwAZQ3e', 'F', 'F', '63 Rue De La Forêt Konacker', '0641885333'),
(164, 'ouchene2008@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$QWt9UsYFGl9IPtcCAe83/.2A7Q3D4uJj6ayzSZLHhV6eihcgig/eu', 'karima', 'joujou', '24 rue des grands bois', '0641885323'),
(165, 'soso@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$OlzkYQApDo/0g/vOZ1/zQOBLPdr/Gr06pT9ZyAwM3DKbI08UcnZZm', 'ouchene', 'soso', '24', '0641885380'),
(171, 'linaloal@gmail.com', '[\"ROLE_USER\"]', '$2y$13$8/sraMMs0tgahWY/251gbO7hrbWR5ouZH305LJ7tKHnX/1SP0INj2', 'daci', 'lina', '24', '0641885380'),
(174, 'bila@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$9t4AESrZcLuPSoARrEV0s.dovuZ2iEFbHLkI6fj6B.YrQqSliyJ.K', 'bila', 'bola', '68 Rue de mine', '0641885380'),
(181, 'ouchenemoussa@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$O25PvuDQlqzI0xjPKJKRxezAujzJiPG51uleFIHgo7Ro3jtFhsihW', 'massi', 'moussa', '63 Rue De La Forêt Konacker', '0641885380'),
(188, 'sesspoir@gmail.com', '[\"ROLE_USER\"]', '$2y$13$rdEjSDxWABuJBKQCSPlkQ.F8oiwR1iUVu/abCJuaT0kNoXN91Qe9W', 'Douane', 'Karima', '24 hhhhhhhhhh', '0641885380'),
(189, 'karima2008@hotmail.com', '[\"ROLE_USER\"]', '$2y$13$WjtLyipohhJtJ2gnQyeW5.djOH7/mEeqJPwp3rVqhl5aVFxjKRguq', 'Douane', 'Karima', '24 bbbbbbbbbbb', '0641885380'),
(201, 'damienbin@gmail.com', '[\"ROLE_USER\"]', '$2y$13$3NhyqybcT8tY9wHxs2fgcOXX/Je49SY68Uhl24nt/9m7Gxgxk/GVm', 'bin', 'damien', '63 Rue De La Forêt Konacker', '0641885380'),
(228, 'mariechenese@gmail.com', '[\"ROLE_USER\"]', '$2y$13$pk5Bldjwgv8o2Jyq5uNGHujQjTEmVWJ1urlKkLE0A1LWe7EiSDts2', 'Chenese', 'marie', '63 Rue De La Forêt  thionville', '0646778901'),
(229, 'ouchenekarima2008@hotmail.fr', '[\"ROLE_USER\"]', '$2y$13$CgQ90qwskrDiJP4unoR7Jeh8wF9rc2z4LsdWn4vhGxUYM2O0ny36S', 'Douane', 'Karima', '98 rue belle vue', '0641885380');

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
-- Indexes for table `historique_reparation`
--
ALTER TABLE `historique_reparation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F78B9B7497934BA` (`reparation_id`);

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT for table `historique_reparation`
--
ALTER TABLE `historique_reparation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT for table `image`
--
ALTER TABLE `image`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=522;

--
-- AUTO_INCREMENT for table `reparation`
--
ALTER TABLE `reparation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `ticket`
--
ALTER TABLE `ticket`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=230;

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
-- Constraints for table `historique_reparation`
--
ALTER TABLE `historique_reparation`
  ADD CONSTRAINT `FK_F78B9B7497934BA` FOREIGN KEY (`reparation_id`) REFERENCES `reparation` (`id`) ON DELETE CASCADE;

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
