<?php
// file: includes/midtrans_config.php

// 1. Muat autoloader dari Composer
// Sesuaikan path jika file ini ada di dalam subfolder
require_once __DIR__ . '/../vendor/autoload.php';

// 2. Muat variabel dari file .env
// __DIR__ . '/../' artinya "naik satu level direktori" dari folder 'includes'
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// 3. Set konfigurasi Midtrans menggunakan variabel dari .env
// Ambil nilai dari .env menggunakan $_ENV['NAMA_VARIABEL']
\Midtrans\Config::$serverKey = $_ENV['MIDTRANS_SERVER_KEY'];
\Midtrans\Config::$isProduction = false; // Ganti jadi true jika sudah production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

?>