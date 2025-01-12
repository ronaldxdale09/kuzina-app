<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $journal_id = $input['journal_id'] ?? 0;
    
    // Start transaction
    $conn->begin_transaction();

    // Delete the journal entry
    $delete_sql = "DELETE FROM food_journal WHERE journal_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $journal_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete journal entry");
    }

    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Journal entry deleted successfully'
    ]);

} catch (Exception $e) {
    $conn->rollback();
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