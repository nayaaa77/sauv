<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Pastikan hanya admin yang bisa akses
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access Denied.");
}
require_once '../includes/db_conn.php';

// =================================================
// PROSES TAMBAH PRODUK (VERSI MULTI-GAMBAR)
// =================================================
if (isset($_POST['add_product'])) {
    // 1. Ambil data teks dari form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $additional_info = $_POST['additional_info'] ?? NULL;
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0; 
    
    // 2. Proses Gambar Utama (Cover)
    $main_image_name = 'default.jpg';
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        $target_dir = "../assets/img/"; 
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        $file = $_FILES['main_image'];
        $image_new_name = time() . "-" . preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $file['name'])));
        $target_file = $target_dir . $image_new_name;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $main_image_name = $image_new_name;
        }
    }

    // 3. Simpan Produk Utama & Ambil ID-nya
    $stmt = $conn->prepare("INSERT INTO products (name, description, additional_info, price, stock, image_url, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssdisi", $name, $description, $additional_info, $price, $stock, $main_image_name, $is_featured);
    $stmt->execute();
    
    $last_product_id = $conn->insert_id;
    $stmt->close();

    // 4. Proses & Simpan Gambar Galeri
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        $gallery_files = $_FILES['gallery_images'];
        $target_dir = "../assets/img/";

        foreach ($gallery_files['name'] as $key => $name) {
            if ($gallery_files['error'][$key] == 0) {
                $tmp_name = $gallery_files['tmp_name'][$key];
                $gallery_image_new_name = time() . "-gallery-" . $key . "-" . preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $name)));
                $target_file = $target_dir . $gallery_image_new_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $stmt_gallery = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                    $stmt_gallery->bind_param("is", $last_product_id, $gallery_image_new_name);
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
// PROSES EDIT PRODUK (BARU)
// =================================================
if (isset($_POST['edit_product'])) {
    // 1. Ambil semua data dari form
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $additional_info = $_POST['additional_info'] ?? NULL;
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $current_main_image = $_POST['current_main_image'];

    // 2. Proses update gambar utama jika ada file baru yang diunggah
    $main_image_name = $current_main_image; // Defaultnya adalah gambar yang lama
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] == 0) {
        // Hapus gambar lama jika bukan default
        if ($current_main_image !== 'default.jpg' && file_exists("../assets/img/" . $current_main_image)) {
            unlink("../assets/img/" . $current_main_image);
        }

        // Upload gambar baru
        $target_dir = "../assets/img/";
        $file = $_FILES['main_image'];
        $image_new_name = time() . "-" . preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $file['name'])));
        $target_file = $target_dir . $image_new_name;

        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            $main_image_name = $image_new_name; // Ganti nama file dengan yang baru
        }
    }

    // 3. Update data produk utama di tabel 'products'
    $stmt = $conn->prepare("UPDATE products SET name=?, description=?, additional_info=?, price=?, stock=?, image_url=?, is_featured=? WHERE id=?");
    $stmt->bind_param("sssdisii", $name, $description, $additional_info, $price, $stock, $main_image_name, $is_featured, $product_id);
    $stmt->execute();
    $stmt->close();

    // 4. Hapus gambar galeri yang dicentang
    if (!empty($_POST['delete_gallery_images'])) {
        foreach ($_POST['delete_gallery_images'] as $image_id_to_delete) {
            // Ambil nama file untuk dihapus dari server
            $stmt_get_img = $conn->prepare("SELECT image_url FROM product_images WHERE id = ?");
            $stmt_get_img->bind_param("i", $image_id_to_delete);
            $stmt_get_img->execute();
            $img_result = $stmt_get_img->get_result()->fetch_assoc();
            if ($img_result) {
                if (file_exists("../assets/img/" . $img_result['image_url'])) {
                    unlink("../assets/img/" . $img_result['image_url']);
                }
            }
            $stmt_get_img->close();

            // Hapus record dari database
            $stmt_del_img = $conn->prepare("DELETE FROM product_images WHERE id = ?");
            $stmt_del_img->bind_param("i", $image_id_to_delete);
            $stmt_del_img->execute();
            $stmt_del_img->close();
        }
    }

    // 5. Tambah gambar galeri baru jika ada
    if (isset($_FILES['gallery_images']) && !empty($_FILES['gallery_images']['name'][0])) {
        $gallery_files = $_FILES['gallery_images'];
        $target_dir = "../assets/img/";

        foreach ($gallery_files['name'] as $key => $name) {
            if ($gallery_files['error'][$key] == 0) {
                $tmp_name = $gallery_files['tmp_name'][$key];
                $gallery_image_new_name = time() . "-gallery-" . $key . "-" . preg_replace('/[^a-z0-9\-\.]/', '', strtolower(str_replace(' ', '-', $name)));
                $target_file = $target_dir . $gallery_image_new_name;

                if (move_uploaded_file($tmp_name, $target_file)) {
                    $stmt_gallery = $conn->prepare("INSERT INTO product_images (product_id, image_url) VALUES (?, ?)");
                    $stmt_gallery->bind_param("is", $product_id, $gallery_image_new_name);
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
// PROSES HAPUS PRODUK (VERSI MULTI-GAMBAR)
// =================================================
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    // 1. Hapus file gambar utama dari server
    $stmt_main_img = $conn->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt_main_img->bind_param("i", $product_id);
    $stmt_main_img->execute();
    $result_main_img = $stmt_main_img->get_result()->fetch_assoc();
    if ($result_main_img && $result_main_img['image_url'] != 'default.jpg') {
        $image_path = '../assets/img/' . $result_main_img['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt_main_img->close();

    // 2. Ambil semua gambar galeri terkait dan hapus filenya
    $stmt_gallery_imgs = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmt_gallery_imgs->bind_param("i", $product_id);
    $stmt_gallery_imgs->execute();
    $result_gallery_imgs = $stmt_gallery_imgs->get_result();
    while ($row = $result_gallery_imgs->fetch_assoc()) {
        $gallery_image_path = '../assets/img/' . $row['image_url'];
        if (file_exists($gallery_image_path)) {
            unlink($gallery_image_path);
        }
    }
    $stmt_gallery_imgs->close();

    // 3. Hapus record dari product_images terlebih dahulu
    $stmt_del_gallery = $conn->prepare("DELETE FROM product_images WHERE product_id = ?");
    $stmt_del_gallery->bind_param("i", $product_id);
    $stmt_del_gallery->execute();
    $stmt_del_gallery->close();

    // 4. Hapus record dari products
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    header('Location: manage_products.php?status=delete_success');
    exit();
}
?>