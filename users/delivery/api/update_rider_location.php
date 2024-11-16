<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$order_id = intval($input['order_id']);
$current_lat = floatval($input['current_lat']);
$current_lng = floatval($input['current_lng']);
$rider_id = $_COOKIE['rider_id'] ?? null;

try {
    // First get delivery coordinates from orders table
    $coords_sql = "SELECT ua.latitude as end_lat, ua.longitude as end_lng,
                         k.latitude as start_lat, k.longitude as start_lng
                  FROM orders o
                  JOIN user_addresses ua ON o.address_id = ua.address_id
                  JOIN kitchens k ON o.kitchen_id = k.kitchen_id
                  WHERE o.order_id = ?";
                  
    $coords_stmt = $conn->prepare($coords_sql);
    $coords_stmt->bind_param("i", $order_id);
    $coords_stmt->execute();
    $coords_result = $coords_stmt->get_result()->fetch_assoc();

    // Check if route record exists
    $check_sql = "SELECT route_id FROM rider_routes WHERE order_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $order_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        // Insert new record with all required fields
        $insert_sql = "INSERT INTO rider_routes 
                      (order_id, rider_id, 
                       start_latitude, start_longitude,
                       end_latitude, end_longitude,
                       current_latitude, current_longitude) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("iidddddd", 
            $order_id,
            $rider_id,
            $coords_result['start_lat'],
            $coords_result['start_lng'],
            $coords_result['end_lat'],
            $coords_result['end_lng'],
            $current_lat,
            $current_lng
        );
    } else {
        // Update only current location
        $update_sql = "UPDATE rider_routes 
                      SET current_latitude = ?,
                          current_longitude = ?,
                          last_updated_at = NOW()
                      WHERE order_id = ? AND rider_id = ?";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ddii", 
            $current_lat, 
            $current_lng, 
            $order_id,
            $rider_id
        );
    }
    
    $stmt->execute();
    echo json_encode([
        'success' => true,
        'message' => 'Location updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Error updating rider location: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>