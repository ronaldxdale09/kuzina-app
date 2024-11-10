<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

// Input handling
$food_id = $_GET['food_id'] ?? null;

try {
    if (!$food_id) {
        throw new Exception("Food ID is missing.");
    }

    // Query to get kitchen details based on food ID
    $sql = "SELECT fl.kitchen_id, k.kitchen_name, k.photo, k.description 
            FROM food_listings fl
            JOIN kitchens k ON fl.kitchen_id = k.kitchen_id
            WHERE fl.food_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $food_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $kitchen = $result->fetch_assoc();
        echo json_encode(['success' => true, 'kitchen' => $kitchen]);
    } else {
        throw new Exception("Kitchen not found for the provided Food ID.");
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
