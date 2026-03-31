<?php
session_name('user_session');
session_start();
$pageTitle = 'Contact Us';
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <div class="form-card" style="max-width: 900px; margin: 32px auto;">
    <h1>Contact HamroPasal</h1>
    <p>Need help with an order, product, or delivery? Our support team is here to assist you.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 24px;">
      <div>
        <h3>Customer Support</h3>
        <p><strong>Phone:</strong> +977 01 1234567</p>
        <p><strong>Email:</strong> <a href="mailto:support@hamropasal.com">support@hamropasal.com</a></p>
        <p><strong>Hours:</strong> Mon-Sat, 7:00 AM to 10:00 PM</p>
      </div>
      <div>
        <h3>Store Location</h3>
        <p>HamroPasal General Store</p>
        <p>New Road, Kathmandu, Nepal</p>
        <p>Fast local delivery available across nearby areas.</p>
      </div>
      <div>
        <h3>Quick Help</h3>
        <p>For order tracking, visit your orders page after logging in.</p>
        <p><a class="btn" href="/hamropasal/orders/">Track My Orders</a></p>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
