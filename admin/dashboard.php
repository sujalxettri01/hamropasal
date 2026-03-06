<?php
session_start();
require '../database/connection.php';
if(!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']){
    header('Location: ../login.php');
    exit;
}
$res1 = $conn->query("SELECT COUNT(*) as cnt FROM products");
$total_products = $res1->fetch_assoc()['cnt'];
$res2 = $conn->query("SELECT COUNT(*) as cnt FROM orders");
$total_orders = $res2->fetch_assoc()['cnt'];
?>
<!DOCTYPE html>
<html>
<head><title>Admin Dashboard</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Admin Dashboard</h1>
<p>Total products: <?php echo $total_products; ?></p>
<p>Total orders: <?php echo $total_orders; ?></p>
<p><a href="manage_products.php">Manage Products</a></p>
<p><a href="../orders.php">View Orders</a></p>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>