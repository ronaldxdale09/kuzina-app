<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid input data');
    }

    // Start transaction
    $conn->begin_transaction();

    // Prepare update statement
    $update_sql = "UPDATE system_settings SET 
                   setting_value = ?,
                   updated_at = NOW(),
                   updated_by = ?
                   WHERE setting_key = ?";
    $stmt = $conn->prepare($update_sql);

    if (!$stmt) {
        throw new Exception('Failed to prepare statement');
    }

    // Get admin ID from cookie or session
    $admin_id = $_COOKIE['admin_id'] ?? 1; // Default to 1 if not set

    // Update each setting
    foreach ($input as $key => $value) {
        $stmt->bind_param("sis", $value, $admin_id, $key);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update setting: $key");
        }
    }

    // Create log entry
    $log_sql = "INSERT INTO admin_logs (admin_id, action_type, target_type, action_details) 
                VALUES (?, 'update', 'settings', ?)";
    $log_stmt = $conn->prepare($log_sql);
    $action_details = json_encode($input);
    $log_stmt->bind_param("is", $admin_id, $action_details);
    
    if (!$log_stmt->execute()) {
        throw new Exception('Failed to create log entry');
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Settings updated successfully'
    ]);

} catch (Exception $e) {
    // Rollback on error
    if (isset($conn)) {
        $conn->rollback();
    }

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($log_stmt)) $log_stmt->close();
    if (isset($conn)) $conn->close();
}
?>