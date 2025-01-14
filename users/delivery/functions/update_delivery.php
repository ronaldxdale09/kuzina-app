<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

function debug_log($message, $data = null) {
    $log_message = date('Y-m-d H:i:s') . " - " . $message;
    if ($data !== null) {
        $log_message .= ": " . print_r($data, true);
    }
    error_log($log_message);
}

$input = file_get_contents('php://input');
debug_log("Received status update request", $input);

$data = json_decode($input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    debug_log("JSON decode error", json_last_error_msg());
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

if (!isset($data['order_id']) || !isset($data['status'])) {
    debug_log("Missing parameters", $data);
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

$rider_id = $_COOKIE['rider_id'] ?? null;
if (!$rider_id) {
    debug_log("No rider ID found");
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$order_id = intval($data['order_id']);
$new_status = $data['status'];

$valid_statuses = ['For Pickup', 'On the Way', 'Delivered'];
if (!in_array($new_status, $valid_statuses)) {
    debug_log("Invalid status", $new_status);
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

$conn->begin_transaction();

try {
    // Get order details with coordinates
    $check_query = "SELECT o.*, 
                           k.fname as kitchen_name, 
                           k.kitchen_id,
                           c.first_name as customer_name,
                           c.customer_id, 
                           c.phone as customer_phone, 
                           ua.street_address,
                           ua.latitude, 
                           ua.longitude,
                           k.latitude as kitchen_lat, 
                           k.longitude as kitchen_lng,
                           o.final_total_amount,
                           o.delivery_fee
                   FROM orders o
                   JOIN kitchens k ON o.kitchen_id = k.kitchen_id
                   JOIN customers c ON o.customer_id = c.customer_id
                   LEFT JOIN user_addresses ua ON o.address_id = ua.address_id
                   WHERE o.order_id = ? AND o.rider_id = ?";
    
    $stmt = $conn->prepare($check_query);
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    
    $stmt->bind_param("ii", $order_id, $rider_id);
    if (!$stmt->execute()) {
        throw new Exception('Execute failed: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Order not found or not assigned to you');
    }

    $order = $result->fetch_assoc();
    debug_log("Current order details", $order);

    // Validate status transition
    $current_status = $order['order_status'];
    $valid_transition = false;
    
    switch ($current_status) {
        case 'For Pickup':
            $valid_transition = ($new_status === 'On the Way');
            break;
        case 'On the Way':
            $valid_transition = ($new_status === 'Delivered');
            break;
        default:
            $valid_transition = false;
    }

    if (!$valid_transition) {
        throw new Exception("Invalid status transition from {$current_status} to {$new_status}");
    }

    // Update order status
    $update_query = "UPDATE orders 
                    SET order_status = ?,
                        updated_at = NOW()
                    WHERE order_id = ? AND rider_id = ?";
    
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("sii", $new_status, $order_id, $rider_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to update order status: ' . $stmt->error);
    }

    // Update delivery details
    $delivery_status = ($new_status === 'Delivered') ? 'Completed' : 'In Progress';
    $delivery_update = "UPDATE delivery_details 
                       SET delivery_status = ?,
                           updated_at = NOW()
                       WHERE order_id = ? AND rider_id = ?";
    
    $stmt = $conn->prepare($delivery_update);
    $stmt->bind_param("sii", $delivery_status, $order_id, $rider_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to update delivery status: ' . $stmt->error);
    }

    // Handle earnings when order is delivered
    if ($new_status === 'Delivered') {
        // Get platform commission rate
        $settings_query = "SELECT setting_value FROM system_settings WHERE setting_key = 'kitchen_commission_rate'";
        $commission_result = $conn->query($settings_query);
        $commission_rate = ($commission_result && $commission_result->num_rows > 0) ? 
            floatval($commission_result->fetch_assoc()['setting_value']) : 15; // Default 15%

        // Calculate earnings
        $total_amount = $order['final_total_amount'];
        $rider_amount = $order['delivery_fee']; // Full delivery fee to rider
        $commission_amount = ($total_amount * $commission_rate) / 100;
        $kitchen_amount = $total_amount - $commission_amount;

        // Insert platform earnings
        $platform_earnings_sql = "INSERT INTO platform_earnings 
            (order_id, order_amount, commission_rate, commission_amount, status) 
            VALUES (?, ?, ?, ?, 'completed')";
        $stmt = $conn->prepare($platform_earnings_sql);
        if (!$stmt) {
            throw new Exception('Failed to prepare platform earnings statement: ' . $conn->error);
        }
        $stmt->bind_param("iddd", $order_id, $total_amount, $commission_rate, $commission_amount);
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert platform earnings: ' . $stmt->error);
        }

        // Update kitchen balance
        $kitchen_update_sql = "UPDATE kitchens 
                             SET balance = balance + ? 
                             WHERE kitchen_id = ?";
        $stmt = $conn->prepare($kitchen_update_sql);
        $stmt->bind_param("di", $kitchen_amount, $order['kitchen_id']);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update kitchen balance');
        }

        // Update rider balance
        $rider_update_sql = "UPDATE delivery_riders 
                           SET balance = balance + ? 
                           WHERE rider_id = ?";
        $stmt = $conn->prepare($rider_update_sql);
        $stmt->bind_param("di", $rider_amount, $rider_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update rider balance');
        }

        // Insert kitchen earnings
        $kitchen_earnings_sql = "INSERT INTO kitchen_earnings 
            (kitchen_id, order_id, amount) 
            VALUES (?, ?, ?)";
        $stmt = $conn->prepare($kitchen_earnings_sql);
        $stmt->bind_param("iid", 
            $order['kitchen_id'],
            $order_id,
            $kitchen_amount  // Commission-adjusted amount
        );
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert kitchen earnings');
        }

        // Insert rider earnings
        $rider_earnings_sql = "INSERT INTO rider_earnings 
            (rider_id, order_id, amount) 
            VALUES (?, ?, ?)";
        $stmt = $conn->prepare($rider_earnings_sql);
        $stmt->bind_param("iid", 
            $rider_id,
            $order_id,
            $rider_amount
        );
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert rider earnings');
        }

        debug_log("Earnings recorded and balances updated", [
            'total_amount' => $total_amount,
            'commission_rate' => $commission_rate,
            'commission_amount' => $commission_amount,
            'kitchen_amount' => $kitchen_amount,
            'rider_amount' => $rider_amount,
            'kitchen_id' => $order['kitchen_id'],
            'rider_id' => $rider_id
        ]);
    }

    // Update rider routes when status changes to 'On the Way'
    if ($new_status === 'On the Way' && isset($data['current_lat'], $data['current_lng'])) {
        $route_insert = "INSERT INTO rider_routes 
                        (order_id, rider_id, 
                         start_latitude, start_longitude,
                         end_latitude, end_longitude,
                         current_latitude, current_longitude)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($route_insert);
        $stmt->bind_param("iiiddddd", 
            $order_id,
            $rider_id,
            $order['kitchen_lat'],
            $order['kitchen_lng'],
            $order['latitude'],
            $order['longitude'],
            $data['current_lat'],
            $data['current_lng']
        );
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to insert rider route: ' . $stmt->error);
        }
    }

    // Create notifications
    $notifications = [];
    if ($new_status === 'On the Way') {
        $notifications[] = [
            'user_id' => $order['customer_id'],
            'user_type' => 'customer',
            'title' => 'Order Picked Up',
            'message' => "Your order #$order_id has been picked up and is on the way"
        ];
        $notifications[] = [
            'user_id' => $order['kitchen_id'],
            'user_type' => 'kitchen',
            'title' => 'Order Picked Up',
            'message' => "Order #$order_id has been picked up by the rider"
        ];
    } elseif ($new_status === 'Delivered') {
        $notifications[] = [
            'user_id' => $order['customer_id'],
            'user_type' => 'customer',
            'title' => 'Order Delivered',
            'message' => "Your order #$order_id has been delivered"
        ];
        $notifications[] = [
            'user_id' => $order['kitchen_id'],
            'user_type' => 'kitchen',
            'title' => 'Order Delivered',
            'message' => "Order #$order_id has been delivered successfully"
        ];
    }

    // Insert notifications
    if (!empty($notifications)) {
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
                throw new Exception('Failed to create notification: ' . $stmt->error);
            }
        }
    }

    $conn->commit();
    debug_log("Status update successful", [
        'order_id' => $order_id,
        'new_status' => $new_status
    ]);

    echo json_encode([
        'success' => true,
        'message' => "Order status updated to $new_status",
        'order_id' => $order_id,
        'status' => $new_status
    ]);

} catch (Exception $e) {
    $conn->rollback();
    debug_log("Error updating status", [
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

$conn->close();
debug_log("Connection closed");
?>