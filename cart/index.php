<?php
session_start();
require '../database/connection.php';

function redirect_cart() { header('Location: ../cart/'); exit; }

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    if($action === 'add' && $id){
        if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if(isset($_SESSION['cart'][$id])){
            $_SESSION['cart'][$id]['qty']++;
        } else {
            $res = $conn->query("SELECT * FROM products WHERE product_id=$id");
            if($prod = $res->fetch_assoc()){
                $_SESSION['cart'][$id] = ['id'=>$id, 'name'=>$prod['name'], 'price'=>$prod['price'], 'image'=>$prod['image'], 'qty'=>1];
            }
        }
    } elseif($action === 'update'){
        $qty = intval($_POST['qty'] ?? 1);
        if($qty <=0){
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id]['qty'] = $qty;
        }
    } elseif($action === 'remove'){
        unset($_SESSION['cart'][$id]);
    }
    redirect_cart();
}

if(isset($_GET['action']) && $_GET['action'] === 'update'){
    $id = intval($_GET['id']);
    $qty = intval($_GET['qty']);
    if($qty <= 0) {
        unset($_SESSION['cart'][$id]);
    } else {
        $_SESSION['cart'][$id]['qty']=$qty;
    }
    redirect_cart();
}
if(isset($_GET['action']) && $_GET['action'] === 'remove'){
    $id = intval($_GET['id']);
    unset($_SESSION['cart'][$id]);
    redirect_cart();
}

$cart = $_SESSION['cart'] ?? [];
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Your Cart - HamroPasal</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Your Cart</h1>
<?php if(empty($cart)): ?>
   <p>Your cart is empty.</p>
<?php else: ?>
<table>
<tr><th>Product</th><th>Qty</th><th>Price</th><th>Action</th></tr>
<?php $total = 0; foreach($cart as $item): ?>
<tr>
<td><?php echo htmlspecialchars($item['name']); ?></td>
<td>
  <input type="number" value="<?php echo $item['qty'];?>" min="1"
    onchange="updateQty(<?php echo $item['id']; ?>, this.value)">
</td>
<td>₹<?php echo $item['price'] * $item['qty']; ?></td>
<td><button onclick="removeItem(<?php echo $item['id']; ?>)">Remove</button></td>
</tr>
<?php $total += $item['price'] * $item['qty']; endforeach;?>
<tr><td colspan="2"></td><td><strong>Total: ₹<?php echo $total; ?></strong></td><td></td></tr>
</table>
<a href="../checkout/"><button>Proceed to Checkout</button></a>
<?php endif; ?>
</div>
<?php include '../partials/footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>