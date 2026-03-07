<?php
session_start();
require __DIR__ . '/../database/connection.php';

if (isset($_SESSION['user'])) {
    header('Location: /hamropasal/');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $error = 'Name, email, and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $emailEscaped = mysqli_real_escape_string($conn, $email);
        $existing = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$emailEscaped'");
        if ($existing && mysqli_num_rows($existing) > 0) {
            $error = 'Email already registered. Please login.';
        } else {
            $nameEscaped = mysqli_real_escape_string($conn, $name);
            $phoneEscaped = mysqli_real_escape_string($conn, $phone);
            $addressEscaped = mysqli_real_escape_string($conn, $address);
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $passwordEscaped = mysqli_real_escape_string($conn, $passwordHash);

            $sql = "INSERT INTO users (name, email, phone, address, password) VALUES ('$nameEscaped', '$emailEscaped', '$phoneEscaped', '$addressEscaped', '$passwordEscaped')";
            if (mysqli_query($conn, $sql)) {
                $success = 'Registration successful. Please login.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <div class="form-card">
    <h1>Create Account</h1>
    <?php if ($error !== ''): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <?php if ($success !== ''): ?><div class="alert success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <form method="post">
      <label for="name">Full Name</label>
      <input id="name" name="name" type="text" required>

      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>

      <label for="phone">Phone</label>
      <input id="phone" name="phone" type="text">

      <label for="address">Address</label>
      <textarea id="address" name="address"></textarea>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <label for="confirm_password">Confirm Password</label>
      <input id="confirm_password" name="confirm_password" type="password" required>

      <button type="submit">Register</button>
    </form>

    <p>Already have an account? <a href="/hamropasal/login/">Login</a></p>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
