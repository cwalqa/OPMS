-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 23, 2024 at 11:43 AM
-- Server version: 5.7.33
-- PHP Version: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `colorwrapinc`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_role`
--

CREATE TABLE `admin_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_role`
--

INSERT INTO `admin_role` (`id`, `admin_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL),
(3, 3, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_03_25_124141_create_quickbooks_tokens_table', 1),
(5, '2024_07_08_185941_quickbooks-estimates', 1),
(6, '2024_07_12_175941_quickbooks_customers', 1),
(7, '2024_07_13_183053_customers_and_items', 2),
(8, '2024_10_21_110137_add_two_factor_columns_to_quickbooks_customers_table', 3),
(9, '2024_10_21_124510_create_quickbooks_admin_table', 4),
(10, '2024_10_21_132833_create_password_resets_table', 5),
(11, '2024_10_21_174229_add_purchase_order_number_and_total_amount_to_quickbooks_estimates_table', 6),
(12, '2024_10_21_174358_add_amount_to_quickbooks_estimate_items_table', 7),
(13, '2024_10_21_185930_add_qr_code_path_to_quickbooks_estimate_items_table', 8),
(14, '2024_10_22_071505_create_permissions_table', 9),
(15, '2024_10_22_071505_create_roles_table', 9),
(16, '2024_10_22_071506_create_admin_role_table', 9),
(17, '2024_10_22_071506_create_role_permission_table', 9),
(18, '2024_10_22_110714_create_admin_role_table', 10),
(19, '2024_10_22_165543_add_status_and_qr_code_to_quickbooks_estimates_table_to_quickbooks_estimate_table', 11),
(20, '2024_10_22_170523_create_notifications_table', 12),
(21, '2024_10_22_211227_create_production_lines_table', 13),
(22, '2024_10_22_221211_add_schedule_date_to_quickbooks_estimates_table', 14),
(23, '2024_10_22_222258_create_production_schedules_table', 15),
(24, '2024_10_22_230920_production_schedules', 16);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('1d417128-3def-4066-948e-48bbf4d8e215', 'App\\Notifications\\OrderApproved', 'App\\Models\\QuickbooksAdmin', 3, '{\"order_id\":1,\"purchase_order_number\":\"DPL-0001-24\",\"customer_name\":\"Walker\",\"total_amount\":\"310000.00\"}', NULL, '2024-10-22 23:06:56', '2024-10-22 23:06:56'),
('5a98ed63-95dc-459c-849a-a01824a742fe', 'App\\Notifications\\OrderApproved', 'App\\Models\\QuickbooksAdmin', 3, '{\"order_id\":6,\"purchase_order_number\":\"DPL-0006-24\",\"customer_name\":\"Walker\",\"total_amount\":\"501000.00\"}', NULL, '2024-10-23 00:31:19', '2024-10-23 00:31:19');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('walker@datapluzz.com', '$2y$12$zosNhv.xK75lvDOqJ01zturX6ywrio3ly8f70NgcFZ.2ABBgUgBHC', '2024-10-21 17:34:54');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'user_management', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(2, 'order_management', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(3, 'line_scheduling', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(4, 'production_management', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(5, 'packaging_delivery', '2024-10-22 11:23:12', '2024-10-22 11:23:12');

-- --------------------------------------------------------

--
-- Table structure for table `production_lines`
--

CREATE TABLE `production_lines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `line_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `max_quantity` int(11) NOT NULL,
  `line_manager_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_order_id` bigint(20) UNSIGNED DEFAULT NULL,
  `line_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `current_production` int(11) NOT NULL DEFAULT '0',
  `order_deadline` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_lines`
--

INSERT INTO `production_lines` (`id`, `line_name`, `max_quantity`, `line_manager_id`, `assigned_order_id`, `line_status`, `current_production`, `order_deadline`, `created_at`, `updated_at`) VALUES
(1, 'Line A', 1000, 3, NULL, 'available', 0, NULL, '2024-10-23 01:28:50', '2024-10-23 01:35:19'),
(2, 'Line B', 1000, 3, NULL, 'available', 0, NULL, '2024-10-23 01:31:30', '2024-10-23 01:31:30');

-- --------------------------------------------------------

--
-- Table structure for table `production_schedules`
--

CREATE TABLE `production_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `line_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` float NOT NULL,
  `schedule_date` date NOT NULL,
  `deadline_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `production_schedules`
--

INSERT INTO `production_schedules` (`id`, `item_id`, `line_id`, `quantity`, `schedule_date`, `deadline_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1000, '2024-10-28', '2024-11-04', '2024-10-23 14:20:25', '2024-10-23 14:20:25'),
(2, 5, 2, 1000, '2024-10-29', '2024-11-08', '2024-10-23 14:21:10', '2024-10-23 14:21:10'),
(3, 5, 1, 1000, '2024-10-30', '2024-11-05', '2024-10-23 14:26:46', '2024-10-23 14:26:46'),
(4, 5, 1, 1000, '2024-10-29', '2024-11-05', '2024-10-23 14:30:47', '2024-10-23 14:30:47'),
(5, 14, 1, 1000, '2024-11-06', '2024-11-09', '2024-10-23 14:33:04', '2024-10-23 14:33:04');

-- --------------------------------------------------------

--
-- Table structure for table `quickbooks_admin`
--

CREATE TABLE `quickbooks_admin` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_admin`
--

INSERT INTO `quickbooks_admin` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `two_factor_code`, `two_factor_expires_at`, `created_at`, `updated_at`) VALUES
(1, 'CWI Admin', 'admin@colorwrap.inc', NULL, '$2y$12$XdsScIxvgFhQWqSR7CkkG.PX6rCry7zFjZ80KXGvrYNuOSR9xkSjO', NULL, NULL, NULL, '2024-10-21 17:15:37', '2024-10-23 14:09:29'),
(2, 'CWI Front Desk', 'frontdesk@colorwrap.inc', NULL, '$2y$12$7Er.N5pcCZjn1s18e.In0.dTI94bpfg/Fe63dOHOq7.tDZ99fZ3HW', NULL, NULL, NULL, '2024-10-22 19:34:07', '2024-10-22 20:04:38'),
(3, 'CWI Line Scheduler', 'scheduler@colorwrap.inc', NULL, '$2y$12$6p8WqAP4tuIkttRr/j4pUeArpDwAw9X75qw4rj53AMak3Z0D.sR5C', NULL, NULL, NULL, '2024-10-22 19:59:12', '2024-10-22 19:59:12');

-- --------------------------------------------------------

--
-- Table structure for table `quickbooks_customer`
--

CREATE TABLE `quickbooks_customer` (
  `id` int(11) NOT NULL,
  `customer_id` int(10) UNSIGNED DEFAULT NULL,
  `fully_qualified_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `two_factor_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_expires_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_customer`
--

INSERT INTO `quickbooks_customer` (`id`, `customer_id`, `fully_qualified_name`, `company_name`, `display_name`, `is_active`, `email`, `password`, `password_changed_at`, `created_at`, `updated_at`, `two_factor_code`, `two_factor_expires_at`) VALUES
(1, 1, 'Amy\'s Bird Sanctuary', 'Amy\'s Bird Sanctuary', 'Amy\'s Bird Sanctuary', '1', 'Birds@Intuit.com', '$2y$12$mm5Q8NCWN9UdscH/nBv6Ce1QFlHbgS7ZCFtIxwBrmewGsoIC.VOqS', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(2, 2, 'Bill\'s Windsurf Shop', 'Bill\'s Windsurf Shop', 'Bill\'s Windsurf Shop', '1', 'Surf@Intuit.com', '$2y$12$nHDgvif6bdiw62SJBbwR1.ADs8E7XjRrkfa7W7J80hKRFZhMICa5O', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(3, 3, 'Cool Cars', 'Cool Cars', 'Cool Cars', '1', 'Cool_Cars@intuit.com', '$2y$12$DjiAwA.yX3khOfpVhJ/TdOuLKOuveqfjqhhg87.wMcEMTnUR1eXoC', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(4, 4, 'Diego Rodriguez', NULL, 'Diego Rodriguez', '1', 'Diego@Rodriguez.com', '$2y$12$lZh3FDxUmWoRx6TVcFSOReax3H1eJNWubyT8UUzZVeokzWtIPa5p6', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(5, 5, 'Dukes Basketball Camp', 'Dukes Basketball Camp', 'Dukes Basketball Camp', '1', 'Dukes_bball@intuit.com', '$2y$12$OP1k1nKHBwtRtHMUrzCG4uYQKFiipB7r/MAfCtGzu.QcrUyN7qRNe', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(6, 6, 'Dylan Sollfrank', NULL, 'Dylan Sollfrank', '1', NULL, '$2y$12$Arr9AWkxsJvekEWZLPLkE.egvhdH/PoN06jPoHrAlZhSkeFg5t6qu', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(7, 7, 'Freeman Sporting Goods', 'Freeman Sporting Goods', 'Freeman Sporting Goods', '1', 'Sporting_goods@intuit.com', '$2y$12$Roh.cvee.1PtmfUDFeIRkuo9w42lnRtKhMuUw2HLA4XZjxEHSFA5y', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(8, 8, 'Freeman Sporting Goods:0969 Ocean View Road', 'Freeman Sporting Goods', '0969 Ocean View Road', '1', 'Sporting_goods@intuit.com', '$2y$12$4YHQbbSxwtM3pNiTJQMptuwzpNaU5alsXYBLfAU1xyI7twVJV/cZ.', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(9, 9, 'Freeman Sporting Goods:55 Twin Lane', 'Freeman Sporting Goods', '55 Twin Lane', '1', 'Sporting_goods@intuit.com', '$2y$12$NG7G5WfKDiGYDa6ayrSKvOB6TiRaOKxFnz9OElnMaFk5HXNlOhU0m', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(10, 10, 'Geeta Kalapatapu', NULL, 'Geeta Kalapatapu', '1', 'Geeta@Kalapatapu.com', '$2y$12$PSiENxAzC7Wz23PrxWTnjuCM5sHRaRuSBcEDjW3Ikbcx7Pylm9SBC', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(11, 11, 'Gevelber Photography', 'Gevelber Photography', 'Gevelber Photography', '1', 'Photography@intuit.com', '$2y$12$xOXM/lnwvyTx/kljQA56XuaTwwcF7h9w7/6vzSJC/YY53VNHScjQ.', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(12, 12, 'Jeff\'s Jalopies', 'Jeff\'s Jalopies', 'Jeff\'s Jalopies', '1', 'Jalopies@intuit.com', '$2y$12$Ci0MSckFNheVGOOuAkNqreJ.yfv17N3u4wVL1poomS.xjVJvF/zdS', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(13, 13, 'John Melton', NULL, 'John Melton', '1', 'John@Melton.com', '$2y$12$zxq5dNwGWsJ1FMis7mUmFOwq95Z6UqLwglY2/87zr67KESQQkMUq.', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(14, 14, 'Kate Whelan', NULL, 'Kate Whelan', '1', 'Kate@Whelan.com', '$2y$12$9cNIu8dMNlZMrwdH53Lrq.JbP4lKxw6ykZp98liNFtbPR2fjndUfK', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(15, 16, 'Kookies by Kathy', 'Kookies by Kathy', 'Kookies by Kathy', '1', 'qbwebsamplecompany@yahoo.com', '$2y$12$QUszs9rlFkG5FujKd9XwWe4MmB7IUPhlerSp0BwX2V1rZLOYgFSju', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(16, 17, 'Mark Cho', NULL, 'Mark Cho', '1', 'Mark@Cho.com', '$2y$12$KsJaKQNbXkQ6ASE3Og7oY.vJZ6Fnifr1ubMWdVyH1oEPQVckw0FYa', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(17, 18, 'Paulsen Medical Supplies', 'Paulsen Medical Supplies', 'Paulsen Medical Supplies', '1', 'Medical@intuit.com', '$2y$12$g19GfxlxbVEbZpyezS0Cw.L5g0WzM0tBqEzVaPGJ95kjoScQhq1KC', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(18, 15, 'Pye\'s Cakes', 'Pye\'s Cakes', 'Pye\'s Cakes', '1', 'pyescakes@intuit.com', '$2y$12$PHoSEk9/66h7V/Kdd5Mkquw2sXKmQ1CZN.xLbKHrfNIq0g.qz.RWi', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(19, 19, 'Rago Travel Agency', 'Rago Travel Agency', 'Rago Travel Agency', '1', 'Rago_Travel@intuit.com', '$2y$12$Cnqrsb82nr5NkKRDJx9bL.feZIz9Pj5MOef0UAu7qdrMPJ0WAP5RW', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(20, 20, 'Red Rock Diner', 'Red Rock Diner', 'Red Rock Diner', '1', 'qbwebsamplecompany@yahoo.com', '$2y$12$KHCcRYE64FwJK6DrKyEThuHrX8K0MwK4bRgaqKDnggtC0Re0moKRq', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(21, 21, 'Rondonuwu Fruit and Vegi', NULL, 'Rondonuwu Fruit and Vegi', '1', 'Tony@Rondonuwu.com', '$2y$12$H5NPivp2qjH5bpjcVkbFNuLjfiTA4D7o3c117YwL2IZvu7TVpfHtW', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(22, 22, 'Shara Barnett', NULL, 'Shara Barnett', '1', 'Shara@Barnett.com', '$2y$12$8a/ZjqhY7LuP36.UL0PrwerDFlP6XmMCpvKpGZEayVk2l6XxZfnt.', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(23, 23, 'Shara Barnett:Barnett Design', 'Barnett Design', 'Barnett Design', '1', 'Design@intuit.com', '$2y$12$bVlFi6ne1HHgOAEZJ5mZa.4FN2CHMh8rKLdHfoAUafg3Uya9r8Ji.', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(24, 24, 'Sonnenschein Family Store', 'Sonnenschein Family Store', 'Sonnenschein Family Store', '1', 'Familiystore@intuit.com', '$2y$12$R4zz8ImtjK1IQo3cKx.Py./soZJBTk3SQy1qylO5P8d2pI5WCWyla', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(25, 25, 'Sushi by Katsuyuki', 'Sushi by Katsuyuki', 'Sushi by Katsuyuki', '1', 'Sushi@intuit.com', '$2y$12$eMXXzCgh.N7xqzzUYoxMe.zVoa2KBJdqZEet0PcDPaIq0NNHkcvwi', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(26, 26, 'Travis Waldron', NULL, 'Travis Waldron', '1', 'Travis@Waldron.com', '$2y$12$PzhxhZ/xiKVnE0NMYEJ4/Oj4yzK3i/LrAQxtOyu2EMbPGR9pZ.mC2', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(27, 27, 'Video Games by Dan', 'Video Games by Dan', 'Video Games by Dan', '1', 'Videogames@intuit.com', '$2y$12$AFEBYMG6u0B8iLWBscRI7uWG6t9.clZC8L8O94GI7NiHa/foiIVwS', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(28, 58, 'Walker', 'Data Pluzz LLC', 'Walker', '1', 'walker@datapluzz.com', '$2y$12$uhazmDEMrY.xUQYNTkf58OuPAE5wicG0MrdTeoLp7SZAjNPxb7qVO', NULL, '2024-07-14 01:45:34', '2024-10-23 00:11:04', NULL, NULL),
(29, 28, 'Wedding Planning by Whitney', 'Wedding Planning by Whitney', 'Wedding Planning by Whitney', '1', 'Dream_Wedding@intuit.com', '$2y$12$av6HZW5FIq/X1P759LySHe.Y4EIJhXEi50FFK.OYaK0icoGTD/Vry', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL),
(30, 29, 'Weiskopf Consulting', 'Weiskopf Consulting', 'Weiskopf Consulting', '1', 'Consulting@intuit.com', '$2y$12$TVgMmf/BpO1tTrIzZHDJWeSZz50I0vI4jY3l7uLERGl8jZJ3ru7NW', NULL, '2024-07-14 01:45:34', '2024-10-21 20:07:51', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quickbooks_estimates`
--

CREATE TABLE `quickbooks_estimates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `qb_estimate_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_ref` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_memo` text COLLATE utf8mb4_unicode_ci,
  `bill_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_order_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `schedule_date` date DEFAULT NULL,
  `is_updated` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `qr_code_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_estimates`
--

INSERT INTO `quickbooks_estimates` (`id`, `qb_estimate_id`, `customer_ref`, `customer_name`, `customer_memo`, `bill_email`, `purchase_order_number`, `total_amount`, `schedule_date`, `is_updated`, `created_at`, `updated_at`, `synced_at`, `status`, `approved_by`, `qr_code_path`) VALUES
(1, '180', '58', 'Walker', 'Test with QR', 'walker@datapluzz.com', 'DPL-0001-24', 310000.00, NULL, '0', '2024-10-21 23:37:33', '2024-10-22 23:06:39', '2024-10-21 23:37:43', 'approved', 1, NULL),
(2, '181', '58', 'Walker', NULL, 'walker@datapluzz.com', 'DPL-0002-24', 15.00, NULL, '0', '2024-10-21 23:46:39', '2024-10-21 23:46:46', '2024-10-21 23:46:46', 'pending', NULL, NULL),
(3, '182', '58', 'Walker', NULL, 'walker@datapluzz.com', 'DPL-0003-24', 50.00, NULL, '0', '2024-10-21 23:56:24', '2024-10-21 23:56:30', '2024-10-21 23:56:30', 'pending', NULL, NULL),
(4, '183', '58', 'Walker', NULL, 'walker@datapluzz.com', 'DPL-0004-24', 75000.00, NULL, '0', '2024-10-22 00:12:21', '2024-10-22 22:42:31', '2024-10-22 00:12:34', 'approved', 1, NULL),
(5, '184', '58', 'Walker', NULL, 'walker@datapluzz.com', 'DPL-0005-24', 275.00, NULL, '0', '2024-10-23 00:22:43', '2024-10-23 00:23:03', '2024-10-23 00:23:03', 'pending', NULL, NULL),
(6, '185', '58', 'Walker', 'To be delivered at 25 Mikaii Avenue, Abacus, MI 21948 by the 15th of November 2024 @ 10 AM', 'walker@datapluzz.com', 'DPL-0006-24', 501000.00, NULL, '0', '2024-10-23 00:26:42', '2024-10-23 00:31:15', '2024-10-23 00:27:11', 'approved', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quickbooks_estimate_items`
--

CREATE TABLE `quickbooks_estimate_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `quickbooks_estimate_id` bigint(20) UNSIGNED NOT NULL,
  `sku` bigint(20) UNSIGNED DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT '1.00',
  `amount` decimal(10,2) NOT NULL,
  `qr_code_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_estimate_items`
--

INSERT INTO `quickbooks_estimate_items` (`id`, `quickbooks_estimate_id`, `sku`, `unit_price`, `quantity`, `amount`, `qr_code_path`, `created_at`, `updated_at`) VALUES
(1, 1, 10, 35.00, 1000.00, 35000.00, 'qrcodes/qr_1_10.png', '2024-10-21 23:37:33', '2024-10-21 23:37:35'),
(2, 1, 5, 275.00, 1000.00, 275000.00, 'qrcodes/qr_1_5.png', '2024-10-21 23:37:35', '2024-10-21 23:37:36'),
(3, 2, 11, 15.00, 1.00, 15.00, 'qrcodes/DPL-0002-24_Pump_1.png', '2024-10-21 23:46:39', '2024-10-21 23:46:41'),
(4, 3, 7, 50.00, 1.00, 50.00, 'qrcodes/DPL-0003-24_Installation_1.png', '2024-10-21 23:56:24', '2024-10-21 23:56:26'),
(5, 4, 4, 75.00, 1000.00, 75000.00, 'qrcodes/DPL-0004-24_Design_1000.png', '2024-10-22 00:12:21', '2024-10-22 00:12:24'),
(6, 5, 5, 275.00, 1.00, 275.00, 'qrcodes/DPL-0005-24_Rock_Fountain_1.png', '2024-10-23 00:22:43', '2024-10-23 00:22:49'),
(7, 6, 4, 75.00, 1000.00, 75000.00, 'qrcodes/DPL-0006-24_Design_1000.png', '2024-10-23 00:26:42', '2024-10-23 00:26:43'),
(8, 6, 7, 50.00, 1000.00, 50000.00, 'qrcodes/DPL-0006-24_Installation_1000.png', '2024-10-23 00:26:43', '2024-10-23 00:26:43'),
(9, 6, 10, 35.00, 1000.00, 35000.00, 'qrcodes/DPL-0006-24_Pest_Control_1000.png', '2024-10-23 00:26:43', '2024-10-23 00:26:43'),
(10, 6, 11, 15.00, 1000.00, 15000.00, 'qrcodes/DPL-0006-24_Pump_1000.png', '2024-10-23 00:26:43', '2024-10-23 00:26:44'),
(11, 6, 5, 275.00, 1000.00, 275000.00, 'qrcodes/DPL-0006-24_Rock_Fountain_1000.png', '2024-10-23 00:26:44', '2024-10-23 00:26:44'),
(12, 6, 15, 10.00, 1000.00, 10000.00, 'qrcodes/DPL-0006-24_Soil_1000.png', '2024-10-23 00:26:44', '2024-10-23 00:26:45'),
(13, 6, 16, 2.00, 1000.00, 2000.00, 'qrcodes/DPL-0006-24_Sprinkler_Heads_1000.png', '2024-10-23 00:26:45', '2024-10-23 00:26:45'),
(14, 6, 17, 4.00, 1000.00, 4000.00, 'qrcodes/DPL-0006-24_Sprinkler_Pipes_1000.png', '2024-10-23 00:26:45', '2024-10-23 00:26:45'),
(15, 6, 18, 35.00, 1000.00, 35000.00, 'qrcodes/DPL-0006-24_Trimming_1000.png', '2024-10-23 00:26:45', '2024-10-23 00:26:46');

-- --------------------------------------------------------

--
-- Table structure for table `quickbooks_item`
--

CREATE TABLE `quickbooks_item` (
  `id` int(11) NOT NULL,
  `item_id` int(10) UNSIGNED DEFAULT NULL,
  `fully_qualified_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sku` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_description` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty_on_hand` int(10) UNSIGNED DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT NULL,
  `income_account_ref` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_item`
--

INSERT INTO `quickbooks_item` (`id`, `item_id`, `fully_qualified_name`, `name`, `sku`, `item_description`, `qty_on_hand`, `unit_price`, `income_account_ref`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'Concrete', 'Concrete', '3', 'Concrete for fountain installation', NULL, 0.00, '48', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(2, 4, 'Design', 'Design', '4', 'Custom Design', NULL, 75.00, '82', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(3, 6, 'Gardening', 'Gardening', '6', 'Weekly Gardening Service', NULL, 0.00, '45', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(4, 2, 'Hours', 'Hours', '2', NULL, NULL, 0.00, '1', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(5, 7, 'Installation', 'Installation', '7', 'Installation of landscape design', NULL, 50.00, '52', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(6, 8, 'Lighting', 'Lighting', '8', 'Garden Lighting', NULL, 0.00, '48', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(7, 9, 'Maintenance & Repair', 'Maintenance & Repair', '9', 'Maintenance & Repair', NULL, 0.00, '53', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(8, 10, 'Pest Control', 'Pest Control', '10', 'Pest Control Services', NULL, 35.00, '54', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(9, 11, 'Pump', 'Pump', '11', 'Fountain Pump', 25, 15.00, '79', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(10, 12, 'Refunds & Allowances', 'Refunds & Allowances', '12', 'Income due to refunds or allowances', NULL, 0.00, '83', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(11, 5, 'Rock Fountain', 'Rock Fountain', '5', 'Rock Fountain', 2, 275.00, '79', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(12, 13, 'Rocks', 'Rocks', '13', 'Garden Rocks', NULL, 0.00, '48', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(13, 1, 'Services', 'Services', '1', NULL, NULL, 0.00, '1', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(14, 14, 'Sod', 'Sod', '14', 'Sod', NULL, 0.00, '49', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(15, 15, 'Soil', 'Soil', '15', '2 cubic ft. bag', NULL, 10.00, '49', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(16, 16, 'Sprinkler Heads', 'Sprinkler Heads', '16', 'Sprinkler Heads', 25, 2.00, '79', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(17, 17, 'Sprinkler Pipes', 'Sprinkler Pipes', '17', 'Sprinkler Pipes', 31, 4.00, '79', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54'),
(18, 18, 'Trimming', 'Trimming', '18', 'Tree and Shrub Trimming', NULL, 35.00, '45', '1', '2024-07-14 01:45:37', '2024-10-21 20:07:54');

-- --------------------------------------------------------

--
-- Table structure for table `quickbooks_tokens`
--

CREATE TABLE `quickbooks_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `realm_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `refresh_token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_tokens`
--

INSERT INTO `quickbooks_tokens` (`id`, `realm_id`, `access_token`, `refresh_token`, `created_at`, `updated_at`) VALUES
(1, '9341452631925553', 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..4huOdgqraghYZgbYdyJGgQ.E97f0LAMc3-yhlJLwfrdysNvu7XOdbmW90JtUJdmjpDVQjCaN5p0Gu1mPt2NwcUIgU44oR8Y9v90VBBO41_ZUDWesZXnHS_SAVf_XxQsooghAXQQBkoOia_Xp5UGQ8sbwTi2y1uCvnNMmRNsfTDRQMQ1xledrUXT3vx-7eQUz07Cb5rNHVxBgT-XGqoEGd7wtddMDIPxlbaV-2RNYx0jPYiRRvTDOS3yirU3FSj6l509DPtxzWVb2c59FsGz6u9lohTkQrOHi78A8rSs7gJOvvBRJdWHz4WkWDzLndKnnZvsxmBeJ6aLWDlufV9W8fW-gtvK4rYHy2AQN0d0ytjaZVt21CHF1OWv_BvBCasZ2Sn0cKvjN9bQvXSXXWmBube-wLLZXe8xv-9yvyqUIGsxK75XzitDQWGn56TjJmyiRdsF3iwtgAT8gFCrydXETEO2ed3AavsqyoRt-RV-lq6bZ1m3gAGEumyx5Z_f60DKp2wzanZuKwvyWYvv2Y9i8UOqY5Mden-vo6zoAC7PCKkKKcOyuTaQossPTKLzDlvO2MKj-mpaPapopX-RJydpG-WfkMZELP1TpXR3Y4TxRkVkqdhJ68oQY9vgTqa-9kTtTCOzsOLGQgim90bEa_u4JmFGy0CNmojGoBPw7Jd4Kn5_RoqO7Dm0m8P1chuLndqIABpl3PPfw7ZHLcrjhYJrpWqULW6gc4E-OJMPXrRfpQ31u3tH3HWYxmhPN2gfKHx8HQJuONBzzZaoMARwnPiDn9hAv5APnHZ6Vrl5sfsyKst9WaKiJV-DSNC9qE6eccp1BK2dPy2zn6sZyQTkEEqMD2UtjKR-if9m6vxzBooGTOMXTQQS-Ph4THQmobmHCc5u0cr9rea036i_VDTzonerNesQ.YIDSJN1x3T1xpskrThTw4w', 'AB11738408088fKIssHsWUuKDMqVRcn0D0vU1QOOo0MfgoXNqf', '2024-07-13 12:07:20', '2024-10-23 15:08:09');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(2, 'Front Desk Personnel', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(3, 'Line Scheduler', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(4, 'Production Manager', '2024-10-22 11:23:12', '2024-10-22 11:23:12'),
(5, 'Delivery Agent', '2024-10-22 11:23:12', '2024-10-22 11:23:12');

-- --------------------------------------------------------

--
-- Table structure for table `role_permission`
--

CREATE TABLE `role_permission` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permission`
--

INSERT INTO `role_permission` (`id`, `role_id`, `permission_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 1, 2, NULL, NULL),
(3, 1, 3, NULL, NULL),
(4, 1, 4, NULL, NULL),
(5, 1, 5, NULL, NULL),
(6, 2, 2, NULL, NULL),
(7, 3, 3, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_role`
--
ALTER TABLE `admin_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_role_admin_id_foreign` (`admin_id`),
  ADD KEY `admin_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `production_lines`
--
ALTER TABLE `production_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_lines_line_manager_id_foreign` (`line_manager_id`),
  ADD KEY `production_lines_assigned_order_id_foreign` (`assigned_order_id`);

--
-- Indexes for table `production_schedules`
--
ALTER TABLE `production_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `production_schedules_item_id_foreign` (`item_id`),
  ADD KEY `production_schedules_line_id_foreign` (`line_id`);

--
-- Indexes for table `quickbooks_admin`
--
ALTER TABLE `quickbooks_admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quickbooks_admin_email_unique` (`email`);

--
-- Indexes for table `quickbooks_customer`
--
ALTER TABLE `quickbooks_customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quickbooks_customer_customer_id_unique` (`customer_id`);

--
-- Indexes for table `quickbooks_estimates`
--
ALTER TABLE `quickbooks_estimates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quickbooks_estimates_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `quickbooks_estimate_items`
--
ALTER TABLE `quickbooks_estimate_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quickbooks_estimate_items_quickbooks_estimate_id_foreign` (`quickbooks_estimate_id`);

--
-- Indexes for table `quickbooks_item`
--
ALTER TABLE `quickbooks_item`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quickbooks_item_item_id_unique` (`item_id`);

--
-- Indexes for table `quickbooks_tokens`
--
ALTER TABLE `quickbooks_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_permission_role_id_foreign` (`role_id`),
  ADD KEY `role_permission_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_role`
--
ALTER TABLE `admin_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `production_lines`
--
ALTER TABLE `production_lines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `production_schedules`
--
ALTER TABLE `production_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `quickbooks_admin`
--
ALTER TABLE `quickbooks_admin`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quickbooks_customer`
--
ALTER TABLE `quickbooks_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `quickbooks_estimates`
--
ALTER TABLE `quickbooks_estimates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `quickbooks_estimate_items`
--
ALTER TABLE `quickbooks_estimate_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `quickbooks_item`
--
ALTER TABLE `quickbooks_item`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `quickbooks_tokens`
--
ALTER TABLE `quickbooks_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_permission`
--
ALTER TABLE `role_permission`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_role`
--
ALTER TABLE `admin_role`
  ADD CONSTRAINT `admin_role_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `quickbooks_admin` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_lines`
--
ALTER TABLE `production_lines`
  ADD CONSTRAINT `production_lines_assigned_order_id_foreign` FOREIGN KEY (`assigned_order_id`) REFERENCES `quickbooks_estimates` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `production_lines_line_manager_id_foreign` FOREIGN KEY (`line_manager_id`) REFERENCES `quickbooks_admin` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `production_schedules`
--
ALTER TABLE `production_schedules`
  ADD CONSTRAINT `production_schedules_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `quickbooks_estimate_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `production_schedules_line_id_foreign` FOREIGN KEY (`line_id`) REFERENCES `production_lines` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quickbooks_estimates`
--
ALTER TABLE `quickbooks_estimates`
  ADD CONSTRAINT `quickbooks_estimates_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `quickbooks_admin` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `quickbooks_estimate_items`
--
ALTER TABLE `quickbooks_estimate_items`
  ADD CONSTRAINT `quickbooks_estimate_items_quickbooks_estimate_id_foreign` FOREIGN KEY (`quickbooks_estimate_id`) REFERENCES `quickbooks_estimates` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permission`
--
ALTER TABLE `role_permission`
  ADD CONSTRAINT `role_permission_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permission_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
