<?php
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/login/');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        mysqli_query($conn, "DELETE FROM products WHERE product_id=$id");
    }
    header('Location: /hamropasal/admin/manage_products.php');
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Manage Products</h1>
  </section>

  <p>
    <a class="btn" href="/hamropasal/admin/add_product.php">Add New Product</a>
    <a class="btn secondary" href="/hamropasal/admin/dashboard.php">Back to Dashboard</a>
  </p>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Category</th>
          <th>Price</th>
          <th>Stock</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($p = mysqli_fetch_assoc($products)): ?>
          <tr>
            <td><?php echo (int) $p['product_id']; ?></td>
            <td><?php echo htmlspecialchars($p['name']); ?></td>
            <td><?php echo htmlspecialchars($p['category']); ?></td>
            <td>Rs. <?php echo number_format((float) $p['price'], 2); ?></td>
            <td><?php echo (int) $p['stock']; ?></td>
            <td><?php echo ((int) $p['is_active'] === 1) ? 'Active' : 'Hidden'; ?></td>
            <td>
              <a class="btn" href="/hamropasal/admin/edit_product.php?id=<?php echo (int) $p['product_id']; ?>">Edit</a>
              <a class="btn danger" href="/hamropasal/admin/manage_products.php?delete=<?php echo (int) $p['product_id']; ?>" onclick="return confirm('Delete this product?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
