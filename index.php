<?php
session_start();
require __DIR__ . '/database/connection.php';

$featuredQuery = mysqli_query($conn, "SELECT * FROM products WHERE is_active=1 ORDER BY created_at DESC LIMIT 8");
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>HamroPasal</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Your Neighborhood General Store, Online</h1>
    <p>Daily essentials, groceries, snacks, and home-care products delivered fast.</p>
  </section>

  <h2>Featured Products</h2>
  <div class="grid">
    <?php while ($p = mysqli_fetch_assoc($featuredQuery)): ?>
      <article class="card">
        <a href="/hamropasal/product/?id=<?php echo (int) $p['product_id']; ?>">
          <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
        </a>
        <div class="card-body">
          <h3><a href="/hamropasal/product/?id=<?php echo (int) $p['product_id']; ?>"><?php echo htmlspecialchars($p['name']); ?></a></h3>
          <p><?php echo htmlspecialchars($p['category']); ?></p>
          <p class="price">Rs. <?php echo number_format((float) $p['price'], 2); ?></p>
          <form method="post" action="/hamropasal/cart/">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?php echo (int) $p['product_id']; ?>">
            <button type="submit">Add to Cart</button>
          </form>
        </div>
      </article>
    <?php endwhile; ?>
  </div>

  <p><a class="btn" href="/hamropasal/products/">Browse All Products</a></p>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
<script src="/hamropasal/js/script.js"></script>
</body>
</html>