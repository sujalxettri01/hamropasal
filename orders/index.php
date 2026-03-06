<?php
session_start();
require '../database/connection.php';
if(!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']){
    header('Location: ../login/');
    exit;
}
$orders = $conn->query("SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id=u.user_id ORDER BY o.order_date DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Orders</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Orders</h1>
<table>
<tr><th>ID</th><th>User</th><th>Date</th><th>Total</th></tr>
<?php while($o=$orders->fetch_assoc()): ?>
<tr>
<td><?php echo $o['order_id']; ?></td>
<td><?php echo htmlspecialchars($o['name']); ?></td>
<td><?php echo $o['order_date']; ?></td>
<td>₹<?php echo $o['total_amount']; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>