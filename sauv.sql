-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 15, 2025 at 01:27 PM
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`id`, `title`, `content`, `image_url`, `author_id`, `created_at`) VALUES
(6, 'Introducing 1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '1756733352_#aesthetic #japan.jpg', 1, '2025-09-01 13:29:12');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_qa`
--

CREATE TABLE `chatbot_qa` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `answer` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `chatbot_qa`
--

INSERT INTO `chatbot_qa` (`id`, `question`, `answer`) VALUES
(1, 'Apa saja metode pembayaran yang tersedia?', 'Kami menerima pembayaran melalui transfer bank, GoPay, dan OVO.'),
(2, 'Berapa lama waktu pengiriman?', 'Untuk wilayah Jabodetabek, estimasi pengiriman adalah 2-3 hari kerja. Untuk luar Jabodetabek, estimasi 4-7 hari kerja.'),
(3, 'Bagaimana cara melacak pesanan saya?', 'Setelah pesanan dikirim, Anda akan menerima nomor resi melalui email. Anda bisa melacaknya di situs web kurir terkait.');

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
(60, 19, 135000.00, 'joko kemal\nJl. Betet blok C1, No 1, RT011/RW012, Jatiasih, Bekasi\njatiasih, Bekasi\nJawa Barat 17875\nIndonesia\nTelp: 085921896187', 'confirmed', NULL, '23229bde-b5c4-47b5-91b4-cb2d2dffff5f', '2025-10-13 15:14:48');

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
(8, 8, 18, 1, 135000.00),
(9, 9, 17, 2, 135000.00),
(10, 10, 17, 2, 135000.00),
(11, 11, 18, 1, 135000.00),
(12, 12, 16, 2, 135000.00),
(13, 13, 17, 1, 135000.00),
(14, 14, 16, 1, 135000.00),
(15, 15, 17, 1, 135000.00),
(16, 16, 17, 1, 135000.00),
(17, 17, 16, 1, 135000.00),
(18, 18, 16, 2, 135000.00),
(19, 19, 16, 2, 135000.00),
(20, 20, 16, 3, 135000.00),
(21, 21, 16, 3, 135000.00),
(22, 22, 16, 2, 135000.00),
(23, 23, 16, 1, 135000.00),
(24, 24, 19, 2, 135000.00),
(25, 25, 19, 1, 135000.00),
(26, 26, 16, 1, 135000.00),
(27, 27, 16, 2, 135000.00),
(28, 28, 17, 2, 135000.00),
(29, 29, 17, 10, 135000.00),
(30, 30, 16, 10, 135000.00),
(31, 31, 16, 1, 135000.00),
(32, 32, 17, 6, 135000.00),
(33, 32, 18, 5, 135000.00),
(34, 33, 18, 5, 135000.00),
(35, 34, 18, 1, 135000.00),
(36, 35, 19, 1, 135000.00),
(37, 36, 19, 1, 135000.00),
(38, 37, 16, 1, 135000.00),
(39, 37, 17, 1, 135000.00),
(40, 38, 19, 1, 135000.00),
(41, 39, 19, 1, 135000.00),
(42, 40, 19, 1, 135000.00),
(43, 41, 16, 1, 135000.00),
(44, 43, 19, 2, 135000.00),
(45, 44, 16, 1, 135000.00),
(46, 45, 17, 1, 135000.00),
(47, 46, 16, 1, 135000.00),
(48, 47, 18, 1, 135000.00),
(49, 48, 16, 1, 135000.00),
(50, 49, 18, 1, 135000.00),
(51, 50, 17, 1, 135000.00),
(52, 51, 19, 1, 135000.00),
(53, 52, 16, 1, 135000.00),
(54, 53, 17, 1, 135000.00),
(55, 54, 18, 1, 135000.00),
(56, 55, 18, 1, 135000.00),
(57, 56, 17, 1, 135000.00),
(58, 57, 16, 1, 135000.00),
(59, 58, 19, 1, 135000.00),
(60, 59, 18, 2, 135000.00),
(61, 60, 17, 1, 135000.00);

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
(16, 'Creme', 'catalog 1', '', 135000.00, '1759468507-catalog-1-creme.png', 10, '2025-10-03 05:15:07', 0),
(17, 'Dune', 'catalog 2', '', 135000.00, '1759468621-catalog-2-dune.png', 9, '2025-10-03 05:17:01', 0),
(18, 'Rosee', 'catalog 3', '', 135000.00, '1759468642-catalog-3-rosee.png', 8, '2025-10-03 05:17:22', 0),
(19, 'Brume', 'catalog 4', '', 135000.00, '1759468664-catalog-4-brume.png', 10, '2025-10-03 05:17:44', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(18, 'alissa', 'alissafania58@gmail.com', '$2y$10$1XrZ9z34ZdVV2MXJyS5QPumDFN48.K.kJtUUFg0wxdS1hSOMyo/sq', 'user', 0, '020c1e1cf5c6458efe4c3c24debdc734753fffb01c3d35bd506f5c912ca2a5c7', NULL, NULL, '2025-10-03 04:53:31'),
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
-- Indexes for table `chatbot_qa`
--
ALTER TABLE `chatbot_qa`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `chatbot_qa`
--
ALTER TABLE `chatbot_qa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
