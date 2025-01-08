<?php
include '../../../connection/db.php'; // Include DB connection

header('Content-Type: application/json');

$response = ['success' => false, 'cart_count' => 0, 'message' => ''];

if (isset($_COOKIE['user_id'])) {
    $customerId =  $_COOKIE['user_id']; // Get the customer ID from the session

    try {
        $sql = "SELECT COUNT(*) AS cart_count FROM cart_items WHERE customer_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $response['success'] = true;
        $response['cart_count'] = $row['cart_count']; // Return the cart item count
    } catch (Exception $e) {
        $response['message'] = 'Error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Customer not logged in.';
}

echo json_encode($response);
?>