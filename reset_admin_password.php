<?php
require_once 'includes/db_conn.php';

// --- KONFIGURASI ---
$admin_email = 'admin@sauvatte.com';
$new_plain_password = 'admin123';
// -------------------

echo "<h1>Admin Password Reset Script</h1>";

// 1. Buat hash baru dari password yang kita tentukan
$new_hashed_password = password_hash($new_plain_password, PASSWORD_BCRYPT);

echo "Password baru: " . htmlspecialchars($new_plain_password) . "<br>";
echo "Hash baru yang akan disimpan: " . htmlspecialchars($new_hashed_password) . "<br><br>";

// 2. Siapkan dan jalankan query UPDATE
$stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ? AND role = 'admin'");
$stmt->bind_param("ss", $new_hashed_password, $admin_email);

if ($stmt->execute()) {
    echo "<h2>BERHASIL!</h2>";
    echo "<p>Password untuk admin <strong>" . htmlspecialchars($admin_email) . "</strong> telah berhasil direset.</p>";
    echo "<p><strong>LANGKAH SELANJUTNYA:</strong></p>";
    echo "<ol><li>Hapus file ini (reset_admin_password.php) dari server Anda.</li><li>Coba login kembali.</li></ol>";
} else {
    echo "<h2>GAGAL!</h2>";
    echo "<p>Gagal mengupdate password di database. Error: " . $stmt->error . "</p>";
}

$stmt->close();
$conn->close();
?>
