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
            $error = 'Incorrect email or password.';
        }
    } else {
        $error = 'Incorrect email or password.';
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
</head>
<body class="login-body">
    <div class="login-container">
        <div class="login-form-panel">
            <div class="login-form-content">
                <h3>The Ateiler Awaits!</h3>
                <p class="subtitle">Sauvatte Content Management</p> <?php if(!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="login_admin.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn-login">Sign In</button>
                </form>
            </div>
        </div>
        <div class="login-image-panel">
            <img src="../assets/img/sauvatte-logo.png" alt="Sauvatte Logo" class="login-logo-right">
        </div>
    </div>
</body>
</html>