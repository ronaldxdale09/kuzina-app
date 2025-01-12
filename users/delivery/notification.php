<?php
include 'includes/header.php';

$rider_id = $_COOKIE['rider_id'] ?? null; // Get kitchen_id from cookie

$stmt = $conn->prepare("
    SELECT 
        notification_id,
        title,
        message,
        is_read,
        created_at
    FROM notifications 
    WHERE user_id = ? 
    AND user_type = 'rider'
    ORDER BY created_at DESC
");

$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<div class="notification-list-page">
    <header class="app-header">
        <div class="header-content">
            <a href="homepage.php" class="back-btn">
                <i class='bx bx-arrow-back'></i>
            </a>
            <h1 class="title">Notifications</h1>
        </div>
    </header>

    <main class="notification-list">
        <?php if (empty($notifications)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class='bx bx-bell'></i>
            </div>
            <h2>No Notifications</h2>
            <p>You'll see your notifications here</p>
        </div>
        <?php else: ?>
        <div class="notification-list-container">
            <?php foreach ($notifications as $notification): ?>
            <a href="javascript:void(0)" 
               onclick="handleNotificationClick(<?php echo $notification['notification_id']; ?>, '<?php echo $notification['title']; ?>')"
               class="notification-item <?php echo !$notification['is_read'] ? 'unread' : ''; ?>"
               data-notification-id="<?php echo $notification['notification_id']; ?>">
                <div class="notification-icon">
                    <?php
                    $icon = 'bx-bell';
                    if (strpos($notification['title'], 'Rider Assigned') !== false) {
                        $icon = 'bx-cycling';
                    } elseif (strpos($notification['title'], 'Order') !== false) {
                        $icon = 'bx-package';
                    } elseif (strpos($notification['title'], 'Application') !== false) {
                        $icon = 'bx-check-circle';
                    }
                    ?>
                    <i class='bx <?php echo $icon; ?>'></i>
                </div>
                <div class="notification-info">
                    <div class="notification-header">
                        <h2><?php echo htmlspecialchars($notification['title']); ?></h2>
                        <span class="time">
                            <?php 
                            $notifTime = strtotime($notification['created_at']);
                            $now = time();
                            $diff = $now - $notifTime;
                            
                            if ($diff < 60) {
                                echo "Just now";
                            } elseif ($diff < 3600) {
                                echo floor($diff/60) . "m ago";
                            } elseif ($diff < 86400) {
                                echo floor($diff/3600) . "h ago";
                            } else {
                                echo date('M j', $notifTime);
                            }
                            ?>
                        </span>
                    </div>
                    <div class="notification-message">
                        <p><?php echo htmlspecialchars($notification['message']); ?></p>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
</div>
<script>
function handleNotificationClick(notificationId, notificationType) {
    // First, mark the notification as read
    markAsRead(notificationId);

    // Then redirect based on notification type
    if (notificationType.includes('Order')) {
        window.location.href = 'order_list.php';
    } else if (notificationType.includes('Rider')) {
        window.location.href = 'order_list.php?tab=1';
    } else if (notificationType.includes('Application')) {
        window.location.href = 'settings.php';
    }
}

function markAsRead(notificationId) {
    // Send AJAX request to mark notification as read
    fetch('functions/mark_notification_read.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `notification_id=${notificationId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove unread class from the notification
            const notification = document.querySelector(`[data-notification-id="${notificationId}"]`);
            if (notification) {
                notification.classList.remove('unread');
            }
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>

<?php include 'includes/appbar.php'; ?>
<?php include 'includes/scripts.php'; ?>
<style>
   .notification-item {
    display: flex;
    padding: 1rem;
    gap: 1rem;
    border-bottom: 1px solid #f1f1f1;
    transition: all 0.2s ease;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
}

.notification-item:hover {
    background-color: #f8f9fa;
    transform: translateX(5px);
}

.notification-item:active {
    background-color: #f0f0f0;
    transform: translateX(0);
}

.notification-item.unread {
    background: #fff9f9;
}

.notification-item.unread:hover {
    background: #fff2f2;
}

.notification-list-page {
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
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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

.notification-list {
    padding: 4.5rem 0 1rem;
    max-width: 768px;
    margin: 0 auto;
}

.notification-list-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    margin: 1rem;
    overflow: hidden;
}

.notification-item {
    display: flex;
    padding: 1rem;
    gap: 1rem;
    border-bottom: 1px solid #f1f1f1;
    transition: background-color 0.2s;
}

.notification-item.unread {
    background: #fff9f9;
}

.notification-icon {
    width: 40px;
    height: 40px;
    background: #fff2f2;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #502121;
    font-size: 1.25rem;
}

.notification-info {
    flex: 1;
    min-width: 0;
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.25rem;
}

.notification-header h2 {
    font-size: 1rem;
    font-weight: 600;
    margin: 0;
    color: #1a1a1a;
}

.time {
    font-size: 0.813rem;
    color: #666;
    white-space: nowrap;
    margin-left: 1rem;
}

.notification-message p {
    margin: 0;
    font-size: 0.875rem;
    color: #666;
    line-height: 1.4;
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
    .notification-list-container {
        margin: 0;
        border-radius: 0;
    }
}
</style>