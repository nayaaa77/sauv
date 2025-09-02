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
    $full_name = $_POST['display_name'] ?? '';

    $stmt = $conn->prepare("UPDATE users SET full_name = ? WHERE id = ?");
    $stmt->bind_param("si", $full_name, $user_id);
    if ($stmt->execute()) {
        $_SESSION['user_name'] = $full_name;
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

// --- LOGIKA UNTUK UPDATE ADDRESS ---
if ($page === 'addresses' && isset($_POST['save_address'])) {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address_line1 = $_POST['address_line1'] ?? '';
    $city = $_POST['city'] ?? '';
    $sub_district = $_POST['sub_district'] ?? '';
    $province = $_POST['province'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $country = 'Indonesia'; // Nilai default
    $house_number = null; // Set house_number ke NULL
    
    $stmt_check = $conn->prepare("SELECT id FROM addresses WHERE user_id = ?");
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $stmt_check->close();

    if ($result_check->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE addresses SET first_name=?, last_name=?, address_line1=?, house_number=?, city=?, sub_district=?, province=?, postal_code=?, country=?, phone=? WHERE user_id=?");
        $stmt->bind_param("ssssssssssi", $first_name, $last_name, $address_line1, $house_number, $city, $sub_district, $province, $postal_code, $country, $phone, $user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO addresses (user_id, first_name, last_name, address_line1, house_number, city, sub_district, province, postal_code, country, phone) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssssssss", $user_id, $first_name, $last_name, $address_line1, $house_number, $city, $sub_district, $province, $postal_code, $country, $phone);
    }
    
    if ($stmt->execute()) {
        $notification = "Address saved successfully.";
    } else {
        $notification = "Failed to save address: " . $stmt->error;
        $notification_type = 'error';
    }
    $stmt->close();
}


// --- PENGAMBILAN DATA ---
$orders = [];
$address = null;
$account_details = null;

$stmt_user = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$account_details = $stmt_user->get_result()->fetch_assoc();
$stmt_user->close();

$stmt_addr = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt_addr->bind_param("i", $user_id);
$stmt_addr->execute();
$address = $stmt_addr->get_result()->fetch_assoc();
$stmt_addr->close();

if ($page === 'orders') {
    $stmt_orders = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
    $stmt_orders->bind_param("i", $user_id);
    $stmt_orders->execute();
    $orders = $stmt_orders->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt_orders->close();
}

$conn->close();
?>
<?php include 'includes/header.php'; ?>

<div class="container account-container">
    <nav class="account-nav">
        <a href="my_account.php?page=orders" class="<?php echo ($page === 'orders') ? 'active' : ''; ?>">Orders</a>
        <a href="my_account.php?page=addresses" class="<?php echo ($page === 'addresses') ? 'active' : ''; ?>">Addresses</a>
        <a href="my_account.php?page=details" class="<?php echo ($page === 'details') ? 'active' : ''; ?>">Account details</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="account-content">
        <?php if (!empty($notification)): ?>
            <div class="notification-box <?php echo $notification_type; ?>"><?php echo $notification; ?></div>
        <?php endif; ?>

        <?php if ($page === 'orders'): ?>
            <h2>My Orders</h2>
            <?php if (empty($orders)): ?>
                <p>Any orders you place will appear here.</p>
            <?php else: ?>
                <table style="width:100%; border-collapse: collapse; font-size: 14px;">
                    <thead>
                        <tr style="background-color:#f9f9f9;">
                            <th style="text-align:left; padding:12px; border-bottom:1px solid #ddd;">Order ID</th>
                            <th style="text-align:left; padding:12px; border-bottom:1px solid #ddd;">Date</th>
                            <th style="text-align:left; padding:12px; border-bottom:1px solid #ddd;">Status</th>
                            <th style="text-align:right; padding:12px; border-bottom:1px solid #ddd;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #eee;">#<?php echo $order['id']; ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                            <td style="text-align:right; padding:12px; border-bottom:1px solid #eee;">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        <?php elseif ($page === 'addresses'): ?>
            <h2>Shipping Address</h2>
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
                    <label for="address_line1">Full Address</label>
                    <input type="text" id="address_line1" name="address_line1"  value="<?php echo htmlspecialchars($address['address_line1'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="province">State / Province <span class="label-translation">(Provinsi)</span></label>
                    <input type="text" id="province" name="province" value="<?php echo htmlspecialchars($address['province'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="city">City / Town <span class="label-translation">(Kota/Kabupaten)</span></label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($address['city'] ?? ''); ?>" required>
                </div>
                <div class="form-group">
                    <label for="sub_district">Sub-District <span class="label-translation">(Kecamatan)</span></label>
                    <input type="text" id="sub_district" name="sub_district" value="<?php echo htmlspecialchars($address['sub_district'] ?? ''); ?>" required>
                </div>
                 <div class="form-group">
                    <label for="postal_code">Postal Code <span class="label-translation">(Kode Pos)</span></label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo htmlspecialchars($address['postal_code'] ?? ''); ?>" required>
                </div>
                 <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($address['phone'] ?? ''); ?>">
                </div>
                
                <button type="submit" name="save_address" class="btn btn-save">Save Address</button>
            </form>

        <?php elseif ($page === 'details'): ?>
            <h2>Account details</h2>
            <div class="details-layout">
                <div class="details-forms">
                    <form class="details-form" action="my_account.php?page=details" method="POST">
                        <div class="form-group">
                            <label for="display_name">Display name *</label>
                            <input type="text" id="display_name" name="display_name" value="<?php echo htmlspecialchars($account_details['full_name'] ?? ''); ?>" required>
                            <p class="field-hint">This will be how your name will be displayed in the account section and header.</p>
                        </div>
                        <button type="submit" name="save_details" class="btn btn-save">Save Changes</button>
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
                        <button type="submit" name="save_password" class="btn btn-save">Save Changes</button>
                    </form>
                </div>

                <aside class="address-display-sidebar">
                    <h3>Shipping Address</h3>
                    <?php if ($address): ?>
                        <p>
                            <?php echo htmlspecialchars($address['first_name'] . ' ' . $address['last_name']); ?><br>
                            <?php echo htmlspecialchars($address['address_line1']); ?><br>
                            <?php echo htmlspecialchars($address['sub_district'] . ', ' . $address['city']); ?><br>
                            <?php echo htmlspecialchars($address['province']); ?><br>
                            <?php if(!empty($address['postal_code'])) echo htmlspecialchars($address['postal_code']) . '<br>'; ?>
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