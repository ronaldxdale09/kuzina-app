<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    // Retrieve JSON data from the POST request
    $paymentData = json_decode(file_get_contents("php://input"), true);
    $customer_id = $_COOKIE['user_id']; // Assuming the user_id is stored in a cookie
    $payment_method = $paymentData['payment_method'];
    $amount = $paymentData['amount'];
    $payment_token = isset($paymentData['payment_token']) ? $paymentData['payment_token'] : null;

    // Check if payment method is valid
    if (!$payment_method || !$amount) {
        throw new Exception("Missing required payment information.");
    }

    // Set the initial payment status based on payment method
    $initial_status = ($payment_method === 'cod') ? 'Completed' : 'Pending';

    // Insert the initial payment record into the database
    $sql = "INSERT INTO payments (customer_id, payment_method, payment_status, amount, payment_token)
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Failed to prepare the insert statement: " . $conn->error);
    }

    $stmt->bind_param("issds", $customer_id, $payment_method, $initial_status, $amount, $payment_token);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute the insert statement: " . $stmt->error);
    }

    // Return a success response with payment ID
    echo json_encode([
        'success' => true,
        'message' => 'Initial payment record created successfully.',
        'payment_id' => $stmt->insert_id,
        'payment_token' => $payment_token
    ]);

    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred during initial payment setup: ' . $e->getMessage()
    ]);
}

$conn->close();
?>
