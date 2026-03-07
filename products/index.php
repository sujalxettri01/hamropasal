<?php
session_start();
require __DIR__ . '/../database/connection.php';

$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');

$conditions = ["is_active=1"];

if ($search !== '') {
    $searchEscaped = mysqli_real_escape_string($conn, $search);
    $conditions[] = "(name LIKE '%$searchEscaped%' OR description LIKE '%$searchEscaped%')";
}
if ($category !== '') {
    $categoryEscaped = mysqli_real_escape_string($conn, $category);
    $conditions[] = "category='$categoryEscaped'";
}

$where = implode(' AND ', $conditions);
$products = mysqli_query($conn, "SELECT * FROM products WHERE $where ORDER BY created_at DESC");
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE is_active=1 ORDER BY category ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Products - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>All Products</h1>
    <p>Find groceries, kitchen essentials, and home-care items.</p>
  </section>

  <div class="form-card">
    <form method="get">
      <label for="q">Search Products</label>
      <input id="q" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Rice, tea, detergent...">

      <label for="category">Category</label>
      <select id="category" name="category">
        <option value="">All categories</option>
        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
          <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo ($category === $cat['category']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($cat['category']); ?>
          </option>
        <?php endwhile; ?>
      </select>

      <button type="submit">Filter</button>
    </form>
  </div>

  <div class="grid">
    <?php while ($p = mysqli_fetch_assoc($products)): ?>
      <article class="card">
        <a href="/hamropasal/product/?id=<?php echo (int) $p['product_id']; ?>">
          <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>">
        </a>
        <div class="card-body">
          <h3><?php echo htmlspecialchars($p['name']); ?></h3>
          <p><?php echo htmlspecialchars($p['category']); ?></p>
          <p class="price">Rs. <?php echo number_format((float) $p['price'], 2); ?></p>
          <p>Stock: <?php echo (int) $p['stock']; ?></p>
          <form method="post" action="/hamropasal/cart/">
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="id" value="<?php echo (int) $p['product_id']; ?>">
            <button type="submit">Add to Cart</button>
          </form>
        </div>
      </article>
    <?php endwhile; ?>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
