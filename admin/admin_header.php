<?php
// Session is already started by the including page
$isAdminLoggedIn = isset($_SESSION['user']) && !empty($_SESSION['user']['is_admin']);
$adminName = $isAdminLoggedIn ? ($_SESSION['user']['name'] ?? 'Admin') : 'Admin';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/hamropasal/css/admin.css">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' - Admin' : 'Admin Panel'; ?></title>
</head>
<body>
<header class="admin-header">
  <div class="admin-container">
    <a class="admin-brand" href="/hamropasal/admin/admin.php">Admin Panel</a>
    <nav class="admin-nav">
      <?php if ($isAdminLoggedIn): ?>
        <a href="/hamropasal/admin/admin.php">Dashboard</a>
        <a href="/hamropasal/admin/manage_products.php">Products</a>
        <a href="/hamropasal/admin/add_product.php">Add Product</a>
        <a href="/hamropasal/admin/orders.php">Orders</a>
        <span class="admin-welcome">Hi, <?php echo htmlspecialchars($adminName); ?></span>
        <a href="/hamropasal/logout/">Logout</a>
      <?php else: ?>
        <a href="/hamropasal/admin/login.php">Login</a>
        <a href="/hamropasal/">Back to Store</a>
      <?php endif; ?>
    </nav>
  </div>
</header>