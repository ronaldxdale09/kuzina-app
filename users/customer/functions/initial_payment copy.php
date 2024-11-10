<?php
include '../../../connection/db.php'; // Include DB connection
header('Content-Type: application/json');
ini_set('display_errors', 0); // Disable error display for response
ini_set('log_errors', 1); // Log errors
error_reporting(E_ALL); // Report all errors

// Retrieve the JSON data from the request
$paymentData = json_decode(file_get_contents("php://input"), true);

// Check if the required parameters are provided
if (!isset($paymentData['payment_method']) || !isset($paymentData['amount']) || !isset($paymentData['payment_token'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters for creating a pending payment record.'
    ]);
    exit;
}

$paymentMethod = $paymentData['payment_method'];
$amount = $paymentData['amount'];
$paymentToken = $paymentData['payment_token'];
$customerId = $_COOKIE['user_id'] ?? null; // Assumes user_id is stored in a cookie

if (!$customerId) {
    echo json_encode([
        'success' => false,
        'message' => 'Customer ID is missing. Please ensure you are logged in.'
    ]);
    exit;
}

try {
    // Prepare and execute the SQL statement to insert the pending payment record
    $sql = "INSERT INTO payments (customer_id, payment_method, payment_status, amount, payment_token) VALUES (?, ?, 'Pending', ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $customerId, $paymentMethod, $amount, $paymentToken);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Pending payment record created successfully.',
            'payment_token' => $paymentToken
        ]);
    } else {
        throw new Exception('Failed to create pending payment record.');
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while creating the pending payment record: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
