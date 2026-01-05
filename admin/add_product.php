<?php 
$page_title = "Add New Product";
include 'includes/header_admin.php'; 
require_once '../includes/db_conn.php';

// --- AMBIL DATA KATEGORI UNTUK LOGIKA JAVASCRIPT ---
$parents_arr = [];
$subs_arr = [];

if ($conn) {
    // 1. Ambil Semua Parent (Main Category)
    $q_p = $conn->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
    while($row = $q_p->fetch_assoc()) { 
        $parents_arr[] = $row; 
    }
    
    // 2. Ambil Semua Sub-Category
    $q_s = $conn->query("SELECT * FROM categories WHERE parent_id IS NOT NULL ORDER BY name ASC");
    while($row = $q_s->fetch_assoc()) { 
        // Grouping berdasarkan ID Induknya (parent_id)
        $subs_arr[$row['parent_id']][] = $row; 
    }
}
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<style>
    /* CSS Image Preview & Layout */
    .image-preview-item {
        position: relative; display: inline-flex; justify-content: center; align-items: center;
        border: 1px solid #ddd; border-radius: 8px; background-color: #f8f9fa;
        overflow: hidden; margin: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .preview-cover { width: 100%; height: 250px; max-width: 300px; }
    .preview-gallery { width: 120px; height: 120px; }
    .image-preview-item img { max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain; display: block; }
    .remove-btn {
        position: absolute; top: 5px; right: 5px; background: #fff; color: #dc3545;
        border: 1px solid #dc3545; border-radius: 50%; width: 22px; height: 22px;
        text-align: center; line-height: 20px; cursor: pointer; font-size: 12px; z-index: 10; transition: all 0.2s;
    }
    .remove-btn:hover { background: #dc3545; color: #fff; }
</style>

<form action="process_product.php" method="POST" enctype="multipart/form-data" id="addProductForm">
    <div class="form-grid">
        <div class="form-column-main">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Product Details</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Creme Scarf" required>
                    </div>

                    <div class="card" style="background-color: #fcfcfc; border: 1px solid #eee; margin-bottom: 20px;">
                        <div class="card-body" style="padding: 15px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                                <h6 style="margin:0; color: #333; font-weight: bold;">Category Setup</h6>
                                
                                <div>
                                    <small class="text-muted" style="font-size: 11px; margin-right: 10px;"> <i class="fas fa-trash"></i> = Delete</small>
                                    <button type="button" class="btn btn-sm btn-success" data-toggle="modal" data-target="#addCategoryModal">
                                        <i class="fas fa-plus"></i> Quick Create Category
                                    </button>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label style="font-weight:600; font-size: 0.9em;">1. Main Category</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1; display: flex; gap: 5px;">
                                        <select class="form-control" id="main_cat_select" name="main_cat_select" style="flex: 1;">
                                            <option value="">-- Select Main Category --</option>
                                            <?php foreach($parents_arr as $p): ?>
                                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger" onclick="deleteCategory('main_cat_select', 'Main Category')" title="Delete selected Main Category">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <span style="font-size: 12px; color: #888; font-weight:bold;">OR</span>
                                    <input type="text" class="form-control" id="new_main_cat" name="new_main_cat" placeholder="Create New Main..." style="flex: 1;">
                                </div>
                            </div>

                            <div class="form-group" style="margin-bottom: 0; margin-top: 15px;">
                                <label style="font-weight:600; font-size: 0.9em;">2. Sub-Category (Optional)</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1; display: flex; gap: 5px;">
                                        <select class="form-control" id="sub_cat_select" name="sub_cat_select" style="flex: 1;" disabled>
                                            <option value="">-- First, Select Main Category --</option>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger" id="btn_delete_sub" onclick="deleteCategory('sub_cat_select', 'Sub-Category')" title="Delete selected Sub-Category" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <span style="font-size: 12px; color: #888; font-weight:bold;">OR</span>
                                    <input type="text" class="form-control" id="new_sub_cat" name="new_sub_cat" placeholder="Create New Sub..." style="flex: 1;">
                                </div>
                                <small class="text-muted" style="display:block; margin-top:5px;">
                                    * If left empty, product will be assigned directly to Main Category.
                                </small>
                            </div>
                        </div>
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
                            <div class="upload-placeholder" id="main-placeholder">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click or drag image here</p>
                            </div>
                        </div>
                        <div class="image-preview-container" id="main-image-preview-container" style="margin-top: 10px;"></div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Gallery Images (Max 4)</label>
                         <div class="image-upload-box" id="gallery-image-box">
                            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            <div class="upload-placeholder" id="gallery-placeholder">
                                <i class="fas fa-images"></i>
                                <p>Click or drag images here</p>
                            </div>
                        </div>
                        <div class="image-preview-container gallery" id="gallery-preview-container" style="display: flex; flex-wrap: wrap; gap: 5px; margin-top: 10px;"></div>
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

<div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="process_product.php" method="POST">
                <div class="modal-body">
                    
                    <div class="alert alert-info" style="font-size: 0.9em; background-color: #e3f2fd; border-color: #b3d7ff; color: #0c5460;">
                        <i class="fas fa-info-circle"></i> <strong>Pro Tip:</strong> 
                        Categories created here will automatically appear as <em>"Coming Soon"</em> on the website until you add products to them.
                    </div>

                    <div class="form-group">
                        <label>Assign to Main Category (Optional)</label>
                        <select name="quick_parent_id" class="form-control">
                            <option value="">-- None (Create as Main Category) --</option>
                            <?php foreach($parents_arr as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Leave empty to create a new Main Category.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Add New Category Name</label>
                        <input type="text" name="quick_cat_name" class="form-control" placeholder="e.g., Summer Collection" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category_only" class="btn btn-success">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>

<script>
const subCategoriesData = <?php echo json_encode($subs_arr); ?>;

$(document).ready(function() {
    $('#description-editor, #info-editor').summernote({
        placeholder: 'Write your content here...', tabsize: 2, height: 150,
        toolbar: [['style', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol', 'paragraph']], ['view', ['codeview']]]
    });

    // === LOGIKA KATEGORI ===
    const mainSelect = $('#main_cat_select');
    const newMainInput = $('#new_main_cat');
    const subSelect = $('#sub_cat_select');
    const newSubInput = $('#new_sub_cat');
    const btnDeleteSub = $('#btn_delete_sub');

    mainSelect.on('change', function() {
        const parentId = $(this).val();
        subSelect.empty().append('<option value="">-- Select Sub-Category (Optional) --</option>');
        btnDeleteSub.prop('disabled', true);
        
        if (parentId && subCategoriesData[parentId]) {
            subSelect.prop('disabled', false);
            subCategoriesData[parentId].forEach(sub => {
                subSelect.append(`<option value="${sub.id}">${sub.name}</option>`);
            });
        } else if (parentId) {
            subSelect.prop('disabled', false);
        } else {
            subSelect.prop('disabled', true).find('option').text('-- First, Select Main Category --');
        }
    });

    subSelect.on('change', function() {
        if ($(this).val()) btnDeleteSub.prop('disabled', false);
        else btnDeleteSub.prop('disabled', true);
    });

    newMainInput.on('input', function() {
        if ($(this).val().length > 0) {
            mainSelect.val('').prop('disabled', true);
            subSelect.empty().append('<option value="">-- Cannot select existing sub --</option>').prop('disabled', true);
            btnDeleteSub.prop('disabled', true);
        } else {
            mainSelect.prop('disabled', false);
            subSelect.empty().append('<option value="">-- First, Select Main Category --</option>').prop('disabled', true);
        }
    });

    newSubInput.on('input', function() {
        if ($(this).val().length > 0) {
            subSelect.val('').prop('disabled', true);
            btnDeleteSub.prop('disabled', true);
        } else {
            if (!newMainInput.val() && mainSelect.val()) {
                subSelect.prop('disabled', false);
            }
        }
    });

    // === IMAGE LOGIC ===
    $('#main_image').on('change', function() {
        const file = this.files[0];
        const container = $('#main-image-preview-container');
        container.empty(); 
        if (file) {
            $('#main-placeholder').hide(); 
            const reader = new FileReader();
            reader.onload = function(e) {
                container.append(`<div class="image-preview-item preview-cover"><img src="${e.target.result}"><div class="remove-btn" onclick="removeMainImage()"><i class="fas fa-times"></i></div></div>`);
            }
            reader.readAsDataURL(file);
        } else { $('#main-placeholder').show(); }
    });
    window.removeMainImage = function() { $('#main_image').val(''); $('#main-image-preview-container').empty(); $('#main-placeholder').show(); };

    const galleryDT = new DataTransfer(); 
    $('#gallery_images').on('change', function() {
        const newFiles = this.files;
        for (let i = 0; i < newFiles.length; i++) { galleryDT.items.add(newFiles[i]); }
        this.files = galleryDT.files;
        renderGalleryPreview();
    });
    function renderGalleryPreview() {
        const container = $('#gallery-preview-container');
        container.empty(); 
        if (galleryDT.files.length > 0) {
            $('#gallery-placeholder').hide();
            Array.from(galleryDT.files).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    container.append(`<div class="image-preview-item preview-gallery"><img src="${e.target.result}"><div class="remove-btn" onclick="removeGalleryItem(${index})"><i class="fas fa-times"></i></div></div>`);
                }
                reader.readAsDataURL(file);
            });
        } else { $('#gallery-placeholder').show(); }
    }
    window.removeGalleryItem = function(index) {
        const tempDT = new DataTransfer();
        for (let i = 0; i < galleryDT.files.length; i++) { if (i !== index) tempDT.items.add(galleryDT.files[i]); }
        galleryDT.items.clear();
        for (let i = 0; i < tempDT.files.length; i++) galleryDT.items.add(tempDT.files[i]);
        document.getElementById('gallery_images').files = galleryDT.files;
        renderGalleryPreview();
    };
});

// === DELETE FUNCTION ===
function deleteCategory(elementId, typeLabel) {
    const select = document.getElementById(elementId);
    const id = select.value;
    if (!id || select.selectedIndex < 0) {
        alert('Please select a ' + typeLabel + ' to delete.');
        return;
    }
    const name = select.options[select.selectedIndex].text;
    let confirmMsg = `Are you sure you want to delete ${typeLabel} "${name.trim()}" PERMANENTLY?\n\n`;
    if (typeLabel === 'Main Category') {
        confirmMsg += "WARNING:\n1. All Sub-Categories will become Main Categories.\n2. Products will become 'Uncategorized'.";
    } else {
        confirmMsg += "Products in this Sub-Category will be moved to its Main Category.";
    }
    if (confirm(confirmMsg)) {
        window.location.href = 'process_product.php?delete_category_id=' + id;
    }
}
</script>