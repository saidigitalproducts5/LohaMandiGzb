-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 27, 2026 at 12:27 PM
-- Server version: 11.8.9-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u617814331_db_4xoT6wx8`
--
#1273 - Unknown collation: 'utf8mb4_uca1400_ai_ci'
-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `type` enum('homepage_banner','sponsored_product','featured_seller','category_banner','popup') NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `clicks` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`id`, `seller_id`, `title`, `type`, `image`, `link`, `category_id`, `product_id`, `start_date`, `end_date`, `clicks`, `status`, `created_at`) VALUES
(1, NULL, 'JSW Steel', 'homepage_banner', 'ads/1790089038_dae21f5caefaafa35c61.png', 'https://lohamandigzb.online/adv', NULL, NULL, '2026-09-21 20:26:00', '2026-10-22 20:26:00', 40, 1, '2026-08-07 22:38:56'),
(2, 13, 'M L ISPAT', 'featured_seller', 'ads/1790509574_5077ac4e4497f8cf9e8b.jpg', NULL, 19, 17, '2026-09-21 19:23:00', '2026-10-22 19:23:00', 42, 1, '2026-08-07 22:38:56'),
(3, 13, 'Chequered Plates', 'sponsored_product', 'ads/1790508805_bf717682450a8037d125.png', NULL, 20, 16, '2026-09-21 20:06:00', '2026-10-22 20:06:00', 22, 1, '2026-08-07 22:38:56'),
(4, NULL, 'All Products Under one Roof', 'category_banner', NULL, 'https://lohamandigzb.online/catalog', NULL, NULL, '2026-09-22 18:49:00', '2030-10-23 18:49:00', 15, 1, '2026-08-07 22:38:56'),
(5, NULL, 'Renamed Popup', 'popup', NULL, '/catalog', NULL, NULL, NULL, NULL, 0, 1, '2026-08-07 22:38:56');

-- --------------------------------------------------------

--
-- Table structure for table `ai_analyses`
--

CREATE TABLE `ai_analyses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `analysis_type` enum('fraud_score','duplicate','recommendation','description','keywords','chat') NOT NULL,
  `subject` varchar(190) DEFAULT NULL,
  `score` decimal(8,2) DEFAULT NULL,
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ai_analyses`
--

INSERT INTO `ai_analyses` (`id`, `user_id`, `seller_id`, `product_id`, `analysis_type`, `subject`, `score`, `data`, `created_at`) VALUES
(6, 23, 14, NULL, 'fraud_score', 'Rathi Steel', 37.00, '{\"risk\": \"medium\", \"flags\": [\"GSTIN missing or unverified\", \"Seller identity not verified\", \"No rating history yet\"]}', '2026-08-09 16:10:02'),
(7, 23, 14, NULL, 'fraud_score', 'Rathi Steel', 37.00, '{\"risk\": \"medium\", \"flags\": [\"GSTIN missing or unverified\", \"Seller identity not verified\", \"No rating history yet\"]}', '2026-08-09 16:10:06'),
(8, 23, 14, NULL, 'fraud_score', 'Rathi Steel', 37.00, '{\"risk\": \"medium\", \"flags\": [\"GSTIN missing or unverified\", \"Seller identity not verified\", \"No rating history yet\"]}', '2026-08-11 15:13:35'),
(11, 22, 7, NULL, 'fraud_score', 'Sharma Iron Traders', 18.00, '{\"risk\": \"low\", \"flags\": [\"GSTIN missing or unverified\"]}', '2026-08-12 17:45:28'),
(12, 22, 7, 8, 'description', 'Fe 550D TMT Bar 16mm (IS 1786)', NULL, '{\"words\": 75}', '2026-08-12 17:45:35'),
(13, 22, 7, 8, 'description', 'Fe 550D TMT Bar 16mm (IS 1786)', NULL, '{\"words\": 75}', '2026-08-12 17:45:47'),
(14, 22, 7, NULL, 'fraud_score', 'Sharma Iron Traders', 18.00, '{\"risk\": \"low\", \"flags\": [\"GSTIN missing or unverified\"]}', '2026-08-12 17:46:02'),
(15, 22, 7, NULL, 'fraud_score', 'Sharma Iron Traders', 18.00, '{\"risk\": \"low\", \"flags\": [\"GSTIN missing or unverified\"]}', '2026-08-12 17:48:43'),
(16, 22, 7, NULL, 'fraud_score', 'Sharma Iron Traders', 18.00, '{\"risk\":\"low\",\"flags\":[\"GSTIN missing or unverified\"]}', '2026-08-20 10:32:56'),
(17, 22, 7, 8, 'description', 'Fe 550D TMT Bar 16mm (IS 1786)', NULL, '{\"words\":75}', '2026-08-20 10:33:11'),
(18, 22, 7, NULL, 'fraud_score', 'Sharma Iron Traders', 18.00, '{\"risk\":\"low\",\"flags\":[\"GSTIN missing or unverified\"]}', '2026-08-20 10:33:23'),
(19, 22, 7, 4, 'description', 'Fe 500D TMT Bar 12mm (IS 1786)', NULL, '{\"words\":89}', '2026-08-20 10:33:28'),
(20, 22, 7, NULL, 'fraud_score', 'Sharma Iron Traders', 18.00, '{\"risk\":\"low\",\"flags\":[\"GSTIN missing or unverified\"]}', '2026-08-20 10:33:35'),
(21, 20, 13, NULL, 'fraud_score', 'M L ISPAT', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-22 12:32:18'),
(22, 20, 13, NULL, 'fraud_score', 'M L ISPAT', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-22 12:51:40'),
(23, 20, 13, NULL, 'fraud_score', 'M L ISPAT', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-22 12:51:53'),
(24, 20, 13, NULL, 'fraud_score', 'M L ISPAT', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-22 12:53:36'),
(25, 20, 13, NULL, 'fraud_score', 'M L ISPAT', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-22 12:54:28'),
(26, 20, 13, NULL, 'fraud_score', 'M L ISPAT', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-22 12:54:28'),
(27, 20, 13, NULL, 'fraud_score', 'M L ISPAT', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-22 12:54:32'),
(28, 31, 17, NULL, 'fraud_score', 'Rainbow Steels', 37.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"Seller identity not verified\",\"No rating history yet\"]}', '2026-08-24 15:32:10'),
(29, 31, 17, NULL, 'fraud_score', 'Rainbow Steels', 23.00, '{\"risk\":\"medium\",\"flags\":[\"GSTIN missing or unverified\",\"No rating history yet\"]}', '2026-08-25 17:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(190) NOT NULL,
  `entity` varchar(64) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `entity`, `entity_id`, `old_value`, `new_value`, `ip`, `created_at`) VALUES
(1, 1, 'seller_moderate', 'seller', 1, '\"pending\"', '\"active\"', '::1', '2026-08-05 10:59:35'),
(2, 1, 'product_moderate', 'product', 1, '\"pending\"', '\"approved\"', '::1', '2026-08-05 11:00:00'),
(3, 1, 'category_update', 'category', 1, NULL, NULL, '::1', '2026-08-05 11:00:03'),
(4, 1, 'brand_update', 'brand', 9, NULL, NULL, '::1', '2026-08-05 11:00:25'),
(5, 1, 'location_update', 'location', 14, NULL, NULL, '::1', '2026-08-05 11:00:27'),
(6, 1, 'seller_update', 'seller', 1, NULL, NULL, '::1', '2026-08-05 11:00:29'),
(7, 1, 'user_status', 'user', 3, '\"active\"', '\"suspended\"', '::1', '2026-08-05 11:00:31'),
(8, 1, 'user_delete', 'user', 3, NULL, NULL, '::1', '2026-08-05 11:00:33'),
(9, 1, 'location_update', 'location', 14, NULL, NULL, '::1', '2026-08-05 11:01:23'),
(10, 1, 'product_moderate', 'product', 2, '\"pending\"', '\"approved\"', '::1', '2026-08-05 17:38:58'),
(11, 1, 'product_moderate', 'product', 3, '\"pending\"', '\"approved\"', '::1', '2026-08-05 18:00:44'),
(12, 1, 'ad_create', 'ad', 6, NULL, NULL, '127.0.0.1', '2026-08-07 22:52:41'),
(13, 1, 'ad_delete', 'ad', 6, NULL, NULL, '127.0.0.1', '2026-08-07 22:52:57'),
(14, 1, 'ad_update', 'ad', 5, NULL, NULL, '127.0.0.1', '2026-08-07 22:52:58'),
(15, 1, 'seller_moderate', 'seller', 14, '\"pending\"', '\"active\"', '::1', '2026-08-11 16:41:55'),
(16, 1, 'seller_moderate', 'seller', 13, '\"pending\"', '\"active\"', '::1', '2026-08-11 16:42:13'),
(17, 1, 'seller_moderate', 'seller', 11, '\"pending\"', '\"active\"', '::1', '2026-08-11 16:42:27'),
(18, 1, 'seller_moderate', 'seller', 7, '\"active\"', '\"suspended\"', '::1', '2026-08-11 16:42:47'),
(19, 1, 'product_moderate', 'product', 14, '\"pending\"', '\"approved\"', '::1', '2026-08-11 16:44:29'),
(20, 1, 'seller_moderate', 'seller', 7, '\"suspended\"', '\"active\"', '::1', '2026-08-11 18:19:12'),
(21, 1, 'product_moderate', 'product', 17, '\"pending\"', '\"approved\"', '127.0.0.1', '2026-08-12 19:01:38'),
(22, 1, 'product_moderate', 'product', 16, '\"pending\"', '\"approved\"', '127.0.0.1', '2026-08-12 19:01:47'),
(23, 1, 'product_moderate', 'product', 15, '\"pending\"', '\"approved\"', '127.0.0.1', '2026-08-12 19:01:55'),
(24, 1, 'ad_create', 'ad', 7, NULL, NULL, '2405:201:4019:21bb:21fb:9eff:2d9f:fae8', '2026-08-20 10:36:26'),
(25, 1, 'ad_update', 'ad', 7, NULL, NULL, '2405:201:4019:21bb:21fb:9eff:2d9f:fae8', '2026-08-20 10:39:18'),
(26, 1, 'user_status', 'user', 1, '\"suspended\"', '\"active\"', '49.36.177.91', '2026-08-20 17:41:18'),
(27, 1, 'seller_moderate', 'seller', 17, '\"pending\"', '\"active\"', '2409:40d0:8:fc8a:8d96:4656:b6f4:d97c', '2026-08-24 17:20:54'),
(28, 1, 'ad_update', 'ad', 7, NULL, NULL, '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 13:50:39'),
(29, 1, 'ad_update', 'ad', 2, NULL, NULL, '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 13:54:16'),
(30, 1, 'seller_update', 'seller', 17, NULL, NULL, '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:10:17'),
(31, 1, 'product_moderate', 'product', 22, '\"pending\"', '\"approved\"', '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:26:16'),
(32, 1, 'product_moderate', 'product', 21, '\"pending\"', '\"approved\"', '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:26:22'),
(33, 1, 'product_moderate', 'product', 19, '\"pending\"', '\"approved\"', '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:26:27'),
(34, 1, 'product_moderate', 'product', 20, '\"pending\"', '\"approved\"', '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:26:32'),
(35, 1, 'product_moderate', 'product', 20, '\"approved\"', '\"approved\"', '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:26:33'),
(36, 1, 'ad_delete', 'ad', 7, NULL, NULL, '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:34:57'),
(37, 1, 'ad_update', 'ad', 3, NULL, NULL, '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:36:22'),
(38, 1, 'ad_update', 'ad', 1, NULL, NULL, '2405:201:4019:204c:b484:5d13:e934:596f', '2026-09-22 14:57:18'),
(39, 1, 'ad_update', 'ad', 4, NULL, NULL, '2405:201:4019:204c:16e:df8:76c3:d1ad', '2026-09-23 13:21:16'),
(40, 1, 'category_update', 'category', 20, NULL, NULL, '2405:201:4019:204c:16e:df8:76c3:d1ad', '2026-09-23 13:24:17'),
(41, 1, 'ad_update', 'ad', 4, NULL, NULL, '2405:201:4019:204c:16e:df8:76c3:d1ad', '2026-09-23 13:26:27'),
(42, 1, 'ad_update', 'ad', 2, NULL, NULL, '2405:201:4019:204c:358d:ea7:eeff:c33d', '2026-09-26 10:22:57'),
(43, 1, 'ad_update', 'ad', 3, NULL, NULL, '2405:201:4019:204c:b88e:5645:cace:cdab', '2026-09-27 11:33:25'),
(44, 1, 'ad_update', 'ad', 2, NULL, NULL, '2405:201:4019:204c:b88e:5645:cace:cdab', '2026-09-27 11:46:14');

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE `auth_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` varchar(255) NOT NULL,
  `device` varchar(190) DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `slug`, `logo`, `status`, `created_at`) VALUES
(1, 'Tata Tiscon', 'tata-tiscon', NULL, 1, '2026-08-04 15:57:30'),
(2, 'JSW', 'jsw', NULL, 1, '2026-08-04 15:57:30'),
(3, 'SAIL', 'sail', NULL, 1, '2026-08-04 15:57:30'),
(4, 'Tata Steel', 'tata-steel', NULL, 1, '2026-08-04 15:57:30'),
(5, 'Vizag', 'vizag', NULL, 1, '2026-08-04 15:57:30'),
(6, 'Kamdhenu', 'kamdhenu', NULL, 1, '2026-08-04 15:57:30'),
(7, 'Jindal', 'jindal', NULL, 1, '2026-08-04 15:57:30'),
(8, 'Shyam Steel', 'shyam-steel', NULL, 1, '2026-08-04 15:57:30'),
(9, 'Ambitious', 'tata-steel-updated', NULL, 1, '2026-08-04 15:57:30'),
(10, 'APL Apollo', 'apl-apollo', NULL, 1, '2026-08-04 15:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `bulk_prices`
--

CREATE TABLE `bulk_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `min_qty` decimal(12,3) NOT NULL,
  `max_qty` decimal(12,3) DEFAULT NULL,
  `unit_price` decimal(14,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bulk_prices`
--

INSERT INTO `bulk_prices` (`id`, `product_id`, `min_qty`, `max_qty`, `unit_price`, `created_at`) VALUES
(1, 4, 10.000, 50.000, 61000.00, '2026-08-07 18:10:53'),
(2, 4, 51.000, 100.000, 60500.00, '2026-08-07 18:10:53'),
(3, 4, 101.000, NULL, 59800.00, '2026-08-07 18:10:53');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `parent_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(120) NOT NULL,
  `slug` varchar(140) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `icon`, `image`, `description`, `status`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, NULL, 'TMT Bars', 'test-cat-renamed', NULL, NULL, 'Reinforcement steel bars for construction', 1, 0, '2026-08-04 15:57:30', '2026-08-05 11:02:51'),
