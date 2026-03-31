<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$userId = (int) $_SESSION['user']['user_id'];

// Redirect admins to admin orders page
if (!empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/orders.php');
    exit;
}

$orderSql = "SELECT o.*, u.name AS user_name FROM orders o JOIN users u ON u.user_id=o.user_id WHERE o.user_id=$userId ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $orderSql);
$pageTitle = 'My Orders';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>My Orders</h1>
  </section>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Phone</th>
          <th>Address</th>
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
            <td><?php echo htmlspecialchars($order['phone']); ?></td>
            <td><?php echo htmlspecialchars(substr($order['address'], 0, 30)) . (strlen($order['address']) > 30 ? '...' : ''); ?></td>
            <td>Rs. <?php echo number_format((float) $order['total_amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
            <td><?php echo htmlspecialchars($order['order_status']); ?></td>
            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
            <td>
              <a class="btn" href="/hamropasal/orders/order_details.php?id=<?php echo (int) $order['order_id']; ?>">View Details</a>
              <a class="btn secondary" href="/hamropasal/orders/track.php?id=<?php echo (int) $order['order_id']; ?>">Track</a>
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
