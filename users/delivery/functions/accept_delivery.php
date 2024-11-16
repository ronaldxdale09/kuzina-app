<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

// Enable error logging
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);
error_log("Starting multiple deliveries order acceptance process");

// Debug function
function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log_message .= ": " . print_r($data, true);
    }
    error_log($log_message);
}

// Get JSON data
$input = file_get_contents('php://input');
debug_log("Received input", $input);

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    debug_log("JSON decode error", json_last_error_msg());
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data: ' . json_last_error_msg()
    ]);
    exit;
}

// Validate required parameters
if (!isset($data['order_id']) || !isset($data['rider_id'])) {
    debug_log("Missing parameters", $data);
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$order_id = intval($data['order_id']);
$rider_id = intval($data['rider_id']);

debug_log("Processing order", ['order_id' => $order_id, 'rider_id' => $rider_id]);

// Start transaction
$conn->begin_transaction();

try {
    // Check if order is still available
    $check_query = "SELECT order_status, rider_id 
                   FROM orders 
                   WHERE order_id = ? 
                   AND order_status = 'For Pickup' 
                   AND rider_id IS NULL";
    
    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $order_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    debug_log("Order availability check result", ['rows' => $result->num_rows]);

    if ($result->num_rows === 0) {
        throw new Exception('Order is no longer available');
    }

    // Get rider's current active orders (for logging purposes only)
    $active_orders_check = "SELECT COUNT(*) as active_count 
                           FROM orders 
                           WHERE rider_id = ? 
                           AND order_status IN ('For Pickup', 'On the Way')";
    
    $stmt = $conn->prepare($active_orders_check);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $active_result = $stmt->get_result();
    $active_count = $active_result->fetch_assoc()['active_count'];
    debug_log("Current active deliveries for rider", ['active_count' => $active_count]);

    // Update the order with rider assignment
    $update_query = "UPDATE orders 
                    SET rider_id = ?, 
                        order_status = 'For Pickup'
                    WHERE order_id = ? 
                    AND order_status = 'For Pickup' 
                    AND rider_id IS NULL";
    
    $stmt = $conn->prepare($update_query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("ii", $rider_id, $order_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    debug_log("Order update result", ['affected_rows' => $stmt->affected_rows]);

    if ($stmt->affected_rows === 0) {
        throw new Exception('Failed to accept order - no rows updated');
    }

    // Create a delivery record
    $delivery_query = "INSERT INTO delivery_details 
                      (order_id, rider_id, delivery_status, created_at) 
                      VALUES (?, ?, 'Pending', NOW())";
    
    $stmt = $conn->prepare($delivery_query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("ii", $order_id, $rider_id);
    if (!$stmt->execute()) {
        debug_log("Delivery insert error", $stmt->error);
        throw new Exception('Failed to create delivery record: ' . $stmt->error);
    }

    $delivery_id = $stmt->insert_id;
    debug_log("Delivery record created", ['delivery_id' => $delivery_id]);

    // Get order details for notification
    $order_query = "SELECT o.order_id, o.customer_id, k.kitchen_id, k.fname as kitchen_name
                   FROM orders o
                   JOIN kitchens k ON o.kitchen_id = k.kitchen_id
                   WHERE o.order_id = ?";
    
    $stmt = $conn->prepare($order_query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("i", $order_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $order_details = $stmt->get_result()->fetch_assoc();
    debug_log("Order details", $order_details);

    // Create notifications
    $notifications = [
        [
            'user_id' => $order_details['customer_id'],
            'user_type' => 'customer',
            'title' => 'Rider Assigned',
            'message' => "A rider has been assigned to your order #$order_id"
        ],
        [
            'user_id' => $order_details['kitchen_id'],
            'user_type' => 'kitchen',
            'title' => 'Rider Assigned',
            'message' => "A rider has been assigned to order #$order_id"
        ]
    ];

    $notification_query = "INSERT INTO notifications 
                          (user_id, user_type, title, message, created_at) 
                          VALUES (?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($notification_query);
    foreach ($notifications as $notification) {
        $stmt->bind_param("isss", 
            $notification['user_id'],
            $notification['user_type'],
            $notification['title'],
            $notification['message']
        );
        if (!$stmt->execute()) {
            debug_log("Notification insert error", [
                'notification' => $notification,
                'error' => $stmt->error
            ]);
            throw new Exception('Failed to create notification: ' . $stmt->error);
        }
        debug_log("Notification created", $notification);
    }

    // Commit transaction
    $conn->commit();
    debug_log("Transaction committed successfully");

    echo json_encode([
        'success' => true,
        'message' => 'Order accepted successfully',
        'order_id' => $order_id,
        'delivery_id' => $delivery_id,
        'active_deliveries' => $active_count + 1
    ]);

} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    debug_log("Error occurred - transaction rolled back", [
        'error_message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug_info' => [
            'error_type' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}

// Close connection
$conn->close();
debug_log("Connection closed");
?>