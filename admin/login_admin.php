<?php
session_start();
require_once '../includes/db_conn.php';

if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
    header('Location: index.php');
    exit();
}

$error = '';
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password_input = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password, role FROM users WHERE email = ? AND role = 'admin'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password_input, $admin['password'])) {
            $_SESSION['user_id'] = $admin['id'];
            $_SESSION['user_name'] = $admin['full_name'];
            $_SESSION['user_role'] = $admin['role'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Email atau Password salah.';
        }
    } else {
        $error = 'Email atau Password salah.';
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Sauvatte</title>
    <link rel="stylesheet" href="assets/css/admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">
    <div class="login-wrapper">
        <div class="login-branding">
            <div class="branding-content">
                <h2>Sauvette</h2>
                <p>Content Management System</p>
            </div>
        </div>
        <div class="login-form-wrapper">
            <div class="login-form-container">
                <h3>Admin Sign In</h3>
                <p class="subtitle">Welcome back! Please enter your details.</p>
                <?php if(!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>
                <form action="login_admin.php" method="POST">
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" name="login" class="btn-login">Sign In</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>