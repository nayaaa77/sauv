<?php
// Selalu mulai sesi untuk dapat mengakses dan menghapusnya
session_start();

// 1. Hapus semua variabel sesi yang terdaftar
$_SESSION = array();

// 2. Hancurkan sesi di server
session_destroy();

// 3. Alihkan pengguna ke halaman login admin
// Pastikan nama file login Anda benar (login_admin.php atau login.php)
header("Location: login_admin.php");
exit();
?>
