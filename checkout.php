<?php
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';
require_once 'config.php'; // ⚠️ WAJIB: Memuat API Key & Origin ID

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

use Midtrans\Config;
use Midtrans\Snap;

$snapToken = $_SESSION['snap_token'] ?? null;
$final_order_id = $_GET['order_id'] ?? $_SESSION['midtrans_order_id'] ?? null;
$is_midtrans_processing = (isset($snapToken) && isset($final_order_id));

if (!is_logged_in()) {
    header('Location: login_register.php');
    exit();
}
if (!$is_midtrans_processing && empty($_SESSION['cart'])) {
    header('Location: index.php'); 
    exit();
}

$user_id = $_SESSION['user_id'];
$shipping_address_str = '';

// Ambil alamat tersimpan (TERMASUK ID LOKASI BARU)
$stmt_addr = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt_addr->bind_param("i", $user_id);
$stmt_addr->execute();
$saved_address = $stmt_addr->get_result()->fetch_assoc();
$stmt_addr->close();


// --- PROSES SUBMIT ORDER ---
if (isset($_POST['place_order'])) {
    
    // DATA DARI HIDDEN INPUT (DIISI OLEH JS)
    $shipping_cost = $_POST['shipping_cost'] ?? 0;
    $shipping_service = $_POST['shipping_service'] ?? 'N/A';
    $destination_district_id = $_POST['destination_district_id'] ?? null;
    
    // VALIDASI ONGKIR
    if (!is_numeric($shipping_cost) || $shipping_cost <= 0 || !isset($destination_district_id) || $destination_district_id == 0) {
        $_SESSION['error_message'] = "Please select a complete address and shipping service.";
        header('Location: checkout.php');
        exit();
    }
    
    // Ambil data pelanggan (Sama seperti kode lama Anda, disesuaikan sedikit)
    $email_result = $conn->query("SELECT email FROM users WHERE id = $user_id");
    $email = $email_result ? $email_result->fetch_assoc()['email'] : '';
    $address_choice = $_POST['address_choice'] ?? 'saved';
    
    if ($address_choice === 'new' || !$saved_address) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $address1 = $_POST['address_line1'];
        $city = $_POST['city_text']; // ⚠️ Ambil dari hidden input nama
        $sub_district = $_POST['sub_district_text']; // ⚠️ Ambil dari hidden input nama
        $province = $_POST['province_text']; // ⚠️ Ambil dari hidden input nama
        $postal_code = $_POST['postal_code'];
        $phone = $_POST['phone'];
    } else {
        $first_name = $saved_address['first_name'];
        $last_name = $saved_address['last_name'];
        $phone = $saved_address['phone'];
        $address1 = $saved_address['address_line1'];
        $city = $saved_address['city'];
        $sub_district = $saved_address['sub_district'];
        $province = $saved_address['province'];
        $postal_code = $saved_address['postal_code'];
    }

    $shipping_address_str = 
        "Service: " . $shipping_service . "\n" .
        "Shipping Cost: Rp " . number_format($shipping_cost, 0, ',', '.') . "\n" .
        "District ID: " . $destination_district_id . "\n" .
        "Receiver: " . $first_name . ' ' . $last_name . "\n" . 
        "Address: " . $address1 . "\n" .
        $sub_district . ', ' . $city . "\n" . 
        $province . ' ' . $postal_code . "\n" . 
        'Phone: ' . $phone;
    
    // Hitung Subtotal Produk
    $subtotal_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal_amount += $item['price'] * $item['quantity'];
    }
    
    // TOTAL JUMLAH = SUBTOTAL + ONGKIR
    $total_amount = $subtotal_amount + $shipping_cost;

    $conn->begin_transaction();
    $order_id = null; 

    try {
        // Simpan Order (Total sudah termasuk ongkir)
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'pending')");
        $stmt_order->bind_param("ids", $user_id, $total_amount, $shipping_address_str);
        $stmt_order->execute();
        $order_id = $conn->insert_id;
        $stmt_order->close();

        // Simpan Order Items
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $stmt_items->bind_param("iiid", $order_id, $product_id, $item['quantity'], $item['price']);
            $stmt_items->execute();
        }
        $stmt_items->close();
        // =========================================================================

        $conn->commit();
        
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        die("Order failed: " . $exception->getMessage());
    }
    
    // --- PROSES MIDTRANS ---
    if ($order_id) {
        require_once 'includes/midtrans_config.php';
        $order_items_details = [];
        
        // Tambahkan item produk
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $order_items_details[] = [
                'id' => $product_id,
                'price' => (int)$item['price'],
                'quantity' => (int)$item['quantity'],
                'name' => $item['name']
            ];
        }
        // Tambahkan Biaya Pengiriman
        $order_items_details[] = [
            'id' => 'SHIPPING_FEE',
            'price' => (int)$shipping_cost,
            'quantity' => 1,
            'name' => "Shipping Fee ({$shipping_service})"
        ];

        $transaction_details = ['order_id' => $order_id . '-' . time(), 'gross_amount'  => (int)$total_amount];
        $customer_details = ['first_name' => $first_name, 'last_name' => $last_name, 'email' => $email, 'phone' => $phone];
        $shipping_address = ['first_name' => $first_name, 'last_name' => $last_name, 'address' => $address1, 'city' => $city, 'postal_code' => $postal_code, 'phone' => $phone, 'country_code' => 'IDN'];
        
        $params = [
            'transaction_details' => $transaction_details,
            'item_details'        => $order_items_details,
            'customer_details'    => $customer_details,
            'shipping_address'    => $shipping_address
        ];
        
        try {
            $snapToken = Snap::getSnapToken($params);
            $_SESSION['snap_token'] = $snapToken;
            $_SESSION['midtrans_order_id'] = $order_id;
            $_SESSION['cart_temp'] = $_SESSION['cart']; 
            unset($_SESSION['cart']);
            header('Location: checkout.php?order_id=' . $order_id);
            exit();
        } catch (Exception $e) {
            die("Payment failed: " . $e->getMessage());
        }
    }
}

