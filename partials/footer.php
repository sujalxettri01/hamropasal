<footer class="site-footer">
  <div class="container">
    <div class="footer-content">
      <div class="footer-section">
        <h3>About HamroPasal</h3>
        <p>HamroPasal is your neighborhood general store online. We deliver daily essentials, groceries, and home care products with fast, reliable service.</p>
        <p>Open: Mon-Sat, 7:00 AM to 10:00 PM</p>
        <p>Phone: +977 01 1234567</p>
        <p>Email: <a href="mailto:support@hamropasal.com">support@hamropasal.com</a></p>
      </div>
      
      <div class="footer-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="/hamropasal/">Home</a></li>
          <li><a href="/hamropasal/products/">Browse Products</a></li>
          <li><a href="/hamropasal/orders/">Track Orders</a></li>
          <li><a href="/hamropasal/cart/">Shopping Cart</a></li>
          <li><a href="/hamropasal/contact.php">Store Locator</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h3>Support</h3>
        <ul>
          <li><a href="/hamropasal/shipping.php">Shipping Info</a></li>
          <li><a href="/hamropasal/returns.php">Return Policy</a></li>
          <li><a href="/hamropasal/faq.php">FAQs</a></li>
          <li><a href="/hamropasal/contact.php">Contact Support</a></li>
        </ul>
      </div>
      
      <div class="footer-section">
        <h3>Subscribe</h3>
        <p>Get updates and exclusive offers directly to your email.</p>
        <form id="newsletter-form" class="footer-newsletter" method="post" action="/hamropasal/newsletter_subscribe.php">
          <input type="email" name="email" placeholder="Enter your email" required>
          <button type="submit" class="btn">Subscribe</button>
          <div id="newsletter-message" class="newsletter-message"></div>
        </form>
      </div>
    </div>
    
    <div class="footer-bottom">
      <p>&copy; <?php echo date('Y'); ?> HamroPasal General Store. All rights reserved. Fresh groceries. Fair prices. Fast delivery.</p>
      <p>Need help? <a href="/hamropasal/contact.php">Contact Support</a> or call +977 01 1234567.</p>
    </div>
  </div>
</footer>
<script>
  document.getElementById('newsletter-form').addEventListener('submit', function (e) {
    e.preventDefault();
    var emailInput = this.querySelector('input[name="email"]');
    var message = document.getElementById('newsletter-message');
    if (!emailInput.value.trim()) {
      message.textContent = 'Please enter a valid email address.';
      message.style.color = '#ad1f1f';
      return;
    }
    message.textContent = 'Thank you for subscribing! Check your inbox soon.';
    message.style.color = '#0f766e';
    this.reset();
  });
</script>
<script src="/hamropasal/js/script.js"></script>
</body>
</html>
