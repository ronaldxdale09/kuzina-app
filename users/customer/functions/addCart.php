<?php
include '../../../connection/db.php'; // Include your DB connection

// Initialize the response array
$response = ['success' => false, 'message' => ''];

// Ensure that POST data exists
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $food_id = $_POST['food_id'];
    $quantity = $_POST['quantity'] ?? 1;
    $customer_id = $_SESSION['customer_id'] ?? 1; // Assuming you have customer_id stored in the session

    // Validate the food ID, quantity, and customer ID
    if ($food_id && $quantity > 0 && $customer_id) {
        // Prepare the statement to get the food listing
        $stmt = $conn->prepare("SELECT food_name, price FROM food_listings WHERE food_id = ?");
        
        // Check if statement preparation was successful
        if ($stmt === false) {
            $response['message'] = 'Failed to prepare the SQL statement.';
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param("i", $food_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // If the product is found
        if ($result && $result->num_rows > 0) {
            $food = $result->fetch_assoc();

            // Check if the item already exists in the cart_items table for this customer
            $cart_check_stmt = $conn->prepare("SELECT cart_item_id, quantity FROM cart_items WHERE customer_id = ? AND food_id = ?");
            
            if ($cart_check_stmt === false) {
                $response['message'] = 'Failed to prepare the cart check statement.';
                echo json_encode($response);
                exit;
            }

            $cart_check_stmt->bind_param("ii", $customer_id, $food_id);
            $cart_check_stmt->execute();
            $cart_check_result = $cart_check_stmt->get_result();

            if ($cart_check_result && $cart_check_result->num_rows > 0) {
                // Item already exists in the cart, so update the quantity
                $cart_item = $cart_check_result->fetch_assoc();
                $new_quantity = $cart_item['quantity'] + $quantity;

                $update_cart_stmt = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE cart_item_id = ?");
                
                if ($update_cart_stmt === false) {
                    $response['message'] = 'Failed to prepare the update statement.';
                    echo json_encode($response);
                    exit;
                }

                $update_cart_stmt->bind_param("ii", $new_quantity, $cart_item['cart_item_id']);
                $update_cart_stmt->execute();
                $update_cart_stmt->close();
            } else {
                // Insert new item into the cart_items table
                $insert_cart_stmt = $conn->prepare("INSERT INTO cart_items (customer_id, food_id, quantity) VALUES (?, ?, ?)");

                if ($insert_cart_stmt === false) {
                    $response['message'] = 'Failed to prepare the insert statement.';
                    echo json_encode($response);
                    exit;
                }

                $insert_cart_stmt->bind_param("iii", $customer_id, $food_id, $quantity);
                $insert_cart_stmt->execute();
                $insert_cart_stmt->close();
            }

            // Success response
            $response['success'] = true;
            $response['message'] = 'Item added to cart successfully!';
        } else {
            $response['message'] = 'Product not found!';
        }

        // Close statements
        $cart_check_stmt->close();
    } else {
        $response['message'] = 'Invalid product, quantity, or customer.';
    }

    // Close the database connection
    if (isset($stmt)) {
        $stmt->close(); // Only close if the statement was prepared
    }
    $conn->close();
}

// Return the response as JSON
echo json_encode($response);
?>
