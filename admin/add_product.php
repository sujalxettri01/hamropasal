<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/login.php');
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

    // Handle file upload (required)
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
    } else {
        $error = 'Please select an image file to upload.';
    }

    if ($name === '' || $category === '' || $price <= 0) {
        $error = 'Name, category and valid price are required.';
    } elseif ($error === '' && $uploadedImage !== '') {

        $nameEsc = mysqli_real_escape_string($conn, $name);
        $catEsc = mysqli_real_escape_string($conn, $category);
        $descEsc = mysqli_real_escape_string($conn, $description);
        $imageEsc = mysqli_real_escape_string($conn, $uploadedImage);

        $sql = "INSERT INTO products (name, category, description, price, stock, image, is_active)
                VALUES ('$nameEsc', '$catEsc', '$descEsc', $price, $stock, '$imageEsc', $isActive)";
        if (mysqli_query($conn, $sql)) {
            header('Location: /hamropasal/admin/manage_products.php');
            exit;
        } else {
            $error = 'Failed to add product.';
        }
    }
}
$pageTitle = 'Add Product';
?>
<?php include __DIR__ . '/admin_header.php'; ?>
<div class="admin-container">
  <div class="admin-form-card">
    <h1>Add Product</h1>
    <?php if ($error !== ''): ?><div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data">
      <label for="name">Name</label>
      <input id="name" name="name" required>

      <label for="category">Category</label>
      <input id="category" name="category" required>

      <label for="price">Price</label>
      <input id="price" name="price" type="number" min="1" step="0.01" required>

      <label for="stock">Stock</label>
      <input id="stock" name="stock" type="number" min="0" value="0" required>

      <label for="image_file">Upload Image</label>
      <input id="image_file" name="image_file" type="file" accept="image/*" required>

      <label for="custom_filename">Custom Filename (optional)</label>
      <input id="custom_filename" name="custom_filename" type="text" placeholder="Leave empty to use original filename">

      <label for="description">Description</label>
      <textarea id="description" name="description"></textarea>

      <label><input type="checkbox" name="is_active" checked style="width:auto; margin-right: 8px;"> Active</label>

      <button type="submit" class="admin-btn">Save Product</button>
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
