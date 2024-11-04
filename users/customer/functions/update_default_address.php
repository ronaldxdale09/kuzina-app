<?php
// Database connection
include '../../../connection/db.php'; // Include DB connection

$customer_id = $_COOKIE['user_id'];

if (isset($_POST['address_id'])) {
    $address_id = $_POST['address_id'];

    // First, set all addresses for this customer to is_default = 0
    $sql = "UPDATE customer_addresses SET is_default = 0 WHERE customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->close();

    // Now, set the selected address to is_default = 1
    $sql = "UPDATE customer_addresses SET is_default = 1 WHERE address_id = ? AND customer_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $address_id, $customer_id);
    $success = $stmt->execute();
    $stmt->close();

    // Return JSON response
    echo json_encode(['success' => $success]);
} else {
    // If address_id is not set, return an error response
    echo json_encode(['success' => false, 'message' => 'Address ID not provided.']);
}
$conn->close();
?>
