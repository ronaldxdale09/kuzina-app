<?php
// Fetch cart items for the customer
$sql = "SELECT ci.quantity, fl.food_name, fl.price
        FROM cart_items ci
        JOIN food_listings fl ON ci.food_id = fl.food_id
        WHERE ci.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

// Initialize totals
$bag_total = 0;
$delivery_fee = 50.00; // Fixed delivery fee
$total_amount = 0;
$items = [];

// Fetch and format each item in the cart
while ($row = $result->fetch_assoc()) {
    $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
    $quantity = (int)$row['quantity'];
    $price = (float)$row['price'];
    $item_total = $price * $quantity;
    $bag_total += $item_total;

    $items[] = [
        'name' => $food_name,
        'quantity' => $quantity,
        'price' => number_format($price, 2),
        'item_total' => number_format($item_total, 2)
    ];
}

$total_amount = $bag_total + $delivery_fee;
$stmt->close();

// Fetch the default address_id for the customer
$sql_address = "SELECT address_id FROM user_addresses WHERE user_id = ? AND is_default = 1 LIMIT 1";
$stmt_address = $conn->prepare($sql_address);
$stmt_address->bind_param("i", $customer_id);
$stmt_address->execute();
$stmt_address->bind_result($default_address_id);
$stmt_address->fetch();
$stmt_address->close();
?>

<section class="order-detail">
    <h3 class="title-2">Order Details</h3>
    <ul>
        <?php foreach ($items as $item): ?>
            <li>
                <span><?php echo $item['name']; ?> (x<?php echo $item['quantity']; ?>)</span>
                <span>₱<?php echo $item['item_total']; ?></span>
            </li>
        <?php endforeach; ?>
        <li>
            <span>Bag total</span>
            <span>₱<?php echo number_format($bag_total, 2); ?></span>
        </li>
        <li>
            <span>Delivery</span>
            <span>₱<?php echo number_format($delivery_fee, 2); ?></span>
        </li>
        <li>
            <span>Total Amount</span>
            <span>₱<?php echo number_format($total_amount, 2); ?></span>
        </li>
    </ul>

    <!-- Hidden input for total amount in centavos (for JavaScript use) -->
    <input type="hidden" id="total-amount" value="<?php echo (int)($total_amount * 100); ?>">

    <!-- Hidden input for default address_id (for JavaScript or form submission) -->
    <input type="hidden" id="default-address-id" value="<?php echo $default_address_id; ?>">
</section>
