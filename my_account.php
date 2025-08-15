<?php
// Selalu mulai session di awal
session_start();
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';

// --- KEAMANAN HALAMAN ---
if (!is_logged_in()) {
    header('Location: login_register.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$page = $_GET['page'] ?? 'orders'; // Halaman default adalah 'orders'
$notification = '';
$notification_type = 'success';

// --- LOGIKA UNTUK UPDATE ACCOUNT DETAILS ---
if ($page === 'details' && isset($_POST['save_details'])) {
    // Langsung ambil display_name sebagai full_name
    $full_name = $_POST['display_name'] ?? '';

    $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
    $stmt->bind_param("si", $full_name, $user_id);
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $full_name; // Ini penting untuk update header
        $notification = "Account details saved successfully.";
    } else {
        $notification = "Failed to save details.";
        $notification_type = 'error';
    }
    $stmt->close();
}

// --- LOGIKA UNTUK UPDATE PASSWORD ---
if ($page === 'details' && isset($_POST['save_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($new_password)) {
        $stmt_pass = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_pass->bind_param("i", $user_id);
        $stmt_pass->execute();
        $user = $stmt_pass->get_result()->fetch_assoc();
        $stmt_pass->close();

        if (password_verify($current_password, $user['password'])) {
            if ($new_password === $confirm_password) {
                $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt_update->bind_param("si", $new_hashed_password, $user_id);
                if ($stmt_update->execute()) {
                    $notification = "Password changed successfully.";
                } else {
                    $notification = "Failed to change password.";
                    $notification_type = 'error';
                }
                $stmt_update->close();
            } else {
                $notification = "New password and confirmation do not match.";
                $notification_type = 'error';
            }
        } else {
            $notification = "Incorrect current password.";
            $notification_type = 'error';
        }
    }
}

// --- [DITAMBAHKAN] LOGIKA UNTUK UPDATE ADDRESS ---
if ($page === 'addresses' && isset($_POST['save_address'])) {
    // Ambil semua data dari form
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address_line1 = $_POST['address'] ?? '';
    $house_number = $_POST['house_number'] ?? '';
    $province = $_POST['province'] ?? '';
    $city = $_POST['city'] ?? '';
    $sub_district = $_POST['sub_district'] ?? '';
    
    // Cek apakah user sudah punya alamat (UPDATE) atau belum (INSERT)
    $stmt_check = $conn->prepare("SELECT id FROM addresses WHERE user_id = ?");
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $stmt_check->close();

    if ($result_check->num_rows > 0) {
        // User sudah punya alamat -> UPDATE
        $stmt = $conn->prepare("UPDATE addresses SET first_name=?, last_name=?, address_line1=?, house_number=?, province=?, city=?, sub_district=? WHERE user_id=?");
        $stmt->bind_param("sssssssi", $first_name, $last_name, $address_line1, $house_number, $province, $city, $sub_district, $user_id);
    } else {
        // User belum punya alamat -> INSERT
        $stmt = $conn->prepare("INSERT INTO addresses (user_id, first_name, last_name, address_line1, house_number, province, city, sub_district) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $user_id, $first_name, $last_name, $address_line1, $house_number, $province, $city, $sub_district);
    }
    
    if ($stmt->execute()) {
        $notification = "Address saved successfully.";
    } else {
        $notification = "Failed to save address.";
        $notification_type = 'error';
    }
    $stmt->close();
}


// --- PENGAMBILAN DATA ---
$orders = [];
$address = [];
$account_details = [];

if ($page === 'orders') {
    // ... Logika pengambilan data orders Anda ...
} elseif ($page === 'addresses') {
    // [DIPERBAIKI] Ambil data alamat untuk ditampilkan di form
    $stmt_addr = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
    $stmt_addr->bind_param("i", $user_id);
    $stmt_addr->execute();
    $address = $stmt_addr->get_result()->fetch_assoc(); // $address akan berisi data atau null
    $stmt_addr->close();
} elseif ($page === 'details') {
    // Ambil detail akun (nama, email)
    $stmt_user = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $account_details = $stmt_user->get_result()->fetch_assoc();
    $stmt_user->close();

    // Ambil juga data alamat untuk ditampilkan di sidebar
    $stmt_addr = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
    $stmt_addr->bind_param("i", $user_id);
    $stmt_addr->execute();
    $address = $stmt_addr->get_result()->fetch_assoc();
    $stmt_addr->close();
}

$conn->close();
?>
<?php include 'includes/header.php'; ?>

<style>
    .account-container { padding-top: 40px; padding-bottom: 80px; }
    .account-nav { display: flex; gap: 40px; border-bottom: 1px solid #e5e5e5; margin-bottom: 40px; }
    .account-nav a { padding: 15px 0; text-decoration: none; color: #555; font-size: 14px; letter-spacing: 0.1em; text-transform: uppercase; position: relative; border-bottom: 2px solid transparent; transition: color 0.3s, border-color 0.3s; }
    .account-nav a.active, .account-nav a:hover { color: #111; font-weight: 500; border-bottom-color: #111; }
    .account-content h2 { font-size: 22px; font-weight: 400; margin-bottom: 30px; letter-spacing: 0.05em; }
    
    /* [PERUBAHAN] Layout untuk halaman details */
    .details-layout {
        display: flex;
        gap: 60px; /* Jarak antara form dan alamat */
    }
    .details-forms {
        flex: 2; /* Form mengambil 2/3 bagian */
    }
    .address-display-sidebar {
        flex: 1; /* Alamat mengambil 1/3 bagian */
        padding-left: 40px;
        border-left: 1px solid #e5e5e5;
    }

    /* Style untuk form details & addresses */
    .details-form { max-width: 650px; }
    .details-form .form-group { margin-bottom: 25px; }
    .details-form label { display: block; font-size: 13px; margin-bottom: 8px; color: #555; }
    .details-form input { width: 100%; padding: 12px; border: 1px solid #e5e5e5; box-sizing: border-box; font-size: 14px; }
    .details-form .field-hint { font-size: 12px; color: #888; margin-top: 5px; }
    .details-form .password-change-header { font-size: 18px; font-weight: 500; margin-top: 40px; margin-bottom: 20px; }
    .details-form .btn-save { display: inline-block; padding: 14px 40px; background: #111; color: #fff; border: none; cursor: pointer; text-transform: uppercase; font-size: 13px; letter-spacing: 0.1em; margin-top: 10px; }
    
    /* Style untuk Address Display di sidebar */
    .address-display-sidebar h3 { font-size: 18px; font-weight: 500; }
    .address-display-sidebar p { color: #555; line-height: 1.7; }
    .address-display-sidebar a { color: #111; text-decoration: underline; font-size: 13px; }

    .notification-box { padding: 15px; margin-bottom: 20px; border-radius: 4px; text-align: center; font-weight: 500; }
    .notification-box.success { background-color: #d4edda; color: #155724; }
    .notification-box.error { background-color: #f8d7da; color: #721c24; }
</style>

<div class="container account-container">
    <nav class="account-nav">
        <a href="my_account.php?page=orders" class="<?php if($page === 'orders') echo 'active'; ?>">Orders</a>
        <a href="my_account.php?page=addresses" class="<?php if($page === 'addresses') echo 'active'; ?>">Addresses</a>
        <a href="my_account.php?page=details" class="<?php if($page === 'details') echo 'active'; ?>">Account details</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="account-content">
        <?php if ($page === 'orders'): ?>
            <p>Anda belum memiliki pesanan.</p>

        <?php elseif ($page === 'addresses'): ?>
            <h2>Shipping Address</h2>
    
            <?php if(!empty($notification)): ?>
                <div class="notification-box <?php echo $notification_type; ?>"><?php echo $notification; ?></div>
            <?php endif; ?>

            <form class="details-form" action="my_account.php?page=addresses" method="POST">
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex: 1;">
                        <label for="first_name">First name *</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($address['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="last_name">Last name *</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($address['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address['address_line1'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="house_number">House Number</label>
                    <input type="text" id="house_number" name="house_number" value="<?php echo htmlspecialchars($address['house_number'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="province">State / Province</label>
                    <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($address['province'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="city">City / Town</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="sub_district">Sub-District *</label>
                    <input type="text" id="sub_district" name="sub_district" value="<?php echo htmlspecialchars($address['sub_district'] ?? ''); ?>" required>
                </div>
                
                <button type="submit" name="save_address" class="btn-save">Save Address</button>
            </form>

        <?php elseif ($page === 'details'): ?>
            <h2>Account details</h2>

            <div class="details-layout">
                <div class="details-forms">
                    <?php if(!empty($notification)): ?>
                        <div class="notification-box <?php echo $notification_type; ?>"><?php echo $notification; ?></div>
                    <?php endif; ?>

                    <form class="details-form" action="my_account.php?page=details" method="POST">
                        <div class="form-group">
                            <label for="display_name">Display name *</label>
                            <input type="text" id="display_name" name="display_name" value="<?php echo htmlspecialchars($account_details['full_name'] ?? ''); ?>" required>
                            <p class="field-hint">This will be how your name will be displayed in the account section and header.</p>
                        </div>
                        <button type="submit" name="save_details" class="btn-save">Save Changes</button>
                    </form>

                    <form class="details-form" action="my_account.php?page=details" method="POST">
                        <h3 class="password-change-header">Password change</h3>
                        <div class="form-group">
                            <label for="current_password">Current password (leave blank to leave unchanged)</label>
                            <input type="password" id="current_password" name="current_password">
                        </div>
                        <div class="form-group">
                            <label for="new_password">New password (leave blank to leave unchanged)</label>
                            <input type="password" id="new_password" name="new_password">
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm new password</label>
                            <input type="password" id="confirm_password" name="confirm_password">
                        </div>
                        <button type="submit" name="save_password" class="btn-save">Save Changes</button>
                    </form>
                </div>

                <aside class="address-display-sidebar">
                    <h3>Shipping Address</h3>
                    <?php if (!empty($address)): ?>
                        <p>
                            <?php echo htmlspecialchars($address['first_name'] . ' ' . $address['last_name']); ?><br>
                            <?php echo htmlspecialchars($address['address_line1']); ?><br>
                            <?php if(!empty($address['house_number'])) echo htmlspecialchars($address['house_number']) . '<br>'; ?>
                            <?php echo htmlspecialchars($address['sub_district'] . ', ' . $address['city']); ?><br>
                            <?php echo htmlspecialchars($address['province'] . ' ' . ($address['postal_code'] ?? '')); ?><br>
                            <?php if(!empty($address['phone'])) echo htmlspecialchars($address['phone']); ?>
                        </p>
                        <a href="my_account.php?page=addresses">Edit Address</a>
                    <?php else: ?>
                        <p>You have not set up a shipping address yet.</p>
                        <a href="my_account.php?page=addresses">Add Address</a>
                    <?php endif; ?>
                </aside>
            </div>

        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>