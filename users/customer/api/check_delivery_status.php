<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    $order_id = filter_input(INPUT_GET, 'order', FILTER_VALIDATE_INT);
    if (!$order_id) {
        throw new Exception('Invalid order ID');
    }

    $query = "SELECT 
        o.order_status,
        o.order_date,
        o.rider_id,
        k.fname AS kitchen_name,
        k.latitude AS kitchen_lat,
        k.longitude AS kitchen_lng,
        CONCAT(dr.first_name, ' ', dr.last_name) AS rider_name,
        rr.current_latitude AS rider_lat,
        rr.current_longitude AS rider_lng,
        rr.start_latitude,
        rr.start_longitude,
        rr.end_latitude,
        rr.end_longitude,
        rr.last_updated_at AS location_updated_at,
        dd.estimated_delivery_time,
        dd.delivery_status,
        TIMESTAMPDIFF(MINUTE, o.order_date, NOW()) as minutes_elapsed
    FROM orders o
    JOIN kitchens k ON o.kitchen_id = k.kitchen_id
    LEFT JOIN delivery_riders dr ON o.rider_id = dr.rider_id
    LEFT JOIN rider_routes rr ON o.order_id = rr.order_id
    LEFT JOIN delivery_details dd ON o.order_id = dd.order_id
    WHERE o.order_id = ?";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception('Failed to prepare query: ' . $conn->error);
    }

    $stmt->bind_param("i", $order_id);
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $order = $result->fetch_assoc();

    if (!$order) {
        throw new Exception('Order not found');
    }

    $status_info = getStatusInfo($order);
    
    $response = [
        'success' => true,
        'status' => $order['order_status'],
        'status_message' => $status_info['message'],
        'delivery_time' => $status_info['time'],
        'minutes_elapsed' => $order['minutes_elapsed']
    ];

    // Add route information if available
    if ($order['rider_lat'] && $order['rider_lng']) {
        $response['route'] = [
            'current' => [
                'lat' => floatval($order['rider_lat']),
                'lng' => floatval($order['rider_lng']),
                'last_updated' => $order['location_updated_at']
            ],
            'start' => [
                'lat' => floatval($order['start_latitude']),
                'lng' => floatval($order['start_longitude'])
            ],
            'end' => [
                'lat' => floatval($order['end_latitude']),
                'lng' => floatval($order['end_longitude'])
            ]
        ];
        $response['rider_name'] = $order['rider_name'];
    }

    if ($order['estimated_delivery_time']) {
        $response['estimated_time'] = date('h:i A', strtotime($order['estimated_delivery_time']));
    }

    echo json_encode($response);

} catch (Exception $e) {
    error_log("Delivery Status Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function getStatusInfo($order) {
    switch ($order['order_status']) {
        case 'Pending':
            return ['message' => 'Order Confirmation', 'time' => 'Awaiting confirmation'];
        case 'Preparing':
            return ['message' => 'Kitchen Preparation', 'time' => '15-20 minutes'];
        case 'For Pickup':
            return ['message' => 'Dispatch in Progress', 'time' => 'Driver proceeding to restaurant'];
        case 'On the Way':
            if ($order['estimated_delivery_time']) {
                $remaining_minutes = max(0, ceil((strtotime($order['estimated_delivery_time']) - time()) / 60));
                $time = $remaining_minutes > 0 ? "Arriving in {$remaining_minutes} minutes" : "Arriving soon";
            } else {
                $time = 'On the way to your location';
            }
            return ['message' => 'Out for Delivery', 'time' => $time];
        case 'Delivered':
            return ['message' => 'Order Completed', 'time' => 'Delivered successfully'];
        case 'Cancelled':
            return ['message' => 'Order Cancelled', 'time' => 'Order has been cancelled'];
        default:
            return ['message' => 'Processing Order', 'time' => 'Status updating...'];
    }
}
?>