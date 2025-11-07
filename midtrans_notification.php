<?php
// midtrans_notification.php (Versi Final dengan Perbaikan Array)

require_once 'includes/db_conn.php';
require_once 'includes/midtrans_config.php';

// Load autoloader Composer
require_once __DIR__ . '/vendor/autoload.php';

use Midtrans\Transaction;

// 1. Terima notifikasi mentah dari Midtrans
$raw_notification = json_decode(file_get_contents('php://input'), true);

// Periksa apakah order_id ada di notifikasi
if (!isset($raw_notification['order_id'])) {
    http_response_code(400);
    error_log("Invalid notification: Missing order_id.");
    die("Invalid notification: Missing order_id.");
}

$order_id_midtrans = $raw_notification['order_id'];

try {
    // 2. Verifikasi status transaksi langsung ke Midtrans
    $status_response = Transaction::status($order_id_midtrans);

} catch (Exception $e) {
    error_log("Midtrans Status Check Failed for order_id " . $order_id_midtrans . ": " . $e->getMessage());
    http_response_code(500);
    die("Midtrans status check failed.");
}

// 3. Konversi respons menjadi array untuk konsistensi
$status_array = json_decode(json_encode($status_response), true);

// Ambil data yang sudah terverifikasi menggunakan sintaks array
$transaction_status = $status_array['transaction_status'] ?? null;
$fraud_status = $status_array['fraud_status'] ?? null;
$transaction_id_midtrans = $status_array['transaction_id'] ?? null;

// Ambil ID order asli dari format "ID-timestamp"
$db_order_id = (int) explode('-', $order_id_midtrans)[0];

// Tentukan status baru di database
$new_status = '';
if (($transaction_status == 'capture' || $transaction_status == 'settlement') && ($fraud_status == 'accept')) {
    $new_status = 'confirmed';
} else if ($transaction_status == 'pending') {
    $new_status = 'pending';
} else if ($transaction_status == 'deny' || $transaction_status == 'cancel' || $transaction_status == 'expire') {
    $new_status = 'cancelled';
} else if ($fraud_status == 'challenge') {
    $new_status = 'pending'; // Anggap 'challenge' sebagai 'pending'
}

// 4. Proses utama: Update database HANYA jika status adalah 'confirmed'
if ($new_status === 'confirmed' && $db_order_id > 0) {
    
    $conn->begin_transaction();
    
    try {
        // Kunci baris order untuk mencegah proses ganda
        $stmt_check = $conn->prepare("SELECT status FROM orders WHERE id = ? FOR UPDATE");
        $stmt_check->bind_param("i", $db_order_id);
        $stmt_check->execute();
        $result = $stmt_check->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Order ID " . $db_order_id . " tidak ditemukan.");
        }
        
        $current_order_status = $result->fetch_assoc()['status'];
        $stmt_check->close();

        // PENTING: Hanya proses jika status order saat ini adalah 'pending'
        if ($current_order_status === 'pending') {
            
            // a. Ambil semua item dari pesanan
            $stmt_items = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
            $stmt_items->bind_param("i", $db_order_id);
            $stmt_items->execute();
            $order_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt_items->close();

            if (empty($order_items)) {
                throw new Exception("Tidak ada item ditemukan untuk Order ID " . $db_order_id);
            }
            
            // b. Kurangi stok untuk setiap item produk
            $stmt_update_stock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            foreach ($order_items as $item) {
                if ($item['product_id'] === NULL || $item['product_id'] <= 0) continue;
                
                $stmt_update_stock->bind_param("iii", $item['quantity'], $item['product_id'], $item['quantity']);
                if (!$stmt_update_stock->execute()) {
                    throw new Exception("Gagal mengurangi stok untuk produk ID: " . $item['product_id']);
                }
                if ($stmt_update_stock->affected_rows === 0) {
                    throw new Exception("Stok tidak mencukupi untuk product_id: " . $item['product_id']);
                }
            }
            $stmt_update_stock->close();

            // c. Update status pesanan menjadi 'confirmed'
            $stmt_update_order = $conn->prepare("UPDATE orders SET status = ?, transaction_id = ? WHERE id = ?");
            $stmt_update_order->bind_param("ssi", $new_status, $transaction_id_midtrans, $db_order_id);
            if (!$stmt_update_order->execute()) {
                throw new Exception("Gagal update status untuk Order ID: " . $db_order_id);
            }
            $stmt_update_order->close();
            
            $conn->commit();
            
        } else {
            $conn->rollback();
        }

    } catch (Exception $e) {
        $conn->rollback();
        error_log("Database Error untuk Order ID " . $db_order_id . ": " . $e->getMessage());
        http_response_code(500);
        die("Internal Server Error.");
    }
} else if (!empty($new_status) && $db_order_id > 0) {
    // Untuk status lain, cukup update status order saja.
    $stmt_update_status = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND status = 'pending'");
    $stmt_update_status->bind_param("si", $new_status, $db_order_id);
    $stmt_update_status->execute();
    $stmt_update_status->close();
}

// Beri tahu Midtrans bahwa notifikasi sudah diterima dengan baik
http_response_code(200);
echo "Notification processed successfully.";
?>