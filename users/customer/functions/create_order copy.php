<?php 
include '../../../connection/db.php';
header('Content-Type: application/json');

$customer_id = $_COOKIE['user_id'];
$payment_token  = $_SESSION['payment_token']; // Passed from the client

try {
    // Begin transaction
    $conn->begin_transaction();

    // Step 1: Retrieve verified payment record
    $sql = "SELECT payment_id, amount, payment_status FROM payments WHERE payment_token = ? AND payment_status = 'Completed'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $payment_token);
    $stmt->execute();
    $payment = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$payment) {
        throw new Exception('Payment not found or not verified.');
    }

    $payment_id = $payment['payment_id'];
    $total_amount = $payment['amount'];
    $final_total_amount = $total_amount; // Assuming final total amount is the same as the amount in this case

    // Step 2: Retrieve kitchen_id from one of the items in the cart
    $kitchen_sql = "SELECT fl.kitchen_id 
                    FROM cart_items ci
                    JOIN food_listings fl ON ci.food_id = fl.food_id
                    WHERE ci.customer_id = ?
                    LIMIT 1";
    $stmt_kitchen = $conn->prepare($kitchen_sql);
    $stmt_kitchen->bind_param("i", $customer_id);
    $stmt_kitchen->execute();
    $stmt_kitchen->bind_result($kitchen_id);
    $stmt_kitchen->fetch();
    $stmt_kitchen->close();

    if (!$kitchen_id) {
        throw new Exception('No kitchen ID found for the items in the cart.');
    }

    // Step 3: Create the order
    $sql_order = "INSERT INTO orders (customer_id, kitchen_id, payment_id, total_amount, order_status, final_total_amount)
                  VALUES (?, ?, ?, ?, 'Confirmed', ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("iiidd", $customer_id, $kitchen_id, $payment_id, $total_amount, $final_total_amount);

    if (!$stmt_order->execute()) {
        throw new Exception('Error creating order.');
    }

    $order_id = $stmt_order->insert_id;
    $stmt_order->close();

    // Step 4: Move items from cart to order_items, including price
    $cart_items_sql = "SELECT ci.food_id, ci.quantity, fl.price 
                       FROM cart_items ci
                       JOIN food_listings fl ON ci.food_id = fl.food_id
                       WHERE ci.customer_id = ?";
    $stmt_cart = $conn->prepare($cart_items_sql);
    $stmt_cart->bind_param("i", $customer_id);
    $stmt_cart->execute();
    $cart_items = $stmt_cart->get_result();

    $order_items_sql = "INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_order_item = $conn->prepare($order_items_sql);

    while ($item = $cart_items->fetch_assoc()) {
        $food_id = $item['food_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        
        $stmt_order_item->bind_param("iiid", $order_id, $food_id, $quantity, $price);
        if (!$stmt_order_item->execute()) {
            throw new Exception('Error moving items to order_items.');
        }
    }

    $stmt_order_item->close();

    // Step 5: Clear cart after order creation
    $clear_cart_sql = "DELETE FROM cart_items WHERE customer_id = ?";
    $stmt_clear_cart = $conn->prepare($clear_cart_sql);
    $stmt_clear_cart->bind_param("i", $customer_id);
    $stmt_clear_cart->execute();
    $stmt_clear_cart->close();

    // Step 6: Update the payments table with order_id
    $update_payment_sql = "UPDATE payments SET order_id = ? WHERE payment_id = ?";
    $stmt_update_payment = $conn->prepare($update_payment_sql);
    $stmt_update_payment->bind_param("ii", $order_id, $payment_id);
    $stmt_update_payment->execute();
    $stmt_update_payment->close();

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id, 'message' => 'Order created successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'An error occurred while creating the order: ' . $e->getMessage()]);
}
$conn->close();
