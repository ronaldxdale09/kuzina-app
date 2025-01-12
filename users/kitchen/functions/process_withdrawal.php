<?php
include '../../../connection/db.php';
header('Content-Type: application/json');


try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Get kitchen ID from cookie/session
    $kitchen_id = $_COOKIE['kitchen_id'] ?? $_SESSION['kitchen_id'] ?? null;
    
    if (!$kitchen_id) {
        throw new Exception('Kitchen ID not found');
    }

    // Validate required fields
    if (!isset($data['amount']) || !isset($data['payment_method'])) {
        throw new Exception('Missing required fields');
    }

    // Validate amount
    $amount = floatval($data['amount']);
    if ($amount < 100) {
        throw new Exception('Minimum withdrawal amount is ₱100');
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Check current balance
        $balance_stmt = $conn->prepare("SELECT balance FROM kitchens WHERE kitchen_id = ? FOR UPDATE");
        $balance_stmt->bind_param("i", $kitchen_id);
        $balance_stmt->execute();
        $result = $balance_stmt->get_result();
        $kitchen = $result->fetch_assoc();

        if (!$kitchen) {
            throw new Exception('Kitchen not found');
        }

        if ($amount > $kitchen['balance']) {
            throw new Exception('Insufficient balance');
        }

        // Create withdrawal request
        $stmt = $conn->prepare(
            "INSERT INTO kitchen_withdrawals 
            (kitchen_id, amount, status, request_date, payment_method, payment_details) 
            VALUES (?, ?, 'pending', NOW(), ?, ?)"
        );
        
        $payment_details = $data['account_number'] ?? '';
        $stmt->bind_param("idss", $kitchen_id, $amount, $data['payment_method'], $payment_details);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create withdrawal request');
        }

        // Update kitchen balance
        $update_stmt = $conn->prepare(
            "UPDATE kitchens 
            SET balance = balance - ? 
            WHERE kitchen_id = ?"
        );
        $update_stmt->bind_param("di", $amount, $kitchen_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception('Failed to update balance');
        }

        // Create notification for kitchen
        $notification_stmt = $conn->prepare(
            "INSERT INTO notifications 
            (user_id, user_type, title, message) 
            VALUES (?, 'kitchen', 'Withdrawal Request', ?)"
        );
        
        $notification_message = "Your withdrawal request for ₱" . number_format($amount, 2) . " has been submitted and is pending approval.";
        $notification_stmt->bind_param("is", $kitchen_id, $notification_message);
        $notification_stmt->execute();

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully',
            'withdrawal_amount' => $amount,
            'new_balance' => $kitchen['balance'] - $amount
        ]);

    } catch (Exception $e) {
        // Rollback transaction on error
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