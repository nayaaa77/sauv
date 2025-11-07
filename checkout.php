<?php
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Impor Namespace untuk kemudahan kode
use Midtrans\Config;
use Midtrans\Snap;

// Tentukan apakah ada transaksi Midtrans yang baru saja dibuat
$snapToken = $_SESSION['snap_token'] ?? null;
$final_order_id = $_GET['order_id'] ?? $_SESSION['midtrans_order_id'] ?? null;
$is_midtrans_processing = (isset($snapToken) && isset($final_order_id));

// ===============================================
// 1. KEAMANAN: Cek Login dan Keranjang
// ===============================================
if (!is_logged_in()) {
    header('Location: login_register.php');
    exit();
}

if (!$is_midtrans_processing && empty($_SESSION['cart'])) {
    header('Location: index.php'); 
    exit();
}
// ===============================================

$user_id = $_SESSION['user_id'];
$shipping_address_str = '';

// Ambil alamat tersimpan dari user
$stmt_addr = $conn->prepare("SELECT * FROM addresses WHERE user_id = ?");
$stmt_addr->bind_param("i", $user_id);
$stmt_addr->execute();
$saved_address = $stmt_addr->get_result()->fetch_assoc();
$stmt_addr->close();


// Proses jika form checkout disubmit
if (isset($_POST['place_order'])) {
    
    // ===============================================
    // 2. Ambil & Susun Data Pelanggan dan Pengiriman
    // ===============================================
    $first_name = '';
    $last_name = '';
    $phone = '';
    $address1 = ''; 
    $city = '';
    $sub_district = '';
    $province = '';
    $postal_code = '';
    
    $email_result = $conn->query("SELECT email FROM users WHERE id = $user_id");
    $email = $email_result ? $email_result->fetch_assoc()['email'] : '';
    
    $address_choice = $_POST['address_choice'] ?? 'saved';
    
    if ($address_choice === 'new' || !$saved_address) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $address1 = $_POST['address_line1'];
        $city = $_POST['city'];
        $sub_district = $_POST['sub_district'];
        $province = $_POST['province'];
        $postal_code = $_POST['postal_code'];
        $phone = $_POST['phone'];
        $country = 'Indonesia';

    } else {
        $first_name = $saved_address['first_name'];
        $last_name = $saved_address['last_name'];
        $phone = $saved_address['phone'];
        $address1 = $saved_address['address_line1'];
        $city = $saved_address['city'];
        $sub_district = $saved_address['sub_district'];
        $province = $saved_address['province'];
        $postal_code = $saved_address['postal_code'];
        $country = 'Indonesia';
    }

    $shipping_address_str = 
        $first_name . ' ' . $last_name . "\n" . 
        $address1 . "\n" .
        $sub_district . ', ' . $city . "\n" . 
        $province . ' ' . $postal_code . "\n" . 
        $country . "\n" .
        'Telp: ' . $phone;
    
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    $conn->begin_transaction();
    $order_id = null; 

    try {
        // 3. Simpan Order ke Database DENGAN STATUS AWAL 'PENDING'
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, status) VALUES (?, ?, ?, 'pending')");
        $stmt_order->bind_param("ids", $user_id, $total_amount, $shipping_address_str);
        $stmt_order->execute();
        $order_id = $conn->insert_id;
        $stmt_order->close();

        // =========================================================================
        // 4. PERBAIKAN BUG UTAMA ADA DI SINI
        // =========================================================================
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $product_id => $item) {
            // Menggunakan '$product_id' dari key array, bukan '$product_id_key' yang tidak ada.
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
    
    // ===============================================
    // 5. Integrasi Midtrans SNAP (API)
    // ===============================================
    if ($order_id) {
        require_once 'includes/midtrans_config.php';
        
        $order_items_details = [];
        $gross_amount = 0;
        
        foreach ($_SESSION['cart'] as $product_id => $item) { // Variabel di sini juga diperbaiki
            $price_int = (int)$item['price'];
            $qty_int = (int)$item['quantity'];
            $order_items_details[] = [
                'id' => $product_id, // Menggunakan $product_id yang benar
                'price' => $price_int,
                'quantity' => $qty_int,
                'name' => $item['name']
            ];
            $gross_amount += $price_int * $qty_int;
        }

        $transaction_details = array(
            'order_id'      => $order_id . '-' . time(),
            'gross_amount'  => $gross_amount,
        );
        
        $customer_details = array(
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'email'         => $email,
            'phone'         => $phone,
        );
        
        $shipping_address = array(
            'first_name'    => $first_name,
            'last_name'     => $last_name,
            'address'       => $address1, 
            'city'          => $city, 
            'postal_code'   => $postal_code,
            'phone'         => $phone,
            'country_code'  => 'IDN'
        );
        
        $params = array(
            'transaction_details' => $transaction_details,
            'item_details'        => $order_items_details,
            'customer_details'    => $customer_details,
            'shipping_address'    => $shipping_address
        );
        
        try {
            $snapToken = Snap::getSnapToken($params);
            
            $_SESSION['snap_token'] = $snapToken;
            $_SESSION['midtrans_order_id'] = $order_id;
            $_SESSION['cart_temp'] = $_SESSION['cart']; 
            unset($_SESSION['cart']);

            header('Location: checkout.php?order_id=' . $order_id);
            exit();
            
        } catch (Exception $e) {
            $error_message = "Midtrans Error: " . $e->getMessage();
            error_log($error_message);
            die("Payment failed: " . $error_message);
        }
    }
}

