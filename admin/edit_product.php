<?php
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /hamropasal/admin/manage_products.php');
    exit;
}

$productResult = mysqli_query($conn, "SELECT * FROM products WHERE product_id=$id LIMIT 1");
$product = ($productResult) ? mysqli_fetch_assoc($productResult) : null;
if (!$product) {
    header('Location: /hamropasal/admin/manage_products.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $image = trim($_POST['image'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($name === '' || $category === '' || $price <= 0) {
        $error = 'Name, category and valid price are required.';
    } else {
        if ($image === '') {
            $image = 'https://via.placeholder.com/300x220?text=Product';
        }

        $nameEsc = mysqli_real_escape_string($conn, $name);
        $catEsc = mysqli_real_escape_string($conn, $category);
        $descEsc = mysqli_real_escape_string($conn, $description);
        $imageEsc = mysqli_real_escape_string($conn, $image);

        $sql = "UPDATE products
                SET name='$nameEsc', category='$catEsc', description='$descEsc', price=$price, stock=$stock, image='$imageEsc', is_active=$isActive
                WHERE product_id=$id";

        if (mysqli_query($conn, $sql)) {
            header('Location: /hamropasal/admin/manage_products.php');
            exit;
        } else {
            $error = 'Failed to update product.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Product - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <div class="form-card">
    <h1>Edit Product</h1>
    <?php if ($error !== ''): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form method="post">
      <label for="name">Name</label>
      <input id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

      <label for="category">Category</label>
      <input id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>

      <label for="price">Price</label>
      <input id="price" name="price" type="number" min="1" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>

      <label for="stock">Stock</label>
      <input id="stock" name="stock" type="number" min="0" value="<?php echo (int) $product['stock']; ?>" required>

      <label for="image">Image URL</label>
      <input id="image" name="image" type="text" value="<?php echo htmlspecialchars($product['image']); ?>">

      <label for="description">Description</label>
      <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>

      <label><input type="checkbox" name="is_active" <?php echo ((int) $product['is_active'] === 1) ? 'checked' : ''; ?> style="width:auto; margin-right: 8px;"> Active</label>

      <button type="submit">Update Product</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
