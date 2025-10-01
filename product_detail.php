<?php 
include 'includes/header.php'; 
include 'includes/db_conn.php';
?>

<div class="page-content">
    <?php
    // Ambil ID produk dari URL
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($product_id === 0) {
        echo "<div class='container'><p>Produk tidak valid atau tidak ditemukan.</p></div>";
    } else {
        // Query untuk mengambil detail produk, informasi tambahan, dan gambar terkait
        $query = "
            SELECT 
                p.id, p.name, p.description, p.additional_info, p.price, p.stock, p.image_url AS main_image,
                pi.image_url AS gallery_image
            FROM 
                products p
            LEFT JOIN 
                (SELECT * FROM product_images WHERE product_id = ? LIMIT 4) pi ON p.id = pi.product_id
            WHERE 
                p.id = ?
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

            if (empty($gallery_images) && !empty($product_data['main_image'])) {
                $gallery_images[] = $product_data['main_image'];
            }
            ?>
            
            <div class="container product-page-container">
                <div class="product-content-wrapper">
                    
                    <a href="javascript:history.back()" class="btn-back" title="Kembali"><i class="fas fa-arrow-left"></i></a>

                    <div class="product-main-content">
                        <div class="product-detail-layout">
                            <div class="product-images">
                                <div class="thumbnails">
                                    <?php foreach ($gallery_images as $image): ?>
                                    <img src="assets/img/<?php echo htmlspecialchars($image); ?>" alt="Thumbnail <?php echo htmlspecialchars($product_data['name']); ?>" class="thumbnail-item">
                                    <?php endforeach; ?>
                                </div>
                                <div class="main-image-container">
                                    <img src="assets/img/<?php echo htmlspecialchars($gallery_images[0] ?? 'placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product_data['name']); ?>" id="main-image">
                                </div>
                            </div>

                            <div class="product-info">
                                <h1><?php echo htmlspecialchars($product_data['name']); ?></h1>
                                <p class="price">Rp <?php echo number_format($product_data['price'], 0, ',', '.'); ?></p>
                                
                                <?php if ($product_data['stock'] > 0 && $product_data['stock'] <= 3): ?>
                                    <p class="stock-status low-stock"><?php echo $product_data['stock']; ?> pieces left!</p>
                                <?php elseif ($product_data['stock'] <= 0): ?>
                                    <p class="stock-status sold-out">Sold Out</p>
                                <?php endif; ?>

                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_data['id']); ?>">
                                    
                                    <?php if ($product_data['stock'] > 0): ?>
                                        <div class="quantity-selector">
                                            <button type="button" class="quantity-btn" id="decrease-qty" <?php if (!is_logged_in()) echo 'disabled'; ?>>-</button>
                                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product_data['stock']; ?>" <?php if (!is_logged_in()) echo 'disabled'; ?>>
                                            <button type="button" class="quantity-btn" id="increase-qty" <?php if (!is_logged_in()) echo 'disabled'; ?>>+</button>
                                        </div>
                                        <button type="submit" name="add_to_cart" class="btn-add-to-cart" <?php if (!is_logged_in()) echo 'disabled'; ?>>Add To Cart</button>
                                    <?php else: ?>
                                        <div class="quantity-selector">
                                            <button type="button" class="quantity-btn" id="decrease-qty" disabled>-</button>
                                            <input type="number" id="quantity" name="quantity" value="0" min="0" disabled>
                                            <button type="button" class="quantity-btn" id="increase-qty" disabled>+</button>
                                        </div>
                                        <button type="submit" name="add_to_cart" class="btn-add-to-cart" disabled>Habis Terjual</button>
                                    <?php endif; ?>
                                </form>

                                <?php if (!is_logged_in() && $product_data['stock'] > 0): ?>
                                    <div class="login-prompt-text">
                                        Anda harus login atau register untuk berbelanja.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="product-tabs">
                            <div class="tab-headers">
                                <a href="#description" class="tab-link active">Description</a>
                                <a href="#additional-info" class="tab-link">Additional Information</a>
                            </div>
                            <div class="tab-content">
                                <div id="description" class="tab-pane active">
                                    <p><?php echo nl2br(htmlspecialchars($product_data['description'])); ?></p>
                                </div>
                                <div id="additional-info" class="tab-pane">
                                    <?php if (!empty($product_data['additional_info'])): ?>
                                        <p><?php echo nl2br(htmlspecialchars($product_data['additional_info'])); ?></p>
                                    <?php else: ?>
                                        <p>Tidak ada informasi tambahan untuk produk ini.</p>
                                    <?php endif; ?>
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

<?php
include 'includes/footer.php'; 
?>