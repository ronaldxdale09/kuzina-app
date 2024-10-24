<?php
include '../../../connection/db.php'; // Include DB connection

$response = ['success' => false, 'message' => '', 'new_price' => 0, 'new_bag_total' => 0];

// Check if the POST data exists
if (isset($_POST['food_id']) && isset($_POST['quantity'])) {
    $food_id = intval($_POST['food_id']);
    $quantity = intval($_POST['quantity']);
    $customer_id = 1; // Assume customer ID is stored in session

    if ($food_id > 0 && $customer_id > 0) {
        if ($quantity > 0) {
            // Update quantity in the cart with prepared statement
            $sql = "UPDATE cart_items SET quantity = ? WHERE food_id = ? AND customer_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $quantity, $food_id, $customer_id);

            if ($stmt->execute()) {
                // Fetch the updated price for the item
                $price_stmt = $conn->prepare("SELECT price FROM food_listings WHERE food_id = ?");
                $price_stmt->bind_param("i", $food_id);
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $food = $price_result->fetch_assoc();
                $updated_price = $food['price'] * $quantity;

                // Fetch the total cart value (bag total) for the customer
                $cart_stmt = $conn->prepare("SELECT SUM(fl.price * ci.quantity) as total FROM cart_items ci JOIN food_listings fl ON ci.food_id = fl.food_id WHERE ci.customer_id = ?");
                $cart_stmt->bind_param("i", $customer_id);
                $cart_stmt->execute();
                $cart_result = $cart_stmt->get_result();
                $cart = $cart_result->fetch_assoc();
                $new_bag_total = $cart['total'] ?? 0;

                $response['success'] = true;
                $response['new_price'] = $updated_price;
                $response['new_bag_total'] = $new_bag_total;
            } else {
                $response['message'] = 'Failed to update cart.';
            }
            $stmt->close();
        } else {
            // If quantity is zero, remove the item from the cart with prepared statement
            $sql = "DELETE FROM cart_items WHERE food_id = ? AND customer_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $food_id, $customer_id);

            if ($stmt->execute()) {
                // Fetch the total cart value (bag total) for the customer after deletion
                $cart_stmt = $conn->prepare("SELECT SUM(fl.price * ci.quantity) as total FROM cart_items ci JOIN food_listings fl ON ci.food_id = fl.food_id WHERE ci.customer_id = ?");
                $cart_stmt->bind_param("i", $customer_id);
                $cart_stmt->execute();
                $cart_result = $cart_stmt->get_result();
                $cart = $cart_result->fetch_assoc();
                $new_bag_total = $cart['total'] ?? 0;

                $response['success'] = true;
                $response['message'] = 'Item removed from cart.';
                $response['new_bag_total'] = $new_bag_total;
            } else {
                $response['message'] = 'Failed to remove item from cart.';
            }
            $stmt->close();
        }
    } else {
        $response['message'] = 'Invalid food or customer ID.';
    }
} else {
    $response['message'] = 'Missing data.';
}

// Return the response as JSON
echo json_encode($response);
?>
