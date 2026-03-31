<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/database/connection.php';

// Redirect admins to admin panel
if (isset($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/admin.php');
    exit;
}

$featuredQuery = mysqli_query($conn, "SELECT * FROM products WHERE is_active=1 ORDER BY created_at DESC LIMIT 8");
$pageTitle = 'Home';
?>
<?php include __DIR__ . '/partials/header.php'; ?>
<div class="container">
  <section class="hero">
    <h1>Your Neighborhood General Store, Online</h1>
    <p>Fresh groceries, daily essentials, and home-care products delivered fast to your doorstep</p>
    <a href="/hamropasal/products/" class="btn">Shop Now</a>
  </section>

  <section class="features">
    <h2>Why Shop With Us?</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon"></div>
        <h3>Fast Delivery</h3>
        <p>Quick and reliable delivery right to your doorstep</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"></div>
        <h3>Fair Prices</h3>
        <p>Best market prices on all your everyday items</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"></div>
        <h3>Quality Products</h3>
        <p>Carefully selected fresh and quality products</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon"></div>
        <h3>Customer Support</h3>
        <p>Reach our support team anytime you need help with your order</p>
      </div>
    </div>
  </section>

  <section class="products-section">
    <h2>Featured Products</h2>
    <p class="section-subtitle">Check out our best-selling items</p>
    <div class="grid">
      <?php while ($p = mysqli_fetch_assoc($featuredQuery)): ?>
        <article class="card">
          <div class="card-image-wrapper">
            <a href="/hamropasal/product/?id=<?php echo (int) $p['product_id']; ?>" class="card-image-link">
              <img src="<?php echo htmlspecialchars($p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
            </a>
            <?php if ($p['stock'] > 0): ?>
              <span class="stock-badge in-stock">In Stock</span>
            <?php endif; ?>
          </div>
          <div class="card-body">
            <h3><a href="/hamropasal/product/?id=<?php echo (int) $p['product_id']; ?>"><?php echo htmlspecialchars($p['name']); ?></a></h3>
            <p class="product-category"><?php echo htmlspecialchars($p['category']); ?></p>
            <p class="price">Rs. <?php echo number_format((float) $p['price'], 2); ?></p>
            <?php if ($p['stock'] > 0): ?>
              <form method="post" action="/hamropasal/cart/" class="add-to-cart-form">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" value="<?php echo (int) $p['product_id']; ?>">
                <input type="hidden" name="qty" value="1">
                <button type="submit" class="btn btn-add-to-cart">
                  Add to Cart
                </button>
              </form>
            <?php endif; ?>
          </div>
        </article>
      <?php endwhile; ?>
    </div>
    <div style="text-align: center; margin-top: 40px;">
      <a class="btn" href="/hamropasal/products/">Browse All Products</a>
    </div>
  </section>

  <section class="cta-section">
    <h2>Join Thousands of Happy Customers</h2>
    <p>Experience convenient shopping with HamroPasal</p>
    <?php if (!isset($_SESSION['user'])): ?>
      <div class="cta-buttons">
        <a href="/hamropasal/register/" class="btn">Create Account</a>
        <a href="/hamropasal/login/" class="btn btn-secondary" style="text-decoration: none;">Login</a>
      </div>
    <?php else: ?>
      <a href="/hamropasal/products/" class="btn">Continue Shopping</a>
    <?php endif; ?>
  </section>
</div>
<?php include __DIR__ . '/partials/footer.php'; ?>