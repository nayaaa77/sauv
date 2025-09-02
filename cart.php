<?php
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';

// Selalu mulai session di awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ===================================================================
// PENGECEKAN LOGIN UNTUK MENGAKSES HALAMAN & SEMUA FUNGSI KERANJANG
// ===================================================================
// Pengecekan ini diletakkan sebelum logika proses form.
// Jika user belum login (tidak ada 'user_id' di session), alihkan.
if (!isset($_SESSION['user_id'])) {
    // Simpan pesan untuk ditampilkan di halaman login
    $_SESSION['message'] = "Anda harus login untuk mengakses keranjang belanja.";
    header('Location: login_register.php'); // <-- DIUBAH DARI login.php
    exit(); // Pastikan script berhenti setelah redirect
}
// ===================================================================

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- LOGIKA PROSES FORM ---

// 1. Proses Tambah ke Keranjang (dari halaman product_detail.php)
if (isset($_POST['add_to_cart'])) {
    // Pengecekan login sudah dilakukan di atas, jadi blok ini sudah aman.
    // Jika Anda ingin pesan yang lebih spesifik saat menambah item,
    // pengecekan bisa diduplikasi di sini, tapi pengecekan di awal sudah cukup.

    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($product_id > 0 && $quantity > 0) {
        // Ambil info produk dari DB termasuk gambar
        $stmt = $conn->prepare("SELECT name, price, image_url, stock FROM products WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($product = $result->fetch_assoc()) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'name'     => $product['name'],
                    'price'    => $product['price'],
                    'quantity' => $quantity,
                    'image'    => $product['image_url'],
                    'stock'    => $product['stock']
                ];
            }
        }
        $stmt->close();
    }
    header('Location: cart.php');
    exit();
}

// 2. Proses Update Keranjang
if (isset($_POST['update_cart'])) {
    // Pengecekan login di awal file sudah melindungi aksi ini
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
        } else {
            unset($_SESSION['cart'][$product_id]); // Hapus jika kuantitas 0
        }
    }
    header('Location: cart.php');
    exit();
}

// 3. Proses Hapus Item
if (isset($_POST['remove_item'])) {
    // Pengecekan login di awal file sudah melindungi aksi ini
    $product_id_to_remove = (int)$_POST['remove_item'];
    unset($_SESSION['cart'][$product_id_to_remove]);
    header('Location: cart.php');
    exit();
}
?>
<?php include 'includes/header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<div class="container">
    <h1>Your Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="cart-empty">
            <p>Your cart is currently empty.</p>
            <a href="shop.php" class="btn">EXPLORE THE COLLECTION</a>
        </div>
    <?php else: ?>
        <form action="cart.php" method="POST">
            <div class="cart-main-content">
                
                <div class="cart-items-list">
                    <?php
                    $total_cart = 0;
                    foreach ($_SESSION['cart'] as $product_id => $item):
                        $subtotal = $item['price'] * $item['quantity'];
                        $total_cart += $subtotal;
                    ?>
                    <div class="cart-item">
                        <div class="product-image">
                            <img src="assets/img/<?php echo htmlspecialchars($item['image'] ?? 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                        <div class="product-details">
                            <p class="name"><?php echo htmlspecialchars($item['name']); ?></p>
                            <p class="price">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?></p>
                        </div>
                        <div class="product-quantity">
                            <div class="quantity-selector">
                                <button type="button" class="quantity-btn-cart decrease-qty-cart" data-id="<?php echo $product_id; ?>">-</button>
                                <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock'] ?? 99; ?>" class="quantity-input-cart" data-id="<?php echo $product_id; ?>">
                                <button type="button" class="quantity-btn-cart increase-qty-cart" data-id="<?php echo $product_id; ?>">+</button>
                            </div>
                        </div>
                        <div class="product-subtotal">
                            Rp <?php echo number_format($subtotal, 0, ',', '.'); ?>
                        </div>
                        <div class="product-remove">
                            <button type="submit" name="remove_item" value="<?php echo $product_id; ?>" class="btn-remove" title="Hapus item">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>Rp <?php echo number_format($total_cart, 0, ',', '.'); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>Calculated at checkout</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>Rp <?php echo number_format($total_cart, 0, ',', '.'); ?></span>
                    </div>
                    <div class="cart-actions">
                        <button type="submit" name="update_cart" class="btn btn-secondary">Update Cart</button>
                        <a href="checkout.php" class="btn">Proceed to Checkout</a>
                    </div>
                </div>

            </div>
            
            <div class="continue-shopping">
                <a href="shop.php"><i class="fa-solid fa-arrow-left"></i> Back to the Collection</a>
            </div>
            
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>