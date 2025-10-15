<?php 
$page_title = "Account Settings";
include 'includes/header_admin.php'; // This header already contains admin protection

// Initialize message variables
$profile_msg = '';
$password_msg = '';
$error_msg = '';

// --- PROCESS PROFILE UPDATE (EMAIL/NAME) ---
if (isset($_POST['update_profile'])) {
    $new_fullname = $_POST['full_name'];
    $new_email = $_POST['email'];
    $admin_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("ssi", $new_fullname, $new_email, $admin_id);
    if ($stmt->execute()) {
        // Also update session data to reflect changes immediately in the header
        $_SESSION['user_name'] = $new_fullname;
        $profile_msg = "Profile updated successfully!";
    } else {
        $error_msg = "Failed to update profile. The email might already be in use.";
    }
    $stmt->close();
}

// --- PROCESS PASSWORD CHANGE ---
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $admin_id = $_SESSION['user_id'];

    // 1. Get current password from the database
    $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt_pass->bind_param("i", $admin_id);
    $stmt_pass->execute();
    $result = $stmt_pass->get_result();
    $admin_data = $result->fetch_assoc();
    $stmt_pass->close();

    // 2. Verify current password
    if (password_verify($current_password, $admin_data['password'])) {
        // 3. Check if new password and confirmation match
        if ($new_password === $confirm_password) {
            // 4. Hash new password and update the database
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

// Get latest admin data to display in the form
$admin_id = $_SESSION['user_id'];
$current_admin_result = $conn->query("SELECT full_name, email FROM users WHERE id = $admin_id");
$current_admin = $current_admin_result->fetch_assoc();
?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<div class="settings-container">
    
    <?php if($profile_msg): ?><div class="alert alert-success"><?php echo $profile_msg; ?></div><?php endif; ?>
    <?php if($password_msg): ?><div class="alert alert-success"><?php echo $password_msg; ?></div><?php endif; ?>
    <?php if($error_msg): ?><div class="alert alert-danger"><?php echo $error_msg; ?></div><?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Update Profile</h4>
        </div>
        <div class="card-body">
            <form action="settings.php" method="POST">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($current_admin['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($current_admin['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Change Password</h4>
        </div>
        <div class="card-body">
            <form action="settings.php" method="POST">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>