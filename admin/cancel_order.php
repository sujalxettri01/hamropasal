<?php
// Start admin session (separate from user session)
session_name('admin_session');
session_start();
require __DIR__ . '/../database/connection.php';

// Check if user is admin
if (!isset($_SESSION['user']) || empty($_SESSION['user']['is_admin'])) {
    header('Location: /hamropasal/admin/login.php');
    exit;
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['cancel_reason'])) {
    $orderId = (int) $_POST['order_id'];
    $cancelReason = trim($_POST['cancel_reason']);

    // Validate inputs
    if ($orderId <= 0 || empty($cancelReason)) {
        header('Location: /hamropasal/admin/orders.php?error=Invalid order or reason');
        exit;
    }

    // Get order and user information
    $orderCheck = mysqli_query($conn, "SELECT user_id, order_status FROM orders WHERE order_id=$orderId LIMIT 1");
    $orderData = mysqli_fetch_assoc($orderCheck);

    if (!$orderData) {
        header('Location: /hamropasal/admin/orders.php?error=Order not found');
        exit;
    }

    // Check if order is already cancelled
    if ($orderData['order_status'] === 'Cancelled') {
        header('Location: /hamropasal/admin/order_details.php?id=' . $orderId . '&error=Order is already cancelled');
        exit;
    }

    $userId = (int) $orderData['user_id'];
    $cancelReasonEsc = mysqli_real_escape_string($conn, $cancelReason);
    $messageText = "Your order has been cancelled.\n\nReason: " . $cancelReason;
    $messageTextEsc = mysqli_real_escape_string($conn, $messageText);

    // Begin transaction for data consistency
    mysqli_query($conn, "START TRANSACTION");

    try {
        // Update order status to Cancelled
        $updateResult = mysqli_query($conn, "UPDATE orders SET order_status='Cancelled' WHERE order_id=$orderId");
        
        if (!$updateResult) {
            throw new Exception("Failed to update order status");
        }

        // Insert cancellation message for user
        $insertResult = mysqli_query($conn, "INSERT INTO messages (user_id, order_id, message_type, message_text) 
                                           VALUES ($userId, $orderId, 'Cancelled', '$messageTextEsc')");
        
        if (!$insertResult) {
            throw new Exception("Failed to send cancellation message");
        }

        // Commit transaction
        mysqli_query($conn, "COMMIT");

        // Redirect with success message
        header('Location: /hamropasal/admin/order_details.php?id=' . $orderId . '&cancelled=1');
        exit;
    } catch (Exception $e) {
        // Rollback on error
        mysqli_query($conn, "ROLLBACK");
        header('Location: /hamropasal/admin/order_details.php?id=' . $orderId . '&error=' . urlencode($e->getMessage()));
        exit;
    }
} else {
    // If no POST data, redirect back
    header('Location: /hamropasal/admin/orders.php');
    exit;
}
?>
