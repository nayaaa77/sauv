<?php 
$page_title = "Manage Orders"; // Set judul halaman
include 'includes/header_admin.php'; 

// Logika untuk update status dan resi
if (isset($_POST['update_order'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $resi_number = !empty($_POST['resi_number']) ? $_POST['resi_number'] : NULL;

    $stmt = $conn->prepare("UPDATE orders SET status = ?, resi_number = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $resi_number, $order_id);
    $stmt->execute();
    $stmt->close();
    // Redirect untuk mencegah re-submit form
    header("Location: orders.php?update=success");
    exit();
}

// Ambil semua data pesanan
$orders_result = $conn->query("
    SELECT o.*, u.full_name 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.id 
    ORDER BY o.order_date DESC
");
?>

<!-- Script untuk mengatur judul di header utama -->
<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<!-- Header Halaman Konten -->
<div class="page-header">
    <h2><?php echo $page_title; ?></h2>
</div>

<!-- Tabel Pesanan di dalam Card -->
<div class="card">
    <div class="card-body">
        <table class="table-products table-orders">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tracking No.</th>
                    <th class="col-actions">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <?php while($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <form action="orders.php" method="POST">
                                <td class="text-center"><?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></td>
                                <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                <td>Rp <?php echo number_format($order['total_amount']); ?></td>
                                <td>
                                    <select name="status" class="form-control">
                                        <option value="pending" <?php if($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="confirmed" <?php if($order['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                        <option value="shipped" <?php if($order['status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                                        <option value="completed" <?php if($order['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                        <option value="cancelled" <?php if($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="resi_number" class="form-control" value="<?php echo htmlspecialchars($order['resi_number'] ?? ''); ?>" placeholder="Input tracking number">
                                </td>
                                <td class="actions">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="update_order" class="btn btn-primary">Update</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-products">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>
