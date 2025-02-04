<?php
include 'includes/header.php';


$kitchen_id = $_COOKIE['kitchen_id'];
$stmt = $conn->prepare("
  SELECT DISTINCT
      c.customer_id,
      c.first_name,
      c.last_name, 
      c.photo,
      m.message,
      m.created_at,
      m.sender_role,
      m.is_read,
      (SELECT COUNT(*) FROM kitchen_customer_messages 
       WHERE customer_id = c.customer_id 
       AND kitchen_id = ? 
       AND sender_role = 'customer' 
       AND is_read = 0) as unread_count
  FROM customers c
  INNER JOIN kitchen_customer_messages m ON c.customer_id = m.customer_id
  WHERE m.kitchen_id = ? 
  AND m.created_at = (
      SELECT MAX(created_at) 
      FROM kitchen_customer_messages 
      WHERE customer_id = c.customer_id 
      AND kitchen_id = ?
  )
  ORDER BY m.created_at DESC
");
$stmt->bind_param("iii", $kitchen_id, $kitchen_id, $kitchen_id);
$stmt->execute();
$chats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" href="assets/css/chat_list.css">

<div class="chat-list-page">
    <header class="app-header">
        <div class="header-content">
            <a href="homepage.php" class="back-btn">
                <i class='bx bx-arrow-back'></i>
            </a>
            <h1 class="title">Customer Messages</h1>
        </div>
    </header>

    <main class="chat-list">
        <?php if (empty($chats)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class='bx bx-message-square-dots'></i>
            </div>
            <h2>No Messages Yet</h2>
            <p>When customers message you, they'll appear here</p>
        </div>
        <?php else: ?>
        <div class="chat-list-container">
            <?php foreach ($chats as $chat): ?>
            <a href="messenger.php?customer_id=<?php echo $chat['customer_id']; ?>" class="chat-item">
                <div class="chat-avatar">
                    <img src="assets/images/avatar/avatar2.png"
                        alt="<?php echo htmlspecialchars($chat['first_name']); ?>">
                    <?php if ($chat['unread_count'] > 0): ?>
                    <span class="unread-dot"></span>
                    <?php endif; ?>
                </div>
                <div class="chat-info">
                    <div class="chat-header">
                        <h2><?php echo htmlspecialchars($chat['first_name'] . ' ' . $chat['last_name']); ?></h2>
                        <span class="time">
                            <?php 
                                  $messageTime = strtotime($chat['created_at']);
                                  $now = time();
                                  $diff = $now - $messageTime;
                                  
                                  if ($diff < 60) {
                                      echo "Just now";
                                  } elseif ($diff < 3600) {
                                      echo floor($diff/60) . "m ago";
                                  } elseif ($diff < 86400) {
                                      echo floor($diff/3600) . "h ago";
                                  } else {
                                      echo date('M j', $messageTime);
                                  }
                                  ?>
                        </span>
                    </div>
                    <div class="chat-preview">
                        <p <?php echo $chat['unread_count'] > 0 ? 'class="unread"' : ''; ?>>
                            <?php if ($chat['sender_role'] === 'kitchen'): ?>
                            <span class="sent-indicator">
                                <i class='bx <?php echo $chat['is_read'] ? 'bx-check-double' : 'bx-check'; ?>'></i>
                            </span>
                            <?php endif; ?>
                            <?php echo htmlspecialchars(substr($chat['message'], 0, 50)) . (strlen($chat['message']) > 50 ? '...' : ''); ?>
                        </p>
                        <?php if ($chat['unread_count'] > 0): ?>
                        <span class="unread-count"><?php echo $chat['unread_count']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>


<?php include 'includes/appbar.php'; ?>
<?php include 'includes/scripts.php'; ?>