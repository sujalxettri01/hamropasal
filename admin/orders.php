<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/login.php');
    exit;
}

if (isset($_GET['set_status'], $_GET['id'])) {
    $allowed = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    $status = trim($_GET['set_status']);
    $orderId = (int) $_GET['id'];
    if (in_array($status, $allowed, true) && $orderId > 0) {
        $statusEsc = mysqli_real_escape_string($conn, $status);
        
        // Get order details for the message
        $orderCheck = mysqli_query($conn, "SELECT user_id FROM orders WHERE order_id=$orderId LIMIT 1");
        $orderData = mysqli_fetch_assoc($orderCheck);
        
        if ($orderData) {
            // Update order status
            mysqli_query($conn, "UPDATE orders SET order_status='$statusEsc' WHERE order_id=$orderId");
            
            // Create status messages for customer notification
            $messages = [
                'Pending' => 'Your order has been received and is pending confirmation.',
                'Processing' => 'Your order is being processed. We will ship it soon!',
                'Shipped' => 'Great news! Your order has been shipped. You can expect it soon.',
                'Delivered' => 'Your order has been delivered. Thank you for shopping with us!',
                'Cancelled' => 'Your order has been cancelled.'
            ];
            
            $messageText = isset($messages[$status]) ? $messages[$status] : 'Your order status has been updated to: ' . $status;
            $messageTextEsc = mysqli_real_escape_string($conn, $messageText);
            $userId = (int) $orderData['user_id'];
            
            // Insert message notification
            mysqli_query($conn, "INSERT INTO messages (user_id, order_id, message_type, message_text) 
                                VALUES ($userId, $orderId, '$statusEsc', '$messageTextEsc')");
        }
    }
    header('Location: /hamropasal/admin/orders.php');
    exit;
}

$orders = mysqli_query($conn, "SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.user_id=o.user_id ORDER BY o.created_at DESC");
$pageTitle = 'Manage Orders';
?>
<?php include __DIR__ . '/admin_header.php'; ?>
<div class="admin-container">
  <section style="margin: 40px 0;">
    <h1>Manage Orders</h1>
  </section>

  <table class="admin-table">
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Payment</th>
        <th>Status</th>
        <th>Total</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($order = mysqli_fetch_assoc($orders)): ?>
        <tr>
          <td><?php echo (int) $order['order_id']; ?></td>
          <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
          <td><?php echo htmlspecialchars($order['phone']); ?></td>
          <td><?php echo htmlspecialchars(substr($order['address'], 0, 30)) . (strlen($order['address']) > 30 ? '...' : ''); ?></td>
          <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
          <td>
            <select onchange="updateStatus(<?php echo (int) $order['order_id']; ?>, this.value)">
              <option value="Pending" <?php echo $order['order_status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
              <option value="Processing" <?php echo $order['order_status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
              <option value="Shipped" <?php echo $order['order_status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
              <option value="Delivered" <?php echo $order['order_status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
              <option value="Cancelled" <?php echo $order['order_status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
          </td>
          <td>Rs. <?php echo number_format((float) $order['total_amount'], 2); ?></td>
          <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
          <td>
            <a class="admin-btn" href="/hamropasal/admin/order_details.php?id=<?php echo (int) $order['order_id']; ?>">View Details</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/admin_footer.php'; ?>
<script>
function updateStatus(orderId, status) {
    if (confirm('Update order status to ' + status + '?')) {
        window.location.href = '/hamropasal/admin/orders.php?set_status=' + encodeURIComponent(status) + '&id=' + orderId;
    } else {
        // Reset select to original value
        location.reload();
    }
}
</script>