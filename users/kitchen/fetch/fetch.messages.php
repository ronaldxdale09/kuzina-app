<?php
$kitchen_id =  $_COOKIE['kitchen_id']; // Example kitchen ID

// Fetch messages for the kitchen
$query = "SELECT * FROM kitchen_customer_messages WHERE kitchen_id = ? ORDER BY created_at ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();

?>