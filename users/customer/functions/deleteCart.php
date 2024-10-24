<?php
include '../../../connection/db.php'; // Include DB connection

$response = ['success' => false, 'message' => ''];

// Check if the POST data exists
if (isset($_POST['food_id'])) {
    $food_id = intval($_POST['food_id']);
    $customer_id = 1; // Assume customer ID is stored in session

    if ($food_id > 0 && $customer_id > 0) {
        // Delete item from the cart
        $sql = "DELETE FROM cart_items WHERE food_id = $food_id AND customer_id = $customer_id";
        if ($conn->query($sql) === TRUE) {
            $response['success'] = true;
            $response['message'] = 'Item removed from cart.';
        } else {
            $response['message'] = 'Failed to remove item from cart.';
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