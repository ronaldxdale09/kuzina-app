<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['withdrawal_id']) || !isset($data['action'])) {
        throw new Exception('Missing required parameters');
    }

    $withdrawal_id = intval($data['withdrawal_id']);
    $action = $data['action'];

    if (!in_array($action, ['completed', 'rejected'])) {
        throw new Exception('Invalid action');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get withdrawal details first
        $stmt = $conn->prepare("
            SELECT w.*, k.balance as kitchen_balance 
            FROM kitchen_withdrawals w
            JOIN kitchens k ON w.kitchen_id = k.kitchen_id
            WHERE w.withdrawal_id = ? AND w.status = 'pending'
        ");
        $stmt->bind_param("i", $withdrawal_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $withdrawal = $result->fetch_assoc();

        if (!$withdrawal) {
            throw new Exception('Withdrawal request not found or already processed');
        }

        // Update withdrawal status
        $update_stmt = $conn->prepare("
            UPDATE kitchen_withdrawals 
            SET status = ?, 
                completion_date = NOW() 
            WHERE withdrawal_id = ?
        ");
        $update_stmt->bind_param("si", $action, $withdrawal_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception('Failed to update withdrawal status');
        }

        // If rejected, return the amount to kitchen's balance
        if ($action === 'rejected') {
            $refund_stmt = $conn->prepare("
                UPDATE kitchens 
                SET balance = balance + ? 
                WHERE kitchen_id = ?
            ");
            $refund_stmt->bind_param("di", $withdrawal['amount'], $withdrawal['kitchen_id']);
            
            if (!$refund_stmt->execute()) {
                throw new Exception('Failed to refund amount');
            }
        }

        // Create notification for kitchen
        $notification_stmt = $conn->prepare("
            INSERT INTO notifications 
            (user_id, user_type, title, message) 
            VALUES (?, 'kitchen', ?, ?)
        ");
        
        $title = $action === 'completed' ? 'Withdrawal Approved' : 'Withdrawal Rejected';
        $message = $action === 'completed' 
            ? "Your withdrawal request for ₱" . number_format($withdrawal['amount'], 2) . " has been approved."
            : "Your withdrawal request for ₱" . number_format($withdrawal['amount'], 2) . " has been rejected. The amount has been returned to your balance.";
        
        $notification_stmt->bind_param("iss", $withdrawal['kitchen_id'], $title, $message);
        
        if (!$notification_stmt->execute()) {
            throw new Exception('Failed to create notification');
        }

        // Log the action
        $log_stmt = $conn->prepare("
            INSERT INTO admin_logs 
            (admin_id, action_type, target_id, target_type, action_details, created_at) 
            VALUES (?, ?, ?, 'withdrawal', ?, NOW())
        ");
        
        $admin_id = $_SESSION['admin_id'] ?? 1; // Get from session or use default
        $action_type = $action === 'completed' ? 'approve_withdrawal' : 'reject_withdrawal';
        $details = json_encode([
            'action' => $action,
            'amount' => $withdrawal['amount'],
            'kitchen_id' => $withdrawal['kitchen_id']
        ]);
        
        $log_stmt->bind_param("isis", $admin_id, $action_type, $withdrawal_id, $details);
        $log_stmt->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Withdrawal request ' . ($action === 'completed' ? 'approved' : 'rejected') . ' successfully',
            'new_balance' => $withdrawal['kitchen_balance']
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>