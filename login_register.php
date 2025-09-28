<?php
/* --- KODE BARU --- */
// 1. Mulai Session dan Alihkan jika sudah login
// ==========================================================
// Selalu mulai session di awal file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Jika pengguna sudah login, langsung alihkan ke halaman akun mereka
if (isset($_SESSION['user_id'])) {
    header('Location: my_account.php');
    exit();
}
// ==========================================================
/* --- AKHIR KODE BARU --- */

require_once 'includes/db_conn.php';
require_once 'includes/functions.php';
require_once 'includes/mailer_config.php'; // Muat konfigurasi email

$errors = [];
// --- PROSES REGISTRASI ---
if (isset($_POST['register'])) {
    // ... (kode registrasi Anda tidak berubah) ...
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak cocok.";
    }
    
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();
    if ($stmt_check->num_rows > 0) {
        $errors[] = "Email sudah terdaftar.";
    }
    $stmt_check->close();

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $verification_token = bin2hex(random_bytes(32));

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, verification_token) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $verification_token);
        
        if ($stmt->execute()) {
            $verification_link = "http://localhost/sauv/verify.php?token=" . $verification_token;
            $email_subject = "Verify Your Account - Sauvatte";
            $email_body = "
                <h3>Welcome to Sauvatte!</h3>
                <p>Please click the link below to verify your account. If the link is not clickable, please copy and paste the full URL into your browser's address bar.</p>
                <p><a href='$verification_link' style='padding: 10px 15px; background-color: #111; color: #fff; text-decoration: none; border-radius: 5px;'>Verify My Account</a></p>
                <p><small>Full Link: $verification_link</small></p>
            ";
            
            send_email($email, $email_subject, $email_body);
            header('Location: check_email.php');
            exit();
        } else {
            $errors[] = "Registrasi gagal, silakan coba lagi.";
        }
        $stmt->close();
    }
}

// --- PROSES LOGIN ---
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['is_verified'] == 0) {
            $errors[] = "Akun Anda belum diverifikasi. Silakan cek email Anda.";
        } elseif (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // Periksa apakah ada URL tujuan setelah login
            if (isset($_SESSION['redirect_to'])) {
                $redirect_url = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']); // Hapus session agar tidak redirect ke sana terus
                header("Location: " . $redirect_url);
            } else {
                header('Location: index.php'); // Tujuan default
            }
            exit();
        } else {
            $errors[] = "Email atau password salah.";
        }
    } else {
        $errors[] = "Akun belum terdaftar. Silakan registrasi terlebih dahulu.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - Sauvatte</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Jost:wght@300;400;500&display=swap');
        body { font-family: 'Jost', sans-serif; background-color: #fff; color: #111; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .account-container { max-width: 400px; width: 100%; text-align: center; padding: 20px; }
        h1 { font-size: 24px; font-weight: 400; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 20px; }
        .logo-container { margin-bottom: 30px; }
        .logo-container img { max-width: 220px; height: auto; }
        .notification { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; text-align: center; font-weight: 500; }
        .notification.success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .error-messages { color: red; background-color: #ffebee; border: 1px solid red; padding: 10px; margin-bottom: 20px; font-size: 14px; text-align: left; }
        
        /* --- KODE BARU --- */
        /* 2. Style untuk Notifikasi Login dari halaman lain */
        .login-alert { padding: 15px; margin-bottom: 20px; border: 1px solid #faebcc; border-radius: 4px; color: #8a6d3b; background-color: #fcf8e3; text-align: center; font-weight: 500;}
        /* --- AKHIR KODE BARU --- */
        
        .form-toggle { display: flex; border: 1px solid #e5e5e5; margin-bottom: 30px; }
        .toggle-btn { flex: 1; padding: 12px 10px; background-color: #f7f7f7; border: none; cursor: pointer; font-size: 14px; text-transform: uppercase; letter-spacing: 0.1em; transition: all 0.3s ease; }
        .toggle-btn.active { background-color: #fff; border: 1px solid #111; margin: -1px; }
        .form-content { text-align: left; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 13px; margin-bottom: 8px; }
        .form-group input { width: 100%; padding: 12px; border: 1px solid #e5e5e5; box-sizing: border-box; font-size: 14px; }
        .btn-primary { width: 100%; padding: 14px; background-color: #111; color: #fff; border: none; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 0.1em; margin-top: 10px; }
        .form-footer { margin-top: 15px; font-size: 13px; }
        .form-footer a { color: #111; text-decoration: none; }
        #register-form { display: none; }
    </style>
</head>
<body>

    <div class="account-container">
        <?php
            if (isset($_SESSION['message']) && !empty($_SESSION['message'])) {
                echo '<div class="login-alert">' . htmlspecialchars($_SESSION['message']) . '</div>';
                unset($_SESSION['message']);
            }
        ?>
        <?php if (!empty($register_notification)): ?>
            <div class="notification success">
                <?php echo $register_notification; ?>
            </div>
        <?php endif; ?>

        <h1>My account</h1>
        <div class="logo-container">
            <img src="assets/img/logo.png" alt="Sauvatte Logo">
        </div>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <p style="margin: 0;"><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form-toggle">
            <button class="toggle-btn active" data-form="login">Sign in</button>
            <button class="toggle-btn" data-form="register">Register</button>
        </div>

        <div class="form-content">
            <form id="login-form" action="login_register.php" method="POST">
                <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                <button type="submit" name="login" class="btn-primary">Sign In</button>
               <div class="form-footer">
               <a href="forgot_password.php">Have you forgotten your password?</a>
               </div>
            </form>
            <form id="register-form" action="login_register.php" method="POST">
                <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" required></div>
                <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
                <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
                <button type="submit" name="register" class="btn-primary">Register</button>
            </form>
        </div>
    </div>
    <script src="assets/js/main.js"></script>
</body>
</html>