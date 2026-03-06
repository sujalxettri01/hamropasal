<?php
require '../database/connection.php';
$id = intval($_GET['id'] ?? 0);
$product = null;
if($id){
   $res = $conn->query("SELECT * FROM products WHERE product_id=$id");
   $product = $res->fetch_assoc();
}
if(!$product){
   header('Location: ../products/');
   exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($product['name']); ?> - HamroPasal</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
  <h1><?php echo htmlspecialchars($product['name']); ?></h1>
  <img src="<?php echo $product['image']; ?>" alt="" style="max-width:300px;">
  <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
  <p>Price: ₹<?php echo $product['price']; ?></p>
  <form method="post" action="../cart/">
    <input type="hidden" name="action" value="add">
    <input type="hidden" name="id" value="<?php echo $product['product_id']; ?>">
    <button type="submit">Add to Cart</button>
  </form>
</div>
<?php include '../partials/footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>