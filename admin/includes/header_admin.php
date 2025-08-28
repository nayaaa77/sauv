<?php
// Cek jika sesi belum dimulai, maka mulai sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Proteksi Halaman
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login_admin.php');
    exit();
}
require_once '../includes/db_conn.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Sauvatte</title>
    <link rel="stylesheet" href="assets/css/admin-style.css"> 
    <!-- Font Awesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar Navigasi -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Sauvatte Admin</h3>
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage_products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="manage_chatbot.php"><i class="fas fa-robot"></i> Chatbot</a></li> 
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <!-- Konten Utama -->
        <main class="main-content">
            <header class="main-header">
                <div class="header-title">
                    <!-- Judul halaman akan diisi oleh setiap halaman -->
                </div>
                <div class="header-user">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
            </header>
            <div class="content-body">
                <!-- Kode HTML dari halaman spesifik (seperti index.php) akan dimulai di sini -->
