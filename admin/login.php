<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (isset($_SESSION['user'])) {
    if (!empty($_SESSION['user']['is_admin'])) {
        header('Location: /hamropasal/admin/admin.php');
        exit;
    } else {
        header('Location: /hamropasal/');
        exit;
    }
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $emailEscaped = mysqli_real_escape_string($conn, $email);
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$emailEscaped' AND is_admin=1 LIMIT 1");
        $user = ($result) ? mysqli_fetch_assoc($result) : null;

        if (!$user || !password_verify($password, $user['password'])) {
            $error = 'Invalid admin email or password.';
        } else {
            $_SESSION['user'] = [
                'user_id' => (int) $user['user_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'is_admin' => (int) $user['is_admin']
            ];
            header('Location: /hamropasal/admin/admin.php');
            exit;
        }
    }
}
$pageTitle = 'Admin Login';
?>
<?php include __DIR__ . '/admin_header.php'; ?>
<div class="admin-container">
  <div class="admin-form-card">
    <h1>Admin Login</h1>
    <?php if ($error !== ''): ?><div class="admin-alert error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
    <form method="post">
      <label for="email">Admin Email</label>
      <input id="email" name="email" type="email" required>

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <button type="submit" class="admin-btn">Login as Admin</button>
    </form>
    <p><a href="/hamropasal/login/">Regular User Login</a></p>
  </div>
</div>
<?php include __DIR__ . '/admin_footer.php'; ?>