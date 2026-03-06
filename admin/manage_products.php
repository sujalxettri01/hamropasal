<?php
session_start();
require '../database/connection.php';
if(!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']){
    header('Location: ../login.php');
    exit;
}
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM products WHERE product_id=$id");
}
$products = $conn->query("SELECT * FROM products");
?>
<!DOCTYPE html>
<html>
<head><title>Manage Products</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Manage Products</h1>
<p><a href="add_product.php">Add New Product</a></p>
<table>
<tr><th>ID</th><th>Name</th><th>Price</th><th>Category</th><th>Actions</th></tr>
<?php while($p=$products->fetch_assoc()): ?>
<tr>
<td><?php echo $p['product_id']; ?></td>
<td><?php echo htmlspecialchars($p['name']); ?></td>
<td>₹<?php echo $p['price']; ?></td>
<td><?php echo htmlspecialchars($p['category']); ?></td>
<td>
  <a href="edit_product.php?id=<?php echo $p['product_id']; ?>">Edit</a> |
  <a href="?delete=<?php echo $p['product_id']; ?>" onclick="return confirm('Delete?');">Delete</a>
</td>
</tr>
<?php endwhile;?>
</table>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>