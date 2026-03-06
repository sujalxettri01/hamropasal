<?php
if(!isset($_SESSION)) session_start();
$cartCount = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $item){
        $cartCount += $item['qty'];
    }
}
?>
<header>
  <div class="logo"><a href="/hamropasal/">HamroPasal</a></div>
  <nav>
    <a href="/hamropasal/products/">Products</a>
    <?php if(isset($_SESSION['user'])): ?>
      <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
      <a href="/hamropasal/logout/">Logout</a>
    <?php else: ?>
      <a href="/hamropasal/login/">Login</a>
      <a href="/hamropasal/register/">Sign Up</a>
    <?php endif; ?>
    <a href="/hamropasal/cart/">Cart(<?php echo $cartCount; ?>)</a>
    <?php if(isset($_SESSION['user']) && $_SESSION['user']['is_admin']): ?>
      <a href="/hamropasal/admin/dashboard.php">Admin</a>
    <?php endif; ?>
  </nav>
</header>