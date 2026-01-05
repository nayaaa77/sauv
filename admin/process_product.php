<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { die("Access Denied."); }
require_once '../includes/db_conn.php';

// HELPER: Resolve Category ID
function resolveCategoryId($conn, $main_select, $main_new, $sub_select, $sub_new) {
    $final_parent_id = NULL;

    // A. Main Category
    if (!empty($main_new)) {
        $name = trim($main_new);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $q = $conn->query("SELECT id FROM categories WHERE slug = '$slug' AND parent_id IS NULL");
        if ($q->num_rows > 0) {
            $final_parent_id = $q->fetch_assoc()['id'];
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, NULL)");
            $stmt->bind_param("ss", $name, $slug);
            if($stmt->execute()) $final_parent_id = $conn->insert_id;
        }
    } else {
        $final_parent_id = !empty($main_select) ? $main_select : NULL;
    }

    if (!$final_parent_id) return NULL;

    // B. Sub Category
    $final_category_id = $final_parent_id; 
    if (!empty($sub_new)) {
        $name = trim($sub_new);
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $stmt = $conn->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $name, $slug, $final_parent_id);
        if($stmt->execute()) $final_category_id = $conn->insert_id;
    } elseif (!empty($sub_select)) {
        $final_category_id = $sub_select;
    }
    return $final_category_id;
}

// 1. ADD PRODUCT
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = resolveCategoryId($conn, $_POST['main_cat_select'] ?? '', $_POST['new_main_cat'] ?? '', $_POST['sub_cat_select'] ?? '', $_POST['new_sub_cat'] ?? '');
    $description = $_POST['description'];
    $additional_info = $_POST['additional_info'] ?? NULL;
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0; 
    
    $main_image_name = 'default.jpg';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = "../assets/img/"; 
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $file = $_FILES['main_image'];
        $clean_name = preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $file['name'])));
        $image_new_name = time() . "-" . uniqid() . "-" . $clean_name;
        $target_file = $target_dir . $image_new_name;
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $main_image_name = $image_new_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO products (name, category_id, description, additional_info, price, stock, image_url, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sissdisi", $name, $category_id, $description, $additional_info, $price, $stock, $main_image_name, $is_featured);
    
    if ($stmt->execute()) {
        $last_product_id = $conn->insert_id;
        $stmt->close();
        if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
            $target_dir = "../assets/img/";
            foreach ($_FILES['gallery_images']['name'] as $key => $fname) {
                if ($_FILES['gallery_images']['error'][$key] == 0) {
                    $clean_name = preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $fname)));
                    $gallery_new_name = time() . "-gal-" . $key . "-" . $clean_name;
                    $target_file = $target_dir . $gallery_new_name;
                    if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], $target_file)) {
                        $stmt_gallery = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                        $stmt_gallery->bind_param("is", $last_product_id, $gallery_new_name);
                        $stmt_gallery->execute();
                        $stmt_gallery->close();
                    }
                }
            }
        }
        header('Location: manage_products.php?status=add_success'); 
    } else { die("Error Insert: " . $conn->error); }
    exit();
}

// 2. EDIT PRODUCT
if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $category_id = resolveCategoryId($conn, $_POST['main_cat_select'] ?? '', $_POST['new_main_cat'] ?? '', $_POST['sub_cat_select'] ?? '', $_POST['new_sub_cat'] ?? '');
    $description = $_POST['description'];
    $additional_info = $_POST['additional_info'] ?? NULL;
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    $current_main_image = $_POST['current_main_image'];
    $main_image_name = $current_main_image; 

    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = "../assets/img/";
        $file = $_FILES['main_image'];
        $clean_name = preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $file['name'])));
        $image_new_name = time() . "-" . uniqid() . "-" . $clean_name;
        $target_file = $target_dir . $image_new_name;
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $main_image_name = $image_new_name;
            if ($current_main_image !== 'default.jpg' && $current_main_image !== $image_new_name) {
                $old_path = $target_dir . $current_main_image;
                if (file_exists($old_path)) { unlink($old_path); }
            }
        }
    } elseif (isset($_POST['delete_main_image']) && $_POST['delete_main_image'] == '1') {
        if ($current_main_image !== 'default.jpg') {
            $path_to_delete = "../assets/img/" . $current_main_image;
            if (file_exists($path_to_delete)) { unlink($path_to_delete); }
        }
        $main_image_name = 'default.jpg';
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, description=?, additional_info=?, price=?, stock=?, image_url=?, is_featured=? WHERE id=?");
    $stmt->bind_param("sissdisii", $name, $category_id, $description, $additional_info, $price, $stock, $main_image_name, $is_featured, $product_id);
    
    if ($stmt->execute()) {
        $stmt->close();
        if (!empty($_POST['delete_gallery_images'])) {
            foreach ($_POST['delete_gallery_images'] as $image_id) {
                $q = $conn->query("SELECT image_url FROM product_images WHERE id = $image_id");
                if ($row = $q->fetch_assoc()) {
                    $path = "../assets/img/" . $row['image_url'];
                    if (file_exists($path)) { unlink($path); }
                }
                $conn->query("DELETE FROM product_images WHERE id = $image_id");
            }
        }
        if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
            $target_dir = "../assets/img/";
            foreach ($_FILES['gallery_images']['name'] as $key => $fname) {
                if ($_FILES['gallery_images']['error'][$key] == 0) {
                    $clean_name = preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $fname)));
                    $gallery_new_name = time() . "-gal-" . $key . "-" . $clean_name;
                    $target_file = $target_dir . $gallery_new_name;
                    if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], $target_file)) {
                        $stmt_gallery = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                        $stmt_gallery->bind_param("is", $product_id, $gallery_new_name);
                        $stmt_gallery->execute();
                        $stmt_gallery->close();
                    }
                }
            }
        }
        header('Location: manage_products.php?status=edit_success');
    } else { die("Error Update: " . $conn->error); }
    exit();
}

