
<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $rider_id = $_COOKIE['rider_id'] ?? $_SESSION['rider_id'] ?? null;
    
    if (!$rider_id) {
        throw new Exception('Rider ID not found');
    }

    if (!isset($data['amount']) || !isset($data['payment_method'])) {
        throw new Exception('Missing required fields');
    }

    $amount = floatval($data['amount']);
    if ($amount < 50) {
        throw new Exception('Minimum withdrawal amount is ₱50');
    }

    $conn->begin_transaction();

    try {
        // Check current balance
        $balance_stmt = $conn->prepare("SELECT balance FROM delivery_riders WHERE rider_id = ? FOR UPDATE");
        $balance_stmt->bind_param("i", $rider_id);
        $balance_stmt->execute();
        $result = $balance_stmt->get_result();
        $rider = $result->fetch_assoc();

        if (!$rider) {
            throw new Exception('Rider not found');
        }

        if ($amount > $rider['balance']) {
            throw new Exception('Insufficient balance');
        }

        // Create withdrawal request
        $stmt = $conn->prepare(
            "INSERT INTO rider_withdrawals 
            (rider_id, amount, status, request_date, payment_method, payment_details) 
            VALUES (?, ?, 'pending', NOW(), ?, ?)"
        );
        
        $payment_details = $data['account_number'] ?? '';
        $stmt->bind_param("idss", $rider_id, $amount, $data['payment_method'], $payment_details);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to create withdrawal request');
        }

        // Update rider balance
        $update_stmt = $conn->prepare(
            "UPDATE delivery_riders 
            SET balance = balance - ? 
            WHERE rider_id = ?"
        );
        $update_stmt->bind_param("di", $amount, $rider_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception('Failed to update balance');
        }

        // Create notification
        $notification_stmt = $conn->prepare(
            "INSERT INTO notifications 
            (user_id, user_type, title, message) 
            VALUES (?, 'rider', 'Withdrawal Request', ?)"
        );
        
        $notification_message = "Your withdrawal request for ₱" . number_format($amount, 2) . " has been submitted and is pending approval.";
        $notification_stmt->bind_param("is", $rider_id, $notification_message);
        $notification_stmt->execute();

        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Withdrawal request submitted successfully',
            'withdrawal_amount' => $amount,
            'new_balance' => $rider['balance'] - $amount
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