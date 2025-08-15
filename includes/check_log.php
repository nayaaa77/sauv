<?php
// File ini untuk memastikan hanya user yang login bisa akses halaman tertentu.
// Pastikan session sudah dimulai (biasanya sudah di db_connect.php)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php?error=Harus login terlebih dahulu!");
    exit();
}
?>