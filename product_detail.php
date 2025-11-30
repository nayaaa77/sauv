<?php 
include 'includes/header.php'; 
include 'includes/db_conn.php';
?>

<div class="page-content">
    <?php
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($product_id === 0) {
        echo "<div class='container'><p>Produk tidak valid.</p></div>";
    } else {
        // 1. Query Data Produk Utama & Gambar
        $query = "
            SELECT 
                p.id, p.name, p.description, p.additional_info, p.price, p.stock, p.image_url AS main_image,
                pi.image_url AS gallery_image
            FROM products p
            LEFT JOIN (SELECT * FROM product_images WHERE product_id = ? LIMIT 4) pi ON p.id = pi.product_id
            WHERE p.id = ?
        ";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $product_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product_data = null;
            $gallery_images = [];
            
            while ($row = $result->fetch_assoc()) {
                if ($product_data === null) {
                    $product_data = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'additional_info' => $row['additional_info'], 
                        'price' => $row['price'],
                        'main_image' => $row['main_image'],
                        'stock' => $row['stock']
                    ];
                }
                if ($row['gallery_image'] !== null) {
                    $gallery_images[] = $row['gallery_image'];
                }
            }

            // 2. LOGIKA GAMBAR: Gabungkan Main Image ke Gallery
            if (!empty($product_data['main_image'])) {
                if (!in_array($product_data['main_image'], $gallery_images)) {
                    array_unshift($gallery_images, $product_data['main_image']);
                }
            }
            if (empty($gallery_images)) {
                $gallery_images[] = 'placeholder.png';
            }
            ?>
            
            <div class="container product-page-container">
                <div class="product-content-wrapper">
                    <div class="product-main-content">
                        
                        <a href="javascript:history.back()" class="btn-back" title="Kembali"><i class="fas fa-arrow-left"></i></a>

                        <div class="product-detail-layout">
                            <div class="product-images">
                                <div class="thumbnails">
                                    <?php foreach ($gallery_images as $image): ?>
                                    <img src="assets/img/<?php echo htmlspecialchars($image); ?>" class="thumbnail-item">
                                    <?php endforeach; ?>
                                </div>
                                <div class="main-image-container">
                                    <img src="assets/img/<?php echo htmlspecialchars($gallery_images[0]); ?>" id="main-image">
                                </div>
                            </div>

                            <div class="product-info">
                                <h1><?php echo htmlspecialchars($product_data['name']); ?></h1>
                                
                                <div class="price-stock-row" style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
                                    <p class="price" style="margin: 0; line-height: 1; font-weight: bold; color: #111;">
                                        Rp <?php echo number_format($product_data['price'], 0, ',', '.'); ?>
                                    </p>
                                    
                                    <?php if ($product_data['stock'] <= 3): ?>
                                        <div class="stock-status-wrapper" style="margin: 0;">
                                            <?php if ($product_data['stock'] > 0): ?>
                                                <div class="stock-badge low" style="margin: 0; padding: 4px 10px; font-size: 11px; display: inline-flex; align-items: center; gap: 5px; background: #fff0f0; border: 1px solid #ffcccc; color: #d9534f; border-radius: 20px;">
                                                    <i class="fas fa-fire"></i> <span>Sisa <?php echo $product_data['stock']; ?>!</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="stock-badge sold" style="margin: 0; padding: 4px 10px; font-size: 11px; display: inline-flex; align-items: center; gap: 5px; background: #eee; border: 1px solid #ddd; color: #888; border-radius: 20px;">
                                                    <i class="fas fa-ban"></i> <span>Habis</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <form action="cart.php" method="POST">
    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_data['id']); ?>">
    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_data['name']); ?>">
    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product_data['price']); ?>">
    <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product_data['main_image']); ?>">
    <?php if ($product_data['stock'] > 0): ?>
        <div class="quantity-selector">
            <button type="button" class="quantity-btn" id="decrease-qty" <?php if (!is_logged_in()) echo 'disabled'; ?>>-</button>
            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product_data['stock']; ?>" <?php if (!is_logged_in()) echo 'disabled'; ?>>
            <button type="button" class="quantity-btn" id="increase-qty" <?php if (!is_logged_in()) echo 'disabled'; ?>>+</button>
        </div>
        
        <button type="submit" name="add_to_cart" class="btn-add-to-cart" <?php if (!is_logged_in()) echo 'disabled'; ?>>Add To Cart</button>
    <?php else: ?>
        <button type="button" class="btn-add-to-cart" disabled style="background:#ccc; border-color:#ccc; color:#fff; cursor: not-allowed;">Stok Habis</button>
    <?php endif; ?>
</form>

                                <?php if (!is_logged_in() && $product_data['stock'] > 0): ?>
                                    <div class="login-card-prompt" style="margin-top: 15px; padding: 10px; border: 1px dashed #ccc; background: #f9f9f9; border-radius: 8px; font-size: 13px; text-align: center;">
                                        <i class="fas fa-lock" style="margin-bottom: 5px; display:block; color:#aaa;"></i>
                                        Ready to make it yours? <br>
                                        Please <a href="login_register.php" style="text-decoration: underline; font-weight: bold;">Login</a> or <a href="login_register.php" style="text-decoration: underline; font-weight: bold;">Register</a> to shop.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-tabs">
                            <div class="tab-headers">
                                <a href="#description" class="tab-link active">Description</a>
                                <a href="#additional-info" class="tab-link">Info</a>
                            </div>
                            <div class="tab-content">
                                <div id="description" class="tab-pane active">
                                    <?php echo nl2br($product_data['description']); ?>
                                </div>
                                <div id="additional-info" class="tab-pane">
                                    <?php echo !empty($product_data['additional_info']) ? nl2br($product_data['additional_info']) : '<p>-</p>'; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php
        } else {
            echo "<div class='container'><p>Produk tidak ditemukan.</p></div>";
        }
        $stmt->close();
    }
    $conn->close();
    ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const decreaseBtn = document.getElementById('decrease-qty');
    const increaseBtn = document.getElementById('increase-qty');
    const qtyInput = document.getElementById('quantity');

    if(decreaseBtn && increaseBtn && qtyInput) {
        // Tombol Kurang
        decreaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(qtyInput.value);
            if (currentValue > 1) {
                qtyInput.value = currentValue - 1;
            }
        });

        // Tombol Tambah
        increaseBtn.addEventListener('click', function() {
            let currentValue = parseInt(qtyInput.value);
            let maxValue = parseInt(qtyInput.getAttribute('max'));
            if (currentValue < maxValue) {
                qtyInput.value = currentValue + 1;
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>