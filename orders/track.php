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

// Redirect admins to admin tracking (if you have one)
if (!empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/order_details.php?id=' . $orderId);
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 'ii', $orderId, $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$order = $result ? mysqli_fetch_assoc($result) : null;

if (!$order) {
    header('Location: /hamropasal/orders/');
    exit;
}

$steps = [
    ['key' => 'Pending', 'label' => 'Ordered'],
    ['key' => 'Processing', 'label' => 'Packed'],
    ['key' => 'Shipped', 'label' => 'Shipped'],
    ['key' => 'Delivered', 'label' => 'Delivered'],
];

$currentStatus = $order['order_status'];
$currentIndex = array_search($currentStatus, array_column($steps, 'key'));

$pageTitle = 'Track Order';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Track Order</h1>
    <p>Order #<?php echo (int) $order['order_id']; ?> — Status: <strong><?php echo htmlspecialchars($order['order_status']); ?></strong></p>
  </section>

  <div class="summary-box">
    <p><strong>Delivery Address:</strong> <?php echo nl2br(htmlspecialchars($order['address'])); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['phone']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
    <p><strong>Total:</strong> Rs. <?php echo number_format((float) $order['total_amount'], 2); ?></p>
    <p><strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></p>
  </div>

  <div class="order-tracker">
    <?php if ($currentStatus === 'Cancelled'): ?>
      <div class="tracker-cancelled">
        <div class="tracker-marker cancelled">X</div>
        <div class="tracker-label">Order Cancelled</div>
      </div>
    <?php else: ?>
      <?php foreach ($steps as $index => $step):
        $isComplete = $index <= $currentIndex;
        $isActive = $index === $currentIndex;
      ?>
        <div class="tracker-step <?php echo $isComplete ? 'complete' : ''; ?> <?php echo $isActive ? 'active' : ''; ?>">
          <div class="tracker-marker"><?php echo $isComplete ? 'Done' : ($isActive ? 'In Progress' : ($index + 1)); ?></div>
          <div class="tracker-label"><?php echo htmlspecialchars($step['label']); ?></div>
          <?php if ($index < count($steps) - 1): ?>
            <div class="tracker-line"></div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <p style="margin-top: 26px;">
    <a class="btn" href="/hamropasal/orders/">Back to My Orders</a>
    <a class="btn secondary" href="/hamropasal/products/">Continue Shopping</a>
  </p>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
