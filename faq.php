<?php
session_name('user_session');
session_start();
$pageTitle = 'FAQs';
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <div class="form-card" style="max-width: 900px; margin: 32px auto;">
    <h1>Frequently Asked Questions</h1>

    <div style="display: grid; gap: 18px; margin-top: 20px;">
      <div>
        <h3>How do I place an order?</h3>
        <p>Browse products, add items to your cart, and complete checkout with your delivery details.</p>
      </div>
      <div>
        <h3>Do I need an account?</h3>
        <p>Yes. Creating an account helps you track orders and view updates.</p>
      </div>
      <div>
        <h3>How can I track my order?</h3>
        <p>After logging in, open the <a href="/hamropasal/orders/">Orders</a> page to view status and details.</p>
      </div>
      <div>
        <h3>What if an item is out of stock?</h3>
        <p>Out-of-stock items cannot be added to the cart until inventory is updated.</p>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
