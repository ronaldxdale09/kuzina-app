<?php
include '../../../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if food ID is provided
    $food_id = $_POST['food_id'] ?? null;

    if ($food_id) {
        // Prepare the SQL delete statement
        $stmt = $conn->prepare("DELETE FROM food_listings WHERE food_id = ?");
        $stmt->bind_param("i", $food_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Item removed successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to remove item.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid item ID.']);
    }

    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
