<?php
include '../../../connection/db.php'; // Include DB connection
header('Content-Type: application/json');
ini_set('display_errors', 0); // Hide errors in the response
ini_set('log_errors', 1); // Log errors to the server error log
error_reporting(E_ALL); // Report all errors

$response = ['success' => false];

// Check if the required parameters are set
if (!isset($_GET['status']) || !isset($_GET['payment_method']) || !isset($_GET['payment_token'])) {
    $response['message'] = 'Missing required parameters for payment verification.';
    echo json_encode($response);
    exit;
}

// Get and sanitize parameters
$status = $_GET['status'];
$paymentMethod = $_GET['payment_method'];
$token = $_GET['payment_token'];

try {
    // Debugging: Log received parameters
    error_log("Payment Verification Attempt - Status: $status, Payment Method: $paymentMethod, Token: $token");

    // Prepare the query to find the payment record based on the token and payment method
    $query = "SELECT * FROM payments WHERE payment_token = ? AND payment_method = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Failed to prepare the query: " . $conn->error);
    }

    $stmt->bind_param("ss", $token, $paymentMethod);
    if (!$stmt->execute()) {
        throw new Exception("Failed to execute the query: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        // Detailed error for debugging - confirming token and method values that did not match
        throw new Exception("No payment record found with token '$token' and payment method '$paymentMethod'.");
    }

    $paymentRecord = $result->fetch_assoc();

    if ($status === 'success') {
        // Update the payment status to "Completed"
        $updateQuery = "UPDATE payments SET payment_status = 'Completed' WHERE payment_token = ?";
        $updateStmt = $conn->prepare($updateQuery);
        if (!$updateStmt) {
            throw new Exception("Failed to prepare the update query: " . $conn->error);
        }

        $updateStmt->bind_param("s", $token);
        if (!$updateStmt->execute()) {
            throw new Exception("Failed to update the payment status: " . $updateStmt->error);
        }
        $_SESSION['payment_token']= $token;

        $response['success'] = true;
        $response['message'] = 'Payment verified and updated successfully.';
    } else {
        $response['message'] = 'Payment verification failed: payment status is not successful.';
    }

} catch (Exception $e) {
    // Log detailed error message for debugging
    error_log('Payment Verification Error: ' . $e->getMessage());
    $response['message'] = 'An error occurred during payment verification: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
