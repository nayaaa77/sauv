<?php
// Selalu mulai session di header agar tersedia di semua halaman
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale-1.0">
    <title>Sauvatte</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <nav class="navbar">
                <div class="nav-brand">
                    <a href="index.php">SAUVATTE</a>
                </div>
                <ul class="nav-menu">
                    <li><a href="shop.php">Shop</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="our_story.php">Our Story</a></li>
                </ul>
                <div class="nav-icons">
                    <span class="divider">|</span>
                    <a href="cart.php" aria-label="Shopping Cart">
                        <i class="fas fa-shopping-cart"></i>
                    </a>

                    <?php if (is_logged_in()): ?>
                        <a href="my_account.php" class="header-user-link" aria-label="My Account">
                            <i class="fas fa-user"></i>
                            <?php if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])): ?>
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <a href="login_register.php" aria-label="My Account">
                            <i class="fas fa-user"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    <main class="main-content">