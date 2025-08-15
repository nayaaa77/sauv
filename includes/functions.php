<?php
// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Memaksa user untuk login, jika belum
function require_login() {
    if (!is_logged_in()) {
        header('Location: login_register.php');
        exit();
    }
}

// Cek apakah user adalah admin
function is_admin() {
    return (is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

// Memaksa user untuk menjadi admin, jika bukan
function require_admin() {
    if (!is_admin()) {
        // Bisa redirect ke halaman login admin atau halaman utama
        header('Location: ../index.php'); 
        exit();
    }
}
?>