// 3. DELETE PRODUCT
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $res = $conn->query("SELECT image_url FROM products WHERE id = $product_id");
    if ($row = $res->fetch_assoc()) {
        if ($row['image_url'] != 'default.jpg') @unlink("../assets/img/" . $row['image_url']);
    }
    $res_gal = $conn->query("SELECT image_url FROM product_images WHERE product_id = $product_id");
    while ($row = $res_gal->fetch_assoc()) { @unlink("../assets/img/" . $row['image_url']); }
    $conn->query("DELETE FROM product_images WHERE product_id = $product_id");
    $conn->query("DELETE FROM products WHERE id = $product_id");
    header('Location: manage_products.php?status=delete_success'); 
    exit();
}

// 4. DELETE CATEGORY
if (isset($_GET['delete_category_id'])) {
    $cat_id = (int)$_GET['delete_category_id'];
    $conn->query("UPDATE products SET category_id = NULL WHERE category_id = $cat_id");
    $conn->query("UPDATE categories SET parent_id = NULL WHERE parent_id = $cat_id");
    $del = $conn->prepare("DELETE FROM categories WHERE id = ?");
    $del->bind_param("i", $cat_id);
    if ($del->execute()) {
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'add_product.php';
        echo "<script>alert('Category deleted successfully!'); window.location.href='$redirect';</script>";
    } else {
        echo "<script>alert('Failed: " . $conn->error . "'); window.history.back();</script>";
    }
    $del->close();
    exit();
}

// 5. QUICK ADD CATEGORY ONLY
if (isset($_POST['add_category_only'])) {
    $parent_id = !empty($_POST['quick_parent_id']) ? $_POST['quick_parent_id'] : NULL;
    $name = trim($_POST['quick_cat_name']);
    
    if (!empty($name)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $check_sql = "SELECT id FROM categories WHERE slug = '$slug'";
        if ($parent_id) $check_sql .= " AND parent_id = '$parent_id'";
        else $check_sql .= " AND parent_id IS NULL";
        
        $check = $conn->query($check_sql);
        if ($check->num_rows > 0) {
            echo "<script>alert('Category already exists!'); window.history.back();</script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO categories (name, slug, parent_id) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $name, $slug, $parent_id);
            if ($stmt->execute()) {
                $redirect = $_SERVER['HTTP_REFERER'] ?? 'add_product.php';
                echo "<script>alert('Category created successfully!'); window.location.href='$redirect';</script>";
            } else { echo "<script>alert('Error: " . $conn->error . "'); window.history.back();</script>"; }
            $stmt->close();
        }
    } else { echo "<script>alert('Category name cannot be empty!'); window.history.back();</script>"; }
    exit();
}
// =================================================
// 6. TOGGLE MANUAL SOLD OUT (QUICK ACTION)
// =================================================
if (isset($_POST['toggle_sold_out'])) {
    $product_id = (int)$_POST['product_id'];
    $current_status = (int)$_POST['current_status'];
    
    // Balik statusnya (Jika 1 jadi 0, Jika 0 jadi 1)
    $new_status = ($current_status == 1) ? 0 : 1;
    
    $stmt = $conn->prepare("UPDATE products SET is_sold_out = ? WHERE id = ?");
    $stmt->bind_param("ii", $new_status, $product_id);
    
    if ($stmt->execute()) {
        $status_msg = ($new_status == 1) ? 'toggled_on' : 'toggled_off';
        $redirect = $_SERVER['HTTP_REFERER'] ?? 'manage_products.php';
        header("Location: manage_products.php?status=$status_msg");
    } else {
        echo "<script>alert('Failed to update status: " . $conn->error . "'); window.history.back();</script>";
    }
    $stmt->close();
    exit();
}
?>