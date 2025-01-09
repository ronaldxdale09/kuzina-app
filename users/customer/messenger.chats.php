<?php
include 'includes/header.php';

$customer_id = $_COOKIE['user_id'];

$stmt = $conn->prepare("
   SELECT 
       k.kitchen_id,
       k.fname,
       k.lname,
       k.photo,
       m.message,
       m.created_at,
       m.sender_role,
       m.is_read,
       (SELECT COUNT(*) FROM kitchen_customer_messages 
        WHERE customer_id = ? 
        AND kitchen_id = k.kitchen_id 
        AND sender_role = 'kitchen' 
        AND is_read = 0) as unread_count
   FROM kitchens k
   INNER JOIN kitchen_customer_messages m ON k.kitchen_id = m.kitchen_id
   WHERE m.customer_id = ? 
   AND m.created_at = (
       SELECT MAX(created_at) 
       FROM kitchen_customer_messages 
       WHERE customer_id = ? 
       AND kitchen_id = k.kitchen_id
   )
   ORDER BY m.created_at DESC
");
$stmt->bind_param("iii", $customer_id, $customer_id, $customer_id);
$stmt->execute();
$chats = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<div class="chat-list-page">
   <header class="app-header">
       <div class="header-content">
           <a href="homepage.php" class="back-btn">
               <i class='bx bx-arrow-back'></i>
           </a>
           <h1 class="title">Messages</h1>
       </div>
   </header>

   <main class="chat-list">
       <?php if (empty($chats)): ?>
           <div class="empty-state">
               <div class="empty-state-icon">
                   <i class='bx bx-message-square-dots'></i>
               </div>
               <h2>No Conversations Yet</h2>
               <p>Start chatting with kitchens to see your messages here</p>
           </div>
       <?php else: ?>
           <div class="chat-list-container">
               <?php foreach ($chats as $chat): ?>
                   <a href="messenger.php?kitchen_id=<?php echo $chat['kitchen_id']; ?>" class="chat-item">
                       <div class="chat-avatar">
                           <img src="../../uploads/profile/<?php echo htmlspecialchars($chat['photo'] ?? 'default.png'); ?>" 
                                alt="<?php echo htmlspecialchars($chat['fname']); ?>">
                           <?php if ($chat['unread_count'] > 0): ?>
                               <span class="unread-dot"></span>
                           <?php endif; ?>
                       </div>
                       <div class="chat-info">
                           <div class="chat-header">
                               <h2><?php echo htmlspecialchars($chat['fname'] . ' ' . $chat['lname']); ?></h2>
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
                                   <?php if ($chat['sender_role'] === 'customer'): ?>
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

<style>
.chat-list-page {
   background: #f8f9fa;
   min-height: 100vh;
}

.app-header {
   position: fixed;
   top: 0;
   left: 0;
   right: 0;
   z-index: 1000;
   background: #fff;
   padding: 1rem;
   box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.header-content {
   max-width: 768px;
   margin: 0 auto;
   display: flex;
   align-items: center;
   gap: 1rem;
}

.back-btn {
   color: #502121;
   font-size: 1.5rem;
}

.title {
   font-size: 1.25rem;
   font-weight: 600;
   margin: 0;
   color: #1a1a1a;
}

.chat-list {
   padding: 4.5rem 0 1rem;
   max-width: 768px;
   margin: 0 auto;
}

.chat-list-container {
   background: #fff;
   border-radius: 12px;
   box-shadow: 0 1px 3px rgba(0,0,0,0.05);
   margin: 1rem;
   overflow: hidden;
}

.chat-item {
   display: flex;
   padding: 1rem;
   gap: 1rem;
   border-bottom: 1px solid #f1f1f1;
   text-decoration: none;
   color: inherit;
   transition: background-color 0.2s;
}

.chat-item:hover {
   background: #f8f9fa;
}

.chat-avatar {
   position: relative;
}

.chat-avatar img {
   width: 56px;
   height: 56px;
   border-radius: 50%;
   object-fit: cover;
}

.unread-dot {
   position: absolute;
   bottom: 2px;
   right: 2px;
   width: 12px;
   height: 12px;
   background: #502121;
   border-radius: 50%;
   border: 2px solid #fff;
}

.chat-info {
   flex: 1;
   min-width: 0;
}

.chat-header {
   display: flex;
   justify-content: space-between;
   align-items: center;
   margin-bottom: 0.25rem;
}

.chat-header h2 {
   font-size: 1rem;
   font-weight: 600;
   margin: 0;
   color: #1a1a1a;
}

.time {
   font-size: 0.813rem;
   color: #666;
}

.chat-preview {
   display: flex;
   align-items: center;
   justify-content: space-between;
}

.chat-preview p {
   margin: 0;
   font-size: 0.875rem;
   color: #666;
   white-space: nowrap;
   overflow: hidden;
   text-overflow: ellipsis;
   max-width: calc(100% - 2rem);
}

.chat-preview p.unread {
   font-weight: 600;
   color: #1a1a1a;
}

.sent-indicator {
   color: #502121;
   margin-right: 0.25rem;
}

.unread-count {
   background: #502121;
   color: #fff;
   font-size: 0.75rem;
   font-weight: 600;
   min-width: 1.25rem;
   height: 1.25rem;
   border-radius: 1rem;
   display: flex;
   align-items: center;
   justify-content: center;
   padding: 0 0.375rem;
}

.empty-state {
   text-align: center;
   padding: 3rem 1.5rem;
}

.empty-state-icon {
   font-size: 3.5rem;
   color: #502121;
   margin-bottom: 1rem;
}

.empty-state h2 {
   font-size: 1.25rem;
   color: #1a1a1a;
   margin: 0 0 0.5rem;
}

.empty-state p {
   color: #666;
   margin: 0;
   font-size: 0.875rem;
}

@media (max-width: 768px) {
   .chat-list-container {
       margin: 0;
       border-radius: 0;
   }
}
</style>