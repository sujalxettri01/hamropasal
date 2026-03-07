<?php
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
if (count($cart) === 0) {
    header('Location: /hamropasal/cart/');
    exit;
}

$error = '';
$total = 0;
foreach ($cart as $item) {
    $total += (float) $item['price'] * (int) $item['qty'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = trim($_POST['customer_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $paymentMethod = trim($_POST['payment_method'] ?? 'Cash on Delivery');

    $allowedPayments = ['Cash on Delivery', 'eSewa', 'Khalti'];
    if (!in_array($paymentMethod, $allowedPayments, true)) {
        $paymentMethod = 'Cash on Delivery';
    }

    if ($customerName === '' || $phone === '' || $address === '') {
        $error = 'Name, phone and address are required.';
    } else {
        mysqli_begin_transaction($conn);
        try {
            $userId = (int) $_SESSION['user']['user_id'];
            $customerNameEsc = mysqli_real_escape_string($conn, $customerName);
            $phoneEsc = mysqli_real_escape_string($conn, $phone);
            $addressEsc = mysqli_real_escape_string($conn, $address);
            $paymentEsc = mysqli_real_escape_string($conn, $paymentMethod);

            $orderSql = "INSERT INTO orders (user_id, customer_name, phone, address, payment_method, total_amount)
                         VALUES ($userId, '$customerNameEsc', '$phoneEsc', '$addressEsc', '$paymentEsc', $total)";

            if (!mysqli_query($conn, $orderSql)) {
                throw new Exception('Order create failed.');
            }

            $orderId = mysqli_insert_id($conn);

            foreach ($cart as $item) {
                $productId = (int) $item['id'];
                $qty = (int) $item['qty'];

                $stockResult = mysqli_query($conn, "SELECT stock, price FROM products WHERE product_id=$productId FOR UPDATE");
                $stockRow = ($stockResult) ? mysqli_fetch_assoc($stockResult) : null;
                if (!$stockRow) {
                    throw new Exception('Product missing during checkout.');
                }

                $currentStock = (int) $stockRow['stock'];
                if ($currentStock < $qty) {
                    throw new Exception('Insufficient stock for: ' . $item['name']);
                }

                $unitPrice = (float) $stockRow['price'];
                $lineTotal = $unitPrice * $qty;
                $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total)
                            VALUES ($orderId, $productId, $qty, $unitPrice, $lineTotal)";
                if (!mysqli_query($conn, $itemSql)) {
                    throw new Exception('Order item insert failed.');
                }

                $updateStockSql = "UPDATE products SET stock = stock - $qty WHERE product_id=$productId";
                if (!mysqli_query($conn, $updateStockSql)) {
                    throw new Exception('Stock update failed.');
                }
            }

            mysqli_commit($conn);
            $_SESSION['cart'] = [];
            header('Location: /hamropasal/order_success/?id=' . $orderId);
            exit;
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = $e->getMessage();
        }
    }
}

$userName = $_SESSION['user']['name'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Checkout</h1>
    <p>Total payable: <strong>Rs. <?php echo number_format($total, 2); ?></strong></p>
  </section>

  <div class="form-card">
    <?php if ($error !== ''): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <label for="customer_name">Full Name</label>
      <input id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($userName); ?>" required>

      <label for="phone">Phone</label>
      <input id="phone" name="phone" required>

      <label for="address">Delivery Address</label>
      <textarea id="address" name="address" required></textarea>

      <label for="payment_method">Payment Method</label>
      <select id="payment_method" name="payment_method">
        <option value="Cash on Delivery">Cash on Delivery</option>
        <option value="eSewa">eSewa</option>
        <option value="Khalti">Khalti</option>
      </select>

      <button type="submit">Place Order</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
