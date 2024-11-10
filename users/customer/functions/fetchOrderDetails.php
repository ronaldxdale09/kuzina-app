<?php
include '../../../connection/db.php'; // Include your DB connection

$customer_id = $_COOKIE['user_id'];

$sql = "SELECT ci.quantity, fl.price 
        FROM cart_items ci
        JOIN food_listings fl ON ci.food_id = fl.food_id
        WHERE ci.customer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

$totalAmount = 0; // Initialize total amount
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
        $quantity = htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8');
        $totalAmount += $price * $quantity;
    }

    // Return updated order details in JSON format
    echo json_encode([
        'success' => true,
        'bag_total' => number_format($totalAmount, 2),
        'total_amount' => number_format($totalAmount + 50, 2), // 50 is delivery fee
        'coupon_discount' => '0.00', // Default coupon discount
        'delivery_fee' => '50.00'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No items in the cart']);
}

$stmt->close();
?>
