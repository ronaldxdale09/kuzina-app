<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    // Get and validate input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['food_id'], $input['kitchen_id'], $input['status'])) {
        throw new Exception('Missing required parameters');
    }

    $food_id = intval($input['food_id']);
    $kitchen_id = intval($input['kitchen_id']);
    $status = intval($input['status']);
    $reason = isset($input['reason']) ? trim($input['reason']) : '';

    // Validate status
    if (!in_array($status, [0, 1])) {
        throw new Exception('Invalid status value');
    }

    // If rejecting, require a reason
    if ($status === 0 && empty($reason)) {
        throw new Exception('Reason is required for rejection');
    }

    // Start transaction
    $conn->begin_transaction();

    // Get food item details for notification
    $food_query = "SELECT food_name FROM food_listings WHERE food_id = ? AND kitchen_id = ?";
    $stmt = $conn->prepare($food_query);
    $stmt->bind_param("ii", $food_id, $kitchen_id);
    $stmt->execute();
    $food_result = $stmt->get_result();
    $food_details = $food_result->fetch_assoc();

    if (!$food_details) {
        throw new Exception('Food item not found');
    }

    // Update food status
    $update_sql = "UPDATE food_listings SET 
                   isApproved = ?,
                   listed = ?,
                   updated_at = NOW()
                   WHERE food_id = ? AND kitchen_id = ?";
                   
    $listed = ($status === 1) ? 1 : 0; // Set listed status same as approved status
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("iiii", $status, $listed, $food_id, $kitchen_id);

    if (!$stmt->execute()) {
        throw new Exception('Failed to update food status: ' . $stmt->error);
    }

    // Create notification
    $notification_sql = "INSERT INTO notifications 
                        (user_id, user_type, title, message, created_at) 
                        VALUES (?, 'kitchen', ?, ?, NOW())";

    $title = $status ? "Food Item Approved" : "Food Item Rejected";
    $message = $status ? 
        "Your food item '{$food_details['food_name']}' has been approved and is now visible to customers." : 
        "Your food item '{$food_details['food_name']}' has been rejected. Reason: $reason";

    $stmt = $conn->prepare($notification_sql);
    $stmt->bind_param("iss", $kitchen_id, $title, $message);

    if (!$stmt->execute()) {
        throw new Exception('Failed to create notification: ' . $stmt->error);
    }

    // Log the action
    $admin_id = $_COOKIE['admin_id'] ?? 0;
    $action_type = $status ? 'approve_food' : 'reject_food';
    $action_details = json_encode([
        'food_id' => $food_id,
        'food_name' => $food_details['food_name'],
        'status' => $status,
        'reason' => $reason
    ]);

    $log_sql = "INSERT INTO admin_logs 
                (admin_id, action_type, target_id, target_type, action_details, created_at)
                VALUES (?, ?, ?, 'food', ?, NOW())";
    
    $stmt = $conn->prepare($log_sql);
    $stmt->bind_param("isis", $admin_id, $action_type, $food_id, $action_details);

    if (!$stmt->execute()) {
        throw new Exception('Failed to log action: ' . $stmt->error);
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => $status ? 
            'Food item approved successfully' : 
            'Food item rejected successfully',
        'details' => [
            'food_id' => $food_id,
            'status' => $status,
            'notification_sent' => true
        ]
    ]);

} catch (Exception $e) {
    // Rollback on error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    error_log("Food approval error: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'details' => [
            'error_type' => get_class($e),
            'error_line' => $e->getLine(),
            'error_file' => basename($e->getFile())
        ]
    ]);

} finally {
    // Close prepared statements and connection
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>