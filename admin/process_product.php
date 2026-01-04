<?php
// 1. Cek Session & Akses Admin
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') { 
    die("Access Denied."); 
}

// 2. Koneksi Database
require_once '../includes/db_conn.php';

// --- FUNGSI HELPER: Handle Kategori Baru ---
function getCategoryId($conn, $cat_id_post, $new_cat_post) {
    if (!empty($new_cat_post)) {
        $name = trim($new_cat_post);
        // Buat slug otomatis dari nama (contoh: "Baju Baru" -> "baju-baru")
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        
        // Cek apakah kategori sudah ada?
        $check = $conn->query("SELECT id FROM categories WHERE slug = '$slug'");
        if ($check->num_rows > 0) {
            return $check->fetch_assoc()['id'];
        }
        
        // Jika belum, insert baru
        $ins = $conn->prepare("INSERT INTO categories (name, slug) VALUES (?, ?)");
        $ins->bind_param("ss", $name, $slug);
        return $ins->execute() ? $conn->insert_id : NULL;
    }
    // Jika tidak bikin baru, pakai yang dipilih dari dropdown
    return !empty($cat_id_post) ? $cat_id_post : NULL;
}

// =================================================
// 1. PROSES TAMBAH PRODUK (ADD)
// =================================================
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $category_id = getCategoryId($conn, $_POST['category_id'], $_POST['new_category']);
    $description = $_POST['description'];
    $additional_info = $_POST['additional_info'] ?? NULL;
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0; 
    
    // --- Upload Main Image ---
    $main_image_name = 'default.jpg';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = "../assets/img/"; 
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }

        $file = $_FILES['main_image'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        // Nama file unik: time + random ID
        $image_new_name = time() . "-" . uniqid() . "." . $ext;
        $target_file = $target_dir . $image_new_name;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $main_image_name = $image_new_name;
        }
    }

    // Insert Data Produk ke DB
    $stmt = $conn->prepare("INSERT INTO products (name, category_id, description, additional_info, price, stock, image_url, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sisssdis", $name, $category_id, $description, $additional_info, $price, $stock, $main_image_name, $is_featured);
    $stmt->execute();
    $last_product_id = $conn->insert_id; // Ambil ID produk yang baru dibuat
    $stmt->close();

    // --- Upload Gallery Images ---
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        $target_dir = "../assets/img/";
        foreach ($_FILES['gallery_images']['name'] as $key => $fname) {
            if ($_FILES['gallery_images']['error'][$key] == 0) {
                $ext = pathinfo($fname, PATHINFO_EXTENSION);
                $gname = time() . "-gal-" . uniqid() . "." . $ext;
                $target_file = $target_dir . $gname;
                
                if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], $target_file)) {
                    $stmt_gallery = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                    $stmt_gallery->bind_param("is", $last_product_id, $gname);
                    $stmt_gallery->execute();
                    $stmt_gallery->close();
                }
            }
        }
    }
    
    header('Location: manage_products.php?status=add_success'); 
    exit();
}

