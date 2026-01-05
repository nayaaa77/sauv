<?php 
// Include file penting
include 'includes/header.php'; 
// Gunakan include_once untuk db_conn agar aman
include_once 'includes/db_conn.php'; 

// --- LOGIKA FILTER KATEGORI ---
$cat_filter_id = 0;
$page_title = "Shop All"; 

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $param = $_GET['category'];
    $stmt_cat = $conn->prepare("SELECT id, name FROM categories WHERE id = ? OR slug = ?");
    $stmt_cat->bind_param("ss", $param, $param);
    $stmt_cat->execute();
    $res_cat = $stmt_cat->get_result();

    if ($row_cat = $res_cat->fetch_assoc()) {
        $cat_filter_id = $row_cat['id'];
        $page_title = ucfirst($row_cat['name']); 
    }
    $stmt_cat->close();
}
?>

<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 30px; 
        width: 100%;
        margin-top: 30px; 
        margin-bottom: 60px;
    }
</style>

<div class="page-content">
    <div class="container">
        <h2 style="margin-top: 40px;"><?php echo htmlspecialchars($page_title); ?></h2>
        <p>Explore the complete collection.</p>
        
        <div class="product-grid">
            <?php
            // --- LOGIKA QUERY DATABASE (PERBAIKAN 1: Tambahkan is_sold_out) ---
            if ($cat_filter_id > 0) {
                // Menambahkan kolom is_sold_out di SELECT
                $sql = "SELECT id, name, price, image_url, stock, is_sold_out FROM products WHERE category_id = ? ORDER BY name ASC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $cat_filter_id);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                // Menambahkan kolom is_sold_out di SELECT
                $sql = "SELECT id, name, price, image_url, stock, is_sold_out FROM products ORDER BY name ASC";
                $result = $conn->query($sql);
            }

            // --- TAMPILAN PRODUK ---
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    
                    // --- PERBAIKAN 2: Logic Sold Out Gabungan ---
                    // Cek apakah stok habis ATAU (is_sold_out ada DAN bernilai 1)
                    $is_really_sold_out = ($row['stock'] <= 0) || (isset($row['is_sold_out']) && $row['is_sold_out'] == 1);
                    
                    // Tentukan class CSS berdasarkan status gabungan
                    $card_class = $is_really_sold_out ? 'product-card sold-out' : 'product-card';
                    
                    echo '<div class="' . $card_class . '">';
                    echo '  <div class="product-image-container">';
                    echo '      <a href="product_detail.php?id=' . $row['id'] . '">';
                    echo '          <img src="./assets/img/' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    
                    // Tampilkan Overlay jika status gabungan TRUE
                    if ($is_really_sold_out) {
                        echo '          <div class="product-overlay">';
                        echo '              <span>SOLD OUT</span>';
                        echo '          </div>';
                    }
                    
                    echo '      </a>';
                    echo '  </div>'; 

                    echo '  <h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '  <p class="product-price">Rp ' . number_format($row['price'], 0, ',', '.') . '</p>';
                    echo '  <a href="product_detail.php?id=' . $row['id'] . '" class="btn-detail">Detail</a>';
                    echo '</div>'; 
                }
            } else {
                echo "<div style='grid-column: 1 / -1; padding: 20px 0; color: #666;'>";
                echo "<p>Belum ada produk yang tersedia di kategori ini.</p>";
                echo "</div>";
            }
            
            if (isset($stmt)) $stmt->close();
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>