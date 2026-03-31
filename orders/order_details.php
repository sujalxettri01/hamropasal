<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($orderId <= 0) {
    header('Location: /hamropasal/orders/');
    exit;
}

$userId = (int) $_SESSION['user']['user_id'];

// Redirect admins to admin order details page
if (!empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/order_details.php?id=' . $orderId);
    exit;
}

$orderQuery = "SELECT * FROM orders WHERE order_id=$orderId AND user_id=$userId LIMIT 1";
$orderResult = mysqli_query($conn, $orderQuery);
$order = ($orderResult) ? mysqli_fetch_assoc($orderResult) : null;

if (!$order) {
    header('Location: /hamropasal/orders/');
    exit;
}

$itemsResult = mysqli_query($conn, "
    SELECT oi.quantity, oi.unit_price, oi.line_total, p.name
    FROM order_items oi
    JOIN products p ON p.product_id = oi.product_id
    WHERE oi.order_id=$orderId
    ORDER BY oi.item_id ASC
");
$pageTitle = 'Order Details';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Order Details</h1>
    <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
      <p class="success-message">Thank you! Your order has been placed successfully.</p>
    <?php endif; ?>
    <p>Order #<?php echo (int) $order['order_id']; ?></p>
  </section>

  <div class="summary-box">
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
    <p><strong>Payment:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
    <p><strong>Total:</strong> Rs. <?php echo number_format((float) $order['total_amount'], 2); ?></p>
    <p><strong>Delivery Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
    <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Product</th>
          <th>Qty</th>
          <th>Unit Price</th>
          <th>Line Total</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($item = mysqli_fetch_assoc($itemsResult)): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['name']); ?></td>
            <td><?php echo (int) $item['quantity']; ?></td>
            <td>Rs. <?php echo number_format((float) $item['unit_price'], 2); ?></td>
            <td>Rs. <?php echo number_format((float) $item['line_total'], 2); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <p>
    <a class="btn" href="/hamropasal/orders/">Back to My Orders</a>
    <a class="btn" href="/hamropasal/orders/track.php?id=<?php echo (int) $order['order_id']; ?>">Track Order</a>
    <a class="btn secondary" href="/hamropasal/products/">Continue Shopping</a>
  </p>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>