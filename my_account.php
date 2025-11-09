<?php
// Selalu mulai session di awal
session_start();
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';
require_once 'config.php'; // ⚠️ DITAMBAHKAN: Memuat API Key

// --- KEAMANAN HALAMAN ---
if (!is_logged_in()) {
    header('Location: login_register.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$page = $_GET['page'] ?? 'orders'; // Halaman default adalah 'orders'
$notification = '';
$notification_type = 'success';

// --- LOGIKA UNTUK UPDATE ACCOUNT DETAILS (KODE ASLI ANDA - TIDAK DIUBAH) ---
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

// --- LOGIKA UNTUK UPDATE PASSWORD (KODE ASLI ANDA - TIDAK DIUBAH) ---
if ($page === 'details' && isset($_POST['save_password'])) {
    // ... (Logika ganti password Anda tetap di sini) ...
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
    
    // Ambil data teks dari dropdown (untuk disimpan di kolom lama)
    $province = $_POST['province_text'] ?? ''; 
    $city = $_POST['city_text'] ?? '';
    $sub_district = $_POST['sub_district_text'] ?? '';
    
    // Ambil ID dari dropdown (untuk disimpan di kolom BARU)
    $province_id = $_POST['province_select'] ?? null;
    $city_id = $_POST['city_select'] ?? null;
    $district_id = $_POST['district_select'] ?? null;
    
    // Ambil data teks lainnya
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $address_line1 = $_POST['address_line1'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $country = 'Indonesia'; // Nilai default dari kode lama
    $house_number = null; // Nilai default dari kode lama
    
    // Cek apakah user sudah punya alamat (UPDATE) atau belum (INSERT)
    $stmt_check = $conn->prepare("SELECT id FROM addresses WHERE user_id = ?");
    $stmt_check->bind_param("i", $user_id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $existing_address = $result->fetch_assoc();
    $stmt_check->close();

    if ($existing_address) {
        // --- UPDATE ALAMAT ---
        $stmt = $conn->prepare("UPDATE addresses SET 
            first_name = ?, last_name = ?, address_line1 = ?, house_number = ?,
            province = ?, city = ?, sub_district = ?, postal_code = ?, phone = ?, country = ?,
            province_id = ?, city_id = ?, district_id = ?
            WHERE user_id = ?");
        
        // ⚠️ PERBAIKAN: 14 Tipe ("ssssssssssiiii") dan 14 Variabel
        $stmt->bind_param("ssssssssssiiii", 
            $first_name, $last_name, $address_line1, $house_number,
            $province, $city, $sub_district, $postal_code, $phone, $country,
            $province_id, $city_id, $district_id,
            $user_id
        );
    } else {
        // --- INSERT ALAMAT BARU ---
        $stmt = $conn->prepare("INSERT INTO addresses 
            (user_id, first_name, last_name, address_line1, house_number,
            province, city, sub_district, postal_code, phone, country,
            province_id, city_id, district_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"); // 14 placeholders
        
        // ⚠️ PERBAIKAN: 14 Tipe ("issssssssssiii") dan 14 Variabel
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


// --- PENGAMBILAN DATA (KODE ASLI ANDA - TIDAK DIUBAH) ---
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
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ORDER NUMBER</th>
                            <th>DATE</th>
                            <th>STATUS</th>
                            <th>TRACKING NUMBER</th>
                            <th>TOTAL</th>
                            <th>ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td><?php echo date('F j, Y', strtotime($order['order_date'])); ?></td>
                            <td><?php echo htmlspecialchars(ucfirst($order['status'])); ?></td>
                            <td class="tracking-number">
                                <?php if (!empty($order['resi_number'])): ?>
                                    <a href="https://www.jne.co.id/id/tracking/trace?q=<?php echo htmlspecialchars($order['resi_number']); ?>" target="_blank">
                                        <?php echo htmlspecialchars($order['resi_number']); ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
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
                    <label for="province_select">State / Province <span class="label-translation">(Provinsi)</span></label>
                    <select id="province_select" name="province_select" required></select>
                    <input type="hidden" id="province_text" name="province_text" value="">
                </div>
                <div class="form-group">
                    <label for="city_select">City / Town <span class="label-translation">(Kota/Kabupaten)</span></label>
                    <select id="city_select" name="city_select" required disabled></select>
                    <input type="hidden" id="city_text" name="city_text" value="">
                </div>
                <div class="form-group">
                    <label for="district_select">Sub-District <span class="label-translation">(Kecamatan)</span></label>
                    <select id="district_select" name="district_select" required disabled></select>
                    <input type="hidden" id="sub_district_text" name="sub_district_text" value="">
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

<script>
$(document).ready(function() {
    // Hanya jalankan skrip ini jika kita berada di halaman 'addresses'
    // dan elemen dropdown-nya ada
    if ($('#province_select').length) { 
    
        // Simpan ID yang tersimpan dari PHP (Gunakan variabel $address dari kode Anda)
        const savedProvinceId = <?php echo json_encode($address['province_id'] ?? null); ?>;
        const savedCityId = <?php echo json_encode($address['city_id'] ?? null); ?>;
        const savedDistrictId = <?php echo json_encode($address['district_id'] ?? null); ?>;

        // --- 1. MEMUAT PROVINSI AWAL ---
        $.getJSON('api/get_location.php?type=province', function(data) {
            const province_select = $('#province_select');
            province_select.append('<option value="">Pilih Provinsi</option>');
            $.each(data, function(key, val) {
                province_select.append('<option value="' + val.id + '" data-name="' + val.name + '">' + val.name + '</option>');
            });
            
            // Jika ada data provinsi tersimpan, pilih provinsi itu
            if (savedProvinceId) {
                province_select.val(savedProvinceId);
                // Trigger 'change' untuk memuat kota secara otomatis
                province_select.trigger('change'); 
            }
        });

        // --- 2. MEMUAT KOTA (SAAT PROVINSI BERUBAH) ---
        $('#province_select').change(function() {
            const province_id = $(this).val();
            const province_name = $(this).find('option:selected').data('name');
            $('#province_text').val(province_name); // Simpan nama ke hidden input
            
            $('#city_select').prop('disabled', true).html('<option value="">Loading...</option>');
            $('#district_select').prop('disabled', true).html('<option value="">Pilih Kecamatan</option>');
            
            // Hapus sisa ID tersimpan agar tidak salah pilih
            let currentSavedCityId = (province_id == savedProvinceId) ? savedCityId : null;
            
            if (province_id) {
                $.getJSON('api/get_location.php?type=city&id=' + province_id, function(data) {
                    const city_select = $('#city_select');
                    city_select.html('<option value="">Pilih Kota/Kabupaten</option>');
                    $.each(data, function(key, val) {
                        city_select.append('<option value="' + val.id + '" data-name="' + val.name + '">' + val.name + '</option>');
                    });
                    
                    // Jika ada data kota tersimpan, pilih kota itu
                    if (currentSavedCityId) {
                        city_select.val(currentSavedCityId);
                        // Trigger 'change' untuk memuat kecamatan
                        city_select.trigger('change');
                    }
                    city_select.prop('disabled', false);
                });
            }
        });

        // --- 3. MEMUAT KECAMATAN (SAAT KOTA BERUBAH) ---
        $('#city_select').change(function() {
            const city_id = $(this).val();
            const city_name = $(this).find('option:selected').data('name');
            $('#city_text').val(city_name); // Simpan nama ke hidden input
            
            $('#district_select').prop('disabled', true).html('<option value="">Loading...</option>');
            
            let currentSavedDistrictId = (city_id == savedCityId) ? savedDistrictId : null;

            if (city_id) {
                $.getJSON('api/get_location.php?type=district&id=' + city_id, function(data) {
                    const district_select = $('#district_select');
                    district_select.html('<option value="">Pilih Kecamatan</option>');
                    $.each(data, function(key, val) {
                        district_select.append('<option value="' + val.id + '" data-name="' + val.name + '">' + val.name + '</option>');
                    });
                    
                    // Jika ada data kecamatan tersimpan, pilih kecamatan itu
                    if (currentSavedDistrictId) {
                        district_select.val(currentSavedDistrictId);
                        // Trigger 'change' untuk menyimpan nama
                        district_select.trigger('change');
                    }
                    district_select.prop('disabled', false);
                });
            }
        });
        
        // --- 4. SIMPAN NAMA KECAMATAN ---
        $('#district_select').change(function() {
            const district_name = $(this).find('option:selected').data('name');
            $('#sub_district_text').val(district_name); // Simpan nama ke hidden input
        });
    }
});
</script>
<?php include 'includes/footer.php'; ?>