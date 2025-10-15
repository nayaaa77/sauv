<?php 
$page_title = "Add New Product";
include 'includes/header_admin.php'; 
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<form action="process_product.php" method="POST" enctype="multipart/form-data">
    <div class="form-grid">
        <div class="form-column-main">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Product Details</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Creme Scarf" required>
                    </div>
                    <div class="form-group">
                        <label for="description-editor">Description</label>
                        <textarea id="description-editor" name="description" rows="8"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="info-editor">Additional Information</label>
                        <textarea id="info-editor" name="additional_info" rows="5"></textarea>
                        <small class="form-text text-muted">Bahan, cara perawatan, ukuran, dll. (Opsional)</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-column-side">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Pricing & Stock</h4>
                </div>
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
                <div class="card-header">
                    <h4 class="card-title">Product Images</h4>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Main Product Image (Cover)</label>
                        <div class="image-upload-box" id="main-image-box">
                            <input type="file" id="main_image" name="main_image" accept="image/*" required>
                            <div class="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click or drag image here</p>
                            </div>
                        </div>
                        <div class="image-preview-container" id="main-image-preview-container"></div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Gallery Images (Max 4)</label>
                         <div class="image-upload-box" id="gallery-image-box">
                            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            <div class="upload-placeholder">
                                <i class="fas fa-images"></i>
                                <p>Click or drag images here</p>
                            </div>
                        </div>
                        <div class="image-preview-container gallery" id="gallery-preview-container"></div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Visibility</h4>
                </div>
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

<script>
$(document).ready(function() {
    // Inisialisasi Summernote Editor
    $('#description-editor, #info-editor').summernote({
        placeholder: 'Write your content here...',
        tabsize: 2,
        height: 150,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['view', ['codeview']]
        ]
    });

    // Fungsi untuk preview gambar
    function handleImagePreview(input, previewContainer, isMultiple) {
        const files = input.files;
        if (files) {
            $(previewContainer).empty(); // Kosongkan preview lama
            // Sembunyikan placeholder
            $(input).closest('.image-upload-box').find('.upload-placeholder').hide();
            
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewItem = `
                        <div class="image-preview-item">
                            <img src="${e.target.result}" alt="Image Preview">
                        </div>`;
                    $(previewContainer).append(previewItem);
                }
                reader.readAsDataURL(file);
            });
        }
    }

    // Event listener untuk input gambar
    $('#main_image').on('change', function() {
        handleImagePreview(this, '#main-image-preview-container', false);
    });

    $('#gallery_images').on('change', function() {
        handleImagePreview(this, '#gallery-preview-container', true);
    });
});
</script>