<?php
// Selalu mulai session di header agar tersedia di semua halaman
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'functions.php';
include_once 'db_conn.php';

$categories = []; // Siapkan array kosong
if (isset($conn) && $conn instanceof mysqli) {
    // Cek apakah koneksi masih terbuka (tidak error)
    if (!mysqli_connect_errno() && $conn->ping()) {
        $query_cat = "SELECT * FROM categories ORDER BY name ASC";
        $result_cat = mysqli_query($conn, $query_cat);
        if ($result_cat) {
            while($row = mysqli_fetch_assoc($result_cat)) {
                $categories[] = $row;
            }
        }
    }
}
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sauvatte</title>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="assets/js/provinces.js"></script> 
    
    <style>
        /* Menyesuaikan Select2 agar pas dengan tinggi form */
        .select2-container--default .select2-selection--single {
            height: 40px; 
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            padding-left: 12px;
            color: #333;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
        }
        .select2-container {
            width: 100% !important; 
        }

        /* Styling Dropdown Navbar (Tambahan untuk menu Shop) */
        .nav-menu li {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 180px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.1);
            z-index: 1000;
            list-style: none;
            padding: 10px 0;
            top: 100%;
            left: 0;
            border-radius: 4px;
        }

        .nav-menu li:hover .dropdown-content {
            display: block;
        }

        .dropdown-content li a {
            padding: 10px 20px;
            display: block;
            color: #333;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s;
        }

        .dropdown-content li a:hover {
            background-color: #f5f5f5;
            color: #000;
        }

        .nav-menu a i {
            margin-left: 5px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="container">
            <nav class="navbar">
                <div class="nav-brand">
                    <a href="index.php">SAUVATTE</a>
                </div>

                <button class="mobile-menu-toggle" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>

                <ul class="nav-menu" id="navMenu">
                    <li class="dropdown">
                        <a href="shop.php">Shop <i class="fas fa-chevron-down"></i></a>
                        <ul class="dropdown-content">
                            <li><a href="shop.php">Semua Produk</a></li>
                            <?php if ($result_cat && mysqli_num_rows($result_cat) > 0): ?>
                                <?php while($cat = mysqli_fetch_assoc($result_cat)): ?>
                                    <li>
                                        <a href="shop.php?category=<?php echo $cat['id']; ?>">
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </a>
                                    </li>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="our_story.php">Our Story</a></li>
                </ul>

                <div class="nav-icons">
                    <span class="divider">|</span>
                    
                    <a href="cart.php" aria-label="Shopping Cart" class="cart-icon-wrapper">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        $cart_count = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            $cart_count = count($_SESSION['cart']);
                        }
                        if ($cart_count > 0):
                        ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>

                    <?php if (is_logged_in()): ?>
                        <a href="my_account.php" class="header-user-link" aria-label="My Account">
                            <i class="fas fa-user"></i>
                            <?php if (isset($_SESSION['user_name']) && !empty($_SESSION['user_name'])): ?>
                                <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <a href="login_register.php" aria-label="Login">
                            <i class="fas fa-sign-in-alt"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>
    <main class="main-content"> 