<?php
// Fetch kitchen details based on the first cart item
$sqlKitchen = "SELECT k.kitchen_id, k.fname, k.lname, k.photo, k.description 
               FROM kitchens k
               JOIN food_listings fl ON k.kitchen_id = fl.kitchen_id
               JOIN cart_items ci ON fl.food_id = ci.food_id
               WHERE ci.customer_id = ?
               LIMIT 1";

$stmtKitchen = $conn->prepare($sqlKitchen);
$stmtKitchen->bind_param("i", $customer_id);
$stmtKitchen->execute();
$resultKitchen = $stmtKitchen->get_result();

// Display kitchen details if found
if ($resultKitchen && $resultKitchen->num_rows > 0) {
    $kitchen = $resultKitchen->fetch_assoc();
    $kitchen_id = htmlspecialchars($kitchen['kitchen_id'], ENT_QUOTES, 'UTF-8');
    $kitchen_name = htmlspecialchars($kitchen['fname'] . ' ' . $kitchen['lname'], ENT_QUOTES, 'UTF-8');
    $kitchen_photo = htmlspecialchars($kitchen['photo'], ENT_QUOTES, 'UTF-8');
    $kitchen_description = htmlspecialchars($kitchen['description'], ENT_QUOTES, 'UTF-8');
?>
    <a href="kitchen.php?id=<?= $kitchen_id ?>" class="kitchen-details">
        <div class="kitchen-header">
            <img src="../../uploads/profile/<?= $kitchen_photo ?>" class="kitchen-img" alt="<?= $kitchen_name ?>" loading="lazy" />
            <div class="kitchen-info">
                <h3><?= $kitchen_name ?></h3>
                <p><?= $kitchen_description ?></p>
            </div>
        </div>
    </a>
    <?php
}
$stmtKitchen->close();

// Fetch cart items and calculate initial bag total
$sql = "SELECT ci.quantity, fl.food_id, fl.food_name, fl.photo1, fl.price
        FROM cart_items ci
        JOIN food_listings fl ON ci.food_id = fl.food_id
        WHERE ci.customer_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$bagTotal = 0;

// Display cart items
if ($result && $result->num_rows > 0) {
    echo '<div class="cart-items">';
    while ($row = $result->fetch_assoc()) {
        $food_id = htmlspecialchars($row['food_id'], ENT_QUOTES, 'UTF-8');
        $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
        $photo1 = htmlspecialchars($row['photo1'], ENT_QUOTES, 'UTF-8');
        $price = floatval($row['price']);
        $quantity = intval($row['quantity']);
        $itemTotal = $price * $quantity;
        $bagTotal += $itemTotal;
    ?>
        <div class="swipe-to-show" data-food-id="<?= $food_id ?>">
            <div class="product-list media">
                <a href="product.php?prod=<?= $food_id ?>">
                    <img src="../../uploads/<?= $photo1 ?>" class="img-fluid" alt="<?= $food_name ?>" loading="lazy" />
                </a>
                <div class="media-body">
                    <a href="product.php?prod=<?= $food_id ?>" class="font-sm title-color"><?= $food_name ?></a>
                    <span class="title-color font-sm">₱<span class="price-display" data-price="<?= $price ?>" data-food-id="<?= $food_id ?>"><?= number_format($itemTotal, 2) ?></span></span>
                </div>
                <div class="plus-minus">
                    <i class="sub" data-food-id="<?= $food_id ?>">-</i>
                    <input type="number" class="quantity-input" value="<?= $quantity ?>" min="0" max="10" data-food-id="<?= $food_id ?>" readonly />
                    <i class="add" data-food-id="<?= $food_id ?>">+</i>
                </div>
            </div>
            <div class="delete-button" data-food-id="<?= $food_id ?>">
                <i class="bx bx-trash"></i>
            </div>
        </div>
    <?php
    }
    echo '</div>';
} else {
    ?>
    <div class="cart-items">
        <div class="empty-cart-container">
            <div class="empty-cart-content">
                <div class="empty-cart-icon">
                    <i class="bx bx-cart-alt"></i>
                </div>
                <h3 class="empty-cart-title">Your Cart is Empty</h3>
                <div class="empty-cart-suggestions">
                    <h5>What you can do:</h5>
                    <ul>
                        <li><i class="bx bx-food-menu"></i> Explore our healthy meal </li>
                        <li><i class="bx bx-store"></i> Find local food providers</li>
                        <li><i class="bx bx-heart"></i> Save your favorite meals</li>
                    </ul>
                </div>
                <div class="empty-cart-actions">
                    <a href="homepage.php" class="btn btn-primary">
                        <i class="bx bx-restaurant"></i> Browse Menu
                    </a>
                </div>
            </div>
        </div>
    </div>
    <br><br>
<?php
}
$stmt->close();
?>

<div id="min-order-warning" class="alert alert-warning" style="display: none;"></div>

<!-- Order Details Section -->
<section class="order-detail pt-1">
    <h3 class="title-2">Order Details</h3>
    <ul>
        <li>
            <span>Bag total</span>
            <span>₱<span id="bag-total"><?= number_format($bagTotal, 2) ?></span></span>
        </li>
        <li id="delivery-fee-section" >
            <span>Delivery</span>
            <span>₱<span id="delivery-fee">0.00</span></span>
        </li>
        <li>
            <span>Total Amount</span>
            <span>₱<span id="total-amount"><?= number_format($bagTotal + $deliveryFee, 2) ?></span></span>
        </li>
    </ul>
</section>