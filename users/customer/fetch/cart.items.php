<?php
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
$deliveryFee = 50.00;
$couponDiscount = 0.00; // Initial value before applying any discount

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
        $bagTotal += $itemTotal; // Add item total to bag total
        ?>
    <div class="swipe-to-show ">
        <div class="product-list media">
            <a href="product.php?prod=<?= $food_id ?>">
                <img src="../../uploads/<?= $photo1 ?>" class="img-fluid" alt="<?= $food_name ?>" loading="lazy" />
            </a>
            <div class="media-body">
                <a href="product.php?prod=<?= $food_id ?>" class="font-sm title-color"><?= $food_name ?></a>
                <span class="content-color font-xs">Quantity: <span class="quantity-display"><?= $quantity ?></span></span>
                <span class="title-color font-sm">₱<span class="price-display" data-price="<?= $price ?>"
                        data-food-id="<?= $food_id ?>"><?= number_format($itemTotal, 2) ?></span></span>
            </div>
            <div class="plus-minus">
                <i class="sub" data-food-id="<?= $food_id ?>">-</i>
                <input type="number" class="quantity-input" value="<?= $quantity ?>" min="1" max="10"
                    data-food-id="<?= $food_id ?>" readonly />
                <i class="add" data-food-id="<?= $food_id ?>">+</i>
            </div>

        </div>
        <div class="delete-button" data-bs-toggle="offcanvas" data-bs-target="#confirmation" aria-controls="confirmation">
            <i data-feather="trash"></i>
        </div>
    </div>
<?php
    }
    echo '</div>
    ';
} else {
    echo "<p>Your cart is empty.</p>";
}
$stmt->close();
?>

<!-- Coupons Section Start -->
<section class="pt-0 coupon-ticket-wrap">
    <div class="coupon-ticket" data-bs-toggle="offcanvas" data-bs-target="#offer-1" aria-controls="offer-1">
        <div class="media">
            <div class="off">
                <span>50</span>
                <span><span>%</span><span>OFF</span> </span>
            </div>
            <div class="media-body">
                <h2 class="title-color">on your first order</h2>
                <span class="content-color">on order above ₱250.00</span>
            </div>
            <div class="big-circle">
                <span></span>
            </div>
            <div class="code">
                <span class="content-color">Use Code: </span>
                <a href="javascript:void(0)">KUZINA123</a>
            </div>
        </div>
        <div class="circle-5 left">
            <span class="circle-shape"></span>
            <span class="circle-shape"></span>
        </div>
        <div class="circle-5 right">
            <span class="circle-shape"></span>
            <span class="circle-shape"></span>
        </div>
    </div>
</section>
<!-- Coupons Section End  -->


<!-- Order Details Section (Only Displayed Once) -->
<section class="order-detail pt-1">
    <h3 class="title-2">Order Details</h3>

    <!-- Detail list Start -->
    <ul>
        <li>
            <span>Bag total</span>
            <span>₱<span id="bag-total"><?= number_format($bagTotal, 2) ?></span></span>
        </li>

        <li class="coupon-row">
            <span>Coupon Discount</span>
            <div class="coupon-section">
                <input type="text" id="coupon-code" placeholder="Enter coupon code">
                <button id="apply-coupon-btn">Apply</button>
                <span class="discount-amount">₱<span
                        id="coupon-discount"><?= number_format($couponDiscount, 2) ?></span></span>
            </div>
        </li>


        <li>
            <span>Delivery</span>
            <span>₱<span id="delivery-fee"><?= number_format($deliveryFee, 2) ?></span></span>
        </li>

        <li>
            <span>Total Amount</span>
            <span>₱<span
                    id="total-amount"><?= number_format($bagTotal + $deliveryFee - $couponDiscount, 2) ?></span></span>
        </li>
    </ul>
    <!-- Detail list End -->
</section>