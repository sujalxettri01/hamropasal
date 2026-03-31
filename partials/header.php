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
    $msgResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM messages WHERE user_id=$userId AND is_read=0");
    $msgData = mysqli_fetch_assoc($msgResult);
    $unreadMessages = $msgData['count'];
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
  <link rel="stylesheet" href="/hamropasal/css/style.css">
  <title><?php echo isset($pageTitle) ? $pageTitle . ' - HamroPasal' : 'HamroPasal'; ?></title>
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="/hamropasal/">
      HamroPasal
    </a>
    
    <form class="search-form" method="get" action="/hamropasal/products/">
      <input type="text" name="q" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>" autocomplete="off">
      <button type="submit">Search</button>
      <div id="search-suggestions-container"></div>
    </form>
    
    <nav class="main-nav">
      <a href="/hamropasal/products/" title="Browse All Products">Products</a>
      <a href="/hamropasal/cart/" title="View Shopping Cart">
        Cart <span style="font-weight: bold; color: #2563eb;">(<?php echo $cartCount; ?>)</span>
      </a>
      <?php if (isset($_SESSION['user'])): ?>
        <a href="/hamropasal/orders/" title="Track Your Orders">Orders</a>
        <a href="/hamropasal/messages/" style="position: relative;" title="View Messages">
          Messages
          <?php if ($unreadMessages > 0): ?>
            <span style="position: absolute; top: -8px; right: -8px; background: #dc2626; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold;"><?php echo $unreadMessages; ?></span>
          <?php endif; ?>
        </a>
        <span class="welcome">Hi, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
        <a href="/hamropasal/logout/" title="Logout">Logout</a>
      <?php else: ?>
        <a href="/hamropasal/login/" title="Sign In">Login</a>
        <a href="/hamropasal/register/" title="Create Account">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
