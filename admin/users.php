<?php 
$page_title = "Manage Users"; // Set judul halaman
include 'includes/header_admin.php'; 

// Logika untuk menghapus user (opsional, bisa ditambahkan nanti)
if (isset($_POST['delete_user'])) {
    $user_id_to_delete = (int)$_POST['user_id'];
    // Pastikan admin tidak bisa menghapus akunnya sendiri
    if ($user_id_to_delete !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user'");
        $stmt->bind_param("i", $user_id_to_delete);
        $stmt->execute();
        $stmt->close();
        header("Location: users.php?delete=success");
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
?>

<!-- Script untuk mengatur judul di header utama -->
<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<!-- Header Halaman Konten -->
<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
</div>

<!-- Tabel Pengguna di dalam Card -->
<div class="card">
    <div class="card-body">
        <table class="table-products">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-name">Full Name</th>
                    <th>Email</th>
                    <th>Registered On</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($users_result && $users_result->num_rows > 0): ?>
                    <?php while($user = $users_result->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo date('d M Y, H:i', strtotime($user['created_at'])); ?></td>
                            <td class="actions">
                                <form action="users.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <button type="submit" name="delete_user" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="no-products">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>
