<?php
// Memulai dengan menyertakan header admin.
include 'includes/header_admin.php';

// Menentukan direktori untuk upload gambar
$upload_dir = '../uploads/';

// --- LOGIKA UNTUK PROSES FORM (ADD, UPDATE, DELETE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author_id = $_SESSION['user_id'] ?? 1;
    $image_url = $_POST['existing_image'] ?? '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        if ($_POST['action'] === 'update' && !empty($image_url) && file_exists($upload_dir . $image_url)) {
            unlink($upload_dir . $image_url);
        }
        // Ambil nama asli dan bersihkan dari karakter aneh
        $original_name = basename($_FILES['image']['name']);
        $safe_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", $original_name);
// Gabungkan dengan timestamp untuk menjadikannya unik
        $image_name = time() . '_' . $safe_name;
        $target_file = $upload_dir . $image_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_url = $image_name;
        } else {
            echo "<div class='alert alert-danger'>Gagal mengupload gambar.</div>";
        }
    }

    if ($_POST['action'] === 'add') {
        $stmt = $conn->prepare("INSERT INTO blog (title, content, image_url, author_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $title, $content, $image_url, $author_id);
        if ($stmt->execute()) {
            header('Location: manage_blog.php?status=added');
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } elseif ($_POST['action'] === 'update') {
        $id = $_POST['id'];
        $stmt = $conn->prepare("UPDATE blog SET title = ?, content = ?, image_url = ?, author_id = ? WHERE id = ?");
        $stmt->bind_param("sssii", $title, $content, $image_url, $author_id, $id);
        if ($stmt->execute()) {
            header('Location: manage_blog.php?status=updated');
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
    exit();
}

// Proses penghapusan data
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt_select = $conn->prepare("SELECT image_url FROM blog WHERE id = ?");
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    if ($row = $result->fetch_assoc()) {
        $image_to_delete = $upload_dir . $row['image_url'];
        if (file_exists($image_to_delete) && !empty($row['image_url'])) {
            unlink($image_to_delete);
        }
    }
    $stmt_select->close();

    $stmt_delete = $conn->prepare("DELETE FROM blog WHERE id = ?");
    $stmt_delete->bind_param("i", $id);
    if ($stmt_delete->execute()) {
        header('Location: manage_blog.php?status=deleted');
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt_delete->error . "</div>";
    }
    $stmt_delete->close();
    exit();
}
?>

<div class="content-header">
    <h1>Manage Blog</h1>
</div>

<?php
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message = '';
    if ($status === 'added') $message = 'Blog post berhasil ditambahkan!';
    if ($status === 'updated') $message = 'Blog post berhasil diperbarui!';
    if ($status === 'deleted') $message = 'Blog post berhasil dihapus!';
    echo "<div class='alert alert-success'>$message</div>";
}

$action = $_GET['action'] ?? 'list';

if ($action === 'add' || $action === 'edit') :
    $post = null;
    $form_action = 'add';
    $button_label = 'Add Post';

    if ($action === 'edit' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("SELECT * FROM blog WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $post = $result->fetch_assoc();
        $stmt->close();
        
        $form_action = 'update';
        $button_label = 'Update Post';
    }
?>
    <div class="card">
        <div class="card-header">
            <h2><?php echo $action === 'add' ? 'Add New Post' : 'Edit Post'; ?></h2>
        </div>
        <div class="card-body" style="padding: 25px;">
            <form action="manage_blog.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $form_action; ?>">
                <?php if ($action === 'edit') : ?>
                    <input type="hidden" name="id" value="<?php echo $post['id']; ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($post['image_url']); ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($post['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="content">Content</label>
                    <textarea id="content" name="content" rows="10"><?php echo $post['content'] ?? ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" accept="image/*" class="form-control-file">
                    <?php if ($action === 'edit' && !empty($post['image_url'])) : ?>
                        <p class="mt-2">Current Image: <br><img src="<?php echo $upload_dir . htmlspecialchars($post['image_url']); ?>" alt="Current Image" width="150"></p>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary"><?php echo $button_label; ?></button>
                    <a href="manage_blog.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

<?php else : ?>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2>Blog Posts</h2>
            <a href="manage_blog.php?action=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Post
            </a>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Title</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT id, title, image_url, created_at FROM blog ORDER BY created_at DESC");
                    if ($result->num_rows > 0) :
                        $count = 1;
                        while ($row = $result->fetch_assoc()) :
                    ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><img src="<?php echo $upload_dir . rawurlencode(htmlspecialchars($row['image_url'])); ?>" alt="Blog Image" width="100"></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                                <td class="actions">
                                    <a href="manage_blog.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="manage_blog.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus post ini?');"><i class="fas fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                    <?php
                        endwhile;
                    else :
                    ?>
                        <tr>
                            <td colspan="5" class="text-center">No blog posts found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php
include 'includes/footer_admin.php';
?>