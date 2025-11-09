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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
        <div class="sidebar-header">
                <img src="../assets/img/logo.png" alt="Sauvatte Admin Logo" class="sidebar-logo">
            </div>
            <ul class="sidebar-menu">
                <li><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="manage_products.php"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="manage_blog.php"><i class="fas fa-newspaper"></i> Blog</a></li>
                <li><a href="orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <div class="header-title"></div>
                <div class="header-user">
                    
<span>Sauvatte HQ</span>
                </div>
            </header>
            <div class="content-body">