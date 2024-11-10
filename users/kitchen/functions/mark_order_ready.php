<?php
include '../../../connection/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order_id'])) {
    $order_id = intval($data['order_id']);
    $kitchen_id = $_COOKIE['kitchen_id']; // Get kitchen ID from cookie

    // First verify the order exists and belongs to this kitchen
    $check_sql = "SELECT order_status 
                  FROM orders 
                  WHERE order_id = ? 
                  AND kitchen_id = ? 
                  AND order_status = 'Preparing'";
    
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $order_id, $kitchen_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Update order status to Ready
        $sql = "UPDATE orders 
                SET order_status = 'For Pickup' 
                WHERE order_id = ? 
                AND kitchen_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $order_id, $kitchen_id);

        if ($stmt->execute()) {
            // Get customer details for notification
            $notify_sql = "SELECT 
                            o.order_id,
                            o.total_amount,
                            o.final_total_amount,
                            c.customer_id,
                            CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                            ua.street_address,
                            ua.city,
                            ua.zip_code
                          FROM orders o
                          JOIN customers c ON o.customer_id = c.customer_id
                          JOIN user_addresses ua ON o.address_id = ua.address_id
                          WHERE o.order_id = ?";
            
            $notify_stmt = $conn->prepare($notify_sql);
            $notify_stmt->bind_param("i", $order_id);
            $notify_stmt->execute();
            $customer_data = $notify_stmt->get_result()->fetch_assoc();

            echo json_encode([
                'success' => true,
                'message' => 'Order status updated to Ready.',
                'order_id' => $order_id,
                'customer_data' => $customer_data
            ]);

            $notify_stmt->close();
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to update order status.'
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Order not found or not in Preparing status.'
        ]);
    }

    $check_stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request.'
    ]);
}

$conn->close();
?>