-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 20, 2024 at 03:39 AM
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
-- Database: `quickbooks`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_quick_books_tokens_1', 'O:26:\"App\\Models\\QuickBooksToken\":30:{s:13:\"\0*\0connection\";s:5:\"mysql\";s:8:\"\0*\0table\";s:17:\"quickbooks_tokens\";s:13:\"\0*\0primaryKey\";s:2:\"id\";s:10:\"\0*\0keyType\";s:3:\"int\";s:12:\"incrementing\";b:1;s:7:\"\0*\0with\";a:0:{}s:12:\"\0*\0withCount\";a:0:{}s:19:\"preventsLazyLoading\";b:0;s:10:\"\0*\0perPage\";i:15;s:6:\"exists\";b:1;s:18:\"wasRecentlyCreated\";b:0;s:28:\"\0*\0escapeWhenCastingToString\";b:0;s:13:\"\0*\0attributes\";a:6:{s:2:\"id\";i:1;s:8:\"realm_id\";s:16:\"9341452631925553\";s:12:\"access_token\";s:991:\"eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..EtUZgJA7BV1f48-GKc4K4A.Wx38rCSNwTipOibg0ZYNQmFwdoSG42RJd23VrJsnnKdddk20JhiZl0JIx91Y9BUZTmTnhHm6BzaHfQ_GWUEvXGrfgM0DgZhis7xfDGw9fkJ7q0RG1c-QSdAOxIqEZk5_-gcpTu38oAGYvYd7X54gUdlP4v73uotiLULJVAAz-gCIl4pveP8dnPOxAMvoYAg_k_RIk6VUhqKv0VhJDk3WE-9p2KXc4DVOr6xz-VmeC-O3SnSY3-xn9G3YGP2zFKJAKbE362DY3sgHDjhysjTh8Z2wlcBvYevxDnj9ZLilSpA3Pj1AbDZxfzzOFtjvmCwFMdtNF6r3ybpBP61YgQtrg9mHGODZpJ52S5ak94hgZ2YFymqU0kn2vUvS7DX69I3W2fWXQ8AoHJz9m_C7K7b0P_0YzauugfRAWChu6lsVJz3OgDt_c6zXGlZFZKh87loqp2M7HHj0jPSZVH-QhFf-pQnXC1BuxrjY5NWZAzYBZGQI1PDlCsNLVnpxhnVSG4t4wGQ-wjYw1vqmhyV5LWsCqdUN0FWMVInftCf5eboA0aUnYjWD5bdaIwMbXk92uDyZfdJJVGADPf4X56yFadyHPZHm0UD_kc5-ctx1rvSDMhMJdOz7Is1wlgSob8iTAGl93lbPA1TMmXCmHHRqyrQmSp6uh4GQ--HRn8Qg2k-nVjyBbJowB575knuJZyBOyCRLHqPgnI0rMGnErQrWg77IqjAcG7XX1Vcwmm2tZGPFQmeRh4yzI-QpYr0S1JTHnnpEwoN9SfvwiVZcI4LzkHtqBLHH5ZRzIA7wGFjNt2139I5DM3ruofUjQnqkEcRwABIpRile_Ce0aCF396yFqiD-as8xAOm-rX_e0sBzFJ84FVXQIrdjZe6VaMXcX7Qknwap.XYNbofXwidv2CAR906LNeg\";s:13:\"refresh_token\";s:50:\"AB11729632111fRF8UMgc1HhpKyosyzTvKvhuIBxEYIYzC1PZl\";s:10:\"created_at\";s:19:\"2024-07-13 08:07:20\";s:10:\"updated_at\";s:19:\"2024-07-14 02:20:41\";}s:11:\"\0*\0original\";a:6:{s:2:\"id\";i:1;s:8:\"realm_id\";s:16:\"9341452631925553\";s:12:\"access_token\";s:991:\"eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..EtUZgJA7BV1f48-GKc4K4A.Wx38rCSNwTipOibg0ZYNQmFwdoSG42RJd23VrJsnnKdddk20JhiZl0JIx91Y9BUZTmTnhHm6BzaHfQ_GWUEvXGrfgM0DgZhis7xfDGw9fkJ7q0RG1c-QSdAOxIqEZk5_-gcpTu38oAGYvYd7X54gUdlP4v73uotiLULJVAAz-gCIl4pveP8dnPOxAMvoYAg_k_RIk6VUhqKv0VhJDk3WE-9p2KXc4DVOr6xz-VmeC-O3SnSY3-xn9G3YGP2zFKJAKbE362DY3sgHDjhysjTh8Z2wlcBvYevxDnj9ZLilSpA3Pj1AbDZxfzzOFtjvmCwFMdtNF6r3ybpBP61YgQtrg9mHGODZpJ52S5ak94hgZ2YFymqU0kn2vUvS7DX69I3W2fWXQ8AoHJz9m_C7K7b0P_0YzauugfRAWChu6lsVJz3OgDt_c6zXGlZFZKh87loqp2M7HHj0jPSZVH-QhFf-pQnXC1BuxrjY5NWZAzYBZGQI1PDlCsNLVnpxhnVSG4t4wGQ-wjYw1vqmhyV5LWsCqdUN0FWMVInftCf5eboA0aUnYjWD5bdaIwMbXk92uDyZfdJJVGADPf4X56yFadyHPZHm0UD_kc5-ctx1rvSDMhMJdOz7Is1wlgSob8iTAGl93lbPA1TMmXCmHHRqyrQmSp6uh4GQ--HRn8Qg2k-nVjyBbJowB575knuJZyBOyCRLHqPgnI0rMGnErQrWg77IqjAcG7XX1Vcwmm2tZGPFQmeRh4yzI-QpYr0S1JTHnnpEwoN9SfvwiVZcI4LzkHtqBLHH5ZRzIA7wGFjNt2139I5DM3ruofUjQnqkEcRwABIpRile_Ce0aCF396yFqiD-as8xAOm-rX_e0sBzFJ84FVXQIrdjZe6VaMXcX7Qknwap.XYNbofXwidv2CAR906LNeg\";s:13:\"refresh_token\";s:50:\"AB11729632111fRF8UMgc1HhpKyosyzTvKvhuIBxEYIYzC1PZl\";s:10:\"created_at\";s:19:\"2024-07-13 08:07:20\";s:10:\"updated_at\";s:19:\"2024-07-14 02:20:41\";}s:10:\"\0*\0changes\";a:0:{}s:8:\"\0*\0casts\";a:0:{}s:17:\"\0*\0classCastCache\";a:0:{}s:21:\"\0*\0attributeCastCache\";a:0:{}s:13:\"\0*\0dateFormat\";N;s:10:\"\0*\0appends\";a:0:{}s:19:\"\0*\0dispatchesEvents\";a:0:{}s:14:\"\0*\0observables\";a:0:{}s:12:\"\0*\0relations\";a:0:{}s:10:\"\0*\0touches\";a:0:{}s:10:\"timestamps\";b:1;s:13:\"usesUniqueIds\";b:0;s:9:\"\0*\0hidden\";a:0:{}s:10:\"\0*\0visible\";a:0:{}s:11:\"\0*\0fillable\";a:5:{i:0;s:13:\"refresh_token\";i:1;s:12:\"access_token\";i:2;s:8:\"realm_id\";i:3;s:10:\"created_at\";i:4;s:10:\"updated_at\";}s:10:\"\0*\0guarded\";a:1:{i:0;s:1:\"*\";}}', 1720945498);

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
(7, '2024_07_13_183053_customers_and_items', 2);

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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_customer`
--

INSERT INTO `quickbooks_customer` (`id`, `customer_id`, `fully_qualified_name`, `company_name`, `display_name`, `is_active`, `email`, `password`, `password_changed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Amy\'s Bird Sanctuary', 'Amy\'s Bird Sanctuary', 'Amy\'s Bird Sanctuary', '1', 'Birds@Intuit.com', '$2y$12$mJ44yAq9uVUY2gzbi1etQu0XHPeILyapopBjrcMXdaRkledJVj.1m', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(2, 2, 'Bill\'s Windsurf Shop', 'Bill\'s Windsurf Shop', 'Bill\'s Windsurf Shop', '1', 'Surf@Intuit.com', '$2y$12$PY91r.ndZd2DULM3gtJtjePNr0RorzlxWxec54OhWqfIyeG3KMQ1a', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(3, 3, 'Cool Cars', 'Cool Cars', 'Cool Cars', '1', 'Cool_Cars@intuit.com', '$2y$12$OOxpWNQ3nl3.Z2AjU5nTZewApT3QqJS9se4KBePJrvILvdUQIVZZK', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(4, 4, 'Diego Rodriguez', NULL, 'Diego Rodriguez', '1', 'Diego@Rodriguez.com', '$2y$12$tp5HeBKh6IQ4OrRiRN1qQeZwgz1TIfkYcaY77zSpbaEUd.YbGtK9m', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(5, 5, 'Dukes Basketball Camp', 'Dukes Basketball Camp', 'Dukes Basketball Camp', '1', 'Dukes_bball@intuit.com', '$2y$12$9sMJcfNwoH/8FNSpYEUZ7OBXj5yxvdXfYV4L0shYGjXL0.EvEcUQO', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(6, 6, 'Dylan Sollfrank', NULL, 'Dylan Sollfrank', '1', NULL, '$2y$12$DaDBFQ7q4s8w0gyRIg0G2.r27SkWl1r8KfyJWmzVxtDRb9IVn.8va', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(7, 7, 'Freeman Sporting Goods', 'Freeman Sporting Goods', 'Freeman Sporting Goods', '1', 'Sporting_goods@intuit.com', '$2y$12$EJ.Twgk49qxAohU8z6mnhekNBffgmdxxRATO9zMI.LTICFovYcZBC', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(8, 8, 'Freeman Sporting Goods:0969 Ocean View Road', 'Freeman Sporting Goods', '0969 Ocean View Road', '1', 'Sporting_goods@intuit.com', '$2y$12$ce.BjmR3Og4OY2U4UyazmelkfLQ0o19j3GwAshkIvjlL8GfzImbRS', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(9, 9, 'Freeman Sporting Goods:55 Twin Lane', 'Freeman Sporting Goods', '55 Twin Lane', '1', 'Sporting_goods@intuit.com', '$2y$12$.LB3gBWyaR87uMsuoRWEGe3RHXahsat6ViT8z9ZPptHjECHAQNOwa', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(10, 10, 'Geeta Kalapatapu', NULL, 'Geeta Kalapatapu', '1', 'Geeta@Kalapatapu.com', '$2y$12$sK9BnF2xYpzYghQ4qan4ye0q/LCy1nvFCdVWYIm/HnvhPoAyOrrLS', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(11, 11, 'Gevelber Photography', 'Gevelber Photography', 'Gevelber Photography', '1', 'Photography@intuit.com', '$2y$12$uetzONmWAJmo6XXmsfdn5e5kwcFtdyC9kBNsqhDTLLU90VGZTtkVm', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(12, 12, 'Jeff\'s Jalopies', 'Jeff\'s Jalopies', 'Jeff\'s Jalopies', '1', 'Jalopies@intuit.com', '$2y$12$/P5iDgKDoPdcYsYILDSnZ.YjPrmk.3tDkYmr8NpPUTV0Gl6Sak8ym', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(13, 13, 'John Melton', NULL, 'John Melton', '1', 'John@Melton.com', '$2y$12$B0knn3qhUKQQo5GvxfV0b.SstOmLnSWpZJ0agQPmykL6/l6S/xhdO', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(14, 14, 'Kate Whelan', NULL, 'Kate Whelan', '1', 'Kate@Whelan.com', '$2y$12$tAD.ZekF.Oco2WGf0N0Awe0ioZX6cQzfvQkZ7b9w5tsuow3554c2q', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(15, 16, 'Kookies by Kathy', 'Kookies by Kathy', 'Kookies by Kathy', '1', 'qbwebsamplecompany@yahoo.com', '$2y$12$HbMEsYP76JUYZ5LKa5zj3uv1y69eR1f./THiV2JFImabKSu0mWnym', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(16, 17, 'Mark Cho', NULL, 'Mark Cho', '1', 'Mark@Cho.com', '$2y$12$BIGrCyed0ZjUX4OrwMyTiOWirdZZq7ol0X4hpuUuMaioS5FmbusK.', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(17, 18, 'Paulsen Medical Supplies', 'Paulsen Medical Supplies', 'Paulsen Medical Supplies', '1', 'Medical@intuit.com', '$2y$12$VZA5IV5FOTB/Xfge5mkcDuG1UgH5z.ubgtpeJodAMSwxrd24SjewW', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(18, 15, 'Pye\'s Cakes', 'Pye\'s Cakes', 'Pye\'s Cakes', '1', 'pyescakes@intuit.com', '$2y$12$n.8mOdF4aCQhop5251mhNu888fWy4jJG8CrI7avYZtXKDZo7IjOqG', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(19, 19, 'Rago Travel Agency', 'Rago Travel Agency', 'Rago Travel Agency', '1', 'Rago_Travel@intuit.com', '$2y$12$XdjTL4Pdc91T/1o7PKoZ5ewf0r4JhuXf0mqXd.xehzulKybNmd9Zy', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(20, 20, 'Red Rock Diner', 'Red Rock Diner', 'Red Rock Diner', '1', 'qbwebsamplecompany@yahoo.com', '$2y$12$uqEtbQNciGwx2rqIyqE1f..crktjM8vjtK2iBLWwOSUjuW2nzznr6', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(21, 21, 'Rondonuwu Fruit and Vegi', NULL, 'Rondonuwu Fruit and Vegi', '1', 'Tony@Rondonuwu.com', '$2y$12$w0Ibn9jchM1ozOwGiRTilOTXJzXNh3VnbRZBW04t1xwVh5Rz6XpVi', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(22, 22, 'Shara Barnett', NULL, 'Shara Barnett', '1', 'Shara@Barnett.com', '$2y$12$9rf0cbDRoZYTvAEnM9Tc.ezd7tyh6OHXo34d5bw2Fl4FJm67A7sHy', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(23, 23, 'Shara Barnett:Barnett Design', 'Barnett Design', 'Barnett Design', '1', 'Design@intuit.com', '$2y$12$2KBeY5IHyqN/X16f6jAVLutpJ1ffXE8Rlysh937C1tiIEWITn5CwS', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(24, 24, 'Sonnenschein Family Store', 'Sonnenschein Family Store', 'Sonnenschein Family Store', '1', 'Familiystore@intuit.com', '$2y$12$e0cVCWsHmwJNupySNEVek.e4Y4ywIgmFiNpx9NbRjpwWHqQRFpiWW', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(25, 25, 'Sushi by Katsuyuki', 'Sushi by Katsuyuki', 'Sushi by Katsuyuki', '1', 'Sushi@intuit.com', '$2y$12$klq/B8U16mTOBbH5Tnc0b.rvC4DqAZhZfMeThH8JISEHKASHpDSzu', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(26, 26, 'Travis Waldron', NULL, 'Travis Waldron', '1', 'Travis@Waldron.com', '$2y$12$YekuRq6IO2Z1a/ag3X1zrOZglIP7Fesf1nlL8xCELOWHmH9j6MkR2', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(27, 27, 'Video Games by Dan', 'Video Games by Dan', 'Video Games by Dan', '1', 'Videogames@intuit.com', '$2y$12$zvFEJHUkKV0Rq5dkADB.V.HlrR0wyhpLZo5VFNUbxx9IWnBydAnkm', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(28, 58, 'Walker', 'Data PLuzz', 'Walker', '1', 'walker@datapluzz.com', '$2y$12$.1Gw/Qwvd8sYzwgVpfvMx.pWfZliQBzADBUcuAK7cy/z.OVoKCCE.', NULL, '2024-07-14 01:45:34', '2024-07-14 07:40:02'),
(29, 28, 'Wedding Planning by Whitney', 'Wedding Planning by Whitney', 'Wedding Planning by Whitney', '1', 'Dream_Wedding@intuit.com', '$2y$12$l9drWf5WAAIZPwB8ui5WKO3Ufom1cm8rBqbgNBv.nR2MeAqvMSFl6', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34'),
(30, 29, 'Weiskopf Consulting', 'Weiskopf Consulting', 'Weiskopf Consulting', '1', 'Consulting@intuit.com', '$2y$12$1AHXekBWrlbqOaOUzoeGXO3T/8F1Uri8QVrfsHpC1lWEQtIEGNLnq', NULL, '2024-07-14 01:45:34', '2024-07-14 01:45:34');

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
  `is_updated` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_estimates`
--

INSERT INTO `quickbooks_estimates` (`id`, `qb_estimate_id`, `customer_ref`, `customer_name`, `customer_memo`, `bill_email`, `is_updated`, `created_at`, `updated_at`, `synced_at`) VALUES
(1, '152', '58', 'Walker', 'new order, be ready by July 30', 'walker@datapluzz.com', '0', '2024-07-14 15:20:28', '2024-07-14 15:57:06', '2024-07-14 15:57:06'),
(2, '153', '9', '55 Twin Lane', 'alert test', 'Sporting_goods@intuit.com', '0', '2024-07-14 15:44:04', '2024-07-14 15:57:11', '2024-07-14 15:57:11'),
(3, '154', '14', 'Kate Whelan', 'new alerts test', 'Kate@Whelan.com', '0', '2024-07-14 15:56:59', '2024-07-14 15:57:16', '2024-07-14 15:57:16'),
(4, '155', '13', 'John Melton', 'order be ready by friday', 'John@Melton.com', '0', '2024-07-14 16:03:29', '2024-07-14 16:07:48', '2024-07-14 16:07:48');

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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quickbooks_estimate_items`
--

INSERT INTO `quickbooks_estimate_items` (`id`, `quickbooks_estimate_id`, `sku`, `unit_price`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 1, 18, 35.00, 10.00, '2024-07-14 15:20:28', '2024-07-14 15:20:28'),
(2, 2, 10, 35.00, 4.00, '2024-07-14 15:44:04', '2024-07-14 15:44:04'),
(3, 3, 7, 50.00, 10.00, '2024-07-14 15:56:59', '2024-07-14 15:56:59'),
(4, 4, 11, 15.00, 10.00, '2024-07-14 16:03:29', '2024-07-14 16:03:29');

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
(1, 3, 'Concrete', 'Concrete', '3', 'Concrete for fountain installation', NULL, 0.00, '48', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(2, 4, 'Design', 'Design', '4', 'Custom Design', NULL, 75.00, '82', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(3, 6, 'Gardening', 'Gardening', '6', 'Weekly Gardening Service', NULL, 0.00, '45', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(4, 2, 'Hours', 'Hours', '2', NULL, NULL, 0.00, '1', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(5, 7, 'Installation', 'Installation', '7', 'Installation of landscape design', NULL, 50.00, '52', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(6, 8, 'Lighting', 'Lighting', '8', 'Garden Lighting', NULL, 0.00, '48', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(7, 9, 'Maintenance & Repair', 'Maintenance & Repair', '9', 'Maintenance & Repair', NULL, 0.00, '53', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(8, 10, 'Pest Control', 'Pest Control', '10', 'Pest Control Services', NULL, 35.00, '54', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(9, 11, 'Pump', 'Pump', '11', 'Fountain Pump', 25, 15.00, '79', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(10, 12, 'Refunds & Allowances', 'Refunds & Allowances', '12', 'Income due to refunds or allowances', NULL, 0.00, '83', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(11, 5, 'Rock Fountain', 'Rock Fountain', '5', 'Rock Fountain', 2, 275.00, '79', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(12, 13, 'Rocks', 'Rocks', '13', 'Garden Rocks', NULL, 0.00, '48', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(13, 1, 'Services', 'Services', '1', NULL, NULL, 0.00, '1', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(14, 14, 'Sod', 'Sod', '14', 'Sod', NULL, 0.00, '49', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(15, 15, 'Soil', 'Soil', '15', '2 cubic ft. bag', NULL, 10.00, '49', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(16, 16, 'Sprinkler Heads', 'Sprinkler Heads', '16', 'Sprinkler Heads', 25, 2.00, '79', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(17, 17, 'Sprinkler Pipes', 'Sprinkler Pipes', '17', 'Sprinkler Pipes', 31, 4.00, '79', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37'),
(18, 18, 'Trimming', 'Trimming', '18', 'Tree and Shrub Trimming', NULL, 35.00, '45', '1', '2024-07-14 01:45:37', '2024-07-14 01:45:37');

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
(1, '9341452631925553', 'eyJlbmMiOiJBMTI4Q0JDLUhTMjU2IiwiYWxnIjoiZGlyIn0..ZFH_eX841tom5FKiSNYBNA.wnZ6sUDKK53wJGL7jqyxndX1H-sOj4sycnNQQ8K5fuUuILlyNd3W6si5XrVrnBvJDP-6wEBO-NE9BbzRLC4-hlG6RBLSK2bB_5gYcpcvsjzjNh8JBkmBK3DIL7UbYJIrPdnzO07Hw9652I8-bfYVai1VY--1NbXYeOMCv7DhqKsth1t23QNQrnNUheDrZigHUrr0LXH2Deu8pnijGEvdVazOGImmwz0i-FuVdZR8DoEgNB3S9spLp4FHk5JJM0rAN7x3x6VUrht9Sw6dyU2gw6uZmXW1N-iFTgHrT6NnG9eV2pZG9hb5BgB3Zn54K2z7Bfm3rn7djjItQRZnfYUeCT9IQJX8vXYmxGxdyZojr3WdYqzL3CD4XROl4z464y47ChFRvE4Hntiq7M7Df-2T2L-Cw4vl8lD-eU_xzCoMpAsyT2ecJu45aAKuCgaq1MvbHdGRAJMwNcZU9ezCANUEz67hrfI4pubclUgzCSlps1HZf5PuqGnFM2N8OWydKQgoA5yzWBImVHLz1McmNWK9ryrJERU7hVoicIVg2PO7kXzoezPNhfIXnBwdXKCu_E9_zp-W84XiS48spsEijE8Mx9OcRaZElPOHu7J9jWIwwLb1jiei6rH4oaGx4kRbjQgwf2yDgotdX9nfWjX2wey4SoMavGFjAEWK-5D1zTOpS2zVWFO4ai0SVWCp9Gt6L9E115YEptyk6eNO8yZ-RYcBaA6Ve_FJaYy3xK6GmN4W8fb2wafYB-KBclt2z1gWcLVitKpiTcUSOPD0HQpwZRUMOOD6McG3Y5zfb3CMvor4HYS4ngz7elqnwDBJn13QelcsrmPb6xzAitnBSSXXGbOcXxTx0QVV4KU32PwP3TICiYeMC0CCUVUrExHfKPyRJJZA.LonNwi4TbfLNRFt63jM4MA', 'AB11729632111fRF8UMgc1HhpKyosyzTvKvhuIBxEYIYzC1PZl', '2024-07-13 12:07:20', '2024-07-14 16:03:29');

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
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

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
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quickbooks_customer`
--
ALTER TABLE `quickbooks_customer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `quickbooks_estimates`
--
ALTER TABLE `quickbooks_estimates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `quickbooks_estimate_items`
--
ALTER TABLE `quickbooks_estimate_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quickbooks_estimate_items`
--
ALTER TABLE `quickbooks_estimate_items`
  ADD CONSTRAINT `quickbooks_estimate_items_quickbooks_estimate_id_foreign` FOREIGN KEY (`quickbooks_estimate_id`) REFERENCES `quickbooks_estimates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
