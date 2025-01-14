<?php
// First, create a PHP file (e.g., getDeliveryFee.php) to fetch the fee
include '../../../connection/db.php';
header('Content-Type: application/json');

$query = "SELECT setting_value FROM system_settings WHERE setting_key = 'rider_fee'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $fee = $result->fetch_assoc()['setting_value'];
    echo json_encode(['success' => true, 'fee' => floatval($fee)]);
} else {
    echo json_encode(['success' => false, 'fee' => 50]); // Default fallback
}
$conn->close();
?>