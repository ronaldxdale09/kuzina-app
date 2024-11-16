<?php 
include '../../../connection/db.php';
header('Content-Type: application/json');

// Input handling
$input = json_decode(file_get_contents("php://input"), true);
$customer_id = $_COOKIE['user_id'];
$payment_method = $input['payment_method'] ?? null;
$payment_token = $input['payment_token'] ?? null;
$is_cod = $input['is_cod'] ?? false;
$delivery_fee = 50;
try {
    if (!$payment_method) {
        throw new Exception("Payment method is missing.");
    }

    // Begin transaction
    $conn->begin_transaction();

    $total_amount = 0.0;

    // Retrieve cart items and calculate total amount
    $cart_items_sql = "SELECT ci.food_id, ci.quantity, fl.price, fl.kitchen_id 
                       FROM cart_items ci
                       JOIN food_listings fl ON ci.food_id = fl.food_id
                       WHERE ci.customer_id = ?";
    $stmt_cart = $conn->prepare($cart_items_sql);
    $stmt_cart->bind_param("i", $customer_id);
    $stmt_cart->execute();
    $cart_items = $stmt_cart->get_result();
    
    $kitchen_id = null;
    while ($item = $cart_items->fetch_assoc()) {
        $total_amount += $item['quantity'] * $item['price'];
        $kitchen_id = $item['kitchen_id'];
    }
    $stmt_cart->close();

    // Retrieve the customer's default address
    $address_sql = "SELECT address_id FROM user_addresses WHERE user_id = ? AND is_default = 1 LIMIT 1";
    $stmt_address = $conn->prepare($address_sql);
    $stmt_address->bind_param("i", $customer_id);
    $stmt_address->execute();
    $address = $stmt_address->get_result()->fetch_assoc();
    $stmt_address->close();

    if (!$address) {
        throw new Exception('Default address not found for the customer.');
    }
    $address_id = $address['address_id'];

    // Calculate final total amount (apply discount if needed)
    $discount_amount = 0.00;
    $final_total_amount = $total_amount - $discount_amount;

    // Step 4: Handle payment record first for non-COD orders
    $payment_id = null;
    if (!$is_cod) {
        // Logging payment token for debug
        error_log("Checking payment record for token: " . $payment_token);
        
        $payment_sql = "SELECT payment_id FROM payments WHERE payment_token = ? AND payment_status = 'Completed'";
        $stmt_payment = $conn->prepare($payment_sql);
        $stmt_payment->bind_param("s", $payment_token);
        $stmt_payment->execute();
        $payment = $stmt_payment->get_result()->fetch_assoc();
        $stmt_payment->close();
    
        if (!$payment) {
            throw new Exception('Payment not found or not verified. Token: ' . $payment_token);
        }
        
        $payment_id = $payment['payment_id'];
    }

    // Step 1: Create the order record with payment_id if it's available
    $sql_order = "INSERT INTO orders (customer_id, kitchen_id, address_id, total_amount, final_total_amount, order_status, payment_id,delivery_fee)
                  VALUES (?, ?, ?, ?, ?, 'Confirmed', ?, ?)";
    $stmt_order = $conn->prepare($sql_order);
    $stmt_order->bind_param("iiiddii", $customer_id, $kitchen_id, $address_id, $total_amount, $final_total_amount, $payment_id, $delivery_fee);

    if (!$stmt_order->execute()) {
        throw new Exception('Error creating order.');
    }

    $order_id = $stmt_order->insert_id;
    $stmt_order->close();

    // Step 2: Insert items into order_items
    $stmt_order_item = $conn->prepare("INSERT INTO order_items (order_id, food_id, quantity, price) VALUES (?, ?, ?, ?)");
    $cart_items->data_seek(0);  // Reset pointer

    while ($item = $cart_items->fetch_assoc()) {
        $stmt_order_item->bind_param("iiid", $order_id, $item['food_id'], $item['quantity'], $item['price']);
        $stmt_order_item->execute();
    }
    $stmt_order_item->close();

    // Step 3: Clear cart items
    $clear_cart_sql = "DELETE FROM cart_items WHERE customer_id = ?";
    $stmt_clear_cart = $conn->prepare($clear_cart_sql);
    $stmt_clear_cart->bind_param("i", $customer_id);
    $stmt_clear_cart->execute();
    $stmt_clear_cart->close();

    // Step 5: Handle payment record for COD (create a new payment entry with 'Pending' status)
    if ($is_cod) {
        $cod_payment_sql = "INSERT INTO payments (order_id, customer_id, payment_method, payment_status, amount)
                            VALUES (?, ?, 'COD', 'Pending', ?)";
        $stmt_cod_payment = $conn->prepare($cod_payment_sql);
        $stmt_cod_payment->bind_param("iid", $order_id, $customer_id, $final_total_amount);
        
        if (!$stmt_cod_payment->execute()) {
            throw new Exception('Error creating COD payment record.');
        }
        
        $stmt_cod_payment->close();
    } else {
        // Update payment record to link it to the order if it’s not COD
        $update_payment_sql = "UPDATE payments SET order_id = ? WHERE payment_id = ?";
        $stmt_update_payment = $conn->prepare($update_payment_sql);
        $stmt_update_payment->bind_param("ii", $order_id, $payment_id);
        $stmt_update_payment->execute();
        $stmt_update_payment->close();
    }

    // Commit transaction
    $conn->commit();

    echo json_encode(['success' => true, 'order_id' => $order_id, 'message' => 'Order created successfully.']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'An error occurred while creating the order: ' . $e->getMessage()]);
}
$conn->close();
