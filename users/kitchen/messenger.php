<?php
include 'includes/header.php';

$kitchen_id = $_COOKIE['kitchen_id'] ?? 0;
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

// Fetch customer details
$stmt = $conn->prepare("SELECT first_name, last_name, photo FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

// Fetch messages function
function fetchMessages($conn, $customer_id, $kitchen_id) {
   $stmt = $conn->prepare("SELECT * FROM kitchen_customer_messages 
       WHERE (customer_id = ? AND kitchen_id = ?) 
       ORDER BY created_at ASC");
   $stmt->bind_param("ii", $customer_id, $kitchen_id);
   $stmt->execute();
   return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

$messages = fetchMessages($conn, $customer_id, $kitchen_id);

// Mark messages as read
$stmt = $conn->prepare("UPDATE kitchen_customer_messages 
   SET is_read = 1 
   WHERE customer_id = ? AND kitchen_id = ? AND sender_role = 'customer'");
$stmt->bind_param("ii", $customer_id, $kitchen_id);
$stmt->execute();
?>

<link rel="stylesheet" href="assets/css/messenger.css">

<body class="chat-app">
    <header class="chat-header">
        <div class="header-content">
            <a href="javascript:void(0);" onclick="window.history.back();" class="back-btn">
                <i class='bx bx-arrow-back'></i>
            </a>
            <div class="contact-info">
                <img src="assets/images/avatar/avatar2.png" alt="Customer" class="contact-avatar">
                <div class="contact-details">
                    <h1><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></h1>
                    <span class="status">Online</span>
                </div>
            </div>
        </div>
    </header>

    <main class="chat-area">
        <div class="messages-container">
            <?php if (empty($messages)): ?>
            <div class="welcome-message">
                <div class="welcome-icon">
                    <i class='bx bx-message-dots'></i>
                </div>
                <h2>Start Your Conversation</h2>
                <p>Send a message to begin chatting with the customer</p>
            </div>
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

            <div class="message <?php echo ($message['sender_role'] === 'kitchen') ? 'outgoing' : 'incoming'; ?>">
                <div class="message-content">
                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                    <div class="message-meta">
                        <span class="time"><?php echo date('h:i A', strtotime($message['created_at'])); ?></span>
                        <?php if ($message['sender_role'] === 'kitchen'): ?>
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
                <button type="submit" class="send-btn">Send</button>
            </div>
        </form>
    </footer>

    <script>
    async function updateMessages() {
        try {
            const response = await fetch(
                `fetch/get_messages.php?kitchen_id=<?php echo $kitchen_id; ?>&customer_id=<?php echo $customer_id; ?>`
                );
            const messages = await response.json();

            if (messages.length > 0) {
                const container = document.querySelector('.messages-container');
                let html = '';
                let currentDate = '';

                messages.forEach(message => {
                    const messageDate = new Date(message.created_at).toISOString().split('T')[0];

                    if (messageDate !== currentDate) {
                        currentDate = messageDate;
                        html += `
                       <div class="date-divider">
                           <span>${new Date(currentDate).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</span>
                       </div>`;
                    }

                    html += `
                   <div class="message ${message.sender_role === 'kitchen' ? 'outgoing' : 'incoming'}">
                       <div class="message-content">
                           <p>${message.message}</p>
                           <div class="message-meta">
                               <span class="time">${new Date(message.created_at).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true })}</span>
                               ${message.sender_role === 'kitchen' ? `
                                   <i class='bx ${message.is_read ? 'bx-check-double' : 'bx-check'}'></i>
                               ` : ''}
                           </div>
                       </div>
                   </div>`;
                });

                container.innerHTML = html;
                container.scrollTop = container.scrollHeight;
            }
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    }

    document.getElementById('messageForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const form = e.target;
        const textarea = form.querySelector('textarea');
        const message = textarea.value.trim();

        if (!message) return;

        const formData = new FormData(form);
        formData.append('sender_role', 'kitchen');

        try {
            textarea.disabled = true;
            const response = await fetch('functions/send_message.php', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                textarea.value = '';
                await updateMessages();
            }
        } catch (error) {
            console.error('Error:', error);
        } finally {
            textarea.disabled = false;
        }
    });

    setInterval(updateMessages, 3000);

    window.onload = () => {
        updateMessages();
    };
    </script>
</body>