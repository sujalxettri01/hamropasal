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

  <p style="margin-top: 20px;">
    <a class="admin-btn secondary" href="/hamropasal/admin/orders.php">Back to Orders</a>
  </p>
</div>
<?php include __DIR__ . '/admin_footer.php'; ?>