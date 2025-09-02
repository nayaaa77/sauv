<?php
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!is_logged_in() || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

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
    $address_choice = $_POST['address_choice'] ?? 'saved';

    if ($address_choice === 'new' || !$saved_address) {
        // Jika pilih alamat baru atau tidak ada alamat tersimpan
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $address1 = $_POST['address_line1'];
        $city = $_POST['city'];
        $sub_district = $_POST['sub_district'];
        $province = $_POST['province'];
        $postal_code = $_POST['postal_code'];
        $phone = $_POST['phone'];
        $country = 'Indonesia';
        
        // Gabungkan semua informasi menjadi satu string alamat pengiriman
        $shipping_address_str = "$first_name $last_name, $phone, $address1, $sub_district, $city, $province, $postal_code, $country";

    } else {
        // Jika pakai alamat tersimpan
        $shipping_address_str = "{$saved_address['first_name']} {$saved_address['last_name']}, {$saved_address['phone']}, {$saved_address['address_line1']}, {$saved_address['sub_district']}, {$saved_address['city']}, {$saved_address['province']}, {$saved_address['postal_code']}, Indonesia";
    }

    // Hitung total harga dari session
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    $conn->begin_transaction();

    try {
        // 1. Simpan ke tabel 'orders'
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
        $stmt_order->bind_param("ids", $user_id, $total_amount, $shipping_address_str);
        $stmt_order->execute();
        $order_id = $stmt_order->insert_id;
        $stmt_order->close();

        // 2. Simpan setiap item ke tabel 'order_items'
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price_per_item) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $product_id => $item) {
            $product_id_int = (int) $product_id;
            $stmt_items->bind_param("iiid", $order_id, $product_id_int, $item['quantity'], $item['price']);
            $stmt_items->execute();
        }
        $stmt_items->close();

        $conn->commit();
        unset($_SESSION['cart']);
        header('Location: order_detail.php?id=' . $order_id . '&confirm=true');
        exit();

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        die("Order failed: " . $exception->getMessage());
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <div class="page-header-custom" style="display: flex; align-items: center; gap: 15px; margin-top: 40px; margin-bottom: 20px;">
        <a href="javascript:history.back()" class="btn-back" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 style="margin: 0;">Checkout</h1>
    </div>
    
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
                        foreach ($_SESSION['cart'] as $item):
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
</div>
<?php include 'includes/footer.php'; ?>