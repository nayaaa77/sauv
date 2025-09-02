<?php 
include 'includes/header.php'; 
include 'includes/db_conn.php';
?>

<div class="page-content">
    <div class="container">
        <h2 style="margin-top: 40px;">Shop All</h2>
        <p>Explore the complete collection.</p>
        
        <div class="product-grid" style="margin-top: 30px; margin-bottom: 60px;">
            <?php
            $sql = "SELECT id, name, price, image_url, stock FROM products ORDER BY name ASC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $card_class = ($row['stock'] <= 0) ? 'product-card sold-out' : 'product-card';
                    
                    echo '<div class="' . $card_class . '">';
                    echo '  <div class="product-image-container">';
                    echo '      <a href="product_detail.php?id=' . $row['id'] . '">';
                    echo '          <img src="./assets/img/' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                    
                    if ($row['stock'] <= 0) {
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
                echo "<p>Belum ada produk yang tersedia.</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>