if ($is_midtrans_processing) {
    $total_amount_midtrans_result = $conn->query("SELECT total_amount FROM orders WHERE id = $final_order_id");
    $total_amount_midtrans = $total_amount_midtrans_result ? $total_amount_midtrans_result->fetch_assoc()['total_amount'] : 0;
}
?>

<!-- KODE HTML DI BAWAH INI SAMA PERSIS DENGAN KODE LAMA ANDA -->
<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header-custom" style="display: flex; align-items: center; gap: 15px; margin-top: 40px; margin-bottom: 20px;">
        <a href="javascript:history.back()" class="btn-back" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 style="margin: 0;">Checkout</h1>
    </div>
    
    <?php if ($is_midtrans_processing): ?>
        <div class="payment-confirmation" style="text-align: center; padding: 50px 20px; max-width: 600px; margin: 40px auto; border: 1px solid #ddd; background: #fff;">
            <h2>Order Confirmed! We're getting this ready for you</h2>
            <p>Total Due: <strong>Rp <?php echo number_format($total_amount_midtrans, 0, ',', '.'); ?></strong></p>
            <button id="pay-button" class="btn" style="padding: 12px 30px; margin-top: 20px;">COMPLETE PAYMENT</button>
        </div>
        
    <?php else: ?>
        <form action="checkout.php" method="POST">
            <div class="checkout-layout">
                <div class="checkout-form">
                    <h3>Shipping Address</h3>

                    <?php if ($saved_address): ?>
                    <div class="address-selection">
                        <div class="address-box saved-address-box">
                            <input type="radio" id="address_saved" name="address_choice" value="saved" checked>
                            <label for="address_saved">
                                <strong>Use Saved Address</strong>
                                <p>
                                    <?php echo htmlspecialchars($saved_address['first_name'] . ' ' . $saved_address['last_name']); ?><br>
                                    <?php echo htmlspecialchars($saved_address['address_line1']); ?><br>
                                    <?php echo htmlspecialchars($saved_address['city'] . ', ' . $saved_address['province'] . ' ' . $saved_address['postal_code']); ?><br>
                                    Indonesia
                                </p>
                            </label>
                        </div>
                        <div class="address-box new-address-toggle">
                            <input type="radio" id="address_new" name="address_choice" value="new">
                            <label for="address_new">
                                <strong>Use a Different Address</strong>
                            </label>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div id="new-address-form" class="new-address-form-container" <?php if ($saved_address) echo 'style="display: none;"'; ?>>
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
                            <label for="address_line1">Full Address</label>
                            <input type="text" id="address_line1" name="address_line1" <?php if (!$saved_address) echo 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="province">State / Province <span class="label-translation">(Provinsi)</span></label>
                            <input type="text" id="province" name="province" <?php if (!$saved_address) echo 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="city">City / Town <span class="label-translation">(Kota/Kabupaten)</span></label>
                            <input type="text" id="city" name="city" <?php if (!$saved_address) echo 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="sub_district">Sub-District <span class="label-translation">(Kecamatan)</span></label>
                            <input type="text" id="sub_district" name="sub_district" <?php if (!$saved_address) echo 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="postal_code">Postal Code <span class="label-translation">(Kode Pos)</span></label>
                            <input type="text" id="postal_code" name="postal_code" <?php if (!$saved_address) echo 'required'; ?>>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone">
                        </div>
                    </div>
                </div>

                <div class="order-summary">
                    <h3>Order Summary</h3>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th style="text-align: left; padding: 8px; border-bottom: 1px solid #ddd;">Produk</th>
                                <th style="text-align: right; padding: 8px; border-bottom: 1px solid #ddd;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_for_display = 0;
                            $cart_items_for_display = $_SESSION['cart_temp'] ?? $_SESSION['cart'];
                            foreach ($cart_items_for_display as $item):
                                $subtotal = $item['price'] * $item['quantity'];
                                $total_for_display += $subtotal;
                            ?>
                            <tr>
                                <td style="padding: 8px;"><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</td>
                                <td style="text-align: right; padding: 8px;">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th style="text-align: left; padding: 8px; border-top: 1px solid #ddd;">Total</th>
                                <th style="text-align: right; padding: 8px; border-top: 1px solid #ddd;">Rp <?php echo number_format($total_for_display, 0, ',', '.'); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <button type="submit" name="place_order" class="btn" style="width: 100%; margin-top: 20px;">Place Order</button>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php 
