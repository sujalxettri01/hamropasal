<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cartCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int) $item['qty'];
    }
}
?>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="/hamropasal/">HamroPasal</a>
    <nav class="main-nav">
      <a href="/hamropasal/products/">Products</a>
      <a href="/hamropasal/cart/">Cart (<?php echo $cartCount; ?>)</a>
      <?php if (isset($_SESSION['user'])): ?>
        <a href="/hamropasal/orders/">My Orders</a>
        <?php if (!empty($_SESSION['user']['is_admin'])): ?>
          <a href="/hamropasal/admin/dashboard.php">Admin</a>
        <?php endif; ?>
        <span class="welcome">Hi, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
        <a href="/hamropasal/logout/">Logout</a>
      <?php else: ?>
        <a href="/hamropasal/login/">Login</a>
        <a href="/hamropasal/register/">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
