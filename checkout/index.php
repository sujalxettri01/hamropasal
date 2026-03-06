<?php
session_start();
require '../database/connection.php';

if(!isset($_SESSION['user'])){
    header('Location: ../login/?redirect=../checkout/');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if(empty($cart)){
    header('Location: ../cart/');
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $address = $conn->real_escape_string($_POST['address']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $payment = $conn->real_escape_string($_POST['payment']);

    $total = 0;
    foreach($cart as $item) $total += $item['price'] * $item['qty'];

    $uid = $_SESSION['user']['user_id'];
    $conn->query("INSERT INTO orders (user_id, total_amount) VALUES ($uid, $total)");
    $order_id = $conn->insert_id;
    foreach($cart as $item){
        $pid = $item['id'];
        $qty = $item['qty'];
        $price = $item['price'];
        $conn->query("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id,$pid,$qty,$price)");
    }
    unset($_SESSION['cart']);
    header("Location: ../order_success/?id=$order_id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout - HamroPasal</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Checkout</h1>
<form method="post">
<label>Name</label><input type="text" name="name" required><br>
<label>Address</label><textarea name="address" required></textarea><br>
<label>Phone</label><input type="text" name="phone" required><br>
<label>Payment</label>
<select name="payment">
<option value="cod">Cash on Delivery</option>
<option value="card">Credit/Debit Card</option>
</select><br>
<button type="submit">Place Order</button>
</form>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>