-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 12, 2026 at 05:12 PM
-- Server version: 8.4.7
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `loha_mandi`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

DROP TABLE IF EXISTS `advertisements`;
CREATE TABLE IF NOT EXISTS `advertisements` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('homepage_banner','sponsored_product','featured_seller','category_banner','popup') COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `clicks` int UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ads_type` (`type`),
  KEY `idx_ads_seller` (`seller_id`),
  KEY `fk_ads_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`id`, `seller_id`, `title`, `type`, `image`, `link`, `category_id`, `product_id`, `start_date`, `end_date`, `clicks`, `status`, `created_at`) VALUES
(1, NULL, 'Summer Steel Sale ? Up to 15% Off', 'homepage_banner', NULL, '/catalog', NULL, NULL, NULL, NULL, 1, 1, '2026-08-07 22:38:56'),
(2, 7, 'Sharma Iron Traders ? Trusted Supplier', 'featured_seller', NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, '2026-08-07 22:38:56'),
(3, 7, 'Fe 500D TMT Bar 12mm', 'sponsored_product', NULL, NULL, 1, 4, NULL, NULL, 1, 1, '2026-08-07 22:38:56'),
(4, NULL, 'TMT Bars Mega Deal', 'category_banner', NULL, '/catalog/category/tmt-bars', 1, NULL, NULL, NULL, 1, 1, '2026-08-07 22:38:56'),
(5, NULL, 'Renamed Popup', 'popup', NULL, '/catalog', NULL, NULL, NULL, NULL, 0, 1, '2026-08-07 22:38:56');

-- --------------------------------------------------------

--
-- Table structure for table `ai_analyses`
--

DROP TABLE IF EXISTS `ai_analyses`;
CREATE TABLE IF NOT EXISTS `ai_analyses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `seller_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `analysis_type` enum('fraud_score','duplicate','recommendation','description','keywords','chat') COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `score` decimal(8,2) DEFAULT NULL,
  `data` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ai_type` (`analysis_type`),
  KEY `idx_ai_seller` (`seller_id`),
  KEY `idx_ai_product` (`product_id`),
  KEY `fk_ai_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(15, 22, 7, NULL, 'fraud_score', 'Sharma Iron Traders', 18.00, '{\"risk\": \"low\", \"flags\": [\"GSTIN missing or unverified\"]}', '2026-08-12 17:48:43');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE IF NOT EXISTS `audit_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `action` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` bigint UNSIGNED DEFAULT NULL,
  `old_value` json DEFAULT NULL,
  `new_value` json DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_entity` (`entity`,`entity_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(23, 1, 'product_moderate', 'product', 15, '\"pending\"', '\"approved\"', '127.0.0.1', '2026-08-12 19:01:55');

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

DROP TABLE IF EXISTS `auth_tokens`;
CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `revoked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_token_user` (`user_id`),
  KEY `idx_token_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_brands_name` (`name`),
  UNIQUE KEY `uq_brands_slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

DROP TABLE IF EXISTS `bulk_prices`;
CREATE TABLE IF NOT EXISTS `bulk_prices` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `min_qty` decimal(12,3) NOT NULL,
  `max_qty` decimal(12,3) DEFAULT NULL,
  `unit_price` decimal(14,2) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bulk_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(140) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`),
  KEY `idx_categories_parent` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(18, NULL, 'Construction Material', 'construction-material', NULL, NULL, 'Construction materials', 1, 0, '2026-08-04 15:57:30', '2026-08-04 15:57:30');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

