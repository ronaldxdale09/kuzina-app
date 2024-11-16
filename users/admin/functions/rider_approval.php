<?php
include '../../../connection/db.php';header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validate inputs
    $rider_id = isset($_POST['rider_id']) ? intval($_POST['rider_id']) : 0;
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

    if (!$rider_id) {
        throw new Exception('Invalid rider ID');
    }

    if (!in_array($action, ['approve', 'reject', 'suspend'])) {
        throw new Exception('Invalid action');
    }

    // Start transaction
    $conn->begin_transaction();

    // Update rider status based on action
    switch ($action) {
        case 'approve':
            // Verify that documents exist
            $doc_check_sql = "SELECT * FROM rider_documents WHERE rider_id = ?";
            $doc_check_stmt = $conn->prepare($doc_check_sql);
            $doc_check_stmt->bind_param("i", $rider_id);
            $doc_check_stmt->execute();
            $doc_result = $doc_check_stmt->get_result();

            if ($doc_result->num_rows === 0) {
                throw new Exception('Cannot approve rider without proper documentation');
            }

            // Update rider approval status and verify documents
            $stmt = $conn->prepare("UPDATE delivery_riders SET isApproved = 1 WHERE rider_id = ?");
            $stmt->bind_param("i", $rider_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to approve rider');
            }

            // Mark documents as verified
            $verify_docs = $conn->prepare("UPDATE rider_documents SET is_verified = 1, verified_at = NOW() WHERE rider_id = ?");
            $verify_docs->bind_param("i", $rider_id);
            $verify_docs->execute();

            // Create notification
            $title = "Application Approved";
            $message = "Your rider application has been approved. You can now start accepting delivery requests.";
            break;

        case 'reject':
            if (empty($reason)) {
                throw new Exception('Reason is required for rejection');
            }

            // Update rider status to rejected
            $stmt = $conn->prepare("UPDATE delivery_riders SET isApproved = 0 WHERE rider_id = ?");
            $stmt->bind_param("i", $rider_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to reject rider');
            }

            // Update document verification status
            $update_docs = $conn->prepare("UPDATE rider_documents SET is_verified = 0 WHERE rider_id = ?");
            $update_docs->bind_param("i", $rider_id);
            $update_docs->execute();

            // Create notification
            $title = "Application Rejected";
            $message = "Your rider application has been rejected. Reason: " . $reason;
            break;

        case 'suspend':
            if (empty($reason)) {
                throw new Exception('Reason is required for suspension');
            }

            // Update rider status to suspended (using 2 to indicate suspension)
            $stmt = $conn->prepare("UPDATE delivery_riders SET isApproved = 2 WHERE rider_id = ?");
            $stmt->bind_param("i", $rider_id);
            
            if (!$stmt->execute()) {
                throw new Exception('Failed to suspend rider');
            }

            // Set rider as unavailable
            $update_availability = $conn->prepare("UPDATE rider_availability SET is_available = 0 WHERE rider_id = ?");
            $update_availability->bind_param("i", $rider_id);
            $update_availability->execute();

            // Create notification
            $title = "Account Suspended";
            $message = "Your rider account has been suspended. Reason: " . $reason;
            break;
    }

    // Insert notification
    $notif_sql = "INSERT INTO notifications (user_id, user_type, title, message) VALUES (?, 'rider', ?, ?)";
    $notif_stmt = $conn->prepare($notif_sql);
    $notif_stmt->bind_param("iss", $rider_id, $title, $message);
    
    if (!$notif_stmt->execute()) {
        throw new Exception('Failed to create notification');
    }

    // Log the action
    $log_sql = "INSERT INTO admin_logs (admin_id, action_type, target_id, target_type, action_details, created_at) 
                VALUES (?, ?, ?, 'rider', ?, NOW())";
    $admin_id = $_COOKIE['admin_id'] ?? 0; // Assuming you store admin_id in cookie
    $log_stmt = $conn->prepare($log_sql);
    $action_details = json_encode([
        'action' => $action,
        'reason' => $reason,
        'previous_status' => null // You could fetch previous status if needed
    ]);
    $log_stmt->bind_param("isis", $admin_id, $action, $rider_id, $action_details);
    
    if (!$log_stmt->execute()) {
        throw new Exception('Failed to log action');
    }

    // Commit transaction
    $conn->commit();

    // Set success response
    $response['success'] = true;
    $response['message'] = 'Rider ' . ucfirst($action) . 'd successfully';

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }
    
    $response['message'] = $e->getMessage();
    
    // Log the error
    error_log("Rider approval error: " . $e->getMessage());
} finally {
    // Close any open statements
    if (isset($stmt)) $stmt->close();
    if (isset($notif_stmt)) $notif_stmt->close();
    if (isset($log_stmt)) $log_stmt->close();
    if (isset($doc_check_stmt)) $doc_check_stmt->close();
    if (isset($verify_docs)) $verify_docs->close();
    if (isset($update_docs)) $update_docs->close();
    if (isset($update_availability)) $update_availability->close();
    if (isset($conn)) $conn->close();
}

// Send response
echo json_encode($response);
?>