// --- DATA UNTUK TAMPILAN HALAMAN ---
if ($is_midtrans_processing) {
    $total_amount_midtrans_result = $conn->query("SELECT total_amount FROM orders WHERE id = $final_order_id");
    $total_amount_midtrans = $total_amount_midtrans_result ? $total_amount_midtrans_result->fetch_assoc()['total_amount'] : 0;
}

$total_for_display = 0;
$total_weight_for_display = 0; // 1. Berat dimulai dari 0
$cart_items_for_display = $_SESSION['cart_temp'] ?? $_SESSION['cart'];

// [FIX ERROR]: Pengecekan agar tidak error jika cart kosong atau weight tidak ada
if ($cart_items_for_display) {
    foreach ($cart_items_for_display as $item) {
        $total_for_display += $item['price'] * $item['quantity'];
        
        // [FIX ERROR KEY]: Gunakan default 200g jika key 'weight' tidak ada
        $weight = isset($item['weight']) ? $item['weight'] : 200; 
        $total_weight_for_display += $weight * $item['quantity'];
    }
}

// 4. Tambahan: RajaOngkir punya berat minimum (biasanya 1000gr).
if ($total_weight_for_display < 1000) {
    $total_weight_for_display = 1000;
}
// === END PERUBAHAN ===


$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']); 
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header-custom" style="display: flex; align-items: center; gap: 15px;">
        <a href="javascript:history.back()" class="btn-back" title="Back"><i class="fas fa-arrow-left"></i></a>
        <h1>Checkout</h1>
    </div>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger" style="color: red; padding: 10px; border: 1px solid red; margin-bottom: 20px;">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if ($is_midtrans_processing): ?>
        <div class="payment-confirmation" style="text-align: center; padding: 50px 20px; max-width: 600px; margin: 40px auto; border: 1px solid #ddd; background: #fff;">
            <h2>Order Confirmed! We're getting this ready for you</h2>
            <p>Total Due: <strong>Rp <?php echo number_format($total_amount_midtrans, 0, ',', '.'); ?></strong></p>
            <button id="pay-button" class="btn" style="padding: 12px 30px; margin-top: 20px;">COMPLETE PAYMENT</button>
        </div>
        
    <?php else: ?>
        <form action="checkout.php" method="POST" id="checkout_form">
            
            <input type="hidden" name="shipping_cost" id="hidden_shipping_cost" value="0">
            <input type="hidden" name="shipping_service" id="hidden_shipping_service" value="">
            <input type="hidden" name="destination_district_id" id="hidden_destination_district_id" value="">
            
            <div class="checkout-layout">
                <div class="checkout-form">
                    <h2>Shipping Address</h2>

                    <?php if ($saved_address): ?>
                        <div class="address-selection">
                        <div class="address-box saved-address-box">
                            <input type="radio" id="address_saved" name="address_choice" value="saved" checked>
                            <label for="address_saved" class="address-card">
                                <div class="addr-icon">
                                    <i class="fas fa-home"></i> </div>
                                <div class="addr-content">
                                    <strong>Use Saved Address</strong>
                                    <p class="addr-preview">
                                        <?php echo htmlspecialchars($saved_address['first_name'] . ' ' . $saved_address['last_name']); ?><br>
                                        <?php echo htmlspecialchars($saved_address['address_line1']); ?><br>
                                        <small><?php echo htmlspecialchars($saved_address['sub_district'] . ', ' . $saved_address['city'] . ', ' . $saved_address['province']); ?></small>
                                    </p>
                                </div>
                                <div class="addr-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </label>
                            
                            <input type="hidden" id="saved_district_id" value="<?php echo htmlspecialchars($saved_address['district_id'] ?? 0); ?>">
                        </div>

                        <div class="address-box new-address-toggle">
                            <input type="radio" id="address_new" name="address_choice" value="new">
                            <label for="address_new" class="address-card">
                                <div class="addr-icon new">
                                    <i class="fas fa-map-marker-alt"></i> </div>
                                <div class="addr-content">
                                    <strong>Use New Address</strong>
                                    <p class="addr-desc">Ship to a different address.</p>
                                </div>
                                <div class="addr-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div id="new-address-form" class="new-address-form-container" <?php if ($saved_address) echo 'style="display: none;"'; ?>>
                        
                        <div class="form-group">
                            <label for="province_select">Province</label>
                            <select id="province_select" name="province_select" class="select2-dynamic" <?php if (!$saved_address) echo 'required'; ?>></select>
                            <input type="hidden" id="province_text" name="province_text" value="">
                        </div>
                        <div class="form-group">
                            <label for="city_select">City / Town</label>
                            <select id="city_select" name="city_select" class="select2-dynamic" <?php if (!$saved_address) echo 'required'; ?> disabled></select>
                            <input type="hidden" id="city_text" name="city_text" value="">
                        </div>
                        <div class="form-group">
                            <label for="district_select">Sub-District</label>
                            <select id="district_select" name="district_select" class="select2-dynamic" <?php if (!$saved_address) echo 'required'; ?> disabled></select>
                            <input type="hidden" id="sub_district_text" name="sub_district_text" value="">
                        </div>
                        <div class="form-group">
                            <label for="address_line1">Full Address</label>
                            <input type="text" id="address_line1" name="address_line1" <?php if (!$saved_address) echo 'required'; ?>>
                        </div>
                        <div style="display: flex; gap: 20px;">
                            <div class="form-group" style="flex: 1;">
                                <label for="first_name">First name *</label>
                                <input type="text" id="first_name" name="first_name" <?php if (!$saved_address) echo 'required'; ?>>
                            </div>
                            <div class="form-group" style="flex: 1;">
                                <label for="last_name">Last name *</label>
                                <input type="text" id="last_name" name="last_name" <?php if (!$saved_address) echo 'required'; ?>>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Postal Code</label>
                            <input type="text" id="postal_code" name="postal_code" <?php if (!$saved_address) echo 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone">
                        </div>
                    </div>
                    
                    <h2 style="margin-top: 30px;">Shipping Method</h2>
                    <div class="form-group">
                        <label for="courier_select">Select Courier:</label>
                        <select id="courier_select" name="courier_select" class="select2-dynamic" required>
                            <option value="">Select a Courier</option>
                            <option value="jne">JNE</option>
                            <option value="tiki">TIKI</option>
                            <option value="pos">POS Indonesia</option>
                            <option value="sicepat">SiCepat</option>
                            <option value="anteraja">AnterAja</option>
                            <option value="jnt">J&T Express</option>
                            <option value="ninja">Ninja Xpress</option>
                            <option value="lion">Lion Parcel</option>
                            <option value="rpx">RPX</option>
                            <option value="ide">ID Express</option>
                            <option value="sap">SAP</option>
                        </select>
                    </div>
                    
                    <div id="cost_options" style="margin-top: 15px; padding: 10px; border: 1px dashed #ccc;">
                        <p>Please complete your address (including District) and select a courier to view shipping costs.</p>
                    </div>
                    
                    <input type="hidden" id="total_weight" value="<?php echo $total_weight_for_display; ?>"> 
                </div>

                <div class="order-summary">
                    <h2>Order Summary</h2>
                    
                    <div class="checkout-items-list">
                        <?php foreach ($cart_items_for_display as $item): ?>
                        <div class="checkout-item">
                            <div class="checkout-img">
                                <?php 
                                    $img_src = $item['image'] ?? $item['main_image'] ?? 'placeholder.png'; 
                                ?>
                                <img src="assets/img/<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                <span class="qty-badge"><?php echo $item['quantity']; ?></span>
                            </div>
                            
                            <div class="checkout-details">
                                <div class="prod-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="prod-price">Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="checkout-totals">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>Rp <?php echo number_format($total_for_display, 0, ',', '.'); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Shipping</span>
                            <span>Rp <span id="shipping_cost_display">0</span></span>
                        </div>
                        <div class="total-row final">
                            <span>Total</span>
                            <span class="gold-text">Rp <span id="grand_total_display"><?php echo number_format($total_for_display, 0, ',', '.'); ?></span></span>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" id="place_order_button" class="btn" style="width: 100%; margin-top: 20px;" disabled>Place Order</button>
                </div>
            
            </div>
        </form>
    <?php endif; ?>
