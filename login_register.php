<?php
// 1. Mulai session & koneksi DB paling pertama
session_start(); 
include 'includes/db_conn.php';

// Cek apakah user sudah login
function check_login_status() {
    return isset($_SESSION['user_id']);
}

// Jika sudah login, lempar langsung
if (check_login_status()) {
    header('Location: index.php');
    exit();
}

$error_msg = '';
$success_msg = '';

// --- PROSES LOGIN (Ditaruh di atas sebelum Header HTML) ---
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, full_name, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            
            // --- BAGIAN INI YANG DIPERBAIKI ---
            // Default ke index.php sesuai permintaanmu
            $redirect_url = 'index.php'; 
            
            // Kalau ada keranjang, prioritaskan ke checkout
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                $redirect_url = 'checkout.php';
            }
            
            header("Location: $redirect_url");
            exit();
        } else {
            $error_msg = "Password salah.";
        }
    } else {
        $error_msg = "Email tidak ditemukan.";
    }
    $stmt->close();
}

// --- PROSES REGISTER (Ditaruh di atas juga) ---
if (isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_msg = "Password konfirmasi tidak cocok.";
    } else {
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error_msg = "Email sudah terdaftar.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $full_name, $email, $hashed_password);

            if ($stmt->execute()) {
                $success_msg = "Registrasi berhasil! Silakan login.";
            } else {
                $error_msg = "Gagal mendaftar. Coba lagi.";
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}

// --- BARU LOAD TAMPILAN (HEADER) SETELAH LOGIC SELESAI ---
// Catatan: Pastikan di dalam includes/header.php TIDAK ADA session_start() lagi
// Atau gunakan @session_start() di sana untuk mencegah error double session.
include 'includes/header.php'; 
?>

<div class="container auth-page-container">
    <div class="auth-card">
        <div class="auth-tabs">
            <button class="toggle-btn active" data-target="login-form">Sign In</button>
            <button class="toggle-btn" data-target="register-form">Register</button>
        </div>

        <?php if ($error_msg): ?>
            <div class="notification error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <?php if ($success_msg): ?>
            <div class="notification success"><?php echo $success_msg; ?></div>
        <?php endif; ?>

        <div id="login-form" class="auth-form active">
            <div class="form-header">
                <h2>Welcome Back</h2>
                <p>Sign in to continue</p>
            </div>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="******" required>
                    </div>
                </div>
                 <div class="form-actions">
                    <a href="forgot_password.php" class="forgot-link">Forget Password?</a>
                </div>
                <button type="submit" name="login" class="btn btn-auth">Sign In</button>
            </form>
        </div>

        <div id="register-form" class="auth-form">
             <div class="form-header">
                <h2>Create Account</h2>
                <p>Elevate your shopping experience</p>
            </div>
            <form action="" method="POST">
                <div class="form-group">
                    <label>Full Name</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="full_name" placeholder="Enter your full name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="example@email.com" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Create your password" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="confirm_password" placeholder="Re-enter your password" required>
                    </div>
                </div>
                <button type="submit" name="register" class="btn btn-auth">REGISTER</button>
            </form>
        </div>
    </div>
</div>

<script>
// Script JS kamu tetap sama
document.addEventListener('DOMContentLoaded', function() {
    const btns = document.querySelectorAll('.toggle-btn');
    const forms = document.querySelectorAll('.auth-form');

    btns.forEach(btn => {
        btn.addEventListener('click', () => {
            btns.forEach(b => b.classList.remove('active'));
            forms.forEach(f => f.classList.remove('active'));

            btn.classList.add('active');
            document.getElementById(btn.dataset.target).classList.add('active');
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>