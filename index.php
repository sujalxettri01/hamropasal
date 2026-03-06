<?php
require 'database/connection.php';
$featuredQuery = $conn->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>HamroPasal</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'partials/header.php'; ?>
<div class="container">
  <h1>Welcome to HamroPasal</h1>
  <h2>Featured Products</h2>
  <div class="product-grid">
    <?php while($p = $featuredQuery->fetch_assoc()): ?>
      <div class="product-card">
        <a href="/hamropasal/product/?id=<?php echo $p['product_id']; ?>">
          <img src="<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
          <h3><?php echo htmlspecialchars($p['name']); ?></h3>
          <p>₹<?php echo $p['price']; ?></p>
        </a>
        <form method="post" action="/hamropasal/cart/">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="<?php echo $p['product_id']; ?>">
          <button type="submit">Add to Cart</button>
        </form>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<?php include 'partials/footer.php'; ?>
<script src="/hamropasal/js/script.js"></script>
</body>
</html>