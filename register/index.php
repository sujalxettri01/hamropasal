<?php
session_start();
require '../database/connection.php';
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $conn->real_escape_string($_POST['phone']);
    if($conn->query("INSERT INTO users (name,email,password,phone) VALUES ('$name','$email','$pass','$phone')")){
        $id = $conn->insert_id;
        $_SESSION['user'] = ['user_id'=>$id,'name'=>$name,'email'=>$email,'is_admin'=>0];
        header('Location: ../');
        exit;
    } else {
        $message = 'Registration error: '.$conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Register</title><link rel="stylesheet" href="../css/style.css"></head>
<body>
<?php include '../partials/header.php'; ?>
<div class="container">
<h1>Register</h1>
<?php if($message) echo "<p>$message</p>"; ?>
<form method="post">
<label>Name</label><input type="text" name="name" required><br>
<label>Email</label><input type="email" name="email" required><br>
<label>Password</label><input type="password" name="password" required><br>
<label>Phone</label><input type="text" name="phone"><br>
<button type="submit">Register</button>
</form>
</div>
<?php include '../partials/footer.php'; ?>
</body>
</html>