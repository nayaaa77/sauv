<?php 
$page_title = "Add New Product";
include 'includes/header_admin.php'; 
require_once '../includes/db_conn.php'; 
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<form action="process_product.php" method="POST" enctype="multipart/form-data">
    <div class="form-grid">
        <div class="form-column-main">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Product Details</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Creme Scarf" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">-- Select Existing Category --</option>
                            <?php
                            $res_cat = $conn->query("SELECT * FROM categories ORDER BY name ASC");
                            while($cat = $res_cat->fetch_assoc()) {
                                echo "<option value='".$cat['id']."'>".htmlspecialchars($cat['name'])."</option>";
                            }
                            ?>
                        </select>
                        <input type="text" class="form-control mt-2" name="new_category" placeholder="Or type NEW Category name here..." style="margin-top: 10px;">
                        <small class="text-muted">Pilih dari list ATAU ketik nama baru jika kategori belum ada.</small>
                    </div>

                    <div class="form-group">
                        <label for="description-editor">Description</label>
                        <textarea id="description-editor" name="description" rows="8"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="info-editor">Additional Information</label>
                        <textarea id="info-editor" name="additional_info" rows="5"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-column-side">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Pricing & Stock</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="price">Price (Rp)</label>
                        <input type="number" class="form-control" id="price" name="price" placeholder="e.g., 135000" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" placeholder="e.g., 10" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title">Product Images</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Main Product Image (Cover)</label>
                        <div class="image-upload-box" id="main-image-box">
                            <input type="file" id="main_image" name="main_image" accept="image/*" required>
                            <div class="upload-placeholder"><i class="fas fa-cloud-upload-alt"></i></div>
                        </div>
                        <div class="image-preview-container" id="main-image-preview-container"></div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Gallery Images (Max 4)</label>
                         <div class="image-upload-box" id="gallery-image-box">
                            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            <div class="upload-placeholder"><i class="fas fa-images"></i><p>Click or drag images here</p></div>
                        </div>
                        <div class="image-preview-container gallery" id="gallery-preview-container"></div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header"><h4 class="card-title">Visibility</h4></div>
                <div class="card-body">
                    <div class="form-group-switch">
                        <label for="is_featured">Feature on Hero Banner?</label>
                        <label class="switch">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
            <button type="submit" name="add_product" class="btn btn-primary btn-block btn-lg">Add Product</button>
        </div>
    </div>
</form>

<?php include 'includes/footer_admin.php'; ?>