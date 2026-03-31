<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/login.php');
    exit;
}

$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"));
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"));
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));
$totalSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) AS total FROM orders WHERE order_status <> 'Cancelled'"));
$pageTitle = 'Dashboard';
?>
<?php include __DIR__ . '/admin_header.php'; ?>
<div class="admin-container">
  <section style="margin: 40px 0;">
    <h1>Admin Dashboard</h1>
  </section>

  <div class="dashboard-grid">
    <div class="stat-card">
      <h3><?php echo (int) $totalProducts['total']; ?></h3>
      <p>Total Products</p>
    </div>
    <div class="stat-card">
      <h3><?php echo (int) $totalOrders['total']; ?></h3>
      <p>Total Orders</p>
    </div>
    <div class="stat-card">
      <h3><?php echo (int) $totalUsers['total']; ?></h3>
      <p>Total Users</p>
    </div>
    <div class="stat-card">
      <h3>Rs. <?php echo number_format((float) $totalSales['total'], 2); ?></h3>
      <p>Total Sales</p>
    </div>
  </div>

  <p>
    <a class="admin-btn" href="/hamropasal/admin/manage_products.php">Manage Products</a>
    <a class="admin-btn secondary" href="/hamropasal/admin/orders.php">Manage Orders</a>
  </p>
</div>
<?php include __DIR__ . '/admin_footer.php'; ?>
