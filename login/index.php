<?php
session_start();
require '../database/connection.php';
$redirect = $_GET['redirect'] ?? '../';
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $_POST['password'];
    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if($user = $res->fetch_assoc()){
        if(password_verify($pass, $user['password'])){
            $_SESSION['user']=$user;
            header("Location:$redirect");
            exit;
        } else {
            $message = 'Invalid credentials';
        }
    } else {
        $message = 'User not found';
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Login</h1>
<?php if($message) echo "<p>$message</p>"; ?>
<form method="post">
<label>Email</label><input type="email" name="email" required><br>
<label>Password</label><input type="password" name="password" required><br>
<button type="submit">Login</button>
</form>
<p><a href="../register/">Register</a></p>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>