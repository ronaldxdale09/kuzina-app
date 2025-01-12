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
$total_items = 0;
$items = [];

// Fetch and format each item in the cart
while ($row = $result->fetch_assoc()) {
    $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
    $quantity = (int)$row['quantity'];
    $price = (float)$row['price'];
    $item_total = $price * $quantity;
    $bag_total += $item_total;
    $total_items += $quantity;

    $items[] = [
        'name' => $food_name,
        'quantity' => $quantity,
        'price' => number_format($price, 2),
        'item_total' => number_format($item_total, 2)
    ];
}

// Calculate delivery fee
$delivery_fee = 50; // Base fee
if($total_items > 1) {
    $delivery_fee += ($total_items - 1) * 10; // Add ₱10 for each additional item
}
$delivery_fee = min($delivery_fee, 150); // Cap at ₱150

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
            <?php if($total_items > 1): ?>
            <small class="delivery-note">
                (Base fee: ₱50 + ₱10 per additional item)
            </small>
            <?php endif; ?>
        </li>
        <li>
            <span>Total Amount</span>
            <span>₱<?php echo number_format($total_amount, 2); ?></span>
        </li>
    </ul>

    <!-- Hidden inputs for JavaScript use -->
    <input type="hidden" id="total-amount" value="<?php echo (int)($total_amount * 100); ?>">
    <input type="hidden" id="default-address-id" value="<?php echo $default_address_id; ?>">
    <input type="hidden" id="delivery-fee" name="delivery_fee" value="<?php echo $delivery_fee; ?>">
    <input type="hidden" id="total-items" value="<?php echo $total_items; ?>">
</section>

<style>
.order-detail li {
    position: relative;
    margin-bottom: 15px;
}

.delivery-note {
    display: block;
    font-size: 0.8em;
    color: #666;
    margin-top: 2px;
}

.order-detail li:last-child {
    margin-top: 20px;
    padding-top: 15px;
    border-top: 1px solid #eee;
    font-weight: bold;
}
</style>