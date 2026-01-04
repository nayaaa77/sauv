<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { die("Access Denied."); }
require_once '../includes/db_conn.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id === 0) { die("Error: Product ID not specified."); }

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) { die("Error: Product not found."); }
$stmt->close();

$stmt_gallery = $conn->prepare("SELECT id, image_url FROM product_images WHERE product_id = ?");
$stmt_gallery->bind_param("i", $product_id);
$stmt_gallery->execute();
$gallery_images = $stmt_gallery->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_gallery->close();

$categories_res = $conn->query("SELECT * FROM categories ORDER BY name ASC");

// ==========================================
// SOLUSI PATH ABSOLUT & ANTI-CACHE
// ==========================================
$main_img_name = htmlspecialchars($product['image_url']);

// Path untuk Browser (Tampilan) - Menggunakan /sauv/assets/img/ agar pasti dari root
// Tambahkan ?v=time() agar browser selalu ambil gambar terbaru (bukan cache)
$display_src = "/sauv/assets/img/" . str_replace([' ', '#'], ['%20', '%23'], $main_img_name) . "?v=" . time();

// Jika nama file kosong/default, arahkan ke default
if (empty($main_img_name) || $main_img_name == 'default.jpg') {
    $display_src = "/sauv/assets/img/default.jpg";
}
// ==========================================

$page_title = "Edit Product";
include 'includes/header_admin.php'; 
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<form action="process_product.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
    <input type="hidden" name="current_main_image" value="<?php echo htmlspecialchars($product['image_url']); ?>">

    <div class="form-grid">
        <div class="form-column-main">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Product Details</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category_id">Category</label>
                        <select name="category_id" id="category_id" class="form-control">
                            <option value="">-- Select Existing Category --</option>
                            <?php 
                            if ($categories_res->num_rows > 0) {
                                while($cat = $categories_res->fetch_assoc()) {
                                    $selected = ($product['category_id'] == $cat['id']) ? 'selected' : '';
                                    echo "<option value='".$cat['id']."' $selected>".htmlspecialchars($cat['name'])."</option>";
                                }
                            }
                            ?>
                        </select>
                        <input type="text" class="form-control mt-2" name="new_category" placeholder="Or type NEW Category name here..." style="margin-top: 10px;">
                    </div>
                    <div class="form-group">
                        <label for="description-editor">Description</label>
                        <textarea id="description-editor" name="description" rows="8"><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="info-editor">Additional Information</label>
                        <textarea id="info-editor" name="additional_info" rows="5"><?php echo htmlspecialchars($product['additional_info']); ?></textarea>
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
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h4 class="card-title">Product Images</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Main Product Image (Cover)</label>
                        <div class="image-upload-box" id="main-image-box">
                            <input type="file" id="main_image" name="main_image" accept="image/*">
                            <div class="upload-placeholder"><i class="fas fa-cloud-upload-alt"></i></div>
                        </div>
                        <div class="image-preview-container" id="main-image-preview-container">
                            <div class="image-preview-item existing">
                                <input type="checkbox" id="del_main" name="delete_main_image" value="1" style="display:none;">
                                <img src="<?php echo $display_src; ?>" alt="Main Image" onerror="this.src='/sauv/assets/img/default.jpg';">
                                <label for="del_main" class="delete-image-label"><i class="fas fa-times"></i></label>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Gallery Images</label>
                        <div class="image-upload-box" id="gallery-image-box">
                            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                             <div class="upload-placeholder"><i class="fas fa-images"></i><p>Add more images</p></div>
                        </div>
                        <div class="image-preview-container gallery" id="gallery-preview-container">
                            <?php foreach ($gallery_images as $image): 
                                // Sama, pakai absolute path + anti cache
                                $g_src = "/sauv/assets/img/" . str_replace([' ', '#'], ['%20', '%23'], $image['image_url']) . "?v=" . time();
                            ?>
                                <div class="image-preview-item existing">
                                    <input type="checkbox" id="del_gal_<?php echo $image['id']; ?>" name="delete_gallery_images[]" value="<?php echo $image['id']; ?>" style="display:none;">
                                    <img src="<?php echo $g_src; ?>" alt="Gallery Image" onerror="this.src='/sauv/assets/img/default.jpg';">
                                    <label for="del_gal_<?php echo $image['id']; ?>" class="delete-image-label"><i class="fas fa-times"></i></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header"><h4 class="card-title">Visibility</h4></div>
                <div class="card-body">
                    <div class="form-group-switch">
                        <label for="is_featured">Feature on Hero Banner?</label>
                        <label class="switch">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1" <?php echo ($product['is_featured'] == 1) ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>

            <button type="submit" name="edit_product" class="btn btn-primary btn-block btn-lg">Update Product</button>
        </div>
    </div>
</form>

<?php include 'includes/footer_admin.php'; ?>

<script>
$(document).ready(function() {
    $('#description-editor, #info-editor').summernote({
        placeholder: 'Content...', tabsize: 2, height: 150,
        toolbar: [['style', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol', 'paragraph']], ['view', ['codeview']]]
    });

    function handleImagePreview(input, previewContainer) {
        if (input.files && input.files[0]) {
            $(previewContainer).empty(); 
            $(input).closest('.image-upload-box').find('.upload-placeholder').hide();
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $(previewContainer).append(`<div class="image-preview-item"><img src="${e.target.result}"></div>`);
                }
                reader.readAsDataURL(file);
            });
        }
    }
    $('#main_image').on('change', function() { handleImagePreview(this, '#main-image-preview-container', false); });
    $('#gallery_images').on('change', function() { handleImagePreview(this, '#gallery-preview-container', true); });
});
</script>