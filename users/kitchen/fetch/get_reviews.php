<?php
include '../../../connection/db.php';
header('Content-Type: application/json');

try {
    $kitchen_id = $_COOKIE['kitchen_id'] ?? null;
    
    if (!$kitchen_id) {
        throw new Exception('Kitchen ID not found');
    }

    // Updated query to include food details
    $query = "SELECT 
                r.review_id,
                r.rating,
                r.comment,
                r.created_at,
                CONCAT(c.first_name, ' ', c.last_name) as customer_name,
                f.food_name,
                f.photo1 as food_photo,
                o.order_id
              FROM reviews r
              JOIN customers c ON r.customer_id = c.customer_id
              JOIN food_listings f ON r.food_id = f.food_id
              JOIN orders o ON r.order_id = o.order_id
              WHERE r.kitchen_id = ?
              ORDER BY r.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $reviews = [];
    while ($row = $result->fetch_assoc()) {
        $reviews[] = [
            'review_id' => $row['review_id'],
            'rating' => (int)$row['rating'],
            'comment' => htmlspecialchars($row['comment']),
            'created_at' => $row['created_at'],
            'customer_name' => htmlspecialchars($row['customer_name']),
            'food_name' => htmlspecialchars($row['food_name']),
            'food_photo' => $row['food_photo'],
            'order_id' => $row['order_id']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'reviews' => $reviews
    ]);

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