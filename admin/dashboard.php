<?php
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$totalProducts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"));
$totalOrders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"));
$totalUsers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users"));
$totalSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(total_amount),0) AS total FROM orders WHERE order_status <> 'Cancelled'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Admin Dashboard</h1>
  </section>

  <div class="grid">
    <div class="summary-box"><h3>Products</h3><p><?php echo (int) $totalProducts['total']; ?></p></div>
    <div class="summary-box"><h3>Orders</h3><p><?php echo (int) $totalOrders['total']; ?></p></div>
    <div class="summary-box"><h3>Users</h3><p><?php echo (int) $totalUsers['total']; ?></p></div>
    <div class="summary-box"><h3>Sales</h3><p>Rs. <?php echo number_format((float) $totalSales['total'], 2); ?></p></div>
  </div>

  <p>
    <a class="btn" href="/hamropasal/admin/manage_products.php">Manage Products</a>
    <a class="btn secondary" href="/hamropasal/orders/">Manage Orders</a>
  </p>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
