<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['notification_id'])) {
        throw new Exception('Notification ID is required');
    }

    $notification_id = (int)$_POST['notification_id'];
    
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE notification_id = ?");
    $stmt->bind_param("i", $notification_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to update notification');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}