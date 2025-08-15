<?php
require_once 'includes/db_conn.php';
$message = '';
$is_success = false;

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ? AND is_verified = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        
        // Update status verifikasi
        $update_stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();
        
        $message = "Your account has been successfully verified!";
        $is_success = true;
    } else {
        $message = "Invalid or expired verification link.";
    }
    $stmt->close();
} else {
    $message = "No verification token provided.";
}
?>
<?php include 'includes/header.php'; ?>
<div class="container" style="text-align: center; padding: 80px 20px;">
    <h1>Account Verification</h1>
    <p style="font-size: 1.2rem;"><?php echo $message; ?></p>
    <?php if ($is_success): ?>
        <a href="login_register.php" class="btn" style="margin-top: 20px;">Proceed to Login</a>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
