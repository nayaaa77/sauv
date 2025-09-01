<?php
// Logika untuk menangkap notifikasi logout
$logout_notification = '';
if (isset($_GET['logout']) && $_GET['logout'] === 'success') {
    $logout_notification = "Anda telah berhasil logout";
}

// Sertakan koneksi database dan header
include 'includes/db_conn.php'; 
include 'includes/header.php'; 
?>

<div class="page-content">
    <?php
    // Bagian untuk menampilkan notifikasi jika ada
    if (!empty($logout_notification)):
    ?>
        <div class="container">
            <div class="notification success">
                <?php echo $logout_notification; ?>
            </div>
        </div>
    <?php endif; ?>

    <section class="hero-banner-section">
        <?php
        // Ambil 1 produk terbaru yang ditandai sebagai 'featured'
        $stmt_hero = $conn->prepare("SELECT id, name, image_url, description FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT 1");
        $stmt_hero->execute();
        $result_hero = $stmt_hero->get_result();
        
        if ($result_hero->num_rows > 0) {
            $hero_product = $result_hero->fetch_assoc();
            ?>
            <a href="product_detail.php?id=<?php echo $hero_product['id']; ?>" class="hero-banner-link">
                <img src="assets/img/<?php echo htmlspecialchars($hero_product['image_url']); ?>" alt="<?php echo htmlspecialchars($hero_product['name']); ?>" class="hero-banner-image">
                <div class="hero-banner-overlay">
                    <h1 class="hero-banner-title"><?php echo htmlspecialchars($hero_product['name']); ?></h1>
                    <p class="hero-banner-desc"><?php echo htmlspecialchars(substr($hero_product['description'], 0, 100)) . '...'; ?></p>
                    <span class="hero-banner-cta">Shop Now</span>
                </div>
            </a>
            <?php
        } else {
            // Opsional: Tampilkan sesuatu jika tidak ada hero banner
        }
        $stmt_hero->close();
        ?>
    </section>

    <div class="container">
        <section class="latest-products-section">
            <div class="section-header">
                <h2>Shop The Latest</h2>
                <a href="shop.php" class="view-all-link">View All</a>
            </div>
            
            <div class="product-grid">
                <?php
                // Ambil 6 produk terbaru
                $result = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
                while ($row = $result->fetch_assoc()):
                ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?php echo $row['id']; ?>">
                        <div class="product-image-container">
                            <img src="./assets/img/<?php echo $row['image_url']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                        </div>
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p class="product-price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>
    
</div>

<?php include 'includes/footer.php'; ?>