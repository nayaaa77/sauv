<?php 
$page_title = "Manage Users"; // Set judul halaman
include 'includes/header_admin.php'; 

// Logika untuk menghapus user
if (isset($_POST['delete_user'])) {
    $user_id_to_delete = (int)$_POST['user_id'];
    // Pastikan admin tidak bisa menghapus akunnya sendiri
    if ($user_id_to_delete !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->bind_param("i", $user_id_to_delete);
        $stmt->execute();
        $stmt->close();
        header("Location: users.php?status=delete_success");
        exit();
    }
}

// Ambil semua data pengguna dengan role 'user'
$users_result = $conn->query("
    SELECT id, full_name, email, created_at 
    FROM users 
    WHERE role = 'user' 
    ORDER BY created_at DESC
");
$user_count = $users_result ? $users_result->num_rows : 0;
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<?php
// Blok untuk menampilkan notifikasi
if (isset($_GET['status']) && $_GET['status'] == 'delete_success') {
    echo "<div class='alert alert-success'>User deleted successfully!</div>";
}
?>

<div class="content-wrapper-card">
    <div class="card-header">
        <h2 class="card-title">All Users <span class="item-count"><?php echo $user_count; ?></span></h2>
    </div>
    <div class="card-body no-padding">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Registered On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($user['created_at'])); ?></td>
                            <td class="actions">
                                <form action="users.php" method="POST" style="display:inline; margin:0;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="action-icon" title="Delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-data">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>