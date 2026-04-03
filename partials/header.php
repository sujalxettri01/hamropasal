<?php
// Session is already started by the including page
$cartCount = 0;
$unreadMessages = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartCount += (int) $item['qty'];
    }
}
// Get unread messages count if user is logged in
if (isset($_SESSION['user']['user_id'])) {
    if (!isset($conn)) {
        require __DIR__ . '/../database/connection.php';
    }
    $userId = (int) $_SESSION['user']['user_id'];
    $stmt = mysqli_prepare($conn, "SELECT COUNT(*) as count FROM messages WHERE user_id=? AND is_read=0");
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $msgResult = mysqli_stmt_get_result($stmt);
    $msgData = mysqli_fetch_assoc($msgResult);
    $unreadMessages = $msgData['count'];
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="/hamropasal/css/style.css">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' - HamroPasal' : 'HamroPasal'; ?></title>
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="/hamropasal/">
      <i class="fas fa-store"></i> HamroPasal
    </a>
    
    <form class="search-form" method="get" action="/hamropasal/products/">
      <input type="text" name="q" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off">
      <button type="submit"><i class="fas fa-search"></i></button>
      <div id="search-suggestions-container"></div>
    </form>
    
    <button class="hamburger" aria-label="Toggle menu">
      <i class="fas fa-bars"></i>
    </button>
    
    <nav class="main-nav">
      <a href="/hamropasal/products/" title="Browse All Products"><i class="fas fa-boxes"></i> Products</a>
      <a href="/hamropasal/cart/" title="View Shopping Cart">
        <i class="fas fa-shopping-cart"></i> Cart <span class="cart-count">(<?php echo $cartCount; ?>)</span>
      </a>
      <?php if (isset($_SESSION['user'])): ?>
        <a href="/hamropasal/orders/" title="Track Your Orders"><i class="fas fa-list"></i> Orders</a>
        <a href="/hamropasal/messages/" class="messages-link" title="View Messages">
          <i class="fas fa-envelope"></i> Messages
          <?php if ($unreadMessages > 0): ?>
            <span class="badge"><?php echo $unreadMessages; ?></span>
          <?php endif; ?>
        </a>
        <span class="welcome">Hi, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
        <a href="/hamropasal/logout/" title="Logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
      <?php else: ?>
        <a href="/hamropasal/login/" title="Sign In"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="/hamropasal/register/" title="Create Account"><i class="fas fa-user-plus"></i> Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<script src="/hamropasal/js/script.js"></script>
