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
    <div class="card">
        <h4>Total Orders</h4>
        <p><?php echo $total_orders ?? 0; ?></p>
        <i class="fas fa-shopping-cart card-icon"></i>
    </div>
    <div class="card">
        <h4>Total Products</h4>
        <p><?php echo $total_products ?? 0; ?></p>
        <i class="fas fa-box card-icon"></i>
    </div>
    <div class="card">
        <h4>Total Users</h4>
        <p><?php echo $total_users ?? 0; ?></p>
        <i class="fas fa-users card-icon"></i>
    </div>
</div>

<div class="chart-container">
    <h3>Sales Overview</h3>
    <canvas id="salesChart"></canvas>
</div>

<?php include 'includes/footer_admin.php'; ?>