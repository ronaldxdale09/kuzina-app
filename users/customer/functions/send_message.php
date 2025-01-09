<?php
include '../../../connection/db.php'; // Include DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_COOKIE['user_id'];
    $kitchen_id = (int)$_POST['kitchen_id'];
    $message = trim($_POST['message']);
    $sender_role = $_POST['sender_role'];

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO kitchen_customer_messages 
            (customer_id, kitchen_id, message, sender_role) 
            VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss", $customer_id, $kitchen_id, $message, $sender_role);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(['success' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to send message']);
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Message cannot be empty']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}