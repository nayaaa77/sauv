<?php
include 'includes/header.php';
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';

// Pastikan user sudah login
if (!is_logged_in()) {
    header('Location: login_register.php');
    exit();
}

// Ambil ID pesanan dari URL dan pastikan itu milik user yang sedang login
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_id = $_SESSION['user_id'];

// Cek apakah ada parameter 'confirm' di URL untuk menampilkan pesan sukses
$show_confirmation_message = isset($_GET['confirm']) && $_GET['confirm'] === 'true';

if ($order_id === 0) {
    echo "<div class='container'><p>Pesanan tidak ditemukan.</p></div>";
    include 'includes/footer.php';
    exit();
}

// Ambil data pesanan utama dan data user
$stmt_order = $conn->prepare(
    "SELECT o.*, u.email, u.full_name, a.phone
     FROM orders o
     JOIN users u ON o.user_id = u.id
     LEFT JOIN addresses a ON o.user_id = a.user_id
     WHERE o.id = ? AND o.user_id = ?"
);
$stmt_order->bind_param("ii", $order_id, $user_id);
$stmt_order->execute();
$order = $stmt_order->get_result()->fetch_assoc();
$stmt_order->close();

if (!$order) {
    echo "<div class='container'><p>Pesanan tidak ditemukan atau Anda tidak memiliki akses.</p></div>";
    include 'includes/footer.php';
    exit();
}

// Format alamat pengiriman yang disimpan dengan format multiline
$formatted_address = nl2br(htmlspecialchars($order['shipping_address']));

// Ambil item-item dalam pesanan
$stmt_items = $conn->prepare(
    "SELECT oi.quantity, oi.price_per_item, p.name
     FROM order_items oi
     JOIN products p ON oi.product_id = p.id
     WHERE oi.order_id = ?"
);
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$order_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt_items->close();

?>

<div class="container order-detail-container">
    
    <?php if ($show_confirmation_message): ?>
    <div class="notification brand" id="order-notification">
        <i class="fas fa-check-circle"></i>
        <span>
            <strong>Thank you for your order!</strong> We've received it and are getting it ready for you.
        </span>
        <button class="close-btn" aria-label="Close">&times;</button>
    </div>
    <?php endif; ?>

    <div class="order-detail-layout">
        <div class="order-details-card">
            <h2>Order Details</h2>
            <div class="details-grid">
                <div class="detail-item">
                    <span>ORDER NUMBER</span>
                    <p>#<?php echo $order['id']; ?></p>
                </div>
                <div class="detail-item">
                    <span>EMAIL</span>
                    <p><?php echo htmlspecialchars($order['email']); ?></p>
                </div>
                <div class="detail-item">
                    <span>PAYMENT METHOD</span>
                    <p>Midtrans Payment Gateway</p>
                </div>
                <div class="detail-item">
                    <span>ORDER DATE</span>
                    <p><?php echo date('F j, Y', strtotime($order['order_date'])); ?></p>
                </div>
                <div class="detail-item new-full-width">
                    <span>DELIVERY ADDRESS</span>
                    <p><?php echo $formatted_address; ?></p>
                </div>
                <div class="detail-item new-full-width tracking-detail">
                    <span>TRACKING NUMBER</span>
                    <?php if (!empty($order['resi_number'])): ?>
                        <p class="tracking-number">
                            <a href="https://www.jne.co.id/id/tracking/trace?q=<?php echo htmlspecialchars($order['resi_number']); ?>" target="_blank">
                                <?php echo htmlspecialchars($order['resi_number']); ?>
                            </a>
                        </p>
                    <?php else: ?>
                        <p>Not yet available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="order-summary-card">
            <h2>Order Summary</h2>
            <div class="summary-items">
                <?php
                $subtotal = 0;
                foreach ($order_items as $item):
                    $item_total = $item['price_per_item'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                <div class="summary-product-row">
                    <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                    <span>Rp <?php echo number_format($item_total); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-totals">
                <div class="summary-total-row">
                    <span>SUBTOTAL</span>
                    <strong>Rp <?php echo number_format($subtotal); ?></strong>
                </div>
                <div class="summary-total-row">
                    <span>SHIPPING</span>
                    <strong>Free Shipping</strong>
                </div>
                <div class="summary-total-row final-total">
                    <span>TOTAL</span>
                    <strong>Rp <?php echo number_format($order['total_amount']); ?></strong>
                </div>
            </div>
        </div>
    </div>
    <div class="back-to-orders">
        <a href="my_account.php?page=orders">← Back to My Orders</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>