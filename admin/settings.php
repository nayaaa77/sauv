<?php 
$page_title = "Account Settings";
include 'includes/header_admin.php'; // Header ini sudah berisi proteksi admin

// Inisialisasi variabel pesan
$profile_msg = '';
$password_msg = '';
$error_msg = '';

// --- PROSES UBAH PROFIL (EMAIL/NAMA) ---
if (isset($_POST['update_profile'])) {
    $new_fullname = $_POST['full_name'];
    $new_email = $_POST['email'];
    $admin_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("ssi", $new_fullname, $new_email, $admin_id);
    if ($stmt->execute()) {
        // Update juga data di session agar langsung berubah di header
        $_SESSION['user_name'] = $new_fullname;
        $profile_msg = "Profile updated successfully!";
    } else {
        $error_msg = "Failed to update profile. The email might already be in use.";
    }
    $stmt->close();
}

// --- PROSES UBAH PASSWORD ---
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_id = $_SESSION['user_id'];

    // 1. Ambil password saat ini dari database
    $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt_pass->bind_param("i", $admin_id);
    $stmt_pass->execute();
    $result = $stmt_pass->get_result();
    $admin_data = $result->fetch_assoc();
    $stmt_pass->close();

    // 2. Verifikasi password saat ini
    if (password_verify($current_password, $admin_data['password'])) {
        // 3. Cek apakah password baru dan konfirmasi cocok
        if ($new_password === $confirm_password) {
            // 4. Hash password baru dan update ke database
            $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_update->bind_param("si", $new_hashed_password, $admin_id);
            if ($stmt_update->execute()) {
                $password_msg = "Password changed successfully!";
            } else {
                $error_msg = "Failed to change password.";
            }
            $stmt_update->close();
        } else {
            $error_msg = "New password and confirmation do not match.";
        }
    } else {
        $error_msg = "Incorrect current password.";
    }
}

// Ambil data admin terbaru untuk ditampilkan di form
$admin_id = $_SESSION['user_id'];
$current_admin_result = $conn->query("SELECT full_name, email FROM users WHERE id = $admin_id");
$current_admin = $current_admin_result->fetch_assoc();

?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<div class="settings-container">
    <?php if($profile_msg): ?><div class="notice success"><?php echo $profile_msg; ?></div><?php endif; ?>
    <?php if($password_msg): ?><div class="notice success"><?php echo $password_msg; ?></div><?php endif; ?>
    <?php if($error_msg): ?><div class="notice error"><?php echo $error_msg; ?></div><?php endif; ?>

    <div class="card">
        <h4>Ubah Detail Akun</h4>
        <form action="settings.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($current_admin['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($current_admin['email']); ?>" required>
            </div>
            <button type="submit" name="update_profile" class="btn">Update Profile</button>
        </form>
    </div>

    <div class="card">
        <h4>Ubah Password</h4>
        <form action="settings.php" method="POST">
            <div class="form-group">
                <label for="current_password">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" name="change_password" class="btn">Change Password</button>
        </form>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>