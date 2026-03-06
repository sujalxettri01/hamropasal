<?php
session_start();
require '../database/connection.php';
$id = intval($_GET['id'] ?? 0);
$order = null;
if($id){
    $res = $conn->query("SELECT * FROM orders WHERE order_id=$id");
    $order = $res->fetch_assoc();
}
if(!$order){
    header('Location: ../');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Order Placed</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Thank you!</h1>
<p>Your order #<?php echo $order['order_id']; ?> has been placed.</p>
<a href="../">Continue shopping</a>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>