<?php
$page_title = "Manage Orders"; // Set judul halaman
include 'includes/header_admin.php';

// Logika untuk update status dan resi
if (isset($_POST['update_order'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    $resi_number = !empty($_POST['resi_number']) ? trim($_POST['resi_number']) : NULL;

    $stmt = $conn->prepare("UPDATE orders SET status = ?, resi_number = ? WHERE id = ?");
    $stmt->bind_param("ssi", $status, $resi_number, $order_id);
    $stmt->execute();
    $stmt->close();
    // Redirect untuk mencegah re-submit form dan menampilkan notifikasi
    header("Location: orders.php?status=update_success");
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

<script>document.querySelector('.header-title').textContent = '<?php echo $page_title; ?>';</script>

<?php
// Blok untuk menampilkan notifikasi
if (isset($_GET['status']) && $_GET['status'] == 'update_success') {
    echo "<div class='alert alert-success'>Order updated successfully!</div>";
}
?>

<div class="content-wrapper-card">
    <div class="card-header">
        <h2 class="card-title">All Orders</h2>
    </div>
    <div class="card-body no-padding">
        <table class="table-modern">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Tracking No.</th>
                    <th class="text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                    <?php while($order = $orders_result->fetch_assoc()): ?>
                        <tr>
                            <form action="orders.php" method="POST">
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['full_name'] ?? 'Guest'); ?></td>
                                <td><?php echo date('d M Y', strtotime($order['order_date'])); ?></td>
                                <td>Rp <?php echo number_format($order['total_amount']); ?></td>
                                <td>
                                    <select name="status" class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                        <option value="pending" <?php if($order['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                                        <option value="confirmed" <?php if($order['status'] == 'confirmed') echo 'selected'; ?>>Confirmed</option>
                                        <option value="shipped" <?php if($order['status'] == 'shipped') echo 'selected'; ?>>Shipped</option>
                                        <option value="completed" <?php if($order['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                                        <option value="cancelled" <?php if($order['status'] == 'cancelled') echo 'selected'; ?>>Cancelled</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="resi_number" class="form-control-sm" value="<?php echo htmlspecialchars($order['resi_number'] ?? ''); ?>" placeholder="Input tracking number">
                                </td>
                                <td class="actions text-right">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <button type="submit" name="update_order" class="btn btn-primary btn-sm">Update</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="no-data">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer_admin.php'; ?>