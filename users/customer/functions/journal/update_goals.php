<?php
include '../../../../connection/db.php';
header('Content-Type: application/json');

try {
    // Get customer ID from cookie
    $customer_id = $_COOKIE['user_id'] ?? null;
    
    if (!$customer_id) {
        throw new Exception('User not authenticated');
    }

    // Validate inputs
    $daily_calories = filter_var($_POST['daily_calories'] ?? 0, FILTER_VALIDATE_INT);
    $daily_protein = filter_var($_POST['daily_protein'] ?? 0, FILTER_VALIDATE_INT);
    $daily_carbs = filter_var($_POST['daily_carbs'] ?? 0, FILTER_VALIDATE_INT);
    $daily_fat = filter_var($_POST['daily_fat'] ?? 0, FILTER_VALIDATE_INT);

    // Basic validation
    if ($daily_calories < 0 || $daily_protein < 0 || $daily_carbs < 0 || $daily_fat < 0) {
        throw new Exception('Goals cannot be negative');
    }

    // Start transaction
    $conn->begin_transaction();

    // Deactivate current active goals
    $deactivate_sql = "UPDATE journal_goals 
                      SET is_active = 0 
                      WHERE customer_id = ? AND is_active = 1";
    $stmt = $conn->prepare($deactivate_sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();

    // Insert new goals
    $insert_sql = "INSERT INTO journal_goals 
                   (customer_id, daily_calories, daily_protein, daily_carbs, daily_fat, is_active) 
                   VALUES (?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iiiii", 
        $customer_id,
        $daily_calories,
        $daily_protein,
        $daily_carbs,
        $daily_fat
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to update goals');
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Goals updated successfully',
        'data' => [
            'daily_calories' => $daily_calories,
            'daily_protein' => $daily_protein,
            'daily_carbs' => $daily_carbs,
            'daily_fat' => $daily_fat
        ]
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn)) {
        $conn->rollback();
    }
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
?>