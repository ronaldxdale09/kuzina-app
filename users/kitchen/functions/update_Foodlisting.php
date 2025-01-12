<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    if (!isset($_POST['food_id']) || !isset($_POST['listed'])) {
        throw new Exception('Missing required parameters');
    }

    $food_id = (int)$_POST['food_id'];
    $listed = (int)$_POST['listed'];

    $query = "UPDATE food_listings SET listed = ? WHERE food_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $listed, $food_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        throw new Exception('Failed to update listing status');
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}

?>