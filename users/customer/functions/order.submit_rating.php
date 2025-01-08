<?php
include '../../../connection/db.php'; // Include DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['order_id']) || !isset($data['reviews'])) {
        echo json_encode(['success' => false, 'message' => 'Missing required data']);
        exit;
    }
    
    $order_id = $data['order_id'];
    $reviews = $data['reviews'];
    
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Get order details
        $stmt = $conn->prepare("SELECT customer_id, kitchen_id FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        
        if (!$order) {
            throw new Exception('Order not found');
        }
        
        // Insert reviews
        $stmt = $conn->prepare("INSERT INTO reviews (order_id, food_id, customer_id, kitchen_id, rating, comment) 
                              VALUES (?, ?, ?, ?, ?, ?)");
        
        foreach ($reviews as $item_id => $review) {
            if ($review['rating'] > 0) { // Only insert if there's a rating
                // Get food_id from order_item
                $foodStmt = $conn->prepare("SELECT food_id FROM order_items WHERE order_item_id = ?");
                $foodStmt->bind_param("i", $item_id);
                $foodStmt->execute();
                $foodResult = $foodStmt->get_result()->fetch_assoc();
                
                if ($foodResult) {
                    $stmt->bind_param("iiiiss", 
                        $order_id,
                        $foodResult['food_id'],
                        $order['customer_id'],
                        $order['kitchen_id'],
                        $review['rating'],
                        $review['comment']
                    );
                    $stmt->execute();
                }
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}