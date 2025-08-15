<?php include 'includes/header_admin.php'; ?>

<div class="container">
    <h2>Add New Product</h2>
    <form action="process_product.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Product Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="5" required></textarea>
        </div>

        <div class="form-group">
            <label>Additional Information</label>
            <textarea name="additional_info" rows="5"></textarea>
            <small>Informasi tambahan seperti bahan, cara perawatan, ukuran, dll. (Opsional)</small>
        </div>
        <div class="form-group">
            <label>Price</label>
            <input type="number" name="price" required>
        </div>
        <div class="form-group">
            <label>Stock</label>
            <input type="number" name="stock" required>
        </div>
        
        <div class="form-group">
            <label>Main Product Image (Cover)</label>
            <input type="file" name="main_image" required>
            <small>Gambar ini akan menjadi gambar utama produk.</small>
        </div>

        <div class="form-group">
            <label>Gallery Images (Max 4)</label>
            <input type="file" name="gallery_images[]" multiple>
            <small>Pilih beberapa gambar sekaligus untuk galeri produk.</small>
        </div>

        <div class="form-group">
            <label for="is_featured">Jadikan Hero Banner?</label>
            <input type="checkbox" name="is_featured" id="is_featured" value="1">
            <small>Ceklis untuk menampilkan produk ini sebagai banner utama di halaman home.</small>
        </div>

        <button type="submit" name="add_product" class="btn">Add Product</button>
    </form>
</div>

<?php include 'includes/footer_admin.php'; ?>