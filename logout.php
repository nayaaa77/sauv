<?php
// Selalu mulai sesi untuk dapat mengakses dan menghapusnya
session_start();

// 1. Hapus semua variabel sesi
$_SESSION = array();

// 2. Hancurkan sesi di server
session_destroy();

// 3. Redirect pengguna ke halaman home DENGAN pesan sukses
// [DIUBAH] Menambahkan ?logout=success di akhir URL
header("Location: index.php?logout=success");
exit();
?>