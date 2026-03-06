<?php
require '../database/connection.php';
$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>All Products - HamroPasal</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
  <h1>Products</h1>
  <div class="product-grid">
    <?php while($p = $products->fetch_assoc()): ?>
      <div class="product-card">
        <a href="../product/?id=<?php echo $p['product_id']; ?>">
          <img src="<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
          <h3><?php echo htmlspecialchars($p['name']); ?></h3>
          <p>₹<?php echo $p['price']; ?></p>
        </a>
        <form method="post" action="../cart/">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="<?php echo $p['product_id']; ?>">
          <button type="submit">Add to Cart</button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<?php include '../partials/footer.php'; ?>
<script src="../js/script.js"></script>
</body>
</html>