(2, NULL, 'MS Plate', 'ms-plate', NULL, NULL, 'Mild steel plates', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(3, NULL, 'MS Angle', 'ms-angle', NULL, NULL, 'Mild steel angle sections', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(4, NULL, 'MS Channel', 'ms-channel', NULL, NULL, 'Mild steel channel sections', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(5, NULL, 'MS Beam', 'ms-beam', NULL, NULL, 'Mild steel beam / I-section', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(6, NULL, 'Steel Pipe', 'steel-pipe', NULL, NULL, 'Mild steel pipes', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(7, NULL, 'GI Pipe', 'gi-pipe', NULL, NULL, 'Galvanized iron pipes', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(8, NULL, 'Square Pipe', 'square-pipe', NULL, NULL, 'Square hollow sections', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(9, NULL, 'Round Pipe', 'round-pipe', NULL, NULL, 'Round hollow sections', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(10, NULL, 'Steel Rod', 'steel-rod', NULL, NULL, 'Steel rods and rounds', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(11, NULL, 'Steel Coil', 'steel-coil', NULL, NULL, 'Hot/cold rolled steel coils', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(12, NULL, 'Steel Sheet', 'steel-sheet', NULL, NULL, 'Steel sheets', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(13, NULL, 'Steel Scrap', 'steel-scrap', NULL, NULL, 'Iron and steel scrap', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(14, NULL, 'Steel Wire', 'steel-wire', NULL, NULL, 'Steel wires and wire rods', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(15, NULL, 'Iron Plate', 'iron-plate', NULL, NULL, 'Iron plates', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(16, NULL, 'Fasteners', 'fasteners', NULL, NULL, 'Bolts, nuts, screws and fasteners', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(17, NULL, 'Industrial Hardware', 'industrial-hardware', NULL, NULL, 'Industrial hardware items', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(18, NULL, 'Construction Material', 'construction-material', NULL, NULL, 'Construction materials', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30'),
(19, NULL, 'HR sheets', 'hr-sheets', NULL, NULL, NULL, 1, 0, '2026-08-20 17:39:51', '2026-08-20 17:39:51'),
(20, NULL, 'Chequered', 'chequered', NULL, NULL, NULL, 1, 0, '2026-08-20 17:40:35', '2026-09-23 13:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rfq_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_message` text DEFAULT NULL,
  `last_message_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `seller_id`, `buyer_id`, `product_id`, `rfq_id`, `last_message`, `last_message_at`, `created_at`) VALUES
(3, 13, 16, NULL, NULL, 'Hello !', '2026-08-20 12:55:54', '2026-08-20 12:55:47'),
(4, 13, 29, NULL, NULL, 'Hello ML ISPAT.  HR Sheet ka rate kya hai ?', '2026-08-21 10:58:51', '2026-08-21 10:58:19'),
(5, 13, 30, NULL, NULL, 'hi', '2026-09-11 12:53:51', '2026-09-11 12:53:43');

-- --------------------------------------------------------

--
-- Table structure for table `crm_customers`
--

CREATE TABLE `crm_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `company` varchar(190) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `source` varchar(32) NOT NULL DEFAULT 'manual',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_deals`
--

CREATE TABLE `crm_deals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `value` decimal(14,2) NOT NULL DEFAULT 0.00,
  `stage` enum('prospecting','qualified','quotation','negotiation','won','lost') NOT NULL DEFAULT 'prospecting',
  `expected_close` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_followups`
--

CREATE TABLE `crm_followups` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `channel` varchar(24) NOT NULL DEFAULT 'call',
  `scheduled_at` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'pending',
  `done_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_meetings`
--

CREATE TABLE `crm_meetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `location` varchar(190) DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'scheduled',
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_notes`
--

CREATE TABLE `crm_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `body` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_tasks`
--

CREATE TABLE `crm_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `category` varchar(32) NOT NULL DEFAULT 'task',
  `due_at` datetime DEFAULT NULL,
  `priority` varchar(16) NOT NULL DEFAULT 'medium',
  `status` varchar(16) NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `warehouse` varchar(190) DEFAULT NULL,
  `warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stock_qty` decimal(14,3) NOT NULL DEFAULT 0.000,
  `low_stock_threshold` decimal(14,3) NOT NULL DEFAULT 0.000,
  `batch_no` varchar(64) DEFAULT NULL,
  `barcode` varchar(64) DEFAULT NULL,
  `qr_code` varchar(64) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_documents`
--

CREATE TABLE `kyc_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `doc_type` enum('gst','pan','aadhaar','business_address_proof','bank','certificate','other') NOT NULL,
  `doc_number` varchar(64) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `reject_reason` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rfq_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(120) DEFAULT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `company` varchar(190) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `lead_type` enum('rfq','product_inquiry','contact','other') NOT NULL DEFAULT 'product_inquiry',
  `status` enum('new','interested','hot','closed','rejected','lost','won') NOT NULL DEFAULT 'new',
  `notes` text DEFAULT NULL,
  `call_history` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`call_history`)),
  `reminder_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `seller_id`, `buyer_id`, `product_id`, `rfq_id`, `name`, `mobile`, `email`, `company`, `message`, `lead_type`, `status`, `notes`, `call_history`, `reminder_at`, `created_at`, `updated_at`) VALUES
(4, 7, 16, NULL, 9, NULL, NULL, NULL, NULL, 'RFQ: MS Channel 100x50x5 (qty 5 ton)', 'rfq', 'new', NULL, NULL, NULL, '2026-08-20 13:33:19', '2026-08-20 13:33:19'),
(5, 13, 29, NULL, 10, NULL, NULL, NULL, NULL, 'RFQ: hr sheets (qty 1 ton)', 'rfq', 'new', NULL, NULL, NULL, '2026-08-20 15:27:27', '2026-08-20 15:27:27'),
(6, 7, 30, NULL, 11, NULL, NULL, NULL, NULL, 'RFQ: MS Channel (qty 5 ton)', 'rfq', 'new', NULL, NULL, NULL, '2026-08-21 14:44:32', '2026-08-21 14:44:32'),
(7, 7, 32, NULL, 12, NULL, NULL, NULL, NULL, 'RFQ: MS ANGLE (qty 2 ton)', 'rfq', 'new', NULL, NULL, NULL, '2026-08-25 16:01:22', '2026-08-25 16:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `city` varchar(64) NOT NULL,
  `state` varchar(64) NOT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `city`, `state`, `pincode`, `is_active`, `created_at`) VALUES
(1, 'Ghaziabad', 'Uttar Pradesh', '201001', 1, '2026-08-04 21:47:15'),
(2, 'Ghaziabad', 'Uttar Pradesh', '201002', 1, '2026-08-04 21:47:15'),
(3, 'Ghaziabad', 'Uttar Pradesh', '201003', 1, '2026-08-04 21:47:15'),
(4, 'Ghaziabad', 'Uttar Pradesh', '201009', 1, '2026-08-04 21:47:15'),
(5, 'Ghaziabad', 'Uttar Pradesh', '201010', 1, '2026-08-04 21:47:15'),
(6, 'Ghaziabad', 'Uttar Pradesh', '201014', 1, '2026-08-04 21:47:15'),
(7, 'Modinagar', 'Uttar Pradesh', '201204', 1, '2026-08-04 21:47:15'),
(8, 'Sahibabad', 'Uttar Pradesh', '201005', 1, '2026-08-04 21:47:15'),
(9, 'Vasundhara', 'Uttar Pradesh', '201012', 1, '2026-08-04 21:47:15'),
(10, 'Indirapuram', 'Uttar Pradesh', '201014', 1, '2026-08-04 21:47:15'),
(11, 'Vaishali', 'Uttar Pradesh', '201010', 1, '2026-08-04 21:47:15'),
(12, 'Noida', 'Uttar Pradesh', '201301', 1, '2026-08-04 21:47:15'),
(13, 'Greater Noida', 'Uttar Pradesh', '201310', 1, '2026-08-04 21:47:15'),
(14, 'Delhi', 'Delhi', '201009', 1, '2026-08-04 21:47:15'),
(15, 'Faridabad', 'Haryana', '121001', 1, '2026-08-04 21:47:15'),
(16, 'Meerut', 'Uttar Pradesh', '250001', 1, '2026-08-04 21:47:15'),
(17, 'Muzaffarnagar', 'Uttar Pradesh', '251001', 1, '2026-08-04 21:47:15'),
(18, 'Hapur', 'Uttar Pradesh', '245101', 1, '2026-08-04 21:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(64) NOT NULL,
  `code` varchar(32) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `duration_days` int(10) UNSIGNED NOT NULL DEFAULT 365,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `name`, `code`, `price`, `duration_days`, `features`, `status`, `created_at`) VALUES
(1, 'Silver', 'silver', 4999.00, 365, '{\"leads_limit\": 500, \"top_listing\": 1, \"products_limit\": 50, \"verified_badge\": 1}', 1, '2026-08-04 15:57:30'),
(2, 'Gold', 'gold', 14999.00, 365, '{\"analytics\": 1, \"leads_limit\": 2000, \"top_listing\": 3, \"products_limit\": 200, \"verified_badge\": 1}', 1, '2026-08-04 15:57:30'),
(3, 'Diamond', 'diamond', 39999.00, 365, '{\"analytics\": 1, \"leads_limit\": 0, \"top_listing\": 5, \"products_limit\": 0, \"verified_badge\": 1, \"priority_support\": 1}', 1, '2026-08-04 15:57:30'),
(4, 'Seller — Yearly', 'seller_yearly', 1.00, 365, '{\"leads_limit\": 2000, \"products_limit\": 200, \"verified_badge\": 1, \"business_listing\": 1}', 1, '2026-08-06 16:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chat_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `message_type` enum('text','image','pdf','invoice','voice','quotation') NOT NULL DEFAULT 'text',
  `body` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `chat_id`, `sender_id`, `message_type`, `body`, `file_path`, `is_read`, `read_at`, `created_at`) VALUES
(6, 3, 16, 'text', 'Hello !', NULL, 0, NULL, '2026-08-20 12:55:54'),
(7, 4, 29, 'text', 'Hello ML ISPAT.  HR Sheet ka rate kya hai ?', NULL, 0, NULL, '2026-08-21 10:58:51'),
(8, 5, 30, 'text', 'hi', NULL, 0, NULL, '2026-09-11 12:53:51');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(190) NOT NULL,
  `body` text DEFAULT NULL,
  `type` enum('sms','email','whatsapp','push','in_app') NOT NULL DEFAULT 'in_app',
  `ref_type` varchar(32) DEFAULT NULL,
  `ref_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `body`, `type`, `ref_type`, `ref_id`, `is_read`, `created_at`) VALUES
(8, 1, 'New RFQ: TMT BAR', 'Qty 2 ton · Sonipat', 'in_app', 'rfq', 6, 0, '2026-08-11 16:33:08'),
(9, 23, 'Business profile active', 'Your business profile on LOHA MANDI is now: active.', 'in_app', 'seller', 14, 0, '2026-08-11 16:41:55'),
(10, 22, 'Business profile active', 'Your business profile on LOHA MANDI is now: active.', 'in_app', 'seller', 13, 1, '2026-08-11 16:42:13'),
(11, 20, 'Business profile active', 'Your business profile on LOHA MANDI is now: active.', 'in_app', 'seller', 11, 1, '2026-08-11 16:42:27'),
(12, 1, 'Business profile suspended', 'Your business profile on LOHA MANDI is now: suspended.', 'in_app', 'seller', 7, 0, '2026-08-11 16:42:47'),
(14, 22, 'Business profile active', 'Your business profile on LOHA MANDI is now: active.', 'in_app', 'seller', 7, 1, '2026-08-11 18:19:12'),
(19, 20, 'New message from Amit Buyer', 'Hi, regarding the TMT quotation', 'in_app', 'chat', 2, 1, '2026-08-11 22:01:17'),
(20, 16, 'New message from Harsh Garg', 'Sure, quote valid till 15 Sep', 'in_app', 'chat', 2, 0, '2026-08-11 22:01:41'),
(22, 20, 'New message from Amit Buyer', 'Hello !', 'in_app', 'chat', 3, 0, '2026-08-20 12:55:54'),
(23, 22, 'New RFQ received', 'A buyer posted an RFQ for MS Channel 100x50x5. Submit your quotation.', 'in_app', 'rfq', 9, 0, '2026-08-20 13:33:19'),
(24, 20, 'New RFQ received', 'A buyer posted an RFQ for hr sheets. Submit your quotation.', 'in_app', 'rfq', 10, 0, '2026-08-20 15:27:27'),
(25, 20, 'New message from Buyer 2335', 'Hello ML ISPAT.  HR Sheet ka rate kya hai ?', 'in_app', 'chat', 4, 0, '2026-08-21 10:58:51'),
(26, 22, 'New RFQ received', 'A buyer posted an RFQ for MS Channel. Submit your quotation.', 'in_app', 'rfq', 11, 0, '2026-08-21 14:44:32'),
(27, 31, 'Business profile active', 'Your business profile on LOHA MANDI is now: active.', 'in_app', 'seller', 17, 0, '2026-08-24 17:20:54'),
(28, 22, 'New RFQ received', 'A buyer posted an RFQ for MS ANGLE. Submit your quotation.', 'in_app', 'rfq', 12, 0, '2026-08-25 16:01:22'),
(29, 20, 'New message from Vijay Gupta', 'hi', 'in_app', 'chat', 5, 0, '2026-09-11 12:53:51'),
(30, 32, 'Quotation received for MS ANGLE', 'Sharma Iron Traders quoted ₹160,480.00.', 'in_app', 'quotation', 4, 0, '2026-09-12 15:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_number` varchar(32) NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `quote_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subtotal` decimal(14,2) NOT NULL,
  `gst_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `shipping` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total` decimal(14,2) NOT NULL,
  `payment_method` varchar(32) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `order_status` enum('confirmed','dispatched','in_transit','delivered','cancelled') NOT NULL DEFAULT 'confirmed',
  `lr_number` varchar(64) DEFAULT NULL,
  `transport_details` text DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_name` varchar(190) NOT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `unit` varchar(16) NOT NULL DEFAULT 'ton',
  `unit_price` decimal(14,2) NOT NULL,
  `total` decimal(14,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mobile` varchar(15) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `otp` varchar(6) NOT NULL,
  `purpose` enum('register','login','forgot','verify_mobile','verify_email') NOT NULL DEFAULT 'register',
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `attempts` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `method` enum('upi','net_banking','debit_card','credit_card','wallet','cod','razorpay','other') NOT NULL,
  `txn_id` varchar(64) DEFAULT NULL,
  `status` enum('pending','success','failed','refunded') NOT NULL DEFAULT 'pending',
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `amount`, `method`, `txn_id`, `status`, `gateway_response`, `created_at`) VALUES
(4, NULL, 20, 9999.00, 'razorpay', 'pay_d7bd43880ee8', 'success', '{\"kind\": \"seller_subscription\", \"plan_id\": 4, \"rzp_order_id\": \"order_13ad1455bea482060c77\"}', '2026-08-08 18:26:08'),
(6, NULL, 22, 9999.00, 'razorpay', 'pay_72f8f7ee9203', 'success', '{\"kind\": \"seller_subscription\", \"plan_id\": 4, \"rzp_order_id\": \"order_c3664c0278b0840fa183\"}', '2026-08-09 10:40:25'),
(7, NULL, 23, 9999.00, 'razorpay', 'pay_f9c516478657', 'success', '{\"kind\": \"seller_subscription\", \"plan_id\": 4, \"rzp_order_id\": \"order_856c4bbff9a0c2753e66\"}', '2026-08-09 16:09:20'),
(11, NULL, 31, 1.00, 'razorpay', 'pay_TTbzRq7uSgI8TO', 'success', '{\"rzp_order_id\":\"order_TTbybOXQzMcE12\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-08-24 12:46:20'),
(12, NULL, 33, 1.00, 'razorpay', 'pay_Ta0pyrruak119J', 'success', '{\"rzp_order_id\":\"order_Ta0pTUn1xHmj3v\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-09 16:58:43'),
(13, NULL, 34, 1.00, 'razorpay', 'pay_Ta0tezhapDhNOG', 'success', '{\"rzp_order_id\":\"order_Ta0sxGbYwuvFV8\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-09 17:02:10'),
(14, NULL, 35, 1.00, 'razorpay', 'pay_Ta0wKajIMVZIz6', 'success', '{\"rzp_order_id\":\"order_Ta0vqEAeefSuof\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-09 17:04:42'),
(15, NULL, 36, 1.00, 'razorpay', 'pay_Ta153MXZ3WsWyv', 'success', '{\"rzp_order_id\":\"order_Ta14Crx6A8f8BU\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-09 17:12:58'),
(16, NULL, 37, 1.00, 'razorpay', 'pay_TaLyOVGHdN3IH7', 'success', '{\"rzp_order_id\":\"order_TaLxiZGvnh2VQf\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-10 13:39:14'),
(17, NULL, 38, 1.00, 'razorpay', 'pay_TaM3FtHlJxlbfI', 'success', '{\"rzp_order_id\":\"order_TaM2e66hkDTGgq\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-10 13:43:50'),
(18, NULL, 39, 1.00, 'razorpay', 'pay_TbwVmvaxyMbSHe', 'success', '{\"rzp_order_id\":\"order_TbwV0WL8yUIIOb\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-14 14:02:48'),
(19, NULL, 40, 1.00, 'razorpay', 'pay_TbwfDt86UTgJJI', 'success', '{\"rzp_order_id\":\"order_TbweYAY6r12ucg\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-14 14:12:14'),
(20, NULL, 41, 1.00, 'razorpay', 'pay_TbwjnZNlrl9WTZ', 'success', '{\"rzp_order_id\":\"order_TbwjSLYcZVuAO9\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-14 14:16:03'),
(21, NULL, 42, 1.00, 'razorpay', 'pay_TbwnUT4ZZZQf1E', 'success', '{\"rzp_order_id\":\"order_TbwmzaCjkMNQ1J\",\"kind\":\"seller_subscription\",\"plan_id\":4}', '2026-09-14 14:19:33');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(190) NOT NULL,
  `slug` varchar(210) NOT NULL,
  `description` text DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`specifications`)),
  `weight` varchar(64) DEFAULT NULL,
  `dimensions` varchar(120) DEFAULT NULL,
  `grade` varchar(64) DEFAULT NULL,
  `hsn_code` varchar(16) DEFAULT NULL,
  `gst_rate` decimal(4,2) NOT NULL DEFAULT 18.00,
  `moq` decimal(12,3) NOT NULL DEFAULT 0.000,
  `unit` varchar(16) NOT NULL DEFAULT 'ton',
  `delivery_time` varchar(64) DEFAULT NULL,
  `price` decimal(14,2) DEFAULT NULL,
  `offer_price` decimal(14,2) DEFAULT NULL,
  `available_stock` decimal(14,3) NOT NULL DEFAULT 0.000,
  `is_bulk` tinyint(1) NOT NULL DEFAULT 0,
  `location` varchar(190) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_sponsored` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `inquiry_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `specifications`, `weight`, `dimensions`, `grade`, `hsn_code`, `gst_rate`, `moq`, `unit`, `delivery_time`, `price`, `offer_price`, `available_stock`, `is_bulk`, `location`, `is_featured`, `is_sponsored`, `is_active`, `status`, `view_count`, `inquiry_count`, `created_at`, `updated_at`) VALUES
(4, 7, 1, 1, 'Fe 500D TMT Bar 12mm (IS 1786)', 'fe-500d-tmt-bar-12mm-is-1786-0872dd', 'High-grade Fe 500D TMT bars conforming to IS 1786. Ideal for residential and commercial construction.', '{\"end\": \"U\", \"length\": \"12 m\", \"ductility\": \"Fe 500D\", \"yielding_stress\": \"500 MPa\"}', NULL, NULL, 'Fe 500D', '72142090', 18.00, 10.000, 'ton', '12mm dia', 64000.00, 61000.00, 120.000, 1, 'Ghaziabad', 1, 0, 1, 'approved', 89, 0, '2026-08-07 18:10:41', '2026-09-25 12:39:51'),
(5, 7, 2, 2, 'MS Plate 6mm CR 4x8', 'ms-plate-6mm-cr-4x8-6cc267', 'Cold-rolled mild steel plate, 6mm thickness, 4x8 feet size. Ideal for fabrication and structural use.', NULL, NULL, NULL, 'IS 2062 E250', '72085210', 18.00, 5.000, 'ton', '6mm', 58000.00, 0.00, 200.000, 1, 'Ghaziabad', 1, 0, 1, 'approved', 36, 0, '2026-08-07 18:10:41', '2026-09-21 01:24:29'),
(6, 7, 3, 3, 'MS Angle 50x50x5 (IS 808)', 'ms-angle-50x50x5-is-808-249971', 'Equal angle 50x50x5mm meeting IS 808, standard stock length 6m.', '{\"leg_a\": \"50\", \"leg_b\": \"50\", \"length\": \"6m\", \"thickness\": \"5\"}', NULL, NULL, 'Fe 500', '72161010', 18.00, 3.000, 'ton', '50x50x5', 57500.00, 54000.00, 80.000, 1, 'Ghaziabad', 1, 0, 1, 'approved', 42, 0, '2026-08-05 18:10:41', '2026-09-19 21:00:04'),
(7, 7, 4, 2, 'MS Channel 100x50x5', 'ms-channel-100x50x5-c418dd', 'MS channel 100x50mm, IS 808 conforming, hot-rolled.', NULL, NULL, NULL, 'Fe 330', '72163110', 18.00, 660.000, 'ton', '100x50x5', 56500.00, 0.00, 60.000, 1, 'Ghaziabad', 0, 0, 1, 'approved', 24, 0, '2026-08-04 18:10:41', '2026-09-21 02:34:52'),
(8, 7, 1, 1, 'Fe 550D TMT Bar 16mm (IS 1786)', 'fe-550d-tmt-bar-16mm-is-1786-e0529e', 'Fe 550D TMT bars for heavy construction, IS 1786.', NULL, NULL, NULL, 'Fe 550D', '72142090', 18.00, 10.000, 'ton', '16mm dia', 64000.00, 60800.00, 90.000, 1, 'Ghaziabad', 0, 0, 1, 'approved', 44, 0, '2026-08-07 18:13:54', '2026-09-23 17:14:51'),
(15, 13, 2, 3, 'MS Plates', 'ms-plates-39c80e', 'MS Plates', NULL, NULL, NULL, 'Fe 330', NULL, 18.00, 0.000, 'ton', NULL, 59000.00, 59000.00, 1000000.000, 1, NULL, 0, 0, 1, 'approved', 23, 0, '2026-08-12 13:17:20', '2026-09-17 17:15:04'),
(16, 13, 2, 3, 'MS Chequered Plates', 'ms-chequered-plates-1ad6d9', 'Looking for high-quality MS Chequered Plates? Look no further! Our MS Chequered Plates come in a thickness of all size, making them durable and strong for a variety of applications.', NULL, NULL, NULL, 'IS 2062 E250', NULL, 18.00, 0.000, 'ton', NULL, 70000.00, 70000.00, 1000.000, 0, NULL, 1, 0, 1, 'approved', 22, 0, '2026-08-12 13:23:57', '2026-09-23 13:25:47'),
(17, 13, 12, 7, 'HR Sheets', 'hr-sheets-3e2c43', 'Available in all desired size', NULL, NULL, NULL, NULL, NULL, 18.00, 0.000, 'ton', NULL, 60500.00, NULL, 10000.000, 1, NULL, 0, 0, 1, 'approved', 19, 0, '2026-08-12 13:27:57', '2026-09-21 03:09:37'),
(19, 17, 10, 2, 'TMT Bars', 'tmt-bars-48e1c8', 'TMT Bars in all size', NULL, NULL, NULL, NULL, NULL, 18.00, 0.000, 'ton', NULL, 55000.00, NULL, 1000.000, 1, NULL, 0, 0, 1, 'approved', 2, 0, '2026-09-22 14:18:09', '2026-09-24 02:16:49'),
(20, 17, 3, 3, 'MS ANGLES', 'ms-angles-c67630', 'MS ANGLES in all sizes', NULL, NULL, NULL, NULL, NULL, 18.00, 0.000, 'ton', NULL, 60000.00, NULL, 1000.000, 0, NULL, 0, 0, 1, 'approved', 2, 0, '2026-09-22 14:19:50', '2026-09-24 02:29:01'),
(21, 17, 5, NULL, 'Steel Beam', 'steel-beam-f22bad', 'For any construction size', NULL, NULL, NULL, NULL, NULL, 18.00, 0.000, 'ton', NULL, 65000.00, NULL, 5000.000, 1, NULL, 1, 0, 1, 'approved', 1, 0, '2026-09-22 14:21:42', '2026-09-23 13:28:38'),
(22, 17, 10, NULL, 'Mild Steel Flat', 'mild-steel-flat-1a951b', 'all size', NULL, NULL, NULL, NULL, NULL, 18.00, 0.000, 'ton', NULL, 65000.00, NULL, 5000.000, 0, NULL, 0, 0, 1, 'approved', 1, 0, '2026-09-22 14:24:48', '2026-09-23 19:53:41');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `file_path`, `is_primary`, `sort_order`, `created_at`) VALUES
(1, 4, 'products/demo-tmt.jpg', 1, 0, '2026-08-07 18:10:53'),
(2, 4, 'products/demo-tmt-2.jpg', 0, 1, '2026-08-07 18:10:53'),
(4, 8, 'products/1786449493_5f596908ff41002f6a7b.jpg', 1, 1, '2026-08-11 17:28:13'),
(5, 7, 'products/1786449608_44489d444e8a4ef78ded.png', 1, 1, '2026-08-11 17:30:08'),
(6, 6, 'products/1786449772_44814ac9c1bfe27db2ed.png', 1, 1, '2026-08-11 17:32:52'),
(7, 4, 'products/1786449804_a07acca9d988e9788cd7.png', 0, 3, '2026-08-11 17:33:24'),
(8, 4, 'products/1786452841_d43b73f715f7c2c261f3.png', 0, 4, '2026-08-11 18:24:01'),
(9, 8, 'products/1786453770_b528cbbd3b178bc71b87.png', 0, 2, '2026-08-11 18:39:30'),
(10, 4, 'products/1786458041_d0474b63f4ab07a06497.png', 0, 5, '2026-08-11 19:50:41'),
(11, 4, 'products/1786458111_e28fb4a5a658132114fd.png', 0, 6, '2026-08-11 19:51:51'),
(12, 5, 'products/1786465033_5790536cbf5f37b65b24.png', 1, 1, '2026-08-11 21:47:13'),
(13, 15, 'products/1786540640_01aef5049362d0defa86.png', 1, 1, '2026-08-12 18:47:20'),
(14, 16, 'products/1786541037_898296f900a61286005e.png', 1, 1, '2026-08-12 18:53:57'),
(15, 17, 'products/1786541277_f3e5db0455a18f12e340.png', 1, 1, '2026-08-12 18:57:57'),
(17, 19, 'products/1790086689_f88e7c2a5d83da520460.png', 1, 1, '2026-09-22 14:18:09'),
(18, 21, 'products/1790086902_3176c713b314c528dd17.png', 1, 1, '2026-09-22 14:21:42'),
(19, 22, 'products/1790087088_8d8db9b09bfde27a5ecc.png', 1, 1, '2026-09-22 14:24:48');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rfq_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `quote_number` varchar(32) NOT NULL,
  `product_name` varchar(190) NOT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `unit` varchar(16) NOT NULL DEFAULT 'ton',
  `unit_price` decimal(14,2) NOT NULL,
  `base_amount` decimal(14,2) NOT NULL,
  `gst_rate` decimal(4,2) NOT NULL DEFAULT 18.00,
  `gst_amount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(14,2) NOT NULL DEFAULT 0.00,
  `transport_charge` decimal(14,2) NOT NULL DEFAULT 0.00,
  `other_charges` decimal(14,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(14,2) NOT NULL,
  `payment_terms` varchar(255) DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','sent','accepted','rejected','expired') NOT NULL DEFAULT 'sent',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `rfq_id`, `seller_id`, `buyer_id`, `quote_number`, `product_name`, `quantity`, `unit`, `unit_price`, `base_amount`, `gst_rate`, `gst_amount`, `discount`, `transport_charge`, `other_charges`, `total_amount`, `payment_terms`, `valid_until`, `notes`, `status`, `created_at`, `updated_at`) VALUES
(4, 12, 7, 32, 'QT-260912-0001', 'MS ANGLE', 2.000, 'ton', 68000.00, 136000.00, 18.00, 24480.00, 0.00, 0.00, 0.00, 160480.00, NULL, '2026-10-08', NULL, 'sent', '2026-09-12 15:17:28', '2026-09-12 15:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `review` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `seller_reply` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfqs`
--

CREATE TABLE `rfqs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `buyer_id` bigint(20) UNSIGNED NOT NULL,
  `product_name` varchar(190) NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `unit` varchar(16) NOT NULL DEFAULT 'ton',
  `specification` text DEFAULT NULL,
  `required_date` date DEFAULT NULL,
  `delivery_location` varchar(190) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `budget_min` decimal(14,2) DEFAULT NULL,
  `budget_max` decimal(14,2) DEFAULT NULL,
  `document_path` varchar(255) DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `status` enum('open','quoted','accepted','closed','cancelled') NOT NULL DEFAULT 'open',
  `source` enum('web','app','phone') NOT NULL DEFAULT 'web',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rfqs`
--

INSERT INTO `rfqs` (`id`, `buyer_id`, `product_name`, `category_id`, `quantity`, `unit`, `specification`, `required_date`, `delivery_location`, `city`, `state`, `budget_min`, `budget_max`, `document_path`, `photo_path`, `status`, `source`, `created_at`, `updated_at`) VALUES
(6, 16, 'TMT BAR', 1, 2.000, 'ton', 'GST included', '2026-09-03', 'Sonipat', 'ghaziabad', 'Uttar Pradesh', NULL, NULL, NULL, NULL, 'open', 'web', '2026-08-11 11:03:08', '2026-08-11 11:03:08'),
(9, 16, 'MS Channel 100x50x5', 4, 5.000, 'ton', '+GST', '2026-08-27', 'GALI NO 2, LAL PURA Muradabad', 'Muradabad', 'UP', 300000.00, 300000.00, NULL, NULL, 'open', 'web', '2026-08-20 13:33:19', '2026-08-20 13:33:19'),
(10, 29, 'hr sheets', 12, 1.000, 'ton', 'cash on delivery', '2026-08-27', 'Meerut', 'ghaziabad', 'UP', 70000.00, 70000.00, NULL, NULL, 'open', 'web', '2026-08-20 15:27:27', '2026-08-20 15:27:27'),
(11, 30, 'MS Channel', 4, 5.000, 'ton', 'JINDAL', '2026-08-31', 'GALI NO 2, Ghantaghar chowk. Ghaziabad', 'ghaziabad', 'UP', 300000.00, 300000.00, NULL, NULL, 'open', 'web', '2026-08-21 14:44:32', '2026-08-21 14:44:32'),
(12, 32, 'MS ANGLE', 3, 2.000, 'ton', '+GST rates', '2026-08-28', 'GALI NO 22, Ghantaghar chowk. Modinagar', 'Modinagar', 'Uttar Pradesh', 100000.00, 120000.00, NULL, NULL, 'quoted', 'web', '2026-08-25 16:01:22', '2026-09-12 15:17:28');

-- --------------------------------------------------------

--
-- Table structure for table `rfq_sellers`
--

CREATE TABLE `rfq_sellers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rfq_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `matched_by` enum('auto','manual','public') NOT NULL DEFAULT 'auto',
  `score` decimal(5,2) NOT NULL DEFAULT 0.00,
  `status` enum('sent','viewed','quoted','rejected') NOT NULL DEFAULT 'sent',
  `viewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rfq_sellers`
--

INSERT INTO `rfq_sellers` (`id`, `rfq_id`, `seller_id`, `matched_by`, `score`, `status`, `viewed_at`, `created_at`) VALUES
(6, 9, 7, 'auto', 10.00, 'sent', '2026-08-20 13:33:47', '2026-08-20 13:33:19'),
(7, 10, 13, 'auto', 10.00, 'sent', NULL, '2026-08-20 15:27:27'),
(8, 11, 7, 'auto', 10.00, 'sent', NULL, '2026-08-21 14:44:32'),
(9, 12, 7, 'auto', 10.00, 'sent', NULL, '2026-08-25 16:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `code` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `created_at`) VALUES
(1, 'super_admin', 'Super Admin', '2026-08-04 21:47:13'),
(2, 'admin', 'Admin', '2026-08-04 21:47:13'),
(3, 'sales_executive', 'Sales Executive', '2026-08-04 21:47:13'),
(4, 'moderator', 'Moderator', '2026-08-04 21:47:13'),
(5, 'buyer', 'Buyer', '2026-08-04 21:47:13'),
(6, 'seller', 'Seller', '2026-08-04 21:47:13'),
(7, 'transporter', 'Transporter', '2026-08-04 21:47:13');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(190) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `gstin` varchar(20) DEFAULT NULL,
  `owner_name` varchar(120) DEFAULT NULL,
  `owner_phone` varchar(15) DEFAULT NULL,
  `business_category_id` int(10) UNSIGNED DEFAULT NULL,
  `address_line1` varchar(190) DEFAULT NULL,
  `address_line2` varchar(190) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `business_hours` varchar(190) DEFAULT NULL,
  `years_in_business` smallint(5) UNSIGNED DEFAULT NULL,
  `employees_count` smallint(5) UNSIGNED DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `about` text DEFAULT NULL,
  `brands` text DEFAULT NULL,
  `infrastructure` text DEFAULT NULL,
  `website` varchar(190) DEFAULT NULL,
  `gst_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `rating_avg` decimal(2,1) NOT NULL DEFAULT 0.0,
  `rating_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `status` enum('pending','active','rejected','suspended') NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `user_id`, `business_name`, `slug`, `gstin`, `owner_name`, `owner_phone`, `business_category_id`, `address_line1`, `address_line2`, `city`, `state`, `pincode`, `latitude`, `longitude`, `business_hours`, `years_in_business`, `employees_count`, `logo`, `banner`, `about`, `brands`, `infrastructure`, `website`, `gst_verified`, `is_verified`, `is_premium`, `rating_avg`, `rating_count`, `status`, `created_at`, `updated_at`) VALUES
(7, 22, 'Sharma Iron Traders', 'sharma-iron-traders', NULL, 'Rajesh Sharma', '9811000001', 1, NULL, NULL, 'Ghaziabad', 'Uttar Pradesh', '201001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 4.8, 24, 'active', '2026-08-07 18:10:28', '2026-08-11 12:49:12'),
(13, 20, 'M L ISPAT', 'm-l-ispat-22', NULL, NULL, NULL, NULL, '1', NULL, 'Ghaziabad', 'Uttar Pradesh', '20185', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0.0, 0, 'active', '2026-08-09 05:10:25', '2026-08-25 16:52:12'),
(14, 23, 'Rathi Steel', 'rathi-steel-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0.0, 0, 'active', '2026-08-09 10:39:20', '2026-08-25 16:52:38'),
(17, 31, 'Rainbow Steels', 'rainbow-steels-31', '09AAZPA5054D1ZM', NULL, '9811680516', 1, 'Loha Mandi', NULL, 'Ghaziabad', 'Uttar Pradesh', '201010', NULL, NULL, '', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 0.0, 0, 'active', '2026-08-24 12:46:20', '2026-09-22 14:10:17'),
(18, 33, 'Anupam Steel', 'anupam-steel-33', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-09 16:58:43', '2026-09-09 16:58:43'),
(19, 34, 'Maa Sharda Steel', 'maa-sharda-steel-34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-09 17:02:10', '2026-09-09 17:02:10'),
(20, 35, 'Ajanta Steel', 'ajanta-steel-35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-09 17:04:42', '2026-09-09 17:04:42'),
(21, 36, 'India Green Steel', 'india-green-steel-36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-09 17:12:58', '2026-09-09 17:12:58'),
(22, 37, 'Sh Bala Ji Tube Alok Ji', 'sh-bala-ji-tube-alok-ji-37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-10 13:39:14', '2026-09-10 13:39:14'),
(23, 38, 'Tulsi Steel', 'tulsi-steel-38', NULL, 'Varun', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-10 13:43:50', '2026-09-10 14:06:46'),
(24, 39, 'Maruti Pipe', 'maruti-pipe-39', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-14 14:02:48', '2026-09-14 14:02:48'),
(25, 40, 'Subhash Abhishek Steels', 'subhash-abhishek-steels-40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-14 14:12:14', '2026-09-14 14:12:14'),
(26, 41, 'Kdjp Abhisek Steel', 'kdjp-abhisek-steel-41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-14 14:16:03', '2026-09-14 14:16:03'),
(27, 42, 'Brij Steel', 'brij-steel-42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'pending', '2026-09-14 14:19:33', '2026-09-14 14:20:52');

-- --------------------------------------------------------

--
-- Table structure for table `seller_media`
--

CREATE TABLE `seller_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `media_type` enum('image','video','certificate','catalog','brochure') NOT NULL DEFAULT 'image',
  `file_path` varchar(255) NOT NULL,
  `title` varchar(190) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_membership`
--

CREATE TABLE `seller_membership` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` int(10) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_membership`
--

INSERT INTO `seller_membership` (`id`, `seller_id`, `plan_id`, `start_date`, `end_date`, `amount`, `status`, `created_at`) VALUES
(4, 13, 4, '2026-08-09', '2027-08-09', 14999.00, 'active', '2026-08-09 10:40:25'),
(5, 14, 4, '2026-08-09', '2027-08-09', 14999.00, 'active', '2026-08-09 16:09:20'),
(8, 17, 4, '2026-08-24', '2027-08-24', 14999.00, 'active', '2026-08-24 12:46:20'),
(9, 18, 4, '2026-09-09', '2027-09-09', 14999.00, 'active', '2026-09-09 16:58:43'),
(10, 19, 4, '2026-09-09', '2027-09-09', 14999.00, 'active', '2026-09-09 17:02:10'),
(11, 20, 4, '2026-09-09', '2027-09-09', 14999.00, 'active', '2026-09-09 17:04:42'),
(12, 21, 4, '2026-09-09', '2027-09-09', 14999.00, 'active', '2026-09-09 17:12:58'),
(13, 22, 4, '2026-09-10', '2027-09-10', 14999.00, 'active', '2026-09-10 13:39:14'),
(14, 23, 4, '2026-09-10', '2027-09-10', 14999.00, 'active', '2026-09-10 13:43:50'),
(15, 24, 4, '2026-09-14', '2027-09-14', 14999.00, 'active', '2026-09-14 14:02:48'),
(16, 25, 4, '2026-09-14', '2027-09-14', 14999.00, 'active', '2026-09-14 14:12:14'),
(17, 26, 4, '2026-09-14', '2027-09-14', 14999.00, 'active', '2026-09-14 14:16:03'),
(18, 27, 4, '2026-09-14', '2027-09-14', 14999.00, 'active', '2026-09-14 14:19:33');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `from_warehouse_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_warehouse_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(14,3) NOT NULL DEFAULT 0.000,
  `status` enum('pending','in_transit','arrived','cancelled') NOT NULL DEFAULT 'pending',
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transporters`
--

CREATE TABLE `transporters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fleet_name` varchar(190) DEFAULT NULL,
  `vehicle_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `route_from` varchar(64) DEFAULT NULL,
  `route_to` varchar(64) DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transporters`
--

INSERT INTO `transporters` (`id`, `user_id`, `fleet_name`, `vehicle_count`, `route_from`, `route_to`, `verified`, `created_at`) VALUES
(1, 15, 'Ghaziabad Freight Lines', 3, 'Ghaziabad', 'Delhi', 0, '2026-08-07 13:22:43'),
(3, 43, 'Subh Transport Co.', 1, 'Ghaziabad', 'Anywhere', 0, '2026-09-27 11:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `transport_bookings`
--

CREATE TABLE `transport_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `transporter_id` bigint(20) UNSIGNED NOT NULL,
  `pickup_address` text NOT NULL,
  `drop_address` text NOT NULL,
  `load_tonnage` decimal(10,3) NOT NULL,
  `vehicle_type` varchar(64) DEFAULT NULL,
  `freight_charge` decimal(14,2) DEFAULT NULL,
  `status` enum('requested','confirmed','in_transit','delivered','cancelled') NOT NULL DEFAULT 'requested',
  `tracking` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tracking`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_bookings`
--

INSERT INTO `transport_bookings` (`id`, `order_id`, `transporter_id`, `pickup_address`, `drop_address`, `load_tonnage`, `vehicle_type`, `freight_charge`, `status`, `tracking`, `created_at`) VALUES
(1, NULL, 1, 'Ghaziabad Karkari Bazar', 'Delhi Anand Vihar', 8.500, 'Trailer', 12500.00, 'in_transit', '[{\"at\": \"2026-08-07 13:23:35\", \"by\": \"Ravi Kumar\", \"note\": \"Vehicle assigned, MN-09-5566\", \"status\": \"confirmed\"}, {\"at\": \"2026-08-07 13:23:37\", \"by\": \"Ravi Kumar\", \"note\": \"Departed Ghaziabad, ETA 4pm\", \"status\": \"in_transit\"}]', '2026-08-07 13:23:21'),
(2, NULL, 3, 'M L ISPAT', 'Panipat', 10.000, 'Eicher 14ft', 15000.00, 'requested', NULL, '2026-09-27 11:40:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` tinyint(3) UNSIGNED NOT NULL DEFAULT 5,
  `name` varchar(120) NOT NULL,
  `email` varchar(190) DEFAULT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `google_id` varchar(64) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `mobile_verified` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive','suspended','deleted') NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `mobile`, `password`, `google_id`, `avatar`, `email_verified`, `mobile_verified`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super Admin', 'admin@lohamandi.com', '9999999999', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 1, 1, 'active', NULL, '2026-08-04 22:04:27', '2026-08-20 17:41:18'),
(2, 4, 'Moderator', 'moderator@lohamandi.com', '9999999998', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 1, 1, 'active', NULL, '2026-08-04 22:04:27', '2026-08-10 17:01:29'),
(15, 7, 'Ravi Kumar', 'ravi@test.com', '9881223300', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-07 13:22:42', '2026-08-10 17:01:29'),
(16, 5, 'Amit Buyer', 'amit@test.com', '9771223300', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-07 13:24:31', '2026-08-10 17:01:29'),
(20, 6, 'Harsh Garg', NULL, '9312508178', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-08 12:56:07', '2026-08-22 12:03:37'),
(22, 6, 'Harish Sharma', NULL, '9910078800', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-09 05:10:25', '2026-08-24 15:30:28'),
(23, 6, 'AMIT RATHI', NULL, '9857654321', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-09 10:39:19', '2026-08-11 11:11:55'),
(29, 5, 'Buyer 2335', NULL, '8888888888', '$2y$10$fKnWbzIUeBsfvYNlJszHJuSVdFB3jgxhS4FhV0agR9/W7x6g37kkO', NULL, NULL, 0, 1, 'active', NULL, '2026-08-20 15:24:16', '2026-08-24 12:44:21'),
(30, 5, 'Vijay Gupta', NULL, '9910078855', '$2y$10$35SKKqg0GSRDW/LG2tBkpOjg/ryZJY2e0Y9ejldR30of4AzYyzXyC', NULL, NULL, 0, 0, 'active', NULL, '2026-08-21 14:41:44', '2026-08-25 16:19:34'),
(31, 6, 'Anurag Agarwal', NULL, '9811680516', '$2y$10$m.Md2MXUh.mYesNO91yRrOq78K.CSP0bHKcxBOaXxFhbDPmvdRDFu', NULL, NULL, 0, 0, 'active', NULL, '2026-08-24 12:46:20', '2026-08-25 12:27:46'),
(32, 5, 'Buyer 8855', NULL, '9988888888', '$2y$10$WTdjLrUqQfhjRjyExIlIbulNTT0b7ylVaAakl2qsvm13zHrMJ5cFm', NULL, NULL, 0, 1, 'active', NULL, '2026-08-25 15:46:48', '2026-08-25 15:57:03'),
(33, 6, 'Anupam Ji', NULL, '9810034443', '$2y$10$nov1jZ9Xb96unxVKOayE5uO31Ujsm3HJlEMT77AShkglXcn3GwTBu', NULL, NULL, 0, 0, 'active', NULL, '2026-09-09 16:58:43', '2026-09-09 16:58:43'),
(34, 6, 'Manoj Mitta  l Maa Sharda Steel', NULL, '9350203657', '$2y$10$iyg9PJfpvY1ZZy.BxVeY3ObMYYveoM129TXRR7BJ/JodiTMHBwfAu', NULL, NULL, 0, 0, 'active', NULL, '2026-09-09 17:02:10', '2026-09-10 13:36:11'),
(35, 6, 'Vishal Ji Ajanta Steel', NULL, '9911112240', '$2y$10$VmPENC4/Q0IbXhxjvfR0weWzejPF9aIMMwh2iPHAnK4/fZ/hQt756', NULL, NULL, 0, 0, 'active', NULL, '2026-09-09 17:04:42', '2026-09-09 17:04:42'),
(36, 6, 'Yatharth Agrawal India Green Steel', NULL, '8368432974', '$2y$10$/VysjgReqotjsXv5R3ax/.B9EsI.VjZteMV9XSq52zFvX82iAkMyq', NULL, NULL, 0, 0, 'active', NULL, '2026-09-09 17:12:58', '2026-09-09 17:12:58'),
(37, 6, 'Sh Bala Ji Tube Alok Ji', NULL, '9891117966', '$2y$10$0gO4GlS.UvXm/U76Ibmt7OG0gdj8klUP7GjP8hMV2kczJT8rjmB0a', NULL, NULL, 0, 0, 'active', NULL, '2026-09-10 13:39:14', '2026-09-10 13:39:14'),
(38, 6, 'Varun Ji Tulsi Steel', NULL, '9999891407', '$2y$10$RLDOYR65.dy/kvZu/.hjZ.XyT5Zf0DHy2wDcQNBzdUj7x0Qyey6Te', NULL, NULL, 0, 0, 'active', NULL, '2026-09-10 13:43:50', '2026-09-10 14:06:46'),
(39, 6, 'Shubham Singhal', NULL, '9899954427', '$2y$10$ZMkbPfFcUm3fU/x.ZmrcvucMCjdGCF2RxnkBBO4wXlfOaJq8P2BmC', NULL, NULL, 0, 0, 'active', NULL, '2026-09-14 14:02:48', '2026-09-14 14:02:48'),
(40, 6, 'Abhishek Gupta', NULL, '7838843200', '$2y$10$exVH3VJmjIvcChm2tNL6eOMawABp6yZGw2L.c3smQTBK25a./OiMm', NULL, NULL, 0, 0, 'active', NULL, '2026-09-14 14:12:14', '2026-09-14 14:12:14'),
(41, 6, 'Kdjp Abhisek Steel', NULL, '9811900719', '$2y$10$i7NPtldg2PB3ExZeIL27TOrClHiWXYjBjRDLZJMHb1.ExNc0/SP/6', NULL, NULL, 0, 0, 'active', NULL, '2026-09-14 14:16:03', '2026-09-14 14:16:03'),
(42, 6, 'Ankit', NULL, '9811992405', '$2y$10$3sFHDf.LOLdtsCNUN34NhuozBM/uIa7QhzrCiG4rd.4dHg5tmFIKa', NULL, NULL, 0, 0, 'active', NULL, '2026-09-14 14:19:33', '2026-09-14 14:20:52'),
(43, 7, 'Subham Gupta', NULL, '8882972336', '$2y$10$Z9kEFS4JZ0BTlz/lO8Gl4uegk08mc0Ka/EwXnBYQWZGiX6xwdwCcG', NULL, NULL, 0, 0, 'active', NULL, '2026-09-27 11:36:57', '2026-09-27 11:36:57');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `address` varchar(190) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `state` varchar(64) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `contact_phone` varchar(15) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `whatsapp_message_logs`
--

CREATE TABLE `whatsapp_message_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `normalized_phone` varchar(20) NOT NULL,
  `template_name` varchar(100) NOT NULL,
  `trigger_source` varchar(100) DEFAULT NULL,
  `send_status` enum('success','failed') DEFAULT 'failed',
  `http_status` smallint(5) UNSIGNED DEFAULT NULL,
  `error_code` int(11) DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `api_response` text DEFAULT NULL,
  `composed_message` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `whatsapp_message_logs`
--

INSERT INTO `whatsapp_message_logs` (`id`, `user_id`, `user_type`, `phone`, `normalized_phone`, `template_name`, `trigger_source`, `send_status`, `http_status`, `error_code`, `error_message`, `api_response`, `composed_message`, `created_at`) VALUES
(1, 31, 'seller', '919811680516', '919811680516', 'text_message', 'seller_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AdWTX61DKYgik2vq9IYAKa_\"}}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(2, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AfGllBvN5XoPnzr-j1yt9IC\"}}', NULL, NULL),
(3, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_otp', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AALFmurq_gKTAlVNvvnVHvv\"}}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(4, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_otp', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AlD57eoNeyjMGQaqX8wBboo\"}}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(5, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"Ayauz5Tobxks6kQgFLHTIwU\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(6, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_otp', 'failed', 400, 100, 'Authorization Error', '{\"error\":{\"message\":\"Authorization Error\",\"code\":100,\"type\":\"OAuthException\",\"fbtrace_id\":\"ABNEoYCpGsOXYkcfRow0lg1\"}}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(7, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 400, 100, 'Authorization Error', '{\"error\":{\"message\":\"Authorization Error\",\"code\":100,\"type\":\"OAuthException\",\"fbtrace_id\":\"AMxPv_KtL1vIpB5vA22j8ws\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(8, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 404, 132001, '(#132001) Template name does not exist in the translation', '{\"error\":{\"message\":\"(#132001) Template name does not exist in the translation\",\"code\":132001,\"type\":\"OAuthException\",\"error_data\":{\"messaging_product\":\"whatsapp\",\"details\":\"template name (whatsapp88) does not exist in en_US\"},\"fbtrace_id\":\"AkjhYWjmcpOHEA1AMlxGhnt\"}}', NULL, NULL),
(9, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_fallback', 'success', 200, NULL, NULL, '{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"919910078855\",\"wa_id\":\"919910078855\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE5OTEwMDc4ODU1FQIAERgSQkEyREZBMTA0Q0QyODhGQ0U2AA==\"}]}', 'Vijay Gupta\nwww.lohamandigzb.online', NULL),
(10, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 400, 131030, '(#131030) Recipient phone number not in allowed list', '{\"error\":{\"message\":\"(#131030) Recipient phone number not in allowed list\",\"code\":131030,\"type\":\"OAuthException\",\"error_data\":{\"messaging_product\":\"whatsapp\",\"details\":\"Recipient phone number not in allowed list: Add recipient phone number to recipient list and try again.\"},\"fbtrace_id\":\"AQJj_3HmUBgUqv7hrW_ZGlD\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(11, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_otp', 'success', 200, NULL, NULL, '{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"919910078855\",\"wa_id\":\"919910078855\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE5OTEwMDc4ODU1FQIAERgSNTY0RTI1NkJGMzk4NjJCMUIwAA==\"}]}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(12, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_otp', 'success', 200, NULL, NULL, '{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"919910078855\",\"wa_id\":\"919910078855\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE5OTEwMDc4ODU1FQIAERgSODkzOEU2REQ2M0ZGNjI0NkREAA==\"}]}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(13, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 404, 132001, '(#132001) Template name does not exist in the translation', '{\"error\":{\"message\":\"(#132001) Template name does not exist in the translation\",\"code\":132001,\"type\":\"OAuthException\",\"error_data\":{\"messaging_product\":\"whatsapp\",\"details\":\"template name (whatsapp88) does not exist in en_US\"},\"fbtrace_id\":\"Ab3AGg92wzN99cWsWAC1rp_\"}}', NULL, NULL),
(14, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_fallback', 'success', 200, NULL, NULL, '{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"919910078855\",\"wa_id\":\"919910078855\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE5OTEwMDc4ODU1FQIAERgSNkNBNzhGNDMyNUUwQ0UzRTUyAA==\"}]}', 'Vijay Gupta\nwww.lohamandigzb.online', NULL),
(15, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 400, 131030, '(#131030) Recipient phone number not in allowed list', '{\"error\":{\"message\":\"(#131030) Recipient phone number not in allowed list\",\"code\":131030,\"type\":\"OAuthException\",\"error_data\":{\"messaging_product\":\"whatsapp\",\"details\":\"Recipient phone number not in allowed list: Add recipient phone number to recipient list and try again.\"},\"fbtrace_id\":\"ANXdtzwjMyf0XvDXjVtBPgT\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(16, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"A2HYY1cmCDceMEkV3esaQ01\"}}', NULL, NULL),
(17, 35, 'seller', '919911112240', '919911112240', 'whatsapp88', 'seller_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AR7kiXKStMflN_35UGcPVIp\"}}', NULL, NULL),
(18, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"ACaE78m5r7OY3_B66SOvMds\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(19, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"A8Ay4_SkgY_GBdwh0zAodTF\"}}', NULL, NULL),
(20, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AcrJnjkSW_tRqtT6Uh60ah2\"}}', NULL, NULL),
(21, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AiEYo1vQzQ3YW_EKNmkkYFK\"}}', NULL, NULL),
(22, 22, 'seller', '919910078800', '919910078800', 'whatsapp88', 'seller_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AUuR4EUrSjdcX4CwKArVIgO\"}}', NULL, NULL),
(23, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_otp', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"Ac31Qa9n-FFdW_24ageFWhY\"}}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(24, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_otp', 'failed', 400, 100, 'Authorization Error', '{\"error\":{\"message\":\"Authorization Error\",\"code\":100,\"type\":\"OAuthException\",\"fbtrace_id\":\"AfOEr_8EGISs32IqERxxR02\"}}', 'You have successfully logged in to www.lohamandigzb.online', NULL),
(25, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"Ao-R00XzFNcF9BsqKmdHZyZ\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(26, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AeI1V7U20lJoGEmG8n5j6mD\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(27, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"Apt5Ee3oHZDlRVbnWOHEkbt\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(28, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AkS6kxPQ29oAot6axSRlzl4\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', NULL),
(29, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"A1z6lk2AWxtRQgDkBg-uR_g\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-17 09:30:42'),
(30, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 404, 132001, '(#132001) Template name does not exist in the translation', '{\"error\":{\"message\":\"(#132001) Template name does not exist in the translation\",\"code\":132001,\"type\":\"OAuthException\",\"error_data\":{\"messaging_product\":\"whatsapp\",\"details\":\"template name (whatsapp88) does not exist in en_US\"},\"fbtrace_id\":\"AXBA1S57-gLac-jhEOqg-1J\"}}', NULL, '2026-09-17 10:22:58'),
(31, 30, 'buyer', '919910078855', '919910078855', 'text_message', 'buyer_login_fallback', 'success', 200, NULL, NULL, '{\"messaging_product\":\"whatsapp\",\"contacts\":[{\"input\":\"919910078855\",\"wa_id\":\"919910078855\"}],\"messages\":[{\"id\":\"wamid.HBgMOTE5OTEwMDc4ODU1FQIAERgSOTI2OUE3MDdDRDcyMThCRDk4AA==\"}]}', 'Vijay Gupta\nwww.lohamandigzb.online', '2026-09-17 10:23:00'),
(32, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 400, 131030, '(#131030) Recipient phone number not in allowed list', '{\"error\":{\"message\":\"(#131030) Recipient phone number not in allowed list\",\"code\":131030,\"type\":\"OAuthException\",\"error_data\":{\"messaging_product\":\"whatsapp\",\"details\":\"Recipient phone number not in allowed list: Add recipient phone number to recipient list and try again.\"},\"fbtrace_id\":\"AW8eW1-_KbRt5EkOMsuOOr0\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-17 10:23:19'),
(33, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AV8jW8Fy3pmjR3WScTrtvlF\"}}', NULL, '2026-09-20 15:28:01'),
(34, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AEGOc-aG9ElByKNmswaIz3J\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-22 12:05:14'),
(35, 31, 'seller', '919811680516', '919811680516', 'whatsapp88', 'seller_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AjyJ_Mx0w2k5sflQMcrdAf9\"}}', NULL, '2026-09-22 14:12:55'),
(36, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AeHt8R5DNkE_Wxoi5UkMhkp\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-23 13:16:03'),
(37, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AwowzQ21FMjmuWig1KJHpbu\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-25 14:33:16'),
(38, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"A6QAP_hDd36N9iqnFQGeW45\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-26 06:59:54'),
(39, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AwsMtYPTqOSsReP9rd9ETmS\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-26 10:21:47'),
(40, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AJqKZ5u5Y1IU2lapKKfs9mG\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-27 07:15:56'),
(41, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AvA7KwbrMPi1yXusINnVSij\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-27 11:30:21'),
(42, 30, 'buyer', '919910078855', '919910078855', 'whatsapp88', 'buyer_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"ABfTZQKSmalyz1TQy1m_gEr\"}}', NULL, '2026-09-27 11:35:00'),
(43, 1, 'admin', '919999999999', '919999999999', 'text_message', 'admin_login', 'failed', 401, 190, 'Authentication Error', '{\"error\":{\"message\":\"Authentication Error\",\"code\":190,\"type\":\"OAuthException\",\"fbtrace_id\":\"AEMLT5BpdcCqfzSHb-yC4FO\"}}', 'You have successfully logged in to www.lohamandigzb.online (Admin Panel)', '2026-09-27 11:41:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ads_type` (`type`),
  ADD KEY `idx_ads_seller` (`seller_id`),
  ADD KEY `fk_ads_category` (`category_id`);

--
-- Indexes for table `ai_analyses`
--
ALTER TABLE `ai_analyses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_type` (`analysis_type`),
  ADD KEY `idx_ai_seller` (`seller_id`),
  ADD KEY `idx_ai_product` (`product_id`),
  ADD KEY `fk_ai_user` (`user_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_entity` (`entity`,`entity_id`);

--
-- Indexes for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token_user` (`user_id`),
  ADD KEY `idx_token_token` (`token`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_brands_name` (`name`),
  ADD UNIQUE KEY `uq_brands_slug` (`slug`);

--
-- Indexes for table `bulk_prices`
--
ALTER TABLE `bulk_prices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_bulk_product` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_categories_slug` (`slug`),
  ADD KEY `idx_categories_parent` (`parent_id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_chat_pair` (`seller_id`,`buyer_id`,`product_id`),
  ADD KEY `idx_chat_seller` (`seller_id`),
  ADD KEY `idx_chat_buyer` (`buyer_id`);

--
-- Indexes for table `crm_customers`
--
ALTER TABLE `crm_customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `crm_deals`
--
ALTER TABLE `crm_deals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `crm_followups`
--
ALTER TABLE `crm_followups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `crm_meetings`
--
ALTER TABLE `crm_meetings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `crm_notes`
--
ALTER TABLE `crm_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `deal_id` (`deal_id`);

--
-- Indexes for table `crm_tasks`
--
ALTER TABLE `crm_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_inv_seller` (`seller_id`),
  ADD KEY `idx_inv_product` (`product_id`),
  ADD KEY `idx_inv_wh` (`warehouse_id`);

--
-- Indexes for table `kyc_documents`
--
ALTER TABLE `kyc_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kyc_user` (`user_id`),
  ADD KEY `idx_kyc_type` (`doc_type`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_leads_seller` (`seller_id`),
  ADD KEY `idx_leads_buyer` (`buyer_id`),
  ADD KEY `idx_leads_status` (`status`),
  ADD KEY `idx_leads_reminder` (`reminder_at`),
  ADD KEY `fk_leads_product` (`product_id`),
  ADD KEY `fk_leads_rfq` (`rfq_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_locations_state` (`state`),
  ADD KEY `idx_locations_city` (`city`),
  ADD KEY `idx_locations_city_state` (`city`,`state`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_plan_code` (`code`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_msg_chat` (`chat_id`),
  ADD KEY `idx_msg_sender` (`sender_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notif_user` (`user_id`,`is_read`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_order_number` (`order_number`),
  ADD KEY `idx_orders_buyer` (`buyer_id`),
  ADD KEY `idx_orders_seller` (`seller_id`),
  ADD KEY `idx_orders_status` (`order_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_oi_order` (`order_id`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_otp_mobile` (`mobile`),
  ADD KEY `idx_otp_email` (`email`),
  ADD KEY `idx_otp_code` (`otp`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pay_order` (`order_id`),
  ADD KEY `idx_pay_user` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_seller_slug` (`seller_id`,`slug`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_brand` (`brand_id`),
  ADD KEY `idx_products_search` (`name`),
  ADD KEY `idx_products_price` (`price`),
  ADD KEY `idx_products_status` (`status`,`is_active`),
  ADD KEY `idx_products_featured` (`is_featured`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pimg_product` (`product_id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_quote_number` (`quote_number`),
  ADD KEY `idx_quote_seller` (`seller_id`),
  ADD KEY `idx_quote_buyer` (`buyer_id`),
  ADD KEY `idx_quote_rfq` (`rfq_id`),
  ADD KEY `idx_quote_status` (`status`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reviews_seller` (`seller_id`),
  ADD KEY `idx_reviews_status` (`status`),
  ADD KEY `fk_reviews_buyer` (`buyer_id`);

--
-- Indexes for table `rfqs`
--
ALTER TABLE `rfqs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_rfq_buyer` (`buyer_id`),
  ADD KEY `idx_rfq_category` (`category_id`),
  ADD KEY `idx_rfq_status` (`status`),
  ADD KEY `idx_rfq_city` (`city`);

--
-- Indexes for table `rfq_sellers`
--
ALTER TABLE `rfq_sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_rfq_seller` (`rfq_id`,`seller_id`),
  ADD KEY `idx_rfs_seller_status` (`seller_id`,`status`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_roles_code` (`code`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_sellers_slug` (`slug`),
  ADD KEY `idx_sellers_user` (`user_id`),
  ADD KEY `idx_sellers_city_state` (`city`,`state`),
  ADD KEY `idx_sellers_gst_verified` (`gst_verified`),
  ADD KEY `idx_sellers_premium` (`is_premium`),
  ADD KEY `idx_sellers_status` (`status`);

--
-- Indexes for table `seller_media`
--
ALTER TABLE `seller_media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_smedia_seller` (`seller_id`);

--
-- Indexes for table `seller_membership`
--
ALTER TABLE `seller_membership`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sm_seller` (`seller_id`),
  ADD KEY `idx_sm_status` (`status`),
  ADD KEY `fk_sm_plan` (`plan_id`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_st_seller` (`seller_id`),
  ADD KEY `idx_st_product` (`product_id`);

--
-- Indexes for table `transporters`
--
ALTER TABLE `transporters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tr_user` (`user_id`);

--
-- Indexes for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tb_order` (`order_id`),
  ADD KEY `idx_tb_transporter` (`transporter_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_mobile` (`mobile`),
  ADD UNIQUE KEY `uq_users_email` (`email`),
  ADD KEY `idx_users_role` (`role_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wh_seller` (`seller_id`);

--
-- Indexes for table `whatsapp_message_logs`
--
ALTER TABLE `whatsapp_message_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_phone` (`phone`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_trigger_source` (`trigger_source`),
  ADD KEY `idx_send_status` (`send_status`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ai_analyses`
--
ALTER TABLE `ai_analyses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bulk_prices`
--
ALTER TABLE `bulk_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `crm_customers`
--
ALTER TABLE `crm_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `crm_deals`
--
ALTER TABLE `crm_deals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `crm_followups`
--
ALTER TABLE `crm_followups`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `crm_meetings`
--
ALTER TABLE `crm_meetings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `crm_notes`
--
ALTER TABLE `crm_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `crm_tasks`
--
ALTER TABLE `crm_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kyc_documents`
--
ALTER TABLE `kyc_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rfqs`
--
ALTER TABLE `rfqs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `rfq_sellers`
--
ALTER TABLE `rfq_sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `seller_media`
--
ALTER TABLE `seller_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seller_membership`
--
ALTER TABLE `seller_membership`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transporters`
--
ALTER TABLE `transporters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `whatsapp_message_logs`
--
ALTER TABLE `whatsapp_message_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD CONSTRAINT `fk_ads_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ads_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ai_analyses`
--
ALTER TABLE `ai_analyses`
  ADD CONSTRAINT `fk_ai_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ai_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ai_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `auth_tokens`
--
ALTER TABLE `auth_tokens`
  ADD CONSTRAINT `fk_token_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bulk_prices`
--
ALTER TABLE `bulk_prices`
  ADD CONSTRAINT `fk_bulk_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `fk_chat_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_chat_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `fk_inv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inv_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inv_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `kyc_documents`
--
ALTER TABLE `kyc_documents`
  ADD CONSTRAINT `fk_kyc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `fk_leads_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_leads_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_leads_rfq` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_leads_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_chat` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orders_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pay_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_products_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_pimg_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quotations`
--
ALTER TABLE `quotations`
  ADD CONSTRAINT `fk_quote_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_quote_rfq` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_quote_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rfqs`
--
ALTER TABLE `rfqs`
  ADD CONSTRAINT `fk_rfq_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rfq_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `rfq_sellers`
--
ALTER TABLE `rfq_sellers`
  ADD CONSTRAINT `fk_rfs_rfq` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rfs_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sellers`
--
ALTER TABLE `sellers`
  ADD CONSTRAINT `fk_sellers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_media`
--
ALTER TABLE `seller_media`
  ADD CONSTRAINT `fk_smedia_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seller_membership`
--
ALTER TABLE `seller_membership`
  ADD CONSTRAINT `fk_sm_plan` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`),
  ADD CONSTRAINT `fk_sm_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `fk_st_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_st_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transporters`
--
ALTER TABLE `transporters`
  ADD CONSTRAINT `fk_tr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `transport_bookings`
--
ALTER TABLE `transport_bookings`
  ADD CONSTRAINT `fk_tb_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_tb_transporter` FOREIGN KEY (`transporter_id`) REFERENCES `transporters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD CONSTRAINT `fk_wh_seller` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
