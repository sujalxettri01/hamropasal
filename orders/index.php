<?php
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$userId = (int) $_SESSION['user']['user_id'];
$isAdmin = !empty($_SESSION['user']['is_admin']);

if ($isAdmin && isset($_GET['set_status'], $_GET['id'])) {
    $allowed = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    $status = trim($_GET['set_status']);
    $orderId = (int) $_GET['id'];
    if (in_array($status, $allowed, true) && $orderId > 0) {
        $statusEsc = mysqli_real_escape_string($conn, $status);
        mysqli_query($conn, "UPDATE orders SET order_status='$statusEsc' WHERE order_id=$orderId");
    }
    header('Location: /hamropasal/orders/');
    exit;
}

$orderSql = "SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.user_id=o.user_id";
if (!$isAdmin) {
    $orderSql .= " WHERE o.user_id=$userId";
}
$orderSql .= " ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $orderSql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Orders - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1><?php echo $isAdmin ? 'All Orders' : 'My Orders'; ?></h1>
  </section>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <?php if ($isAdmin): ?><th>Customer</th><?php endif; ?>
          <th>Phone</th>
          <th>Total</th>
          <th>Payment</th>
          <th>Status</th>
          <th>Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($order = mysqli_fetch_assoc($orders)): ?>
          <tr>
            <td>#<?php echo (int) $order['order_id']; ?></td>
            <?php if ($isAdmin): ?><td><?php echo htmlspecialchars($order['user_name']); ?></td><?php endif; ?>
            <td><?php echo htmlspecialchars($order['phone']); ?></td>
            <td>Rs. <?php echo number_format((float) $order['total_amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
            <td><?php echo htmlspecialchars($order['order_status']); ?></td>
            <td><?php echo htmlspecialchars($order['created_at']); ?></td>
            <td>
              <a class="btn" href="/hamropasal/order_success/?id=<?php echo (int) $order['order_id']; ?>">View</a>
              <?php if ($isAdmin): ?>
                <a class="btn secondary" href="/hamropasal/orders/?id=<?php echo (int) $order['order_id']; ?>&set_status=Processing">Processing</a>
                <a class="btn secondary" href="/hamropasal/orders/?id=<?php echo (int) $order['order_id']; ?>&set_status=Delivered">Delivered</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
