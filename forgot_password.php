<?php
session_start();
// Atur zona waktu PHP ke Waktu Indonesia Barat agar konsisten dengan database
date_default_timezone_set('Asia/Jakarta');

require_once 'includes/db_conn.php';
require_once 'includes/mailer_config.php';
$message = '';

// Proses form saat tombol "Reset Password" ditekan
if (isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah email ada di database
    if ($result->num_rows === 1) {
        // Buat token reset yang aman dan waktu kedaluwarsa (1 jam dari sekarang)
        $reset_token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Simpan token dan waktu kedaluwarsa ke database
        $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires_at = ? WHERE email = ?");
        $update_stmt->bind_param("sss", $reset_token, $expires, $email);
        $update_stmt->execute();

        // Siapkan link dan konten email
        $reset_link = "http://localhost/sauv/reset_password.php?token=" . $reset_token;
        $email_subject = "Password Reset Request - Sauvatte";
        $email_body = "
            <h3>Password Reset</h3>
            <p>Someone requested a password reset for your account. If this was you, click the link below. This link is valid for 1 hour.</p>
            <p>If the link is not clickable, please copy and paste the full URL into your browser's address bar.</p>
            <p><a href='$reset_link' style='padding: 10px 15px; background-color: #111; color: #fff; text-decoration: none; border-radius: 5px;'>Reset My Password</a></p>
            <p><small>Full Link: $reset_link</small></p>
        ";
        
        // Kirim email sungguhan
        send_email($email, $email_subject, $email_body);
    }
    
    // Selalu tampilkan pesan ini untuk keamanan, terlepas dari apakah email ditemukan atau tidak
    $message = "If an account with that email exists, a password reset link has been sent to your inbox.";
}
?>
<?php include 'includes/header.php'; ?>
<div class="container" style="text-align: center; padding: 80px 20px; max-width: 500px; margin: auto;">
    <h1>Have you Forgotten Your Password ?</h1>
    <p style="color: #555;">If you've forgotten your password, enter your e-mail address and we'll send you an e-mail.</p>
    
    <?php if (!empty($message)): ?>
        <p style="color: green; font-weight: 500;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form action="forgot_password.php" method="POST" style="text-align: left; margin-top: 30px;">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" required style="width: 100%; padding: 12px; border: 1px solid #e5e5e5; box-sizing: border-box;">
        </div>
        <button type="submit" name="reset_password" class="btn" style="width: 100%; padding: 14px; background: #111; color: #fff; border: none; cursor: pointer; text-transform: uppercase;">Reset Password</button>
    </form>

    <!-- Kotak simulasi email sudah dihapus -->

</div>
<?php include 'includes/footer.php'; ?>
