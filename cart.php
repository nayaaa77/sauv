<?php
require_once 'includes/db_conn.php';
require_once 'includes/functions.php';

// Selalu mulai session di awal
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// PENGECEKAN LOGIN
if (!isset($_SESSION['user_id'])) {
    $_SESSION['message'] = "Anda harus login untuk mengakses keranjang belanja.";
    header('Location: login_register.php');
    exit();
}

// Inisialisasi keranjang
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// --- LOGIKA PROSES FORM ---

// 1. Tambah ke Keranjang
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($product_id > 0 && $quantity > 0) {
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

// 2. Update Keranjang
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $product_id => $quantity) {
        $quantity = (int)$quantity;
        if ($quantity > 0) {
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            }
        } else {
            unset($_SESSION['cart'][$product_id]);
        }
    }
    header('Location: cart.php');
    exit();
}

// 3. Hapus Item
if (isset($_POST['remove_item'])) {
    $product_id_to_remove = (int)$_POST['remove_item'];
    unset($_SESSION['cart'][$product_id_to_remove]);
    header('Location: cart.php');
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h1>Your Cart</h1>

    <?php if (empty($_SESSION['cart'])): ?>
        <div class="cart-empty">
            <p>Your cart is currently empty.</p>
            <a href="shop.php" class="btn">EXPLORE THE COLLECTION</a>
        </div>
    <?php else: ?>
        <form action="cart.php" method="POST" id="cart-form">
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
                                <input type="number" name="quantities[<?php echo $product_id; ?>]" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock'] ?? 99; ?>" class="quantity-input-cart monitor-change" data-id="<?php echo $product_id; ?>">
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
                        <button type="submit" name="update_cart" class="btn btn-secondary" id="btn-update-desktop">Update Cart</button>
                        <a href="checkout.php" class="btn" id="btn-checkout-desktop">Proceed to Checkout</a>
                    </div>
                </div>

            </div>
            
            <div class="continue-shopping">
                <a href="shop.php"><i class="fa-solid fa-arrow-left"></i> Back to the Collection</a>
            </div>

            
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Ambil elemen input angka
    const qtyInputs = document.querySelectorAll('.monitor-change');
    
    // 2. Ambil SEMUA tombol Update & Checkout (Desktop & Mobile)
    const updateBtns = document.querySelectorAll('#btn-update-desktop, .btn-update-mobile');
    const checkoutBtns = document.querySelectorAll('#btn-checkout-desktop, .btn-checkout-mobile');
    
    // Tombol plus minus
    const plusBtns = document.querySelectorAll('.increase-qty-cart');
    const minusBtns = document.querySelectorAll('.decrease-qty-cart');

    let isCartDirty = false;

    // Fungsi: Tandai tombol Update jadi merah
    function markAsDirty() {
        if (!isCartDirty) {
            isCartDirty = true;
            
            // Ubah gaya UNTUK SEMUA tombol update (PC & Mobile)
            updateBtns.forEach(btn => {
                btn.classList.add('btn-needs-update'); // Tambah class merah
                btn.innerHTML = '⚠ SAVE CHANGES';
            });

            // Ubah gaya tombol checkout jadi pudar
            checkoutBtns.forEach(btn => {
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
            });
        }
    }

    // Event Listener Input & Tombol Plus Minus
    qtyInputs.forEach(input => {
        input.addEventListener('input', markAsDirty);
        input.addEventListener('change', markAsDirty);
    });

    plusBtns.forEach(btn => {
        btn.addEventListener('click', () => { setTimeout(markAsDirty, 100); });
    });
    minusBtns.forEach(btn => {
        btn.addEventListener('click', () => { setTimeout(markAsDirty, 100); });
    });

    // CEGAT Checkout jika belum simpan
    checkoutBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (isCartDirty) {
                e.preventDefault(); 
                alert('Perubahan belum disimpan. Mohon klik "SAVE CHANGES" dulu.');
                // Scroll ke tombol update (utamakan yang mobile jika di layar kecil)
                if(window.innerWidth <= 768) {
                    document.querySelector('.btn-update-mobile').scrollIntoView({ behavior: 'smooth' });
                } else {
                    document.getElementById('btn-update-desktop').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>