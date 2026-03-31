<?php
session_name('user_session');
session_start();
$pageTitle = 'Return Policy';
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <div class="form-card" style="max-width: 900px; margin: 32px auto;">
    <h1>Return Policy</h1>
    <p>We want you to be happy with every order from HamroPasal.</p>

    <ul style="line-height: 1.8; padding-left: 20px; margin-top: 20px;">
      <li>Damaged, incorrect, or expired items can be reported within <strong>24 hours</strong> of delivery.</li>
      <li>Perishable goods should be checked immediately upon arrival.</li>
      <li>Approved returns are eligible for replacement, refund, or store credit.</li>
      <li>Please keep your invoice or order number ready when contacting support.</li>
    </ul>

    <p style="margin-top: 20px;">To request help, visit our <a href="/hamropasal/contact.php">contact page</a>.</p>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
