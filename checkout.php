<?php
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';

// Selalu mulai session di awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Wajib login dan keranjang tidak boleh kosong
if (!is_logged_in() || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit();
}

// Proses jika form checkout disubmit
if (isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $address1 = $_POST['address1'];
    $city = $_POST['city'];
    $province = $_POST['province'];
    $postal_code = $_POST['postal_code'];
    $country = $_POST['country'];
    
    // Gabungkan alamat menjadi satu string
    $shipping_address = "$address1, $city, $province, $postal_code, $country";
    
    // Hitung total harga dari session
    $total_amount = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // Mulai transaksi database
    $conn->begin_transaction();

    try {
        // 1. Simpan ke tabel 'orders'
        $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address) VALUES (?, ?, ?)");
        $stmt_order->bind_param("ids", $user_id, $total_amount, $shipping_address);
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

        // Commit transaksi jika semua berhasil
        $conn->commit();

        // Kosongkan keranjang
        unset($_SESSION['cart']);

        // Redirect ke halaman konfirmasi
        header('Location: order_confirmation.php?order_id=' . $order_id);
        exit();

    } catch (mysqli_sql_exception $exception) {
        // Rollback jika terjadi kesalahan
        $conn->rollback();
        // Tampilkan pesan error
        die("Order failed: " . $exception->getMessage());
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <button onclick="history.back()" class="btn btn-secondary" style="margin-top: 20px;">Back</button>
    <h2 style="margin-top: 20px;">Checkout</h2>
    <div class="checkout-layout">
        <div class="checkout-form">
            <form action="checkout.php" method="POST">
                <h3>Shipping Address</h3>
                <div class="form-group">
                    <label>Address Line 1</label>
                    <input type="text" name="address1" required>
                </div>
                <div class="form-group">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>
                <div class="form-group">
                    <label>Province</label>
                    <input type="text" name="province" required>
                </div>
                <div class="form-group">
                    <label>Postal Code</label>
                    <input type="text" name="postal_code" required>
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" value="Indonesia" required>
                </div>
                <button type="submit" name="place_order" class="btn">Place Order</button>
            </form>
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
        </div>
    </div>
</div>

<style>
/* Style tambahan untuk layout checkout */
.checkout-layout {
    display: flex;
    gap: 40px;
    margin-bottom: 40px;
}
.checkout-form {
    flex: 2; /* Form lebih besar */
}
.order-summary {
    flex: 1; /* Ringkasan lebih kecil */
    background-color: #f9f9f9;
    padding: 20px;
    border-radius: 5px;
    align-self: flex-start; /* Agar summary tidak memanjang ke bawah */
}
</style>

<?php include 'includes/footer.php'; ?>