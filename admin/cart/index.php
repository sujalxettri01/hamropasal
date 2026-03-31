<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/../database/connection.php';

// Redirect admins to admin panel
if (isset($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/admin.php');
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Clean up invalid cart items
foreach ($_SESSION['cart'] as $id => $item) {
    if (!is_array($item) || !isset($item['id'], $item['name'], $item['price'], $item['qty'], $item['stock'])) {
        unset($_SESSION['cart'][$id]);
    }
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = '';

if ($action === 'add') {
    $id = (int) ($_POST['id'] ?? 0);
    $qty = max(1, (int) ($_POST['qty'] ?? 1));

    if ($id > 0) {
        $productResult = mysqli_query($conn, "SELECT product_id, name, price, stock, image FROM products WHERE product_id=$id AND is_active=1 LIMIT 1");
        $product = ($productResult) ? mysqli_fetch_assoc($productResult) : null;

        if ($product) {
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] += $qty;
                $message = 'Quantity updated in cart!';
            } else {
                $_SESSION['cart'][$id] = [
                    'id' => (int) $product['product_id'],
                    'name' => $product['name'],
                    'price' => (float) $product['price'],
                    'image' => $product['image'],
                    'qty' => $qty,
                    'stock' => (int) $product['stock']
                ];
                $message = 'Item added to cart!';
            }

            if ($_SESSION['cart'][$id]['qty'] > (int) $product['stock']) {
                $_SESSION['cart'][$id]['qty'] = (int) $product['stock'];
                $message = 'Quantity adjusted to available stock.';
            }
        }
    }

    session_write_close();
    header('Location: ?msg=' . urlencode($message));
    exit;
}

if ($action === 'update') {
    $id = (int) ($_POST['id'] ?? 0);
    $qty = (int) ($_POST['qty'] ?? 1);

    if ($id > 0 && isset($_SESSION['cart'][$id])) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
            $message = 'Item removed from cart.';
        } else {
            $_SESSION['cart'][$id]['qty'] = min($qty, (int) $_SESSION['cart'][$id]['stock']);
            $message = 'Cart updated!';
        }
    }

    session_write_close();
    header('Location: ?msg=' . urlencode($message));
    exit;
}

if ($action === 'remove') {
    $id = (int) ($_GET['id'] ?? 0);
    if ($id > 0 && isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
        $message = 'Item removed from cart.';
    }
    session_write_close();
    header('Location: ?msg=' . urlencode($message));
    exit;
}

$items = $_SESSION['cart'];
$total = 0;
foreach ($items as $item) {
    $total += ((float) $item['price'] * (int) $item['qty']);
}

$message = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  <title>Cart - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Your Shopping Cart</h1>
    <?php if ($message !== ''): ?>
      <p style="color: #28a745; font-weight: bold; margin-top: 10px;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
  </section>

  <?php if (count($items) === 0): ?>
    <div class="form-card">
      <p>Your cart is empty.</p>
      <a class="btn" href="/hamropasal/products/">Continue Shopping</a>
    </div>
  <?php else: ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?php echo htmlspecialchars($item['name']); ?></td>
              <td>Rs. <?php echo number_format((float) $item['price'], 2); ?></td>
              <td>
                <form method="post" action="/hamropasal/cart/" style="display:flex; gap:8px; align-items:center;">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id" value="<?php echo (int) $item['id']; ?>">
                  <input type="number" name="qty" value="<?php echo (int) $item['qty']; ?>" min="1" max="<?php echo (int) $item['stock']; ?>" style="max-width:90px;" autocomplete="off">
                  <button type="submit">Update</button>
                </form>
              </td>
              <td>Rs. <?php echo number_format((float) $item['price'] * (int) $item['qty'], 2); ?></td>
              <td><a class="btn danger" href="/hamropasal/cart/?action=remove&id=<?php echo (int) $item['id']; ?>">Remove</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="summary-box">
      <h3>Grand Total: Rs. <?php echo number_format($total, 2); ?></h3>
      <p>
        <a class="btn" href="/hamropasal/checkout/">Proceed to Checkout</a>
        <a class="btn secondary" href="/hamropasal/products/">Continue Shopping</a>
      </p>
    </div>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
<script src="/hamropasal/js/script.js"></script>
</body>
</html>