// =================================================
// 2. PROSES EDIT PRODUK (UPDATE)
// =================================================
if (isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $category_id = getCategoryId($conn, $_POST['category_id'], $_POST['new_category']);
    $description = $_POST['description'];
    $additional_info = $_POST['additional_info'] ?? NULL;
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    // Ambil nama gambar saat ini (dari input hidden)
    $current_main_image = $_POST['current_main_image'];
    $main_image_name = $current_main_image;

    // A. Cek jika User menekan tombol Hapus (X) pada Cover
    if (isset($_POST['delete_main_image']) && $_POST['delete_main_image'] == '1') {
        if ($current_main_image !== 'default.jpg') {
            $path_to_delete = "../assets/img/" . trim($current_main_image);
            // Hapus file jika ada
            if (file_exists($path_to_delete)) { unlink($path_to_delete); }
        }
        $main_image_name = 'default.jpg';
    }

    // B. Cek jika User Upload Cover Baru
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = "../assets/img/";
        
        // Hapus file lama dulu (biar server gak penuh)
        if ($main_image_name !== 'default.jpg') {
            $old_path = $target_dir . trim($main_image_name);
            if (file_exists($old_path)) { unlink($old_path); }
        }

        $file = $_FILES['main_image'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $image_new_name = time() . "-" . uniqid() . "." . $ext;
        $target_file = $target_dir . $image_new_name;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $main_image_name = $image_new_name;
        }
    }

    // Update Data Produk di DB
    $stmt = $conn->prepare("UPDATE products SET name=?, category_id=?, description=?, additional_info=?, price=?, stock=?, image_url=?, is_featured=? WHERE id=?");
    $stmt->bind_param("sisssdisi", $name, $category_id, $description, $additional_info, $price, $stock, $main_image_name, $is_featured, $product_id);
    $stmt->execute();
    $stmt->close();

    // C. Hapus Gallery yang Dipilih (Checkbox)
    if (!empty($_POST['delete_gallery_images'])) {
        foreach ($_POST['delete_gallery_images'] as $image_id_to_delete) {
            // Ambil nama file dulu untuk dihapus fisik
            $stmt_get = $conn->prepare("SELECT image_url FROM product_images WHERE id = ?");
            $stmt_get->bind_param("i", $image_id_to_delete);
            $stmt_get->execute();
            $row = $stmt_get->get_result()->fetch_assoc();
            $stmt_get->close();
            
            if ($row) {
                $file_del = "../assets/img/" . trim($row['image_url']);
                if (file_exists($file_del)) { unlink($file_del); }
            }
            
            // Hapus record dari DB
            $conn->query("DELETE FROM product_images WHERE id = $image_id_to_delete");
        }
    }

    // D. Tambah Gallery Baru
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        $target_dir = "../assets/img/";
        foreach ($_FILES['gallery_images']['name'] as $key => $fname) {
            if ($_FILES['gallery_images']['error'][$key] == 0) {
                $ext = pathinfo($fname, PATHINFO_EXTENSION);
                $gname = time() . "-gal-" . uniqid() . "." . $ext;
                $target_file = $target_dir . $gname;
                
                if (move_uploaded_file($_FILES['gallery_images']['tmp_name'][$key], $target_file)) {
                    $stmt_gallery = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                    $stmt_gallery->bind_param("is", $product_id, $gname);
                    $stmt_gallery->execute();
                    $stmt_gallery->close();
                }
            }
        }
    }
    
    header('Location: manage_products.php?status=edit_success'); 
    exit();
}

// =================================================
// 3. PROSES HAPUS PRODUK (DELETE - FORCE DELETE)
// =================================================
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    // A. Hapus Cover Utama (Fisik)
    $stmt_main = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt_main->bind_param("i", $product_id);
    $stmt_main->execute();
    $res_main = $stmt_main->get_result()->fetch_assoc();
    $stmt_main->close();

    if ($res_main && !empty($res_main['image_url']) && $res_main['image_url'] != 'default.jpg') {
        $file_path = "../assets/img/" . trim($res_main['image_url']);
        // Coba hapus, abaikan error jika file sudah hilang (@)
        if (file_exists($file_path)) { unlink($file_path); } 
        else { @unlink($file_path); }
    }

    // B. Hapus Gallery (Fisik)
    $stmt_gallery = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmt_gallery->bind_param("i", $product_id);
    $stmt_gallery->execute();
    $res_gallery = $stmt_gallery->get_result();
    
    while ($row = $res_gallery->fetch_assoc()) {
        if (!empty($row['image_url'])) {
            $gal_path = "../assets/img/" . trim($row['image_url']);
            if (file_exists($gal_path)) { unlink($gal_path); }
            else { @unlink($gal_path); }
        }
    }
    $stmt_gallery->close();

    // C. Hapus Data dari Database (Wajib berhasil walau file hilang)
    // Hapus child (gallery) dulu karena Foreign Key
    $conn->query("DELETE FROM product_images WHERE product_id = $product_id");
    // Hapus parent (products)
    $conn->query("DELETE FROM products WHERE id = $product_id");

    header('Location: manage_products.php?status=delete_success'); 
    exit();
}
?>