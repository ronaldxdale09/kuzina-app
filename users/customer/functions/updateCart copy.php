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
            // Update quantity in the cart
            $sql = "UPDATE cart_items SET quantity = $quantity WHERE food_id = $food_id AND customer_id = $customer_id";
            if ($conn->query($sql) === TRUE) {
                // Fetch the updated price for the item
                $result = $conn->query("SELECT price FROM food_listings WHERE food_id = $food_id");
                $food = $result->fetch_assoc();
                $updated_price = $food['price'] * $quantity;

                // Fetch the total cart value (bag total) for the customer
                $cart_result = $conn->query("SELECT SUM(fl.price * ci.quantity) as total FROM cart_items ci JOIN food_listings fl ON ci.food_id = fl.food_id WHERE ci.customer_id = $customer_id");
                $cart = $cart_result->fetch_assoc();
                $new_bag_total = $cart['total'];

                $response['success'] = true;
                $response['new_price'] = $updated_price;
                $response['new_bag_total'] = $new_bag_total;
            } else {
                $response['message'] = 'Failed to update cart.';
            }
        } else {
            // If quantity is zero, remove the item from the cart
            $sql = "DELETE FROM cart_items WHERE food_id = $food_id AND customer_id = $customer_id";
            if ($conn->query($sql) === TRUE) {
                // Fetch the total cart value (bag total) for the customer after deletion
                $cart_result = $conn->query("SELECT SUM(fl.price * ci.quantity) as total FROM cart_items ci JOIN food_listings fl ON ci.food_id = fl.food_id WHERE ci.customer_id = $customer_id");
                $cart = $cart_result->fetch_assoc();
                $new_bag_total = $cart['total'] ?? 0; // In case the cart is empty

                $response['success'] = true;
                $response['message'] = 'Item removed from cart.';
                $response['new_bag_total'] = $new_bag_total;
            } else {
                $response['message'] = 'Failed to remove item from cart.';
            }
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
