<?php
session_start();
require __DIR__ . '/../database/connection.php';

if (isset($_SESSION['user'])) {
    header('Location: /hamropasal/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $emailEscaped = mysqli_real_escape_string($conn, $email);
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$emailEscaped' LIMIT 1");
        $user = ($result) ? mysqli_fetch_assoc($result) : null;

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid email or password.';
        } else {
            $_SESSION['user'] = [
                'user_id' => (int) $user['user_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'is_admin' => (int) $user['is_admin']
            ];
            header('Location: /hamropasal/');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - HamroPasal</title>
  <link rel="stylesheet" href="/hamropasal/css/style.css">
</head>
<body>
<?php include __DIR__ . '/../partials/header.php'; ?>
<div class="container">
  <div class="form-card">
    <h1>Login</h1>
    <?php if ($error !== ''): ?><div class="alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <button type="submit">Login</button>
    </form>
    <p>New here? <a href="/hamropasal/register/">Create an account</a></p>
  </div>
</div>
<?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
