<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

$response = [
    'success' => false,
    'settings' => null,
    'message' => ''
];

try {
    $query = "SELECT setting_key, setting_value FROM system_settings 
              WHERE setting_key IN ('rider_fee', 'min_order_amount', 'max_delivery_radius')";
    $result = $conn->query($query);
    
    if ($result) {
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        
        $response['success'] = true;
        $response['settings'] = $settings;
    } else {
        throw new Exception("Error fetching settings");
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} finally {
    if (isset($conn)) $conn->close();
}

echo json_encode($response);
?>