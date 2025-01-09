<?php
include '../../../connection/db.php'; // Include DB connection

$kitchen_id = isset($_GET['kitchen_id']) ? (int)$_GET['kitchen_id'] : 0;
$customer_id = isset($_GET['customer_id']) ? (int)$_GET['customer_id'] : 0;

$stmt = $conn->prepare("SELECT * FROM kitchen_customer_messages 
    WHERE (customer_id = ? AND kitchen_id = ?) 
    ORDER BY created_at ASC");
$stmt->bind_param("ii", $customer_id, $kitchen_id);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

header('Content-Type: application/json');
echo json_encode($messages);