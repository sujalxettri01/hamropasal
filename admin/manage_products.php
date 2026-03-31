<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/login.php');
    exit;
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    if ($id > 0) {
        // First delete related order items (child records)
        mysqli_query($conn, "DELETE FROM order_items WHERE product_id=$id");
        // Then delete the product
        mysqli_query($conn, "DELETE FROM products WHERE product_id=$id");
    }
    header('Location: /hamropasal/admin/manage_products.php');
    exit;
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY product_id DESC");
$pageTitle = 'Manage Products';
?>
<?php include __DIR__ . '/admin_header.php'; ?>
<div class="admin-container">
  <section style="margin: 40px 0;">
    <h1>Manage Products</h1>
  </section>

  <p>
    <a class="admin-btn" href="/hamropasal/admin/add_product.php">Add New Product</a>
    <a class="admin-btn secondary" href="/hamropasal/admin/admin.php">Back to Dashboard</a>
  </p>

  <table class="admin-table">
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
            <a class="admin-btn" href="/hamropasal/admin/edit_product.php?id=<?php echo (int) $p['product_id']; ?>">Edit</a>
            <a class="admin-btn danger delete-btn" href="/hamropasal/admin/manage_products.php?delete=<?php echo (int) $p['product_id']; ?>">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/admin_footer.php'; ?>
