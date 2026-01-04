<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Gunakan require_once untuk keamanan
require_once 'includes/db_conn.php'; 
require_once 'includes/header.php'; 

$cat_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
?>

<div class="page-content">
    <div class="container">
        <h2 style="margin-top: 40px; margin-bottom: 20px;">
            <?php 
            if($cat_filter > 0) {
                $c_stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
                $c_stmt->bind_param("i", $cat_filter);
                $c_stmt->execute();
                $res_cat_name = $c_stmt->get_result()->fetch_assoc();
                echo "Category: " . htmlspecialchars($res_cat_name['name']);
            } else {
                echo "Shop All";
            }
            ?>
        </h2>
        
        <div class="product-grid" style="margin-top: 30px; margin-bottom: 60px;">
            <?php
            if ($cat_filter > 0) {
                $sql = "SELECT * FROM products WHERE category_id = ? ORDER BY id DESC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $cat_filter);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                $sql = "SELECT * FROM products ORDER BY id DESC";
                $result = $conn->query($sql);
            }

            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $card_class = ($row['stock'] <= 0) ? 'product-card sold-out' : 'product-card';
                    
                    // --- LOGIKA PATH GAMBAR ---
                    $img_filename = $row['image_url'];
                    
                    // Cek apakah di database tersimpan path lengkap atau cuma nama file
                    if (strpos($img_filename, 'assets/img/') !== false || strpos($img_filename, 'uploads/') !== false) {
                        $path_asli = $img_filename; // Sudah ada pathnya
                    } else {
                        // Asumsikan ada di assets/img/ jika cuma nama file
                        $path_asli = 'assets/img/' . $img_filename;
                    }

                    // Encode URL agar spasi dan # terbaca browser
                    // Contoh: "baju lebaran #1.jpg" -> "baju%20lebaran%20%231.jpg"
                    $img_final = str_replace([' ', '#'], ['%20', '%23'], $path_asli);
                    // ---------------------------
                    ?>
                    
                    <div class="<?php echo $card_class; ?>">
                        <div class="product-image-container">
                            <a href="product_detail.php?id=<?php echo $row['id']; ?>">
                                <img src="<?php echo $img_final; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" onerror="this.onerror=null; this.src='assets/img/default.jpg';">
                                
                                <?php if ($row['stock'] <= 0): ?>
                                    <div class="product-overlay"><span>SOLD OUT</span></div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="product-info">
                            <h3><a href="product_detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></a></h3>
                            <p class="product-price">Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></p>
                        </div>
                    </div>
                <?php }
            } else {
                echo "<p class='text-center w-100'>Produk tidak ditemukan.</p>";
            }
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>