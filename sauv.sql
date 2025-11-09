-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 07, 2025 at 12:27 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sauv`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address_line1` varchar(255) NOT NULL,
  `house_number` varchar(50) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `sub_district` varchar(100) DEFAULT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `country` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `first_name`, `last_name`, `address_line1`, `house_number`, `city`, `sub_district`, `province`, `postal_code`, `country`, `phone`) VALUES
(1, 14, 'kinaya', 'azzahra', 'Bumi Mutiara', 'J24', 'Bekasi', 'kecamatan gunung putri', 'Jawa Barat', '17222', '', '08181818181818'),
(2, 16, 'Kinaya', 'Azzahra', 'Bumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969', NULL, 'Kabupaten Bogor', 'Kec. Gunung Putri', 'Jawa Barat', '16969', 'Indonesia', '085878061240'),
(3, 19, 'joko', 'kemal', 'Jl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi', NULL, 'Bekasi', 'jatiasih', 'Jawa Barat', '17875', 'Indonesia', '085921896187');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` mediumtext NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `author_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_pinned` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `title`, `content`, `image_url`, `author_id`, `created_at`, `is_pinned`) VALUES
(6, 'Introducing 1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '1756733352_#aesthetic #japan.jpg', 1, '2025-09-01 13:29:12', 1),
(10, 'Testing 1', '<p>#WEAREMEMBERSHIP</p><p><img src=\"data:image/jpeg;base64,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\" data-filename=\"lambang univ gunadarma.jpg\" style=\"width: 225.985px;\"><br></p>', '1761153489_download.jfif', 1, '2025-10-22 17:18:09', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  `status` enum('pending','confirmed','shipped','completed','cancelled') DEFAULT 'pending',
  `resi_number` varchar(100) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `shipping_address`, `status`, `resi_number`, `transaction_id`, `order_date`) VALUES
(1, 14, 20000.00, 'jl. bumi mutiara, bogor, Jawa Barat, 17875, Indonesia', 'confirmed', 'asdasdsadadsadassds', NULL, '2025-08-05 17:32:31'),
(2, 16, 120000.00, 'Bumi Mutiara blok JE 20/14, Kabupaten Bogor, Jawa Barat, 16969, Indonesia', 'pending', NULL, NULL, '2025-09-01 16:36:18'),
(3, 16, 120000.00, 'Bumi Mutiara blok JE 20/14, Kabupaten Bogor, Jawa Barat, 16969, Indonesia', 'pending', NULL, NULL, '2025-09-02 11:10:21'),
(4, 16, 120000.00, 'Kinaya Azzahra, 085878061240, Bumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969, Kec. Gunung Putri, Kabupaten Bogor, Jawa Barat, 16969, Indonesia', 'completed', NULL, NULL, '2025-09-02 11:52:40'),
(5, 16, 120000.00, 'Kinaya Azzahra, 085878061240, Bumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969, Kec. Gunung Putri, Kabupaten Bogor, Jawa Barat, 16969, Indonesia', 'pending', NULL, NULL, '2025-09-02 11:55:38'),
(6, 16, 240000.00, 'Kinaya Azzahra, 085878061240, Bumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969, Kec. Gunung Putri, Kabupaten Bogor, Jawa Barat, 16969, Indonesia', 'pending', NULL, NULL, '2025-09-22 11:53:18'),
(7, 16, 120000.00, 'Kinaya Azzahra, 085878061240, Bumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969, Kec. Gunung Putri, Kabupaten Bogor, Jawa Barat, 16969, Indonesia', 'pending', NULL, NULL, '2025-09-22 12:00:32'),
(8, 19, 135000.00, 'alissa fania, 08128877089, jalan sukadamai, tanah sereal , bogor, JAWA BARAT, 16165, Indonesia', 'pending', NULL, NULL, '2025-10-03 09:29:13'),
(9, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 16:56:13'),
(10, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:01:30'),
(11, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:02:12'),
(12, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:02:37'),
(13, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:16:40'),
(14, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:16:59'),
(15, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:17:23'),
(16, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:17:58'),
(17, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-07 17:20:35'),
(18, 16, 270000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-08 14:56:47'),
(19, 16, 270000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-08 14:56:55'),
(20, 19, 405000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 15:19:04'),
(21, 19, 405000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 15:21:38'),
(22, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 15:30:47'),
(23, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:00:27'),
(24, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:08:20'),
(25, 19, 135000.00, 'zayn malik\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:09:44'),
(26, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:24:22'),
(27, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:30:57'),
(28, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:48:04'),
(29, 19, 1350000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:49:57'),
(30, 19, 1350000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 16:57:48'),
(31, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, NULL, '2025-10-08 17:10:12'),
(32, 19, 1485000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 17:19:38'),
(33, 19, 675000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, NULL, '2025-10-08 17:26:29'),
(34, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 17:29:11'),
(35, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 17:29:35'),
(36, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 17:35:09'),
(37, 16, 270000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-08 17:36:28'),
(38, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-08 17:46:12'),
(39, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 17:53:00'),
(40, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:05:04'),
(41, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:30:52'),
(42, 19, 0.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:30:52'),
(43, 16, 270000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-08 18:32:18'),
(44, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:33:10'),
(45, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:38:12'),
(46, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:48:30'),
(47, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:52:54'),
(48, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-08 18:59:50'),
(49, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-12 16:10:13'),
(50, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, NULL, '2025-10-12 16:12:50'),
(51, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, NULL, '2025-10-12 16:20:28'),
(52, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-12 16:53:58'),
(53, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-12 17:01:47'),
(54, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-12 17:12:24'),
(55, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-12 17:24:23'),
(56, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-13 14:52:48'),
(57, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-13 14:56:13'),
(58, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-13 15:00:06'),
(59, 19, 270000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, 'ac9acf24-de8c-470b-a99c-57254bb1054c', '2025-10-13 15:07:34'),
(60, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, '23229bde-b5c4-47b5-91b4-cb2d2dffff5f', '2025-10-13 15:14:48'),
(61, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-17 15:00:28'),
(62, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-17 15:43:05'),
(63, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-17 16:47:06'),
(64, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-17 16:59:36'),
(65, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-17 17:03:27'),
(66, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-17 17:08:13'),
(67, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'pending', NULL, NULL, '2025-10-17 17:18:06'),
(68, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, '85d99069-a1f9-43c3-8f90-a385c0f86e7a', '2025-10-17 17:20:26'),
(69, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-23 15:03:55'),
(70, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-25 12:04:36'),
(71, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-25 12:04:50'),
(72, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-26 13:41:23'),
(73, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-26 13:42:47'),
(74, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-26 13:43:11'),
(75, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-10-26 14:28:23'),
(76, 16, 135000.00, 'Kinaya Azzahra\nBumi Mutiara blok JE 20/14 RT 09 RW 32, ID 16969\nKec. Gunung Putri, Kabupaten Bogor\nJawa Barat 16969\nIndonesia\nTelp: 085878061240', 'pending', NULL, NULL, '2025-11-03 14:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price_per_item` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price_per_item`) VALUES
(1, 1, NULL, 1, 20000.00),
(2, 2, NULL, 1, 120000.00),
(3, 3, NULL, 1, 120000.00),
(4, 4, NULL, 1, 120000.00),
(5, 5, NULL, 1, 120000.00),
(6, 6, NULL, 2, 120000.00),
(7, 7, NULL, 1, 120000.00),
(8, 8, NULL, 1, 135000.00),
(9, 9, NULL, 2, 135000.00),
(10, 10, NULL, 2, 135000.00),
(11, 11, NULL, 1, 135000.00),
(12, 12, NULL, 2, 135000.00),
(13, 13, NULL, 1, 135000.00),
(14, 14, NULL, 1, 135000.00),
(15, 15, NULL, 1, 135000.00),
(16, 16, NULL, 1, 135000.00),
(17, 17, NULL, 1, 135000.00),
(18, 18, NULL, 2, 135000.00),
(19, 19, NULL, 2, 135000.00),
(20, 20, NULL, 3, 135000.00),
(21, 21, NULL, 3, 135000.00),
(22, 22, NULL, 2, 135000.00),
(23, 23, NULL, 1, 135000.00),
(24, 24, NULL, 2, 135000.00),
(25, 25, NULL, 1, 135000.00),
(26, 26, NULL, 1, 135000.00),
(27, 27, NULL, 2, 135000.00),
(28, 28, NULL, 2, 135000.00),
(29, 29, NULL, 10, 135000.00),
(30, 30, NULL, 10, 135000.00),
(31, 31, NULL, 1, 135000.00),
(32, 32, NULL, 6, 135000.00),
(33, 32, NULL, 5, 135000.00),
(34, 33, NULL, 5, 135000.00),
(35, 34, NULL, 1, 135000.00),
(36, 35, NULL, 1, 135000.00),
(37, 36, NULL, 1, 135000.00),
(38, 37, NULL, 1, 135000.00),
(39, 37, NULL, 1, 135000.00),
(40, 38, NULL, 1, 135000.00),
(41, 39, NULL, 1, 135000.00),
(42, 40, NULL, 1, 135000.00),
(43, 41, NULL, 1, 135000.00),
(44, 43, NULL, 2, 135000.00),
(45, 44, NULL, 1, 135000.00),
(46, 45, NULL, 1, 135000.00),
(47, 46, NULL, 1, 135000.00),
(48, 47, NULL, 1, 135000.00),
(49, 48, NULL, 1, 135000.00),
(50, 49, NULL, 1, 135000.00),
(51, 50, NULL, 1, 135000.00),
(52, 51, NULL, 1, 135000.00),
(53, 52, NULL, 1, 135000.00),
(54, 53, NULL, 1, 135000.00),
(55, 54, NULL, 1, 135000.00),
(56, 55, NULL, 1, 135000.00),
(57, 56, NULL, 1, 135000.00),
(58, 57, NULL, 1, 135000.00),
(59, 58, NULL, 1, 135000.00),
(60, 59, NULL, 2, 135000.00),
(61, 60, NULL, 1, 135000.00),
(62, 61, NULL, 1, 135000.00),
(63, 62, NULL, 1, 135000.00),
(64, 63, NULL, 1, 135000.00),
(65, 64, NULL, 1, 135000.00),
(66, 65, NULL, 1, 135000.00),
(67, 66, NULL, 1, 135000.00),
(68, 67, NULL, 1, 135000.00),
(69, 68, NULL, 1, 135000.00),
(70, 69, NULL, 1, 135000.00),
(71, 70, 27, 1, 135000.00),
(72, 71, 28, 1, 135000.00),
(73, 72, 27, 1, 135000.00),
(74, 73, 27, 1, 135000.00),
(75, 74, 20, 1, 135000.00),
(76, 75, 20, 1, 135000.00),
(77, 76, 28, 1, 135000.00);

-- --------------------------------------------------------

--
-- Table structure for table `our_story`
--

CREATE TABLE `our_story` (
  `id` int(1) NOT NULL DEFAULT 1,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `our_story`
--

INSERT INTO `our_story` (`id`, `title`, `content`, `image_url`) VALUES
(1, 'Our Story', 'Ini adalah cerita tentang bagaimana Sauvett dimulai...', 'assets/images/our_story_default.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `additional_info` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `stock` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_featured` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `additional_info`, `price`, `image_url`, `stock`, `created_at`, `is_featured`) VALUES
(20, 'Latte - Chiffon Scarf | Celine Collection', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\"><b>Size: 180 cm x 70 cm</b></p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\"><b><br></b></p><p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Made from lightweight chiffon with an elegant flow, this scarf is created to move as smoothly as you do. Light and breathable, it provides comfort without compromising style. It’s a classic and timeless piece designed to enhance every part of your day.</p><div><br></div>', '<p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058175-img3320.jpg', 10, '2025-10-21 14:49:35', 1),
(21, 'White - Chiffon Scarf | Celine Collection', '<p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\"><b>Size: 180 cm x 70 cm</b></p><p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">\r\n</p><p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Made from lightweight chiffon with an elegant flow, this scarf is created to move as smoothly as you do. Light and breathable, it provides comfort without compromising style. It’s a classic and timeless piece designed to enhance every part of your day.</p>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058256-img3319.jpg', 10, '2025-10-21 14:50:56', 0),
(22, 'Esspreso - Chiffon Scarf | Celine Collection', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\"><b>Size: 180 cm x 70 cm</b></p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">\r\n</p><p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Made from lightweight chiffon with an elegant flow, this scarf is created to move as smoothly as you do. Light and breathable, it provides comfort without compromising style. It’s a classic and timeless piece designed to enhance every part of your day.</p><div style=\"text-align: justify; \"><br></div>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058350-img3323.jpg', 10, '2025-10-21 14:52:30', 0),
(23, 'Sapphire - Chiffon Scarf | Serene Collection', '<p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\"><b>Size: 180 cm x 70 cm</b></p><p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">\r\n</p><p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Made from lightweight chiffon with an elegant flow, this scarf is created to move as smoothly as you do. Light and breathable, it provides comfort without compromising style. It’s a classic and timeless piece designed to enhance every part of your day.</p>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058446-img3316.jpg', 10, '2025-10-21 14:54:06', 0),
(24, 'Ruby - Chiffon Scarf | Serene Collection', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"font-weight: bolder;\">Size: 180 cm x 70 cm</span></p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">\r\n</p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Made from lightweight chiffon with an elegant flow, this scarf is created to move as smoothly as you do. Light and breathable, it provides comfort without compromising style. It’s a classic and timeless piece designed to enhance every part of your day.</p>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058493-img3317.jpg', 10, '2025-10-21 14:54:53', 0),
(25, 'Pearl - Chiffon Scarf | Serene Collection', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"font-weight: bolder;\">Size: 180 cm x 70 cm</span></p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">\r\n</p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Made from lightweight chiffon with an elegant flow, this scarf is created to move as smoothly as you do. Light and breathable, it provides comfort without compromising style. It’s a classic and timeless piece designed to enhance every part of your day.</p>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058545-img3318.jpg', 10, '2025-10-21 14:55:45', 0),
(26, 'Rosee - Viscose Scarf | Fleur Collection', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"font-weight: bolder;\">Size: 185 cm x 75 cm</span></p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">\r\n</p><p class=\"QN2lPu\" style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"text-align: start;\">Crafted from a premium soft-structured fabric with the feel of viscose and the strength of polyester, this scarf is made to follow your pace, from quiet mornings to city rush hours. Lightweight, breathable, and effortlessly flowy, it’s designed to stay with you&nbsp;all&nbsp;day&nbsp;long.</span></p>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058664-catalog-3-rosee.png', 10, '2025-10-21 14:57:44', 0),
(27, 'Creme - Viscose Scraf | Fleur Collection', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"font-weight: bolder;\">Size: 185 cm x 75 cm</span></p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">\r\n</p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"text-align: start;\">Crafted from a premium soft-structured fabric with the feel of viscose and the strength of polyester, this scarf is made to follow your pace, from quiet mornings to city rush hours. Lightweight, breathable, and effortlessly flowy, it’s designed to stay with you&nbsp;all&nbsp;day&nbsp;long.</span></p>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058795-catalog-1-creme.png', 10, '2025-10-21 14:59:55', 0),
(28, 'Dune - Viscose Scarf | Fleur Collection', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"font-weight: bolder;\">Size: 185 cm x 75 cm</span></p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">\r\n</p><p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\"><span style=\"text-align: start;\">Crafted from a premium soft-structured fabric with the feel of viscose and the strength of polyester, this scarf is made to follow your pace, from quiet mornings to city rush hours. Lightweight, breathable, and effortlessly flowy, it’s designed to stay with you&nbsp;all&nbsp;day&nbsp;long.</span></p>', '<p class=\"QN2lPu\" style=\"margin-right: 0px; margin-bottom: 0px; margin-left: 0px; white-space-collapse: preserve; text-align: justify; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px;\">Notes:</p><ol><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sebelum melakukan pembelian, mohon periksa terlebih dahulu detail produk untuk memastikan kesesuaiannya.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Warna produk pada foto memiliki akurasi sekitar 85%, namun bisa sedikit berbeda tergantung pencahayaan dan pengaturan layar perangkat yang digunakan.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Keluhan hanya dapat diajukan maksimal 7x24 jam setelah produk diterima.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Harap sertakan video unboxing saat mengajukan komplain. Pengajuan tanpa video unboxing tidak dapat diproses.</li><li style=\"text-align: justify; margin-right: 0px; margin-bottom: 0px; margin-left: 0px; color: rgba(0, 0, 0, 0.8); font-family: Roboto, SHPBurmese, SHPKhmer, &quot;Helvetica Neue&quot;, Helvetica, Arial, 文泉驛正黑, &quot;WenQuanYi Zen Hei&quot;, &quot;Hiragino Sans GB&quot;, &quot;儷黑 Pro&quot;, &quot;LiHei Pro&quot;, &quot;Heiti TC&quot;, 微軟正黑體, &quot;Microsoft JhengHei UI&quot;, &quot;Microsoft JhengHei&quot;, sans-serif; font-size: 14px; white-space-collapse: preserve;\">Sauvatte tidak menerima pengembalian produk dengan alasan \"berubah pikiran\" atau \"produk tidak cocok\".</li></ol>', 135000.00, '1761058837-catalog-2-dune.png', 10, '2025-10-21 15:00:37', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_url`) VALUES
(4, 23, '1761058446-gallery-0-img3275.jpg'),
(5, 24, '1761058493-gallery-0-img3279.jpg'),
(6, 25, '1761058545-gallery-0-img3284.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = belum, 1 = sudah',
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `is_verified`, `verification_token`, `reset_token`, `reset_token_expires_at`, `created_at`) VALUES
(1, 'Admin', 'admin@sauvatte.com', '$2y$10$.fa709q1smdzxQ0uhjD2j.gFA90nkbTYGqeAVufdoMGilQcGAUp4.', 'admin', 0, NULL, NULL, NULL, '2025-07-18 15:19:42'),
(14, 'naya', 'kinaya.azzahra77@gmail.com', '$2y$10$TXph8okhpB/0VmFP/F.CO.KfDz9D.TY7wPApv1wK1TCtnZVsaX9cK', 'user', 1, NULL, NULL, NULL, '2025-07-25 15:44:10'),
(16, 'blanca', 'kinayaazzahra11@gmail.com', '$2y$10$lTN3V4p4c6tukx/K4wFuxOUGKyJVTMDcXlkr1DwCgzC.2eAdjkqzG', 'user', 1, NULL, NULL, NULL, '2025-08-14 18:22:09'),
(17, 'lemon', 'rahazzayanaki13@gmail.com', '$2y$10$mTVODecTz.3IcIPgLH8ljeP1oluKcFa/ZaEyza/iRJg4FFSy67gBe', 'user', 1, NULL, NULL, NULL, '2025-09-22 11:21:27'),
(19, 'Testing', 'testing123@gmail.com', '$2y$10$ZADrbJf485GI4sLWOFiFH.NAAanOBNLZiZmHZdaaqMplFMfmx0z7a', 'user', 1, NULL, NULL, NULL, '2025-10-03 06:19:03');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `our_story`
--
ALTER TABLE `our_story`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `addresses`
--
ALTER TABLE `addresses`
  ADD CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
