<?php 
$page_title = "Dashboard"; // Variabel untuk judul halaman
include 'includes/header_admin.php'; 

// [PERBAIKAN] Ambil data statistik dengan cara yang lebih aman untuk mencegah error
$products_result = $conn->query("SELECT COUNT(id) as count FROM products");
$total_products = $products_result ? $products_result->fetch_assoc()['count'] : 0;

$orders_result = $conn->query("SELECT COUNT(id) as count FROM orders");
$total_orders = $orders_result ? $orders_result->fetch_assoc()['count'] : 0;

$users_result = $conn->query("SELECT COUNT(id) as count FROM users WHERE role = 'user'");
$total_users = $users_result ? $users_result->fetch_assoc()['count'] : 0;

?>

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Orders</span>
            <span class="stat-value"><?php echo $total_orders ?? 0; ?></span>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Products</span>
            <span class="stat-value"><?php echo $total_products ?? 0; ?></span>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <span class="stat-title">Total Users</span>
            <span class="stat-value"><?php echo $total_users ?? 0; ?></span>
        </div>
    </div>
    
    <a href="https://dashboard.tawk.to/" target="_blank" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-comment-dots"></i>
            </div>
            <div class="stat-info">
                <span class="stat-title">Tawk.to Dashboard</span>
                <span class="stat-value">Go to Chat</span>
            </div>
        </div>
    </a>
</div>


<?php include 'includes/footer_admin.php'; ?>