</div>

<?php 
if ($is_midtrans_processing) {
    require_once 'includes/midtrans_config.php';
}
include 'includes/footer.php'; 
?>

<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('#province_select, #city_select, #district_select, #courier_select').select2({
        width: '100%'
    });

    const SUB_TOTAL = <?php echo $total_for_display; ?>;
    let current_shipping_cost = 0;
    
    // --- 0. FUNGSI UTILITY ---
    const formatNumber = (num) => new Intl.NumberFormat('id-ID').format(num);
    const updateGrandTotal = (cost) => {
        current_shipping_cost = cost;
        const total = SUB_TOTAL + current_shipping_cost;
        $('#shipping_cost_display').text(formatNumber(current_shipping_cost));
        $('#grand_total_display').text(formatNumber(total));
    };

    const resetShipping = () => {
        $('#hidden_shipping_cost').val(0);
        $('#hidden_shipping_service').val('');
        $('#place_order_button').prop('disabled', true);
        $('#cost_options').html('<p>Please complete your address (including District) and select a courier to view shipping costs.</p>');
        updateGrandTotal(0);
    };

    // --- 1. LOGIKA PILIH ALAMAT ---
    $('input[name="address_choice"]').change(function() {
        if ($(this).val() === 'new') {
            $('#new-address-form').slideDown();
        } else {
            $('#new-address-form').slideUp();
        }
        // Panggil calculateShippingCost setelah 500ms untuk memastikan form 'saved' sudah ter-load
        setTimeout(calculateShippingCost, 500);
    });

    // --- 2. FUNGSI MEMUAT LOKASI (ALAMAT BARU) ---
    $.getJSON('api/get_location.php?type=province', function(data) {
        const province_select = $('#province_select');
        province_select.append('<option value="">Select Province</option>');
        if (data && Array.isArray(data)) {
            $.each(data, function(key, val) {
                let valId = val.id || val.province_id;
                let valName = val.name || val.province;
                
                if(valId && valName) {
                    province_select.append('<option value="' + valId + '" data-name="' + valName + '">' + valName + '</option>');
                }
            });
        }
    });

    $('#province_select').change(function() {
        const province_id = $(this).val();
        const province_name = $(this).find('option:selected').data('name');
        $('#province_text').val(province_name || '');
        
        $('#city_select').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change');
        $('#district_select').prop('disabled', true).html('<option value="">Select District</option>').trigger('change');
        resetShipping();
        
        if (province_id) {
            $.getJSON('api/get_location.php?type=city&id=' + province_id, function(data) {
                const city_select = $('#city_select');
                city_select.html('<option value="">Select City / Town</option>');
                if (data && Array.isArray(data)) {
                    $.each(data, function(key, val) {
                        let valId = val.id || val.city_id;
                        let valName = val.name || val.city_name;
                        
                        if(valId && valName) {
                            city_select.append('<option value="' + valId + '" data-name="' + valName + '">' + valName + '</option>');
                        }
                    });
                }
                city_select.prop('disabled', false).trigger('change');
            });
        }
    });

    $('#city_select').change(function() {
        const city_id = $(this).val();
        const city_name = $(this).find('option:selected').data('name');
        $('#city_text').val(city_name || '');
        
        $('#district_select').prop('disabled', true).html('<option value="">Loading...</option>').trigger('change');
        resetShipping();
        
        if (city_id) {
            $.getJSON('api/get_location.php?type=district&id=' + city_id, function(data) {
                const district_select = $('#district_select');
                district_select.html('<option value="">Select District</option>');
                if (data && Array.isArray(data)) {
                    $.each(data, function(key, val) {
                        let valId = val.id || val.district_id || val.subdistrict_id;
                        let valName = val.name || val.district_name || val.subdistrict_name;
                        
                        if(valId && valName) {
                            district_select.append('<option value="' + valId + '" data-name="' + valName + '">' + valName + '</option>');
                        }
                    });
                }
                district_select.prop('disabled', false).trigger('change');
            });
        }
    });
    
    // --- 3. FUNGSI UTAMA: HITUNG ONGKIR ---
    function calculateShippingCost() {
        const address_choice = $('input[name="address_choice"]:checked').val();
        let destination_id = 0;
        
        if (address_choice === 'new') {
            destination_id = $('#district_select').val();
        } else {
            destination_id = $('#saved_district_id').val();
        }
        
        if (destination_id && destination_id > 0) {
            $('#hidden_destination_district_id').val(destination_id);
        } else {
            $('#hidden_destination_district_id').val('');
        }

        const weight = $('#total_weight').val();
        const courier = $('#courier_select').val();
        
        if (destination_id && destination_id > 0 && weight > 0 && courier) {
            $('#cost_options').html('<p>Calculating shipping cost...</p>');
            
            $.post('api/calculate_cost.php', {
                destination_id: destination_id,
                weight: weight,
                courier: courier
            }, function(response) {
                let output = '';
                if (response.meta && response.meta.code == 200 && response.data.length > 0) {
                    output += '<h4>Select Service:</h4>';
                    $.each(response.data, function(index, service) {
                        const cost = service.cost;
                        const etd = service.etd;
                        const serviceName = service.service;
                        const formattedCost = formatNumber(cost);
                        
                        output += `<label class="service-option">
                                    <input type="radio" name="shipping_service" value="${serviceName}" data-cost="${cost}">
                                    ${serviceName} - Rp ${formattedCost} (${etd} days)
                                </label><br>`;
                    });
                } else {
                     let errorMsg = 'Service not available for this route.';
                     if(response.meta && response.meta.message) {
                         errorMsg = `Error: ${response.meta.message}`;
                     }
                    output = `<div class="alert-error">${errorMsg}</div>`;
                }
                $('#cost_options').html(output);
            }, 'json').fail(function() {
                 $('#cost_options').html('<div class="alert-error">Error: Connection failed.</div>');
            });
        } else {
            resetShipping();
        }
    }
    
    // --- 4. PEMICU (TRIGGER) KALKULASI ---
    $('#district_select').change(function() {
        const district_id = $(this).val();
        const district_name = $(this).find('option:selected').data('name');
        $('#sub_district_text').val(district_name || '');
        
        calculateShippingCost();
    });

    $('#courier_select').change(calculateShippingCost);

    $('#cost_options').on('change', 'input[name="shipping_service"]', function() {
        const cost = parseInt($(this).data('cost'));
        const serviceName = $(this).val();

        updateGrandTotal(cost);
        
        $('#hidden_shipping_cost').val(cost);
        $('#hidden_shipping_service').val(serviceName);
        $('#place_order_button').prop('disabled', false);
    });

    if ($('input[name="address_choice"]:checked').val() === 'saved') {
        setTimeout(calculateShippingCost, 1000);
    }
});
</script>
<?php 
if ($is_midtrans_processing): 
?>
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="<?php echo Config::$clientKey; ?>">
    </script>
    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        if (payButton) {
            payButton.onclick = function () {
                snap.pay('<?php echo $snapToken; ?>', {
                    onSuccess: function(result){
                        window.location.href = 'order_detail.php?id=<?php echo $final_order_id; ?>&confirm=true';
                    },
                    onPending: function(result){
                        window.location.href = 'order_detail.php?id=<?php echo $final_order_id; ?>';
                    },
                    onError: function(result){
                        alert("Payment unsuccessful. Please try again.");
                    },
                    onClose: function(){
                        alert('You closed the payment window before completing the transaction.');
                    }
                });
            };
        }
    </script>
<?php
unset($_SESSION['snap_token']);
unset($_SESSION['midtrans_order_id']);
unset($_SESSION['cart_temp']);
endif;
?>
</body>
</html>