<?php
session_name('user_session');
session_start();
$pageTitle = 'Shipping Info';
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <div class="form-card" style="max-width: 900px; margin: 32px auto;">
    <h1>Shipping Information</h1>
    <p>We deliver daily essentials quickly and safely across our service areas.</p>

    <ul style="line-height: 1.8; padding-left: 20px; margin-top: 20px;">
      <li><strong>Standard delivery:</strong> Same day or next day for most local orders.</li>
      <li><strong>Order processing:</strong> Orders are usually packed within 1-2 hours during business time.</li>
      <li><strong>Delivery hours:</strong> 8:00 AM to 8:00 PM, Monday to Saturday.</li>
      <li><strong>Shipping charges:</strong> Calculated at checkout based on your area.</li>
      <li><strong>Tracking:</strong> You can check order updates from your account orders page.</li>
    </ul>

    <p style="margin-top: 20px;"><a class="btn" href="/hamropasal/products/">Continue Shopping</a></p>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
