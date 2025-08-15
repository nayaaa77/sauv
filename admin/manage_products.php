<?php 
$page_title = "Manage Products"; // Set judul halaman
include 'includes/header_admin.php'; 
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
    <a href="add_product.php" class="btn btn-primary">+ Add New Product</a>
</div>

<div class="card">
    <div class="card-body">
        <table class="table-products">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-image">Image</th>
                    <th class="col-name">Name</th>
                    <th class="col-price">Price</th>
                    <th class="col-stock">Stock</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ambil semua produk dari database
                $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
                if ($result && $result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><img src="../assets/img/<?php echo htmlspecialchars($row['image_url']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>Rp <?php echo number_format($row['price']); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td class="actions">
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Edit</a>
                        <form action="process_product.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_product" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php 
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="6" class="no-products">No products found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>