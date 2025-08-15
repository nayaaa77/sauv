<?php
// Ganti nilai ini sesuai dengan konfigurasi database Anda
define('DB_HOST', 'localhost');
define('DB_USER', 'test');
define('DB_PASS', 'admin');
define('DB_NAME', 'sauv');

// Membuat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// [PENAMBAHAN] Atur zona waktu koneksi ke UTC+7 (Waktu Indonesia Barat)
$conn->query("SET time_zone = '+07:00'");

?>