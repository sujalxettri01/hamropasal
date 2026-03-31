<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/../database/connection.php';

if (!isset($_SESSION['user'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$success = isset($_GET['success']) && $_GET['success'] === '1' ? '&success=1' : '';
if ($orderId <= 0) {
    header('Location: /hamropasal/orders/');
    exit;
}

$userId = (int) $_SESSION['user']['user_id'];
$isAdmin = !empty($_SESSION['user']['is_admin']);

// Redirect to appropriate order details page
if ($isAdmin) {
    // Switch to admin session to check admin login
    session_name('admin_session');
    session_start();
    if (isset($_SESSION['user']) && !empty($_SESSION['user']['is_admin'])) {
        header('Location: /hamropasal/admin/order_details.php?id=' . $orderId . $success);
    } else {
        header('Location: /hamropasal/admin/login.php');
    }
    exit;
} else {
    header('Location: /hamropasal/orders/order_details.php?id=' . $orderId . $success);
}
exit;
