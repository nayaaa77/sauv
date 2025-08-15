<?php
require_once 'includes/db_conn.php';
$token = $_GET['token'] ?? '';
$message = '';
$show_form = false;

// Langkah 1: Validasi token dari URL
if ($token) {
    $stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Jika token valid, izinkan untuk menampilkan form
        $show_form = true;
    } else {
        // Jika token tidak valid atau kedaluwarsa
        $message = "Invalid or expired password reset link.";
    }
    $stmt->close();
}

// Langkah 2: Proses form saat password baru disubmit
if (isset($_POST['update_password'])) {
    $token_from_form = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi ulang token dari form untuk keamanan
    $verify_stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    $verify_stmt->bind_param("s", $token_from_form);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();

    if ($verify_result->num_rows === 1) {
        if ($new_password === $confirm_password) {
            // Jika password cocok, hash dan update ke database
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE reset_token = ?");
            $update_stmt->bind_param("ss", $hashed_password, $token_from_form);
            $update_stmt->execute();
            $update_stmt->close();

            $message = "Your password has been updated successfully! You can now log in.";
            $show_form = false;
        } else {
            $message = "Passwords do not match.";
            $show_form = true; // Tetap tampilkan form jika password tidak cocok
        }
    } else {
        $message = "Invalid or expired password reset link.";
        $show_form = false;
    }
    $verify_stmt->close();
}
?>
<?php include 'includes/header.php'; ?>
<div class="container" style="text-align: center; padding: 80px 20px; max-width: 500px; margin: auto;">
    <h1>Create New Password</h1>
    
    <?php if (!empty($message)): ?>
        <p style="font-weight: 500; color: <?php echo $show_form ? 'red' : 'green'; ?>;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if ($show_form): ?>
    <form action="reset_password.php?token=<?php echo htmlspecialchars($token); ?>" method="POST" style="text-align: left; margin-top: 30px;">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" name="new_password" required style="width: 100%; padding: 12px; border: 1px solid #e5e5e5; box-sizing: border-box;">
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm New Password</label>
            <input type="password" name="confirm_password" required style="width: 100%; padding: 12px; border: 1px solid #e5e5e5; box-sizing: border-box;">
        </div>
        <button type="submit" name="update_password" class="btn" style="width: 100%; padding: 14px; background: #111; color: #fff; border: none; cursor: pointer; text-transform: uppercase;">Update Password</button>
    </form>
    <?php elseif (empty($message) && !$token): ?>
        <p style="color: red;">Invalid request.</p>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
