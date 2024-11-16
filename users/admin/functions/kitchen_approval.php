<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate inputs
    $kitchen_id = isset($_POST['kitchen_id']) ? intval($_POST['kitchen_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    if (!$kitchen_id) {
        throw new Exception('Invalid kitchen ID');
    }

    if (!in_array($action, ['approve', 'reject', 'suspend'])) {
        throw new Exception('Invalid action');
    }

    // Start transaction
    $conn->begin_transaction();

    // Update kitchen status based on action
    switch ($action) {
        case 'approve':
            // Update kitchen status to approved
            $stmt = $conn->prepare("UPDATE kitchens SET isApproved = 1 WHERE kitchen_id = ?");
            $stmt->bind_param("i", $kitchen_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to approve kitchen');
            }

            // Create notification for kitchen
            $title = "Application Approved";
            $message = "Your kitchen application has been approved. You can now start accepting orders.";
            break;

        case 'reject':
            if (empty($reason)) {
                throw new Exception('Reason is required for rejection');
            }

            // Update kitchen status to rejected
            $stmt = $conn->prepare("UPDATE kitchens SET isApproved = 0 WHERE kitchen_id = ?");
            $stmt->bind_param("i", $kitchen_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to reject kitchen');
            }

            // Create notification for kitchen
            $title = "Application Rejected";
            $message = "Your kitchen application has been rejected. Reason: " . $reason;
            break;

        case 'suspend':
            if (empty($reason)) {
                throw new Exception('Reason is required for suspension');
            }

            // Update kitchen status to suspended
            $stmt = $conn->prepare("UPDATE kitchens SET isApproved = 2 WHERE kitchen_id = ?");
            $stmt->bind_param("i", $kitchen_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to suspend kitchen');
            }

            // Create notification for kitchen
            $title = "Kitchen Suspended";
            $message = "Your kitchen has been suspended. Reason: " . $reason;
            break;
    }

    // Insert notification
    $notif_sql = "INSERT INTO notifications (user_id, user_type, title, message) VALUES (?, 'kitchen', ?, ?)";
    $notif_stmt = $conn->prepare($notif_sql);
    $notif_stmt->bind_param("iss", $kitchen_id, $title, $message);
    
    if (!$notif_stmt->execute()) {
        throw new Exception('Failed to create notification');
    }

    // Log the action
    $log_sql = "INSERT INTO admin_logs (admin_id, action_type, target_id, target_type, action_details, created_at) 
                VALUES (?, ?, ?, 'kitchen', ?, NOW())";
    $admin_id = $_COOKIE['admin_id'] ?? 0; // Assuming you store admin_id in cookie
    $log_stmt = $conn->prepare($log_sql);
    $action_details = json_encode([
        'action' => $action,
        'reason' => $reason,
        'previous_status' => null // You could fetch previous status if needed
    ]);
    $log_stmt->bind_param("isis", $admin_id, $action, $kitchen_id, $action_details);
    
    if (!$log_stmt->execute()) {
        throw new Exception('Failed to log action');
    }

    // Commit transaction
    $conn->commit();

    // Set success response
    $response['success'] = true;
    $response['message'] = 'Kitchen ' . ucfirst($action) . 'd successfully';

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }
    
    $response['message'] = $e->getMessage();
    
    // Log the error
    error_log("Kitchen approval error: " . $e->getMessage());
} finally {
    // Close any open statements
    if (isset($stmt)) $stmt->close();
    if (isset($notif_stmt)) $notif_stmt->close();
    if (isset($log_stmt)) $log_stmt->close();
    if (isset($conn)) $conn->close();
}

// Send response
echo json_encode($response);
?>