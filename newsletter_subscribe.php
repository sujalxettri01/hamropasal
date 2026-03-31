<?php
session_name('user_session');
session_start();

$pageTitle = 'Newsletter Subscription';
$email = trim($_POST['email'] ?? '');
$isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
$isValid = $isPost && filter_var($email, FILTER_VALIDATE_EMAIL);
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <div class="form-card" style="max-width: 720px; margin: 32px auto; text-align: center;">
    <h1>Newsletter Subscription</h1>

    <?php if ($isPost && $isValid): ?>
      <p style="font-size: 1.1rem; color: #0f766e;"><strong>Thank you for subscribing!</strong></p>
      <p>We will share updates and special offers with <strong><?php echo htmlspecialchars($email); ?></strong>.</p>
    <?php elseif ($isPost): ?>
      <p style="font-size: 1.1rem; color: #b91c1c;"><strong>Please enter a valid email address.</strong></p>
      <p>You can go back and try again from the footer form.</p>
    <?php else: ?>
      <p>Use the newsletter form in the website footer to subscribe for updates and offers.</p>
    <?php endif; ?>

    <p style="margin-top: 20px;">
      <a class="btn" href="/hamropasal/">Back to Home</a>
      <a class="btn secondary" href="/hamropasal/products/" style="margin-left: 8px;">Browse Products</a>
    </p>
  </div>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>
