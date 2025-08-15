<?php
session_start();
// Pastikan hanya admin yang bisa akses dan ada ID produk
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}
require_once '../includes/db_conn.php';

// 1. Ambil ID Produk dari URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($product_id === 0) {
    die("Error: Product ID not specified.");
}

// 2. Ambil data produk utama dari database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Error: Product not found.");
}
$product = $result->fetch_assoc();
$stmt->close();

// 3. Ambil data gambar galeri
$stmt_gallery = $conn->prepare("SELECT id, image_url FROM product_images WHERE product_id = ?");
$stmt_gallery->bind_param("i", $product_id);
$stmt_gallery->execute();
$gallery_images = $stmt_gallery->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_gallery->close();

// Sertakan header admin setelah semua logika pengambilan data
include 'includes/header_admin.php'; 
?>

<div class="container">
    <h2>Edit Product: <?php echo htmlspecialchars($product['name']); ?></h2>
    
    <form action="process_product.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
        <input type="hidden" name="current_main_image" value="<?php echo htmlspecialchars($product['image_url']); ?>">

        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Additional Information</label>
            <textarea name="additional_info" rows="5"><?php echo htmlspecialchars($product['additional_info']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
        </div>

        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Main Product Image (Cover)</label>
            <p>Current Image:</p>
            <img src="../assets/img/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Main Image" style="max-width: 150px; margin-bottom: 10px;">
            <br>
            <label>Replace Main Image (Optional):</label>
            <input type="file" name="main_image">
            <small>Kosongkan jika tidak ingin mengganti gambar utama.</small>
        </div>

        <div class="form-group">
            <label>Gallery Images</label>
            <p>Current Gallery:</p>
            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 15px;">
                <?php if (empty($gallery_images)): ?>
                    <p>No gallery images.</p>
                <?php else: ?>
                    <?php foreach ($gallery_images as $image): ?>
                        <div style="position: relative; text-align: center;">
                            <img src="../assets/img/<?php echo htmlspecialchars($image['image_url']); ?>" alt="Gallery Image" style="max-width: 100px;">
                            <br>
                            <label style="font-size: 0.8em;">
                                <input type="checkbox" name="delete_gallery_images[]" value="<?php echo $image['id']; ?>"> Delete
                            </label>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <label>Add New Gallery Images (Optional):</label>
            <input type="file" name="gallery_images[]" multiple>
            <small>Pilih gambar baru untuk ditambahkan ke galeri. Untuk menghapus, centang kotak "Delete" di bawah gambar.</small>
        </div>

        <div class="form-group">
            <label for="is_featured">Jadikan Hero Banner?</label>
            <input type="checkbox" name="is_featured" id="is_featured" value="1" <?php echo ($product['is_featured'] == 1) ? 'checked' : ''; ?>>
            <small>Ceklis untuk menampilkan produk ini sebagai banner utama di halaman home.</small>
        </div>

        <button type="submit" name="edit_product" class="btn">Update Product</button>
    </form>
</div>

<?php include 'includes/footer_admin.php'; ?>