<?php
// Selalu mulai session di awal
session_start();
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';
require_once 'config.php'; 

// --- KEAMANAN HALAMAN ---
if (!is_logged_in()) {
    header('Location: login_register.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$page = $_GET['page'] ?? 'orders'; // Halaman default adalah 'orders'
$notification = '';
$notification_type = 'success';

// --- LOGIKA UPDATE DATA ---
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

if ($page === 'addresses' && isset($_POST['save_address'])) {
    $province = $_POST['province_text'] ?? ''; 
    $city = $_POST['city_text'] ?? '';
    $sub_district = $_POST['sub_district_text'] ?? '';
    
    $province_id = $_POST['province_select'] ?? null;
    $city_id = $_POST['city_select'] ?? null;
    $district_id = $_POST['district_select'] ?? null;
    
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address_line1 = $_POST['address_line1'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $country = 'Indonesia';
    $house_number = null;
    
    $stmt_check = $conn->prepare("SELECT id FROM addresses WHERE user_id = ?");
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $existing_address = $result->fetch_assoc();
    $stmt_check->close();

    if ($existing_address) {
        $stmt = $conn->prepare("UPDATE addresses SET 
            first_name = ?, last_name = ?, address_line1 = ?, house_number = ?,
            province = ?, city = ?, sub_district = ?, postal_code = ?, phone = ?, country = ?,
            province_id = ?, city_id = ?, district_id = ?
            WHERE user_id = ?");
        
        $stmt->bind_param("ssssssssssiiii", 
            $first_name, $last_name, $address_line1, $house_number,
            $province, $city, $sub_district, $postal_code, $phone, $country,
            $province_id, $city_id, $district_id,
            $user_id
        );
    } else {
        $stmt = $conn->prepare("INSERT INTO addresses 
            (user_id, first_name, last_name, address_line1, house_number,
            province, city, sub_district, postal_code, phone, country,
            province_id, city_id, district_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); 
        
        $stmt->bind_param("issssssssssiii", 
            $user_id, $first_name, $last_name, $address_line1, $house_number,
            $province, $city, $sub_district, $postal_code, $phone, $country,
            $province_id, $city_id, $district_id
        );
    }
    
    if ($stmt->execute()) {
        $notification = "Address saved successfully.";
    } else {
        $notification = "Failed to save address: " . $stmt->error;
        $notification_type = 'error';
    }
    $stmt->close();
}

// --- AMBIL DATA ---
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

// --- PERBAIKAN: JANGAN TUTUP KONEKSI DISINI ---
// $conn->close();  <-- Baris ini dihapus agar header.php bisa pakai $conn
?>

<?php include 'includes/header.php'; ?>

<div class="container account-container">
    
    <nav class="account-nav desktop-nav">
        <a href="my_account.php?page=orders" class="<?php echo ($page === 'orders') ? 'active' : ''; ?>">Orders</a>
        <a href="my_account.php?page=addresses" class="<?php echo ($page === 'addresses') ? 'active' : ''; ?>">Addresses</a>
        <a href="my_account.php?page=details" class="<?php echo ($page === 'details') ? 'active' : ''; ?>">Account details</a>
        <a href="logout.php">Logout</a>
    </nav>

    <div class="mobile-nav-grid">
        <a href="my_account.php?page=orders" class="account-card-link <?php echo ($page === 'orders') ? 'active' : ''; ?>">
            <div class="icon-wrapper gold"><i class="fas fa-shopping-bag"></i></div>
            <span class="menu-title">My Orders</span>
        </a>
        <a href="my_account.php?page=addresses" class="account-card-link <?php echo ($page === 'addresses') ? 'active' : ''; ?>">
            <div class="icon-wrapper blue"><i class="fas fa-map-marker-alt"></i></div>
            <span class="menu-title">Addresses</span>
        </a>
        <a href="my_account.php?page=details" class="account-card-link <?php echo ($page === 'details') ? 'active' : ''; ?>">
            <div class="icon-wrapper green"><i class="fas fa-user-cog"></i></div>
            <span class="menu-title">Details</span>
        </a>
        <a href="logout.php" class="account-card-link">
            <div class="icon-wrapper red"><i class="fas fa-sign-out-alt"></i></div>
            <span class="menu-title">Logout</span>
        </a>
    </div>
    <div class="account-content">
        <?php if (!empty($notification)): ?>
            <div class="notification-box <?php echo $notification_type; ?>"><?php echo $notification; ?></div>
        <?php endif; ?>

        <?php if ($page === 'orders'): ?>
            <h2>My Orders</h2>
            <?php if (empty($orders)): ?>
                <p>Any orders you place will appear here.</p>
            <?php else: ?>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ORDER</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                            <th>RESI</th>
                            <th>TOTAL</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                            <td class="tracking-number">
                                <?php if (!empty($order['resi_number'])): ?>
                                    <a href="https://www.jne.co.id/id/tracking/trace?q=<?php echo htmlspecialchars($order['resi_number']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($order['resi_number']); ?>
                                    </a>
                                <?php else: ?> - <?php endif; ?>
                            </td>
                            <td>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                            <td><a href="order_detail.php?id=<?php echo $order['id']; ?>" class="btn-view">View</a></td>
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
                        <label>First name *</label>
                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($address['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Last name *</label>
                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($address['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Full Address</label>
                    <input type="text" name="address_line1"  value="<?php echo htmlspecialchars($address['address_line1'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>State / Province</label>
                    <select id="province_select" name="province_select" class="select2-dynamic" required></select>
                    <input type="hidden" id="province_text" name="province_text" value="">
                </div>
                <div class="form-group">
                    <label>City / Town</label>
                    <select id="city_select" name="city_select" class="select2-dynamic" required disabled></select>
                    <input type="hidden" id="city_text" name="city_text" value="">
                </div>
                <div class="form-group">
                    <label>Sub-District</label>
                    <select id="district_select" name="district_select" class="select2-dynamic" required disabled></select>
                    <input type="hidden" id="sub_district_text" name="sub_district_text" value="">
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" value="<?php echo htmlspecialchars($address['postal_code'] ?? ''); ?>" required>
                 </div>
                 <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($address['phone'] ?? ''); ?>">
                 </div>
                
                <button type="submit" name="save_address" class="btn btn-save">Save Address</button>
            </form>

        <?php elseif ($page === 'details'): ?>
            <h2>Account details</h2>
            <div class="details-layout">
                <div class="details-forms">
                    <form class="details-form" action="my_account.php?page=details" method="POST">
                        <div class="form-group">
                            <label>Display name *</label>
                            <input type="text" name="display_name" value="<?php echo htmlspecialchars($account_details['full_name'] ?? ''); ?>" required>
                        </div>
                        <button type="submit" name="save_details" class="btn btn-save">Save Changes</button>
                    </form>

                    <form class="details-form" action="my_account.php?page=details" method="POST">
                        <h3 class="password-change-header">Password change</h3>
                        <div class="form-group">
                            <label>Current password</label>
                            <input type="password" name="current_password">
                        </div>
                        <div class="form-group">
                            <label>New password</label>
                            <input type="password" name="new_password">
                        </div>
                        <div class="form-group">
                            <label>Confirm new password</label>
                            <input type="password" name="confirm_password">
                        </div>
                        <button type="submit" name="save_password" class="btn btn-save">Save Changes</button>
                    </form>
                </div>

                <aside class="address-display-sidebar">
                    <h3>Current Address</h3>
                    <?php if ($address): ?>
                        <p>
                            <?php echo htmlspecialchars($address['first_name'] . ' ' . $address['last_name']); ?><br>
                            <?php echo htmlspecialchars($address['address_line1']); ?><br>
                            <?php echo htmlspecialchars($address['sub_district'] . ', ' . $address['city']); ?><br>
                            <?php echo htmlspecialchars($address['province']); ?><br>
                        </p>
                    <?php else: ?>
                        <p>No address set.</p>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    if ($('#province_select').length) { 
        $('#province_select, #city_select, #district_select').select2({ width: '100%' });

        const savedProvinceId = <?php echo json_encode($address['province_id'] ?? null); ?>;
        const savedCityId = <?php echo json_encode($address['city_id'] ?? null); ?>;
        const savedDistrictId = <?php echo json_encode($address['district_id'] ?? null); ?>;

        function loadLocalProvinces() {
            const province_select = $('#province_select');
            province_select.append('<option value="">Pilih Provinsi</option>');
            
            if (typeof localProvinces !== 'undefined' && Array.isArray(localProvinces)) {
                $.each(localProvinces, function(key, val) {
                    province_select.append('<option value="' + val.id + '" data-name="' + val.name + '">' + val.name + '</option>');
                });
            }
            if (savedProvinceId) {
                province_select.val(savedProvinceId).trigger('change'); 
            }
        }
        loadLocalProvinces();

        $('#province_select').change(function() {
            const province_id = $(this).val();
            const province_name = $(this).find('option:selected').data('name');
            $('#province_text').val(province_name || ''); 
            
            $('#city_select').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change');
            $('#district_select').prop('disabled', true).html('<option value="">Pilih Kecamatan</option>').trigger('change');
            
            if (province_id) {
                $.getJSON('api/get_location.php?type=city&id=' + province_id, function(data) {
                    const city_select = $('#city_select');
                    city_select.html('<option value="">Pilih Kota/Kabupaten</option>');
                    if (data && Array.isArray(data)) {
                        $.each(data, function(key, val) {
                            let valId = val.id || val.city_id;
                            let valName = val.name || val.city_name;
                            if(valId && valName) city_select.append('<option value="' + valId + '" data-name="' + valName + '">' + valName + '</option>');
                        });
                    }
                    city_select.prop('disabled', false);
                    if (savedCityId && province_id == savedProvinceId) city_select.val(savedCityId);
                    city_select.trigger('change');
                });
            }
        });

        $('#city_select').change(function() {
            const city_id = $(this).val();
            const city_name = $(this).find('option:selected').data('name');
            $('#city_text').val(city_name || ''); 
            $('#district_select').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change');

            if (city_id) {
                $.getJSON('api/get_location.php?type=district&id=' + city_id, function(data) {
                    const district_select = $('#district_select');
                    district_select.html('<option value="">Pilih Kecamatan</option>');
                     if (data && Array.isArray(data)) {
                        $.each(data, function(key, val) {
                            let valId = val.id || val.district_id || val.subdistrict_id;
                            let valName = val.name || val.district_name || val.subdistrict_name;
                            if(valId && valName) district_select.append('<option value="' + valId + '" data-name="' + valName + '">' + valName + '</option>');
                        });
                    }
                    district_select.prop('disabled', false);
                    if (savedDistrictId && city_id == savedCityId) district_select.val(savedDistrictId);
                    district_select.trigger('change'); 
                });
            }
        });
        
        $('#district_select').change(function() {
            const district_name = $(this).find('option:selected').data('name');
            $('#sub_district_text').val(district_name || ''); 
        });
    }
});
</script>
<?php include 'includes/footer.php'; ?>