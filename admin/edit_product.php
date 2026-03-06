<?php
session_start();
require '../database/connection.php';
if(!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']){
    header('Location: ../login.php');
    exit;
}
$id = intval($_GET['id'] ?? 0);
$res = $conn->query("SELECT * FROM products WHERE product_id=$id");
$product = $res->fetch_assoc();
if(!$product) { header('Location: manage_products.php'); exit; }
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $cat = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $desc = $conn->real_escape_string($_POST['description']);
    $image = $conn->real_escape_string($_POST['image']);
    $conn->query("UPDATE products SET name='$name',category='$cat',price=$price,description='$desc',image='$image' WHERE product_id=$id");
    header('Location: manage_products.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Product</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Edit Product</h1>
<form method="post">
<label>Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>
<label>Category</label><input type="text" name="category" value="<?php echo htmlspecialchars($product['category']); ?>"><br>
<label>Price</label><input type="text" name="price" value="<?php echo $product['price']; ?>" required><br>
<label>Description</label><textarea name="description"><?php echo htmlspecialchars($product['description']); ?></textarea><br>
<label>Image URL</label><input type="text" name="image" value="<?php echo htmlspecialchars($product['image']); ?>"><br>
<button type="submit">Update</button>
</form>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>