// Pindahkan blok PHP ini ke bawah sebelum include footer
if ($is_midtrans_processing) {
    // Muat konfigurasi Midtrans di sini agar $clientKey tersedia
    require_once 'includes/midtrans_config.php';
}
include 'includes/footer.php'; 

// ===> SEMUA KODE JAVASCRIPT DIPINDAHKAN KE SINI <===
if ($is_midtrans_processing): 
?>
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="<?php echo Config::$clientKey; ?>">
    </script>
    <script type="text/javascript">
        // Karena skrip ini sekarang ada di akhir body, kita tidak perlu 'DOMContentLoaded'
        var payButton = document.getElementById('pay-button');
        
        // Selalu cek apakah elemennya ada sebelum memasang event
        if (payButton) {
            payButton.onclick = function () {
                // Panggil Midtrans Snap Pop-up
                snap.pay('<?php echo $snapToken; ?>', {
                    onSuccess: function(result){
                        // Pembayaran sukses, arahkan ke halaman detail dengan penanda konfirmasi
                        console.log("Pembayaran Berhasil:", result);
                        window.location.href = 'order_detail.php?id=<?php echo $final_order_id; ?>&confirm=true';
                    },
                    onPending: function(result){
                        // Pembayaran tertunda, arahkan ke halaman detail untuk lihat status pending
                        console.log("Pembayaran Tertunda:", result);
                        window.location.href = 'order_detail.php?id=<?php echo $final_order_id; ?>';
                    },
                    onError: function(result){
                        // Pembayaran gagal, beri tahu user dan biarkan di halaman checkout
                        console.log("Pembayaran Gagal:", result);
                        alert("Pembayaran Gagal. Silakan coba lagi.");
                    },
                    onClose: function(){
                        // Pengguna menutup pop-up
                        alert('Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.');
                    }
                });
            };
        }
    </script>
<?php
// Bersihkan token dari session setelah digunakan
unset($_SESSION['snap_token']);
unset($_SESSION['midtrans_order_id']);
unset($_SESSION['cart_temp']);
endif;
?>