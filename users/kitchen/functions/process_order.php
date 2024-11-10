<?php
include '../../../connection/db.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order_id'])) {
    $order_id = intval($data['order_id']);

    // Update the order status to "Preparing"
    $sql = "UPDATE orders SET order_status = 'Preparing' WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Order status updated to Preparing.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}

$conn->close();
?>