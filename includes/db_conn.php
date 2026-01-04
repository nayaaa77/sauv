<?php
// 1. Definisikan Konstanta (Cek dulu apakah sudah ada agar tidak error)
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', 'admin123'); // Password Anda tetap admin123
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'sauv');
}

// 2. Buat Koneksi (Hanya jika variabel $conn belum tersedia atau sudah tertutup)
if (!isset($conn) || !($conn instanceof mysqli)) {
    
    // Membuat koneksi baru
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Koneksi ke database gagal: " . $conn->connect_error);
    }

    // [PENAMBAHAN] Atur zona waktu koneksi ke UTC+7 (Waktu Indonesia Barat)
    // Kode ini tetap dipertahankan sesuai permintaan Anda
    $conn->query("SET time_zone = '+07:00'");
}
?>