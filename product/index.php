<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/../database/connection.php';
require __DIR__ . '/../config/image_helper.php';

// Redirect admins to admin panel
if (isset($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/admin.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /hamropasal/products/');
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM products WHERE product_id=$id AND is_active=1 LIMIT 1");
$product = ($result) ? mysqli_fetch_assoc($result) : null;

if (!$product) {
    header('Location: /hamropasal/products/');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['name']); ?> - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <div class="form-card" style="max-width: 920px;">
    <div style="display: grid; grid-template-columns: minmax(240px, 320px) 1fr; gap: 18px; align-items: start;">
      <img src="<?php echo getProductDetailImageUrl($product['name'], $product['category'], $product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width:100%; border-radius: 10px; max-height: 320px; object-fit: cover;">
      <div>
        <h1><?php echo htmlspecialchars($product['name']); ?></h1>
        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
        <p class="price">Rs. <?php echo number_format((float) $product['price'], 2); ?></p>
        <p><strong>Stock:</strong> <?php echo (int) $product['stock']; ?></p>
        <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

        <form method="post" action="/hamropasal/cart/">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="id" value="<?php echo (int) $product['product_id']; ?>">
          <label for="qty">Quantity</label>
          <input id="qty" type="number" name="qty" min="1" max="<?php echo (int) $product['stock']; ?>" value="1" style="max-width: 110px;">
          <button type="submit">Add to Cart</button>
        </form>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
