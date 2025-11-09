<?php
// Selalu mulai sesi untuk dapat mengakses dan menghapusnya
session_start();

// Simpan pesan notifikasi di session SEBELUM session dihancurkan
$_SESSION['flash_message'] = "See you next time! You've been logged out.";

// 1. Hapus semua variabel sesi yang terkait dengan login
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_role']);

// 2. Redirect pengguna ke halaman home (tanpa parameter URL)
header("Location: index.php");
exit();
?>