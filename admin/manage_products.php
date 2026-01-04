<?php 
$page_title = "Manage Products"; 
include 'includes/header_admin.php'; 
require_once '../includes/db_conn.php'; // Pastikan koneksi database ada

// Menghitung jumlah total produk
$count_result = $conn->query("SELECT COUNT(id) as total FROM products");
$product_count = $count_result ? $count_result->fetch_assoc()['total'] : 0;
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<?php
// Blok Notifikasi
if (isset($_GET['status'])) {
    $message = '';
    if ($_GET['status'] == 'add_success') {
        $message = 'Product added successfully!';
    } elseif ($_GET['status'] == 'edit_success') {
        $message = 'Product updated successfully!';
    } elseif ($_GET['status'] == 'delete_success') {
        $message = 'Product deleted successfully!';
    }

    if ($message) {
        echo "<div class='alert alert-success'>{$message}</div>";
    }
}
?>

<div class="content-wrapper-card">
    <div class="card-header">
        <h2 class="card-title">Products <span class="item-count"><?php echo $product_count; ?></span></h2>
        <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Create New</a>
    </div>

    <div class="card-body no-padding">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th> <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Query diperbarui dengan LEFT JOIN untuk mengambil nama kategori
                $sql = "SELECT p.*, c.name as category_name 
                        FROM products p 
                        LEFT JOIN categories c ON p.category_id = c.id 
                        ORDER BY p.id DESC";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0):
                    $nomor = 1; 
                    while ($row = $result->fetch_assoc()):
                        
                        // --- LOGIKA PERBAIKAN GAMBAR (Sama seperti Shop & Edit) ---
                        $img_filename = $row['image_url'];
                        $img_path_raw = '../assets/img/' . $img_filename; // Default folder
                        
                        // Cek apakah ada di uploads (folder lama)
                        if (!file_exists($img_path_raw) && file_exists('../uploads/' . $img_filename)) {
                            $img_path_raw = '../uploads/' . $img_filename;
                        }
                        
                        // Encode URL agar spasi dan # terbaca browser
                        $img_display = str_replace([' ', '#'], ['%20', '%23'], $img_path_raw);
                        // -----------------------------------------------------------
                ?>
                <tr>
                    <td><?php echo $nomor; ?></td> 
                    <td>
                        <img src="<?php echo $img_display; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                    </td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                        <?php if($row['is_featured']): ?>
                            <span style="font-size: 10px; background: #e3f2fd; color: #0d47a1; padding: 2px 5px; border-radius: 3px; margin-left: 5px;">Featured</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php 
                        // Tampilkan nama kategori atau 'Uncategorized' jika kosong
                        echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); 
                        ?>
                    </td>
                    <td>Rp <?php echo number_format($row['price']); ?></td>
                    <td>
                        <?php 
                        if ($row['stock'] <= 0) {
                            echo '<span style="color:red; font-weight:bold;">Out of Stock</span>';
                        } else {
                            echo $row['stock'];
                        }
                        ?>
                    </td>
                    <td class="actions">
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="action-icon" title="Edit"><i class="fas fa-pen"></i></a>
                        <form action="process_product.php" method="POST" style="display:inline; margin:0;">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete_product" class="action-icon" title="Delete" onclick="return confirm('Are you sure you want to delete this product?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php
                    $nomor++;
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="7" class="no-data">No products found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>