<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

$customer_id = $_COOKIE['user_id'];

try {
    // Query to get the total count of items and the total price from the cart
    $cart_sql = "SELECT 
                    COUNT(ci.food_id) AS item_count,
                    SUM(ci.quantity * fl.price) AS total_price
                 FROM cart_items ci
                 JOIN food_listings fl ON ci.food_id = fl.food_id
                 WHERE ci.customer_id = ?";
    
    $stmt = $conn->prepare($cart_sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Prepare the response data
    $item_count = $result['item_count'] ?? 0;
    $total_price = $result['total_price'] ?? 0.00;

    echo json_encode([
        'success' => true,
        'item_count' => $item_count,
        'total_price' => number_format($total_price, 2)
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while fetching cart data: ' . $e->getMessage()
    ]);
}

$conn->close();
