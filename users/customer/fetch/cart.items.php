<?php 
$sql = "SELECT ci.quantity, fl.food_id, fl.food_name, fl.photo1, fl.price 
        FROM cart_items ci
        JOIN food_listings fl ON ci.food_id = fl.food_id
        WHERE ci.customer_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Use htmlspecialchars to escape output and prevent XSS attacks
        $food_id = htmlspecialchars($row['food_id'], ENT_QUOTES, 'UTF-8');
        $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
        $photo1 = htmlspecialchars($row['photo1'], ENT_QUOTES, 'UTF-8');
        $price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
        $quantity = htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8');

        // Calculate total price for this item
        $total_price = number_format($price * $quantity, 2);
        ?>
        <div class="swipe-to-show" data-food-id="<?= $food_id ?>">
            <div class="product-list media">
                <a href="product.php?prod=<?= $food_id ?>">
                    <img src="../../uploads/<?= $photo1 ?>" class="img-fluid" alt="<?= $food_name ?>" loading="lazy" />
                </a>
                <div class="media-body">
                    <a href="product.php?prod=<?= $food_id ?>" class="font-sm title-color"><?= $food_name ?></a>
                    <span class="content-color font-xs">Quantity: <span class="quantity-display"><?= $quantity ?></span></span>
                    <span class="title-color font-sm">₱<span class="price-display"><?= $total_price ?></span></span>
                </div>
                <div class="plus-minus">
                    <i class="sub" data-food-id="<?= $food_id ?>">-</i>
                    <input type="number" class="quantity-input" value="<?= $quantity ?>" min="1" max="10" data-food-id="<?= $food_id ?>" readonly />
                    <i class="add" data-food-id="<?= $food_id ?>">+</i>
                </div>
                <div class="delete-button" data-food-id="<?= $food_id ?>">
                    <i data-feather="trash"></i> <!-- Trash icon for delete -->
                </div>
            </div>
        </div>
        <?php
    }
} else {
    // If no items are found in the cart
    echo "<p>Your cart is empty.</p>";
}

$stmt->close();
?>
