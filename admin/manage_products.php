<?php 
$page_title = "Manage Products"; // Judul ini akan ditampilkan di header utama
include 'includes/header_admin.php'; 

// Menghitung jumlah total produk untuk ditampilkan di judul
$count_result = $conn->query("SELECT COUNT(id) as total FROM products");
$product_count = $count_result ? $count_result->fetch_assoc()['total'] : 0;
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<div class="breadcrumb">
    <a href="index.php">Home</a> &gt; Products
</div>

<div class="content-wrapper-card">
    <div class="card-header">
        <h2 class="card-title">Products <span class="item-count"><?php echo $product_count; ?></span></h2>
        <a href="add_product.php" class="btn btn-primary">+ Create New</a>
    </div>

    <div class="card-body no-padding">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
                if ($result && $result->num_rows > 0):
                    $counter = $result->num_rows;
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo str_pad($counter--, 2, '0', STR_PAD_LEFT); ?></td>
                    <td><img src="../assets/img/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>Rp <?php echo number_format($row['price']); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td class="actions">
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="action-icon" title="Edit"><i class="fas fa-pen"></i></a>
                        <form action="process_product.php" method="POST" style="display:inline; margin:0;">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_product" class="action-icon" title="Delete" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="6" class="no-data">No products found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>