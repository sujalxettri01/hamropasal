<?php
// Start user session (separate from admin session)
session_name('user_session');
session_start();
require __DIR__ . '/../database/connection.php';

$pageTitle = 'My Messages';

// Check if user is logged in
if (!isset($_SESSION['user']['user_id'])) {
    header('Location: /hamropasal/login/');
    exit;
}

$userId = (int) $_SESSION['user']['user_id'];

// Mark message as read if action is provided
if (isset($_GET['mark_read']) && (int) $_GET['mark_read'] > 0) {
    $messageId = (int) $_GET['mark_read'];
    mysqli_query($conn, "UPDATE messages SET is_read=1 WHERE message_id=$messageId AND user_id=$userId");
    header('Location: /hamropasal/messages/');
    exit;
}

// Get all messages for the user
$messagesResult = mysqli_query($conn, "SELECT m.*, o.order_id, o.order_status 
                                      FROM messages m 
                                      JOIN orders o ON m.order_id = o.order_id 
                                      WHERE m.user_id=$userId 
                                      ORDER BY m.created_at DESC");

// Get unread count
$unreadResult = mysqli_query($conn, "SELECT COUNT(*) as count FROM messages WHERE user_id=$userId AND is_read=0");
$unreadData = mysqli_fetch_assoc($unreadResult);
$unreadCount = $unreadData['count'];
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container" style="margin: 40px auto; max-width: 800px;">
  <section style="margin: 40px 0;">
    <h1>My Messages</h1>
    <?php if ($unreadCount > 0): ?>
      <p style="color: #ffc107; font-size: 14px; margin-top: 10px;">You have <strong><?php echo $unreadCount; ?></strong> unread message(s)</p>
    <?php endif; ?>
  </section>

  <?php if (mysqli_num_rows($messagesResult) > 0): ?>
    <div style="margin-top: 30px;">
      <?php while ($message = mysqli_fetch_assoc($messagesResult)): ?>
        <div style="background: <?php echo $message['is_read'] ? '#f8f9fa' : '#fff3cd'; ?>; border: 1px solid #ddd; padding: 20px; margin-bottom: 15px; border-radius: 8px;">
          <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
            <div>
              <h3 style="margin: 0 0 5px 0; font-size: 16px;">
                Order #<?php echo (int) $message['order_id']; ?> - 
                <span style="color: #666; font-size: 14px;"><?php echo htmlspecialchars($message['message_type']); ?></span>
              </h3>
              <p style="margin: 0; color: #999; font-size: 12px;">
                <?php echo date('M d, Y H:i', strtotime($message['created_at'])); ?>
              </p>
            </div>
            <?php if (!$message['is_read']): ?>
              <span style="background: #ffc107; color: #000; padding: 4px 12px; border-radius: 4px; font-size: 12px; font-weight: bold;">NEW</span>
            <?php endif; ?>
          </div>
          
          <p style="margin: 15px 0; color: #333; line-height: 1.6;">
            <?php echo htmlspecialchars($message['message_text']); ?>
          </p>
          
          <div style="background: #f0f0f0; padding: 10px 15px; border-radius: 4px; margin-top: 10px;">
            <p style="margin: 0; font-size: 13px;"><strong>Current Order Status:</strong> <span style="color: #007bff;"><?php echo htmlspecialchars($message['order_status']); ?></span></p>
          </div>

          <?php if (!$message['is_read']): ?>
            <div style="margin-top: 10px;">
              <a href="/hamropasal/messages/?mark_read=<?php echo (int) $message['message_id']; ?>" style="font-size: 12px; color: #007bff; text-decoration: none;">Mark as read</a>
            </div>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 8px; margin-top: 40px;">
      <p style="font-size: 18px; color: #666;">No messages yet</p>
      <p style="color: #999; margin-top: 10px;">You will receive messages when your orders are updated</p>
      <a href="/hamropasal/orders/" style="display: inline-block; margin-top: 20px; padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px;">View My Orders</a>
    </div>
  <?php endif; ?>

  <p style="margin-top: 40px; text-align: center;">
    <a href="/hamropasal/" style="color: #007bff; text-decoration: none;">Back to Home</a>
  </p>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
