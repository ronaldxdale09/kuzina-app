<?php
include 'includes/header.php';

$kitchen_id = isset($_GET['kitchen_id']) ? (int)$_GET['kitchen_id'] : 0;
$customer_id = $_COOKIE['user_id'];

// Fetch kitchen details
$stmt = $conn->prepare("SELECT fname, lname, photo FROM kitchens WHERE kitchen_id = ?");
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$kitchen = $stmt->get_result()->fetch_assoc();

// Fetch messages function
function fetchMessages($conn, $customer_id, $kitchen_id) {
   $stmt = $conn->prepare("SELECT * FROM kitchen_customer_messages 
       WHERE (customer_id = ? AND kitchen_id = ?) 
       ORDER BY created_at ASC");
   $stmt->bind_param("ii", $customer_id, $kitchen_id);
   $stmt->execute();
   return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Initial messages fetch
$messages = fetchMessages($conn, $customer_id, $kitchen_id);

// Mark messages as read
$stmt = $conn->prepare("UPDATE kitchen_customer_messages 
   SET is_read = 1 
   WHERE customer_id = ? AND kitchen_id = ? AND sender_role = 'kitchen'");
$stmt->bind_param("ii", $customer_id, $kitchen_id);
$stmt->execute();
?>

<link rel="stylesheet" href="assets/css/messenger.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<body class="chat-app">

    <!-- Header -->
    <header class="chat-header">
        <div class="header-content">
            <a href="javascript:void(0);" onclick="window.history.back();" class="back-btn">
                <i class='bx bx-arrow-back'></i>
            </a>
            <div class="contact-info">
                <img src="../../uploads/kitchen_photos/<?php echo htmlspecialchars($kitchen['photo'] ?? 'assets/images/default-avatar.png'); ?>"
                    alt="Kitchen" class="contact-avatar">
                <div class="contact-details">
                    <h1><?php echo htmlspecialchars($kitchen['fname'] . ' ' . $kitchen['lname']); ?></h1>
                    <span class="status">Online</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Chat Area -->
    <main class="chat-area">
        <div class="messages-container">
            <?php if (empty($messages)): ?>
            <!-- Empty state HTML remains the same -->
            <?php else: ?>
            <?php 
                $currentDate = '';
                foreach ($messages as $message): 
                    $messageDate = date('Y-m-d', strtotime($message['created_at']));
                    if ($messageDate != $currentDate):
                        $currentDate = $messageDate;
                ?>
            <div class="date-divider">
                <span><?php echo date('F j, Y', strtotime($currentDate)); ?></span>
            </div>
            <?php endif; ?>

            <div class="message <?php echo ($message['sender_role'] === 'customer') ? 'outgoing' : 'incoming'; ?>">
                <div class="message-content">
                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                    <div class="message-meta">
                        <span class="time"><?php echo date('h:i A', strtotime($message['created_at'])); ?></span>
                        <?php if ($message['sender_role'] === 'customer'): ?>
                        <i class='bx <?php echo $message['is_read'] ? 'bx-check-double' : 'bx-check'; ?>'></i>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <footer class="chat-input">
        <form id="messageForm" class="input-form">
            <input type="hidden" name="kitchen_id" value="<?php echo $kitchen_id; ?>">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            <div class="input-container">
                <textarea name="message" placeholder="Type a message..." required></textarea>
                <button type="submit" class="send-btn">
                    <i class='bx bxs-paper-plane'></i>

                </button>
            </div>
        </form>
    </footer>


    <script>
    async function updateMessages() {
        try {
            const response = await fetch(
                `fetch/messenger.get_messages.php?kitchen_id=<?php echo $kitchen_id; ?>&customer_id=<?php echo $customer_id; ?>`
            );
            const messages = await response.json();

            if (messages.length > 0) {
                const container = document.querySelector('.messages-container');
                const isScrolledToBottom = container.scrollHeight - container.clientHeight <= container.scrollTop +
                    1;
                let html = '';
                let currentDate = '';

                messages.forEach(message => {
                    const messageDate = new Date(message.created_at).toISOString().split('T')[0];

                    // Handle date divider
                    if (messageDate !== currentDate) {
                        currentDate = messageDate;
                        html += `
                        <div class="date-divider">
                            <span>${new Date(currentDate).toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric' 
                            })}</span>
                        </div>`;
                    }

                    // Escape HTML to prevent XSS
                    const escapedMessage = message.message
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");

                    // Build message HTML
                    html += `
                    <div class="message ${message.sender_role === 'customer' ? 'outgoing' : 'incoming'}">
                        <div class="message-content">
                            <p>${escapedMessage}</p>
                            <div class="message-meta">
                                <span class="time">${new Date(message.created_at).toLocaleTimeString('en-US', { 
                                    hour: 'numeric', 
                                    minute: '2-digit', 
                                    hour12: true 
                                })}</span>
                                ${message.sender_role === 'customer' ? `
                                    <i class='bx ${message.is_read ? 'bx-check-double' : 'bx-check'}'></i>
                                ` : ''}
                            </div>
                        </div>
                    </div>`;
                });

                container.innerHTML = html;

                // Smooth scroll to bottom only if user was already at bottom or it's a new message
                if (isScrolledToBottom) {
                    setTimeout(() => {
                        container.scrollTo({
                            top: container.scrollHeight,
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    // Handle message form submission
    document.getElementById('messageForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const textarea = form.querySelector('textarea');
        const message = textarea.value.trim();

        if (!message) return;

        const formData = new FormData(form);
        formData.append('sender_role', 'customer');

        try {
            textarea.disabled = true;
            const response = await fetch('functions/send_message.php', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                textarea.value = '';
                await updateMessages();

                // Force scroll to bottom after sending a message
                const container = document.querySelector('.messages-container');
                setTimeout(() => {
                    container.scrollTo({
                        top: container.scrollHeight,
                        behavior: 'smooth'
                    });
                }, 100);
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            textarea.disabled = false;
            textarea.focus(); // Return focus to textarea
        }
    });

    // Auto-resize textarea
    const textarea = document.querySelector('textarea');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });

    // Update messages every 5 seconds
    const messageUpdateInterval = setInterval(updateMessages, 5000);

    // Initial load
    window.onload = () => {
        updateMessages();
    };

    // Cleanup interval on page unload
    window.addEventListener('unload', () => {
        clearInterval(messageUpdateInterval);
    });
    </script>
</body>

</html>