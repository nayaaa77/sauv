<?php
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { die("Access Denied."); }
require_once '../includes/db_conn.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id === 0) { die("Error: Product ID not specified."); }

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { die("Error: Product not found."); }
$product = $result->fetch_assoc();
$stmt->close();

$stmt_gallery = $conn->prepare("SELECT id, image_url FROM product_images WHERE product_id = ?");
$stmt_gallery->bind_param("i", $product_id);
$stmt_gallery->execute();
$gallery_images = $stmt_gallery->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_gallery->close();

// LOGIKA PARENT/SUB
$current_main_id = '';
$current_sub_id = '';
if ($product['category_id']) {
    $check_cat = $conn->query("SELECT id, parent_id FROM categories WHERE id = " . $product['category_id']);
    if ($cat_row = $check_cat->fetch_assoc()) {
        if ($cat_row['parent_id'] == NULL) {
            $current_main_id = $cat_row['id'];
        } else {
            $current_sub_id = $cat_row['id'];
            $current_main_id = $cat_row['parent_id'];
        }
    }
}

$parents_arr = [];
$subs_arr = [];
if ($conn) {
    $q_p = $conn->query("SELECT * FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
    while($row = $q_p->fetch_assoc()) { $parents_arr[] = $row; }
    $q_s = $conn->query("SELECT * FROM categories WHERE parent_id IS NOT NULL ORDER BY name ASC");
    while($row = $q_s->fetch_assoc()) { $subs_arr[$row['parent_id']][] = $row; }
}

$page_title = "Edit Product";
include 'includes/header_admin.php'; 
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<style>
    /* Same CSS as add_product */
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

<form action="process_product.php" method="POST" enctype="multipart/form-data" id="editProductForm">
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

                    <div class="card" style="background-color: #fcfcfc; border: 1px solid #eee; margin-bottom: 20px;">
                        <div class="card-body" style="padding: 15px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">
                                <h6 style="margin:0; color: #333; font-weight: bold;">Category Setup</h6>
                                <small class="text-muted" style="font-size: 11px;"> <i class="fas fa-trash"></i> = Delete</small>
                            </div>
                            
                            <div class="form-group">
                                <label style="font-weight:600; font-size: 0.9em;">1. Main Category</label>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <div style="flex: 1; display: flex; gap: 5px;">
                                        <select class="form-control" id="main_cat_select" name="main_cat_select" style="flex: 1;">
                                            <option value="">-- Select Main Category --</option>
                                            <?php foreach($parents_arr as $p): ?>
                                                <option value="<?php echo $p['id']; ?>" <?php echo ($p['id'] == $current_main_id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($p['name']); ?>
                                                </option>
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
                                        <select class="form-control" id="sub_cat_select" name="sub_cat_select" style="flex: 1;">
                                            <option value="">-- Select Sub-Category --</option>
                                            <?php 
                                            if($current_main_id && isset($subs_arr[$current_main_id])) {
                                                foreach($subs_arr[$current_main_id] as $s) {
                                                    $selected = ($s['id'] == $current_sub_id) ? 'selected' : '';
                                                    echo "<option value='{$s['id']}' $selected>{$s['name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <button type="button" class="btn btn-outline-danger" id="btn_delete_sub" onclick="deleteCategory('sub_cat_select', 'Sub-Category')" title="Delete selected Sub-Category" <?php echo ($current_sub_id) ? '' : 'disabled'; ?>>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <span style="font-size: 12px; color: #888; font-weight:bold;">OR</span>
                                    <input type="text" class="form-control" id="new_sub_cat" name="new_sub_cat" placeholder="Create New Sub..." style="flex: 1;">
                                </div>
                            </div>
                        </div>
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
                            <div class="upload-placeholder" id="main-placeholder" style="<?php echo ($product['image_url'] && $product['image_url'] != 'default.jpg') ? 'display:none;' : ''; ?>">
                                <i class="fas fa-cloud-upload-alt"></i><p>Click to change cover</p>
                            </div>
                        </div>
                        <div class="image-preview-container" id="main-image-preview-container" style="margin-top: 10px;">
                            <?php if ($product['image_url'] && $product['image_url'] != 'default.jpg'): ?>
                                <div class="image-preview-item preview-cover" id="existing-cover-preview">
                                    <img src="../assets/img/<?php echo htmlspecialchars($product['image_url']); ?>">
                                    <div class="remove-btn" onclick="removeExistingMainImage()"><i class="fas fa-times"></i></div>
                                    <input type="checkbox" id="del_main_input" name="delete_main_image" value="1" style="display:none;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Gallery Images</label>
                         <div class="image-upload-box" id="gallery-image-box">
                            <input type="file" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            <div class="upload-placeholder" id="gallery-placeholder" style="<?php echo (!empty($gallery_images)) ? 'display:none;' : ''; ?>">
                                <i class="fas fa-images"></i><p>Add more images</p>
                            </div>
                        </div>
                        <div class="image-preview-container gallery" id="gallery-preview-container" style="display: flex; flex-wrap: wrap; gap: 5px; margin-top: 10px;">
                            <?php foreach ($gallery_images as $img): ?>
                                <div class="image-preview-item preview-gallery" id="existing-gal-<?php echo $img['id']; ?>">
                                    <img src="../assets/img/<?php echo htmlspecialchars($img['image_url']); ?>">
                                    <div class="remove-btn" onclick="removeExistingGallery(<?php echo $img['id']; ?>)"><i class="fas fa-times"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div id="deleted-gallery-inputs"></div>
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
const subCategoriesData = <?php echo json_encode($subs_arr); ?>;

$(document).ready(function() {
    $('#description-editor, #info-editor').summernote({
        placeholder: 'Write your content here...', tabsize: 2, height: 150,
        toolbar: [['style', ['bold', 'italic', 'underline', 'clear']], ['para', ['ul', 'ol', 'paragraph']], ['view', ['codeview']]]
    });

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
            subSelect.prop('disabled', true);
        }
    });

    subSelect.on('change', function() {
        if ($(this).val()) btnDeleteSub.prop('disabled', false);
        else btnDeleteSub.prop('disabled', true);
    });

    newMainInput.on('input', function() {
        if ($(this).val().length > 0) {
            mainSelect.prop('disabled', true);
            subSelect.empty().prop('disabled', true);
            btnDeleteSub.prop('disabled', true);
        } else {
            mainSelect.prop('disabled', false);
        }
    });

    newSubInput.on('input', function() {
        if ($(this).val().length > 0) {
            subSelect.prop('disabled', true);
            btnDeleteSub.prop('disabled', true);
        } else if (!newMainInput.val()) {
            subSelect.prop('disabled', false);
        }
    });

    $('#main_image').on('change', function() {
        const file = this.files[0];
        const container = $('#main-image-preview-container');
        container.empty(); $('#main-placeholder').hide();
        if (file) {
            $('#del_main_input').prop('checked', false);
            const reader = new FileReader();
            reader.onload = function(e) {
                container.append(`<div class="image-preview-item preview-cover"><img src="${e.target.result}"><div class="remove-btn" onclick="removeNewMainImage()"><i class="fas fa-times"></i></div></div>`);
            }
            reader.readAsDataURL(file);
        } else { $('#main-placeholder').show(); }
    });
    window.removeExistingMainImage = function() { $('#existing-cover-preview').remove(); $('#del_main_input').prop('checked', true); $('#main-placeholder').show(); };
    window.removeNewMainImage = function() { $('#main_image').val(''); $('#main-image-preview-container').empty(); $('#main-placeholder').show(); };

    const galleryDT = new DataTransfer(); 
    $('#gallery_images').on('change', function() {
        const newFiles = this.files;
        for (let i = 0; i < newFiles.length; i++) { galleryDT.items.add(newFiles[i]); }
        this.files = galleryDT.files;
        $('#gallery-placeholder').hide(); renderNewGalleryPreview();
    });
    function renderNewGalleryPreview() {
        $('.image-preview-item.new-upload').remove();
        Array.from(galleryDT.files).forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#gallery-preview-container').append(`<div class="image-preview-item preview-gallery new-upload"><img src="${e.target.result}"><div class="remove-btn" onclick="removeNewGalleryItem(${index})"><i class="fas fa-times"></i></div></div>`);
            }
            reader.readAsDataURL(file);
        });
    }
    window.removeNewGalleryItem = function(index) {
        const tempDT = new DataTransfer();
        for (let i = 0; i < galleryDT.files.length; i++) { if (i !== index) tempDT.items.add(galleryDT.files[i]); }
        galleryDT.items.clear();
        for (let i = 0; i < tempDT.files.length; i++) galleryDT.items.add(tempDT.files[i]);
        document.getElementById('gallery_images').files = galleryDT.files;
        if (galleryDT.files.length === 0 && $('.image-preview-item').length === 0) { $('#gallery-placeholder').show(); }
        renderNewGalleryPreview();
    };
    window.removeExistingGallery = function(dbId) {
        $('#existing-gal-' + dbId).remove();
        $('#deleted-gallery-inputs').append(`<input type="hidden" name="delete_gallery_images[]" value="${dbId}">`);
        if ($('.image-preview-item').length === 0) { $('#gallery-placeholder').show(); }
    };
});

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