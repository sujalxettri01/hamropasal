<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/../database/connection.php';
require __DIR__ . '/../config/image_helper.php';

// Redirect admins to admin panel
if (isset($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/admin.php');
    exit;
}

$search = trim($_GET['q'] ?? '');
$category = trim($_GET['category'] ?? '');
$sort = trim($_GET['sort'] ?? 'newest');
$page = (int)($_GET['page'] ?? 1);
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$perPage = 12;
$offset = ($page - 1) * $perPage;

$conditions = ["is_active=1"];
$params = [];
$types = '';

if ($search !== '') {
    $conditions[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}
if ($category !== '') {
    $conditions[] = "category=?";
    $params[] = $category;
    $types .= 's';
}
if ($minPrice !== '' && is_numeric($minPrice)) {
    $conditions[] = "price >= ?";
    $params[] = (float)$minPrice;
    $types .= 'd';
}
if ($maxPrice !== '' && is_numeric($maxPrice)) {
    $conditions[] = "price <= ?";
    $params[] = (float)$maxPrice;
    $types .= 'd';
}

$where = implode(' AND ', $conditions);

$orderBy = 'created_at DESC';
switch ($sort) {
    case 'name':
        $orderBy = 'name ASC';
        break;
    case 'price_asc':
        $orderBy = 'price ASC';
        break;
    case 'price_desc':
        $orderBy = 'price DESC';
        break;
    case 'newest':
    default:
        $orderBy = 'created_at DESC';
        break;
}

// Get total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM products WHERE $where";
$stmt = mysqli_prepare($conn, $countQuery);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total = mysqli_fetch_assoc($result)['total'];
$totalPages = ceil($total / $perPage);

// Get products
$query = "SELECT * FROM products WHERE $where ORDER BY $orderBy LIMIT ? OFFSET ?";
$params[] = $perPage;
$params[] = $offset;
$types .= 'ii';

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$products = mysqli_stmt_get_result($stmt);

// Get categories
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE is_active=1 ORDER BY category ASC");

$pageTitle = 'Products';
?>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <section class="products-hero">
    <h1>Shop All Products</h1>
    <p>Discover our wide selection of groceries, kitchen essentials, and home-care items</p>
  </section>

  <div class="products-wrapper">
    <!-- Sidebar Filters -->
    <aside class="filters-sidebar">
      <div class="filters-header">
        <h2>Filters</h2>
        <a href="/hamropasal/products/" class="clear-all-btn">Clear All</a>
      </div>
      
      <form method="get" class="filters-form">
        <fieldset class="filter-group">
          <legend>Search Products</legend>
          <input type="text" id="q" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Rice, tea, detergent..." class="filter-input">
        </fieldset>

        <fieldset class="filter-group">
          <legend>Category</legend>
          <select id="category" name="category" class="filter-select">
            <option value="">All categories</option>
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
              <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo ($category === $cat['category']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['category']); ?>
              </option>
            <?php endwhile; ?>
          </select>
        </fieldset>

        <fieldset class="filter-group">
          <legend>Price Range</legend>
          <div class="price-inputs">
            <div>
              <label for="min_price">Min</label>
              <input id="min_price" name="min_price" type="number" min="0" step="0.01" value="<?php echo htmlspecialchars($minPrice); ?>" placeholder="0" class="filter-input">
            </div>
            <div>
              <label for="max_price">Max</label>
              <input id="max_price" name="max_price" type="number" min="0" step="0.01" value="<?php echo htmlspecialchars($maxPrice); ?>" placeholder="10000" class="filter-input">
            </div>
          </div>
        </fieldset>

        <fieldset class="filter-group">
          <legend>Sort By</legend>
          <select id="sort" name="sort" class="filter-select">
            <option value="newest" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest</option>
            <option value="name" <?php echo ($sort === 'name') ? 'selected' : ''; ?>>Name A-Z</option>
            <option value="price_asc" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?>>Price Low to High</option>
            <option value="price_desc" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?>>Price High to Low</option>
          </select>
        </fieldset>

        <button type="submit" class="btn filter-btn">Apply Filters</button>
      </form>
    </aside>

    <!-- Products Grid -->
    <main class="products-main">
      <?php if (mysqli_num_rows($products) > 0): ?>
        <div class="results-header">
          <p class="results-count">Showing <strong><?php echo $offset + 1; ?></strong> - <strong><?php echo min($offset + $perPage, $total); ?></strong> of <strong><?php echo $total; ?></strong> products</p>
        </div>
        
        <div class="grid">
          <?php while ($p = mysqli_fetch_assoc($products)): ?>
            <article class="card product-card">
              <div class="card-image-wrapper">
                <a href="/hamropasal/product/?id=<?php echo (int) $p['product_id']; ?>" class="card-image-link">
                  <img src="<?php echo getProductImageUrl($p['name'], $p['category'], $p['image']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" loading="lazy">
                </a>
                <?php if ($p['stock'] > 0 && $p['stock'] < 5): ?>
                  <span class="stock-badge in-stock">Limited Stock</span>
                <?php elseif ($p['stock'] > 0): ?>
                  <span class="stock-badge in-stock">In Stock</span>
                <?php else: ?>
                  <span class="stock-badge out-of-stock">Out of Stock</span>
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
                    <input type="number" name="qty" min="1" max="<?php echo (int) $p['stock']; ?>" value="1" class="qty-input" title="Quantity">
                    <button type="submit" class="btn btn-add-to-cart">
                      Add
                    </button>
                  </form>
                <?php else: ?>
                  <button class="btn btn-out-of-stock" disabled>Out of Stock</button>
                <?php endif; ?>
              </div>
            </article>
          <?php endwhile; ?>
        </div>

        <?php if ($totalPages > 1): ?>
          <div class="pagination">
            <?php
            $queryParams = http_build_query(array_filter([
              'q' => $search,
              'category' => $category,
              'min_price' => $minPrice,
              'max_price' => $maxPrice,
              'sort' => $sort
            ]));
            ?>
            <?php if ($page > 1): ?>
              <a href="?<?php echo $queryParams; ?>&page=1" class="btn btn-page">First</a>
              <a href="?<?php echo $queryParams; ?>&page=<?php echo $page - 1; ?>" class="btn btn-page">Previous</a>
            <?php endif; ?>
            
            <div class="page-info">Page <?php echo $page; ?> of <?php echo $totalPages; ?></div>
            
            <?php if ($page < $totalPages): ?>
              <a href="?<?php echo $queryParams; ?>&page=<?php echo $page + 1; ?>" class="btn btn-page">Next</a>
              <a href="?<?php echo $queryParams; ?>&page=<?php echo $totalPages; ?>" class="btn btn-page">Last</a>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      <?php else: ?>
        <div class="no-results">
          <div style="font-size: 3rem; margin-bottom: 16px;">No Results</div>
          <h2>No Products Found</h2>
          <p>We couldn't find any products matching your criteria.</p>
          <p style="color: #475569; font-size: 0.95rem;">Try adjusting your search or using different filters.</p>
          <a href="/hamropasal/products/" class="btn" style="margin-top: 20px;">View All Products</a>
        </div>
      <?php endif; ?>
    </main>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
