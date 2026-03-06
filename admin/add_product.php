<?php
session_start();
require '../database/connection.php';
if(!isset($_SESSION['user']) || !$_SESSION['user']['is_admin']){
    header('Location: ../login.php');
    exit;
}
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $cat = $conn->real_escape_string($_POST['category']);
    $price = floatval($_POST['price']);
    $desc = $conn->real_escape_string($_POST['description']);
    $image = $conn->real_escape_string($_POST['image']);
    $conn->query("INSERT INTO products (name,category,price,description,image) VALUES ('$name','$cat',$price,'$desc','$image')");
    header('Location: manage_products.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Product</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Add Product</h1>
<form method="post">
<label>Name</label><input type="text" name="name" required><br>
<label>Category</label><input type="text" name="category"><br>
<label>Price</label><input type="text" name="price" required><br>
<label>Description</label><textarea name="description"></textarea><br>
<label>Image URL</label><input type="text" name="image"><br>
<button type="submit">Save</button>
</form>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>