DROP TABLE IF EXISTS `chats`;
CREATE TABLE IF NOT EXISTS `chats` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `rfq_id` bigint UNSIGNED DEFAULT NULL,
  `last_message` text COLLATE utf8mb4_unicode_ci,
  `last_message_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_chat_pair` (`seller_id`,`buyer_id`,`product_id`),
  KEY `idx_chat_seller` (`seller_id`),
  KEY `idx_chat_buyer` (`buyer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_customers`
--

DROP TABLE IF EXISTS `crm_customers`;
CREATE TABLE IF NOT EXISTS `crm_customers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `name` varchar(190) NOT NULL,
  `company` varchar(190) DEFAULT NULL,
  `email` varchar(190) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `source` varchar(32) NOT NULL DEFAULT 'manual',
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_deals`
--

DROP TABLE IF EXISTS `crm_deals`;
CREATE TABLE IF NOT EXISTS `crm_deals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `value` decimal(14,2) NOT NULL DEFAULT '0.00',
  `stage` enum('prospecting','qualified','quotation','negotiation','won','lost') NOT NULL DEFAULT 'prospecting',
  `expected_close` date DEFAULT NULL,
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_followups`
--

DROP TABLE IF EXISTS `crm_followups`;
CREATE TABLE IF NOT EXISTS `crm_followups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `deal_id` bigint UNSIGNED DEFAULT NULL,
  `channel` varchar(24) NOT NULL DEFAULT 'call',
  `scheduled_at` datetime DEFAULT NULL,
  `note` text,
  `status` varchar(16) NOT NULL DEFAULT 'pending',
  `done_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_meetings`
--

DROP TABLE IF EXISTS `crm_meetings`;
CREATE TABLE IF NOT EXISTS `crm_meetings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `deal_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `starts_at` datetime DEFAULT NULL,
  `ends_at` datetime DEFAULT NULL,
  `location` varchar(190) DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT 'scheduled',
  `notes` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_notes`
--

DROP TABLE IF EXISTS `crm_notes`;
CREATE TABLE IF NOT EXISTS `crm_notes` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `deal_id` bigint UNSIGNED DEFAULT NULL,
  `body` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `customer_id` (`customer_id`),
  KEY `deal_id` (`deal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `crm_tasks`
--

DROP TABLE IF EXISTS `crm_tasks`;
CREATE TABLE IF NOT EXISTS `crm_tasks` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `customer_id` bigint UNSIGNED DEFAULT NULL,
  `deal_id` bigint UNSIGNED DEFAULT NULL,
  `title` varchar(190) NOT NULL,
  `category` varchar(32) NOT NULL DEFAULT 'task',
  `due_at` datetime DEFAULT NULL,
  `priority` varchar(16) NOT NULL DEFAULT 'medium',
  `status` varchar(16) NOT NULL DEFAULT 'open',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `seller_id` (`seller_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

DROP TABLE IF EXISTS `inventory`;
CREATE TABLE IF NOT EXISTS `inventory` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `warehouse` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `stock_qty` decimal(14,3) NOT NULL DEFAULT '0.000',
  `low_stock_threshold` decimal(14,3) NOT NULL DEFAULT '0.000',
  `batch_no` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barcode` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qr_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_inv_seller` (`seller_id`),
  KEY `idx_inv_product` (`product_id`),
  KEY `idx_inv_wh` (`warehouse_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kyc_documents`
--

DROP TABLE IF EXISTS `kyc_documents`;
CREATE TABLE IF NOT EXISTS `kyc_documents` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `doc_type` enum('gst','pan','aadhaar','business_address_proof','bank','certificate','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `doc_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `verified_by` bigint UNSIGNED DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `reject_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kyc_user` (`user_id`),
  KEY `idx_kyc_type` (`doc_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

DROP TABLE IF EXISTS `leads`;
CREATE TABLE IF NOT EXISTS `leads` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `buyer_id` bigint UNSIGNED DEFAULT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `rfq_id` bigint UNSIGNED DEFAULT NULL,
  `name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `lead_type` enum('rfq','product_inquiry','contact','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'product_inquiry',
  `status` enum('new','interested','hot','closed','rejected','lost','won') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `call_history` json DEFAULT NULL,
  `reminder_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_leads_seller` (`seller_id`),
  KEY `idx_leads_buyer` (`buyer_id`),
  KEY `idx_leads_status` (`status`),
  KEY `idx_leads_reminder` (`reminder_at`),
  KEY `fk_leads_product` (`product_id`),
  KEY `fk_leads_rfq` (`rfq_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

DROP TABLE IF EXISTS `locations`;
CREATE TABLE IF NOT EXISTS `locations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `city` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_locations_state` (`state`),
  KEY `idx_locations_city` (`city`),
  KEY `idx_locations_city_state` (`city`,`state`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

DROP TABLE IF EXISTS `membership_plans`;
CREATE TABLE IF NOT EXISTS `membership_plans` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `duration_days` int UNSIGNED NOT NULL DEFAULT '365',
  `features` json DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_plan_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `name`, `code`, `price`, `duration_days`, `features`, `status`, `created_at`) VALUES
(1, 'Silver', 'silver', 4999.00, 365, '{\"leads_limit\": 500, \"top_listing\": 1, \"products_limit\": 50, \"verified_badge\": 1}', 1, '2026-08-04 15:57:30'),
(2, 'Gold', 'gold', 14999.00, 365, '{\"analytics\": 1, \"leads_limit\": 2000, \"top_listing\": 3, \"products_limit\": 200, \"verified_badge\": 1}', 1, '2026-08-04 15:57:30'),
(3, 'Diamond', 'diamond', 39999.00, 365, '{\"analytics\": 1, \"leads_limit\": 0, \"top_listing\": 5, \"products_limit\": 0, \"verified_badge\": 1, \"priority_support\": 1}', 1, '2026-08-04 15:57:30'),
(4, 'Seller — Yearly', 'seller_yearly', 9999.00, 365, '{\"leads_limit\": 2000, \"products_limit\": 200, \"verified_badge\": 1, \"business_listing\": 1}', 1, '2026-08-06 16:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `chat_id` bigint UNSIGNED NOT NULL,
  `sender_id` bigint UNSIGNED NOT NULL,
  `message_type` enum('text','image','pdf','invoice','voice','quotation') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `body` text COLLATE utf8mb4_unicode_ci,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_msg_chat` (`chat_id`),
  KEY `idx_msg_sender` (`sender_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci,
  `type` enum('sms','email','whatsapp','push','in_app') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_app',
  `ref_type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ref_id` bigint UNSIGNED DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`,`is_read`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(20, 16, 'New message from Harsh Garg', 'Sure, quote valid till 15 Sep', 'in_app', 'chat', 2, 0, '2026-08-11 22:01:41');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `seller_id` bigint UNSIGNED NOT NULL,
  `quote_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(14,2) NOT NULL,
  `gst_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `shipping` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total` decimal(14,2) NOT NULL,
  `payment_method` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `order_status` enum('confirmed','dispatched','in_transit','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirmed',
  `lr_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transport_details` text COLLATE utf8mb4_unicode_ci,
  `shipping_address` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_order_number` (`order_number`),
  KEY `idx_orders_buyer` (`buyer_id`),
  KEY `idx_orders_seller` (`seller_id`),
  KEY `idx_orders_status` (`order_status`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `unit` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ton',
  `unit_price` decimal(14,2) NOT NULL,
  `total` decimal(14,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_oi_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

DROP TABLE IF EXISTS `otps`;
CREATE TABLE IF NOT EXISTS `otps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` enum('register','login','forgot','verify_mobile','verify_email') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'register',
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_otp_mobile` (`mobile`),
  KEY `idx_otp_email` (`email`),
  KEY `idx_otp_code` (`otp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `method` enum('upi','net_banking','debit_card','credit_card','wallet','cod','razorpay','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `txn_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','success','failed','refunded') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gateway_response` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pay_order` (`order_id`),
  KEY `idx_pay_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `amount`, `method`, `txn_id`, `status`, `gateway_response`, `created_at`) VALUES
(4, NULL, 20, 9999.00, 'razorpay', 'pay_d7bd43880ee8', 'success', '{\"kind\": \"seller_subscription\", \"plan_id\": 4, \"rzp_order_id\": \"order_13ad1455bea482060c77\"}', '2026-08-08 18:26:08'),
(6, NULL, 22, 9999.00, 'razorpay', 'pay_72f8f7ee9203', 'success', '{\"kind\": \"seller_subscription\", \"plan_id\": 4, \"rzp_order_id\": \"order_c3664c0278b0840fa183\"}', '2026-08-09 10:40:25'),
(7, NULL, 23, 9999.00, 'razorpay', 'pay_f9c516478657', 'success', '{\"kind\": \"seller_subscription\", \"plan_id\": 4, \"rzp_order_id\": \"order_856c4bbff9a0c2753e66\"}', '2026-08-09 16:09:20');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `brand_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(210) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `specifications` json DEFAULT NULL,
  `weight` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dimensions` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hsn_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_rate` decimal(4,2) NOT NULL DEFAULT '18.00',
  `moq` decimal(12,3) NOT NULL DEFAULT '0.000',
  `unit` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ton',
  `delivery_time` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(14,2) DEFAULT NULL,
  `offer_price` decimal(14,2) DEFAULT NULL,
  `available_stock` decimal(14,3) NOT NULL DEFAULT '0.000',
  `is_bulk` tinyint(1) NOT NULL DEFAULT '0',
  `location` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_sponsored` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `view_count` int UNSIGNED NOT NULL DEFAULT '0',
  `inquiry_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_products_seller_slug` (`seller_id`,`slug`),
  KEY `idx_products_category` (`category_id`),
  KEY `idx_products_brand` (`brand_id`),
  KEY `idx_products_search` (`name`),
  KEY `idx_products_price` (`price`),
  KEY `idx_products_status` (`status`,`is_active`),
  KEY `idx_products_featured` (`is_featured`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `seller_id`, `category_id`, `brand_id`, `name`, `slug`, `description`, `specifications`, `weight`, `dimensions`, `grade`, `hsn_code`, `gst_rate`, `moq`, `unit`, `delivery_time`, `price`, `offer_price`, `available_stock`, `is_bulk`, `location`, `is_featured`, `is_sponsored`, `is_active`, `status`, `view_count`, `inquiry_count`, `created_at`, `updated_at`) VALUES
(4, 7, 1, 1, 'Fe 500D TMT Bar 12mm (IS 1786)', 'fe-500d-tmt-bar-12mm-is-1786-0872dd', 'High-grade Fe 500D TMT bars conforming to IS 1786. Ideal for residential and commercial construction.', '{\"end\": \"U\", \"length\": \"12 m\", \"ductility\": \"Fe 500D\", \"yielding_stress\": \"500 MPa\"}', NULL, NULL, 'Fe 500D', '72142090', 18.00, 10.000, 'ton', '12mm dia', 64000.00, 61000.00, 120.000, 1, 'Ghaziabad', 1, 0, 1, 'approved', 14, 0, '2026-08-07 18:10:41', '2026-08-11 21:44:25'),
(5, 7, 2, 2, 'MS Plate 6mm CR 4x8', 'ms-plate-6mm-cr-4x8-6cc267', 'Cold-rolled mild steel plate, 6mm thickness, 4x8 feet size. Ideal for fabrication and structural use.', NULL, NULL, NULL, 'IS 2062 E250', '72085210', 18.00, 5.000, 'ton', '6mm', 58000.00, 0.00, 200.000, 1, 'Ghaziabad', 1, 0, 1, 'approved', 10, 0, '2026-08-07 18:10:41', '2026-08-12 19:02:44'),
(6, 7, 3, 3, 'MS Angle 50x50x5 (IS 808)', 'ms-angle-50x50x5-is-808-249971', 'Equal angle 50x50x5mm meeting IS 808, standard stock length 6m.', '{\"leg_a\": \"50\", \"leg_b\": \"50\", \"length\": \"6m\", \"thickness\": \"5\"}', NULL, NULL, 'Fe 500', '72161010', 18.00, 3.000, 'ton', '50x50x5', 57500.00, 54000.00, 80.000, 1, 'Ghaziabad', 1, 0, 1, 'approved', 2, 0, '2026-08-05 18:10:41', '2026-08-12 17:51:33'),
(7, 7, 4, 2, 'MS Channel 100x50x5', 'ms-channel-100x50x5-c418dd', 'MS channel 100x50mm, IS 808 conforming, hot-rolled.', NULL, NULL, NULL, 'Fe 330', '72163110', 18.00, 660.000, 'ton', '100x50x5', 56500.00, 0.00, 60.000, 1, 'Ghaziabad', 0, 0, 1, 'approved', 1, 0, '2026-08-04 18:10:41', '2026-08-11 12:00:08'),
(8, 7, 1, 1, 'Fe 550D TMT Bar 16mm (IS 1786)', 'fe-550d-tmt-bar-16mm-is-1786-e0529e', 'Fe 550D TMT bars for heavy construction, IS 1786.', NULL, NULL, NULL, 'Fe 550D', '72142090', 18.00, 10.000, 'ton', '16mm dia', 64000.00, 60800.00, 90.000, 1, 'Ghaziabad', 0, 0, 1, 'approved', 1, 0, '2026-08-07 18:13:54', '2026-08-11 13:09:30'),
(15, 13, 2, 3, 'MS Plates', 'ms-plates-39c80e', 'MS Plates', NULL, NULL, NULL, 'Fe 330', NULL, 18.00, 0.000, 'ton', NULL, 59000.00, 59000.00, 1000000.000, 1, NULL, 0, 0, 1, 'approved', 0, 0, '2026-08-12 13:17:20', '2026-08-12 13:31:55'),
(16, 13, 2, 3, 'MS Chequered Plates', 'ms-chequered-plates-1ad6d9', 'Looking for high-quality MS Chequered Plates? Look no further! Our MS Chequered Plates come in a thickness of all size, making them durable and strong for a variety of applications.', NULL, NULL, NULL, 'IS 2062 E250', NULL, 18.00, 0.000, 'ton', NULL, 70000.00, 70000.00, 1000.000, 0, NULL, 0, 0, 1, 'approved', 1, 0, '2026-08-12 13:23:57', '2026-08-12 19:02:57'),
(17, 13, 12, 7, 'HR Sheets', 'hr-sheets-3e2c43', 'Available in all desired size', NULL, NULL, NULL, NULL, NULL, 18.00, 0.000, 'ton', NULL, 60500.00, NULL, 10000.000, 1, NULL, 0, 0, 1, 'approved', 0, 0, '2026-08-12 13:27:57', '2026-08-12 13:31:38');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pimg_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(15, 17, 'products/1786541277_f3e5db0455a18f12e340.png', 1, 1, '2026-08-12 18:57:57');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

DROP TABLE IF EXISTS `quotations`;
CREATE TABLE IF NOT EXISTS `quotations` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rfq_id` bigint UNSIGNED DEFAULT NULL,
  `seller_id` bigint UNSIGNED NOT NULL,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `quote_number` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `unit` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ton',
  `unit_price` decimal(14,2) NOT NULL,
  `base_amount` decimal(14,2) NOT NULL,
  `gst_rate` decimal(4,2) NOT NULL DEFAULT '18.00',
  `gst_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `discount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `transport_charge` decimal(14,2) NOT NULL DEFAULT '0.00',
  `other_charges` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(14,2) NOT NULL,
  `payment_terms` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('draft','sent','accepted','rejected','expired') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_quote_number` (`quote_number`),
  KEY `idx_quote_seller` (`seller_id`),
  KEY `idx_quote_buyer` (`buyer_id`),
  KEY `idx_quote_rfq` (`rfq_id`),
  KEY `idx_quote_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `review` text COLLATE utf8mb4_unicode_ci,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seller_reply` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reviews_seller` (`seller_id`),
  KEY `idx_reviews_status` (`status`),
  KEY `fk_reviews_buyer` (`buyer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfqs`
--

DROP TABLE IF EXISTS `rfqs`;
CREATE TABLE IF NOT EXISTS `rfqs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `buyer_id` bigint UNSIGNED NOT NULL,
  `product_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int UNSIGNED DEFAULT NULL,
  `quantity` decimal(14,3) NOT NULL,
  `unit` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ton',
  `specification` text COLLATE utf8mb4_unicode_ci,
  `required_date` date DEFAULT NULL,
  `delivery_location` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `budget_min` decimal(14,2) DEFAULT NULL,
  `budget_max` decimal(14,2) DEFAULT NULL,
  `document_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('open','quoted','accepted','closed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `source` enum('web','app','phone') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_rfq_buyer` (`buyer_id`),
  KEY `idx_rfq_category` (`category_id`),
  KEY `idx_rfq_status` (`status`),
  KEY `idx_rfq_city` (`city`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rfqs`
--

INSERT INTO `rfqs` (`id`, `buyer_id`, `product_name`, `category_id`, `quantity`, `unit`, `specification`, `required_date`, `delivery_location`, `city`, `state`, `budget_min`, `budget_max`, `document_path`, `photo_path`, `status`, `source`, `created_at`, `updated_at`) VALUES
(6, 16, 'TMT BAR', 1, 2.000, 'ton', 'GST included', '2026-09-03', 'Sonipat', 'ghaziabad', 'Uttar Pradesh', NULL, NULL, NULL, NULL, 'open', 'web', '2026-08-11 11:03:08', '2026-08-11 11:03:08');

-- --------------------------------------------------------

--
-- Table structure for table `rfq_sellers`
--

DROP TABLE IF EXISTS `rfq_sellers`;
CREATE TABLE IF NOT EXISTS `rfq_sellers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `rfq_id` bigint UNSIGNED NOT NULL,
  `seller_id` bigint UNSIGNED NOT NULL,
  `matched_by` enum('auto','manual','public') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto',
  `score` decimal(5,2) NOT NULL DEFAULT '0.00',
  `status` enum('sent','viewed','quoted','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sent',
  `viewed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rfq_seller` (`rfq_id`,`seller_id`),
  KEY `idx_rfs_seller_status` (`seller_id`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` tinyint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

DROP TABLE IF EXISTS `sellers`;
CREATE TABLE IF NOT EXISTS `sellers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `business_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gstin` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_name` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `owner_phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_category_id` int UNSIGNED DEFAULT NULL,
  `address_line1` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_line2` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `business_hours` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `years_in_business` smallint UNSIGNED DEFAULT NULL,
  `employees_count` smallint UNSIGNED DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_unicode_ci,
  `brands` text COLLATE utf8mb4_unicode_ci,
  `infrastructure` text COLLATE utf8mb4_unicode_ci,
  `website` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gst_verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `is_premium` tinyint(1) NOT NULL DEFAULT '0',
  `rating_avg` decimal(2,1) NOT NULL DEFAULT '0.0',
  `rating_count` int UNSIGNED NOT NULL DEFAULT '0',
  `status` enum('pending','active','rejected','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sellers_slug` (`slug`),
  KEY `idx_sellers_user` (`user_id`),
  KEY `idx_sellers_city_state` (`city`,`state`),
  KEY `idx_sellers_gst_verified` (`gst_verified`),
  KEY `idx_sellers_premium` (`is_premium`),
  KEY `idx_sellers_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `user_id`, `business_name`, `slug`, `gstin`, `owner_name`, `owner_phone`, `business_category_id`, `address_line1`, `address_line2`, `city`, `state`, `pincode`, `latitude`, `longitude`, `business_hours`, `years_in_business`, `employees_count`, `logo`, `banner`, `about`, `brands`, `infrastructure`, `website`, `gst_verified`, `is_verified`, `is_premium`, `rating_avg`, `rating_count`, `status`, `created_at`, `updated_at`) VALUES
(7, 22, 'Sharma Iron Traders', 'sharma-iron-traders', NULL, 'Rajesh Sharma', '9811000001', 1, NULL, NULL, 'Ghaziabad', 'Uttar Pradesh', '201001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 4.8, 24, 'active', '2026-08-07 18:10:28', '2026-08-11 12:49:12'),
(13, 20, 'M L ISPAT', 'm-l-ispat-22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'active', '2026-08-09 05:10:25', '2026-08-12 18:39:04'),
(14, 23, 'Rathi Steel', 'rathi-steel-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0.0, 0, 'active', '2026-08-09 10:39:20', '2026-08-11 11:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `seller_media`
--

DROP TABLE IF EXISTS `seller_media`;
CREATE TABLE IF NOT EXISTS `seller_media` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `media_type` enum('image','video','certificate','catalog','brochure') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_smedia_seller` (`seller_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seller_membership`
--

DROP TABLE IF EXISTS `seller_membership`;
CREATE TABLE IF NOT EXISTS `seller_membership` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `plan_id` int UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `status` enum('active','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sm_seller` (`seller_id`),
  KEY `idx_sm_status` (`status`),
  KEY `fk_sm_plan` (`plan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seller_membership`
--

INSERT INTO `seller_membership` (`id`, `seller_id`, `plan_id`, `start_date`, `end_date`, `amount`, `status`, `created_at`) VALUES
(4, 13, 4, '2026-08-09', '2027-08-09', 9999.00, 'active', '2026-08-09 10:40:25'),
(5, 14, 4, '2026-08-09', '2027-08-09', 9999.00, 'active', '2026-08-09 16:09:20');

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE IF NOT EXISTS `stock_transfers` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `from_warehouse_id` bigint UNSIGNED DEFAULT NULL,
  `to_warehouse_id` bigint UNSIGNED NOT NULL,
  `quantity` decimal(14,3) NOT NULL DEFAULT '0.000',
  `status` enum('pending','in_transit','arrived','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_st_seller` (`seller_id`),
  KEY `idx_st_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transporters`
--

DROP TABLE IF EXISTS `transporters`;
CREATE TABLE IF NOT EXISTS `transporters` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `fleet_name` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_count` int UNSIGNED NOT NULL DEFAULT '0',
  `route_from` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `route_to` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tr_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transporters`
--

INSERT INTO `transporters` (`id`, `user_id`, `fleet_name`, `vehicle_count`, `route_from`, `route_to`, `verified`, `created_at`) VALUES
(1, 15, 'Ghaziabad Freight Lines', 3, 'Ghaziabad', 'Delhi', 0, '2026-08-07 13:22:43');

-- --------------------------------------------------------

--
-- Table structure for table `transport_bookings`
--

DROP TABLE IF EXISTS `transport_bookings`;
CREATE TABLE IF NOT EXISTS `transport_bookings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `transporter_id` bigint UNSIGNED NOT NULL,
  `pickup_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `drop_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `load_tonnage` decimal(10,3) NOT NULL,
  `vehicle_type` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `freight_charge` decimal(14,2) DEFAULT NULL,
  `status` enum('requested','confirmed','in_transit','delivered','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'requested',
  `tracking` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_tb_order` (`order_id`),
  KEY `idx_tb_transporter` (`transporter_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transport_bookings`
--

INSERT INTO `transport_bookings` (`id`, `order_id`, `transporter_id`, `pickup_address`, `drop_address`, `load_tonnage`, `vehicle_type`, `freight_charge`, `status`, `tracking`, `created_at`) VALUES
(1, NULL, 1, 'Ghaziabad Karkari Bazar', 'Delhi Anand Vihar', 8.500, 'Trailer', 12500.00, 'in_transit', '[{\"at\": \"2026-08-07 13:23:35\", \"by\": \"Ravi Kumar\", \"note\": \"Vehicle assigned, MN-09-5566\", \"status\": \"confirmed\"}, {\"at\": \"2026-08-07 13:23:37\", \"by\": \"Ravi Kumar\", \"note\": \"Departed Ghaziabad, ETA 4pm\", \"status\": \"in_transit\"}]', '2026-08-07 13:23:21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` tinyint UNSIGNED NOT NULL DEFAULT '5',
  `name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT '0',
  `mobile_verified` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('active','inactive','suspended','deleted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_mobile` (`mobile`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `mobile`, `password`, `google_id`, `avatar`, `email_verified`, `mobile_verified`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Super Admin', 'admin@lohamandi.com', '9999999999', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 1, 1, 'suspended', NULL, '2026-08-04 22:04:27', '2026-08-11 11:12:47'),
(2, 4, 'Moderator', 'moderator@lohamandi.com', '9999999998', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 1, 1, 'active', NULL, '2026-08-04 22:04:27', '2026-08-10 17:01:29'),
(15, 7, 'Ravi Kumar', 'ravi@test.com', '9881223300', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-07 13:22:42', '2026-08-10 17:01:29'),
(16, 5, 'Amit Buyer', 'amit@test.com', '9771223300', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-07 13:24:31', '2026-08-10 17:01:29'),
(20, 6, 'Harsh Garg', NULL, '9874563210', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-08 12:56:07', '2026-08-11 11:12:27'),
(22, 6, 'Harish Sharma', NULL, '9910078855', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-09 05:10:25', '2026-08-11 12:49:12'),
(23, 6, 'AMIT RATHI', NULL, '9857654321', '$2y$10$xVlPNvccaI8phI3gMml.ZuVvbhS8uQ8y8hyuDsibTFrBFd2.CPKjG', NULL, NULL, 0, 0, 'active', NULL, '2026-08-09 10:39:19', '2026-08-11 11:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

DROP TABLE IF EXISTS `warehouses`;
CREATE TABLE IF NOT EXISTS `warehouses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `seller_id` bigint UNSIGNED NOT NULL,
  `name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(190) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pincode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_phone` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wh_seller` (`seller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
