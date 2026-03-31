<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/login.php');
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /hamropasal/admin/manage_products.php');
    exit;
}

$productResult = mysqli_query($conn, "SELECT * FROM products WHERE product_id=$id LIMIT 1");
$product = ($productResult) ? mysqli_fetch_assoc($productResult) : null;
if (!$product) {
    header('Location: /hamropasal/admin/manage_products.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $customFilename = trim($_POST['custom_filename'] ?? '');
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    // Handle file upload (optional for edit)
    $uploadedImage = '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $originalName = $_FILES['image_file']['name'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);
        
        // Use custom filename if provided, otherwise use original
        $filename = $customFilename !== '' ? $customFilename . '.' . $fileExtension : $originalName;
        
        $uploadPath = $uploadDir . $filename;
        
        if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
            $uploadedImage = '/hamropasal/uploads/' . $filename;
        } else {
            $error = 'Failed to upload image.';
        }
    }

    if ($name === '' || $category === '' || $price <= 0) {
        $error = 'Name, category and valid price are required.';
    } elseif ($error === '') {
        // Use uploaded image if available, otherwise keep existing
        $finalImage = $uploadedImage !== '' ? $uploadedImage : $product['image'];

        $nameEsc = mysqli_real_escape_string($conn, $name);
        $catEsc = mysqli_real_escape_string($conn, $category);
        $descEsc = mysqli_real_escape_string($conn, $description);
        $imageEsc = mysqli_real_escape_string($conn, $finalImage);

        $sql = "UPDATE products
                SET name='$nameEsc', category='$catEsc', description='$descEsc', price=$price, stock=$stock, image='$imageEsc', is_active=$isActive
                WHERE product_id=$id";

        if (mysqli_query($conn, $sql)) {
            header('Location: /hamropasal/admin/manage_products.php');
            exit;
        } else {
            $error = 'Failed to update product.';
        }
    }
}
$pageTitle = 'Edit Product';
?>
<?php include __DIR__ . '/admin_header.php'; ?>
<div class="admin-container">
  <div class="admin-form-card">
    <h1>Edit Product</h1>
    <?php if ($error !== ''): ?><div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <label for="name">Name</label>
      <input id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

      <label for="category">Category</label>
      <input id="category" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>

      <label for="price">Price</label>
      <input id="price" name="price" type="number" min="1" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>

      <label for="stock">Stock</label>
      <input id="stock" name="stock" type="number" min="0" value="<?php echo (int) $product['stock']; ?>" required>

      <label for="image_file">Upload New Image</label>
      <input id="image_file" name="image_file" type="file" accept="image/*">

      <label for="custom_filename">Custom Filename (optional)</label>
      <input id="custom_filename" name="custom_filename" type="text" placeholder="Leave empty to use original filename">

      <label for="description">Description</label>
      <textarea id="description" name="description"><?php echo htmlspecialchars($product['description']); ?></textarea>

      <label><input type="checkbox" name="is_active" <?php echo ((int) $product['is_active'] === 1) ? 'checked' : ''; ?> style="width:auto; margin-right: 8px;"> Active</label>

      <button type="submit" class="admin-btn">Update Product</button>
    </form>
  </div>
</div>
<?php include __DIR__ . '/admin_footer.php'; ?>
<script>
document.getElementById('image_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const filename = file.name;
        const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
        document.getElementById('custom_filename').value = nameWithoutExt;
    }
});
</script>
