<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /hamropasal/admin/orders.php');
    exit;
}

$orderResult = mysqli_query($conn, "SELECT o.*, u.name AS user_name, u.email FROM orders o JOIN users u ON u.user_id=o.user_id WHERE o.order_id=$id LIMIT 1");
$order = ($orderResult) ? mysqli_fetch_assoc($orderResult) : null;
if (!$order) {
    header('Location: /hamropasal/admin/orders.php');
    exit;
}

$itemsResult = mysqli_query($conn, "SELECT oi.*, p.name AS product_name FROM order_items oi JOIN products p ON p.product_id=oi.product_id WHERE oi.order_id=$id");
$pageTitle = 'Order Details';
?>
<?php include __DIR__ . '/admin_header.php'; ?>
<div class="admin-container">
  <section style="margin: 40px 0;">
    <h1>Order #<?php echo (int) $order['order_id']; ?> Details</h1>
    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
      <p style="color: #28a745; font-weight: bold; margin-bottom: 20px;">Order has been placed successfully!</p>
    <?php endif; ?>
    <?php if (isset($_GET['cancelled']) && $_GET['cancelled'] === '1'): ?>
      <p style="color: #28a745; font-weight: bold; margin-bottom: 20px;">✓ Order cancelled successfully! Customer has been notified.</p>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
      <p style="color: #dc3545; font-weight: bold; margin-bottom: 20px;">⚠ Error: <?php echo htmlspecialchars($_GET['error']); ?></p>
    <?php endif; ?>
  </section>

  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 40px;">
    <div class="stat-card">
      <h3>Customer Information</h3>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_name']); ?> (<?php echo htmlspecialchars($order['email']); ?>)</p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
      <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
    </div>

    <div class="stat-card">
      <h3>Order Information</h3>
      <p><strong>Order ID:</strong> #<?php echo (int) $order['order_id']; ?></p>
      <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
      <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
      <p><strong>Total:</strong> Rs. <?php echo number_format((float) $order['total_amount'], 2); ?></p>
      <p><strong>Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
    </div>
  </div>

  <h2>Order Items</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Product</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($item = mysqli_fetch_assoc($itemsResult)): ?>
        <tr>
          <td><?php echo htmlspecialchars($item['product_name']); ?></td>
          <td><?php echo (int) $item['quantity']; ?></td>
          <td>Rs. <?php echo number_format((float) $item['unit_price'], 2); ?></td>
          <td>Rs. <?php echo number_format((float) $item['line_total'], 2); ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <?php if ($order['order_status'] !== 'Cancelled' && $order['order_status'] !== 'Delivered'): ?>
    <section id="cancel-section" style="margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #dc3545;">
      <h3 style="margin-top: 0; color: #dc3545;">Cancel Order</h3>
      <p style="color: #666; margin-bottom: 20px;">If you need to cancel this order, provide a reason that will be sent to the customer:</p>
      <form method="POST" action="/hamropasal/admin/cancel_order.php" onsubmit="return confirm('Are you sure you want to cancel this order and notify the customer?');">
        <input type="hidden" name="order_id" value="<?php echo (int) $order['order_id']; ?>">
        <div style="margin-bottom: 15px;">
          <label for="cancel_reason" style="display: block; margin-bottom: 8px; font-weight: bold;">Cancellation Reason:</label>
          <textarea id="cancel_reason" name="cancel_reason" required style="width: 100%; min-height: 100px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-family: Arial, sans-serif; resize: vertical;" placeholder="e.g., Out of stock, Customer request, Payment issue, etc."></textarea>
        </div>
        <button type="submit" style="background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">Cancel Order & Notify Customer</button>
      </form>
    </section>
  <?php endif; ?>

  <p style="margin-top: 20px;">
    <a class="admin-btn secondary" href="/hamropasal/admin/orders.php">Back to Orders</a>
  </p>
</div>
<?php include __DIR__ . '/admin_footer.php'; ?>