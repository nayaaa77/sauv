<?php
// Selalu mulai session di header agar tersedia di semua halaman
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Gunakan include_once untuk menghindari double include error
include_once __DIR__ . '/db_conn.php'; 
require_once __DIR__ . '/functions.php';

// --- LOGIKA AMBIL KATEGORI (HIERARKI + CEK PRODUK) ---
$mega_menu = [];
$active_category_ids = []; // Array untuk menampung ID kategori yang punya produk

if (isset($conn) && $conn instanceof mysqli) {
    // Cek koneksi aman
    if (!mysqli_connect_errno() && $conn->ping()) {
        
        // 1. Ambil ID kategori yang SUDAH memiliki produk
        $query_prod = "SELECT DISTINCT category_id FROM products";
        $result_prod = mysqli_query($conn, $query_prod);
        if ($result_prod) {
            while ($p_row = mysqli_fetch_assoc($result_prod)) {
                $active_category_ids[] = $p_row['category_id'];
            }
        }

        // 2. Ambil semua kategori
        $query_cat = "SELECT * FROM categories ORDER BY parent_id ASC, name ASC";
        $result_cat = mysqli_query($conn, $query_cat);
        
        $categories_raw = [];
        if ($result_cat) {
            while($row = mysqli_fetch_assoc($result_cat)) {
                $categories_raw[] = $row;
            }
        }

        // Tahap 1: Siapkan Array untuk Parent (Kategori Utama)
        foreach ($categories_raw as $cat) {
            if ($cat['parent_id'] === NULL) {
                // Simpan ID, Slug, dan Nama untuk pengecekan nanti
                $mega_menu[$cat['name']] = [
                    'id' => $cat['id'],
                    'slug' => $cat['slug'],
                    'children' => [] 
                ]; 
            }
        }

        // Tahap 2: Masukkan Sub-Category (Anak) ke dalam Parent yang tepat
        foreach ($categories_raw as $cat) {
            if ($cat['parent_id'] !== NULL) {
                foreach ($categories_raw as $parent) {
                    if ($parent['id'] == $cat['parent_id']) {
                        if (isset($mega_menu[$parent['name']])) {
                            $mega_menu[$parent['name']]['children'][] = [
                                'name' => $cat['name'],
                                'slug' => $cat['slug']
                            ];
                        }
                        break;
                    }
                }
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
        /* --- CSS MEGA MENU KREATIF --- */
        .nav-menu li {
            position: relative;
            padding: 10px 0;
        }

        /* Container Dropdown */
        .dropdown-content {
            display: block;
            visibility: hidden;
            opacity: 0;
            position: absolute;
            background-color: #ffffff;
            min-width: 450px;
            box-shadow: 0px 10px 30px rgba(0,0,0,0.08);
            z-index: 1000;
            top: 100%;
            left: -20px;
            border-radius: 8px;
            padding: 25px;
            border: 1px solid rgba(0,0,0,0.05);
            
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;

            transform: translateY(15px);
            transition: all 0.3s ease-in-out;
        }

        .nav-menu li:hover .dropdown-content {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .menu-column h3 {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #000;
            margin: 0 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #d4af37;
            display: inline-block;
        }
        
        /* Link pada judul H3 agar bisa diklik */
        .menu-column h3 a {
            text-decoration: none;
            color: inherit;
            transition: color 0.2s;
        }
        .menu-column h3 a:hover {
            color: #555;
        }

        .menu-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .menu-column ul li {
            margin-bottom: 8px;
            padding: 0;
        }

        .menu-column ul li a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            display: block;
            transition: all 0.2s;
            position: relative;
            padding-left: 0;
        }

        .menu-column ul li a:hover {
            color: #000;
            padding-left: 10px;
        }

        .menu-column ul li a:hover::before {
            content: '›';
            position: absolute;
            left: 0;
            color: #d4af37;
        }
        
        /* Style khusus Coming Soon */
        .coming-soon-text {
            font-size: 12px; 
            color: #aaa; 
            font-style: italic; 
            margin-top: 5px;
            display: block;
        }

        .fa-chevron-down {
            font-size: 0.7em;
            margin-left: 4px;
            vertical-align: middle;
            transition: transform 0.3s;
        }

        .nav-menu li:hover .fa-chevron-down {
            transform: rotate(180deg);
        }

        /* Select2 Fixes */
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
                        <a href="shop.php" style="cursor: pointer;">
                            SHOP <i class="fas fa-chevron-down"></i>
                        </a>
                        
                        <div class="dropdown-content">
                            <?php if (!empty($mega_menu)): ?>
                                <?php foreach ($mega_menu as $parent_name => $data): ?>
                                    
                                    <?php 
                                        $sub_categories = $data['children'];
                                        $parent_id = $data['id'];
                                        $parent_slug = $data['slug'];
                                        
                                        // Cek apakah kategori ini punya produk?
                                        $has_products = in_array($parent_id, $active_category_ids);
                                    ?>

                                    <div class="menu-column">
                                        <h3>
                                            <a href="shop.php?category=<?php echo htmlspecialchars($parent_slug); ?>">
                                                <?php echo htmlspecialchars($parent_name); ?>
                                            </a>
                                        </h3>
                                        
                                        <?php if (!empty($sub_categories)): ?>
                                            <ul>
                                                <?php foreach ($sub_categories as $item): ?>
                                                    <li>
                                                        <a href="shop.php?category=<?php echo htmlspecialchars($item['slug']); ?>">
                                                            <?php echo htmlspecialchars($item['name']); ?>
                                                        </a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        
                                        <?php elseif ($has_products): ?>
                                            <ul>
                                                <li>
                                                    <a href="shop.php?category=<?php echo htmlspecialchars($parent_slug); ?>" style="font-weight:500;">
                                                        Lihat Koleksi <?php echo htmlspecialchars($parent_name); ?>
                                                    </a>
                                                </li>
                                            </ul>

                                        <?php else: ?>
                                            <span class="coming-soon-text">Coming Soon</span>
                                        
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding: 20px; text-align: center; color: #999; grid-column: span 2;">
                                    Belum ada kategori.
                                </div>
                            <?php endif; ?>

                            <div style="grid-column: span 2; margin-top: 15px; text-align: center; border-top: 1px dashed #eee; padding-top: 15px;">
                                <a href="shop.php" style="font-size: 12px; font-weight: bold; text-decoration: underline; color: #333;">LIHAT SEMUA PRODUK</a>
                            </div>
                        </div>
                    </li>
                    
                    <li><a href="blog.php">BLOG</a></li>
                    <li><a href="our_story.php">OUR STORY</a></li>
                </ul>

                <div class="nav-icons">
                    <span class="divider">|</span>
                    
                    <a href="cart.php" aria-label="Shopping Cart" class="cart-icon-wrapper">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        // Logika Hitung Keranjang
                        $cart_count = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                if (is_array($item)) {
                                    $qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                                    $cart_count += $qty;
                                } elseif (is_numeric($item)) {
                                    $cart_count += (int)$item;
                                } else {
                                    $cart_count++;
                                }
                            }
                        }
                        
                        if ($cart_count > 0):
                        ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>

                    <?php if (isset($_SESSION['user_id']) || (function_exists('is_logged_in') && is_logged_in())): ?>
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