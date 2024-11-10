<?php
function fetch_and_render_cart_items($conn, $customer_id) {
    $sql = "SELECT ci.quantity, fl.food_id, fl.food_name, fl.photo1, fl.price 
            FROM cart_items ci
            JOIN food_listings fl ON ci.food_id = fl.food_id
            WHERE ci.customer_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $totalAmount = 0; // Initialize total amount
        while ($row = $result->fetch_assoc()) {
            $food_id = htmlspecialchars($row['food_id'], ENT_QUOTES, 'UTF-8');
            $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
            $photo1 = htmlspecialchars($row['photo1'], ENT_QUOTES, 'UTF-8');
            $price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
            $quantity = htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8');

            // Calculate the total amount for the cart
            $totalAmount += $price * $quantity;
            ?>
<div class="swipe-to-show" data-food-id="<?= $food_id ?>">
    <div class="product-list media">
        <a href="product.php?prod=<?= $food_id ?>">
            <img src="../../uploads/<?= $photo1 ?>" class="img-fluid" alt="<?= $food_name ?>" loading="lazy" />
        </a>
        <div class="media-body">
            <a href="product.php?prod=<?= $food_id ?>" class="font-sm title-color"><?= $food_name ?></a>
            <span class="content-color font-xs">Quantity: <span class="quantity-display"><?= $quantity ?></span></span>
            <span class="title-color font-sm">₱<span
                    class="price-display"><?= number_format($price * $quantity, 2) ?></span></span>
        </div>
        <div class="plus-minus">
            <i class="sub" data-food-id="<?= $food_id ?>">-</i>
            <input type="number" class="quantity-input" value="<?= $quantity ?>" min="1" max="10"
                data-food-id="<?= $food_id ?>" readonly />
            <i class="add" data-food-id="<?= $food_id ?>">+</i>
        </div>
        <div class="delete-button" data-food-id="<?= $food_id ?>">
            <i data-feather="trash"></i> <!-- Trash icon for delete -->
        </div>
    </div>
</div>
<?php
        }
        // Output order details
        ?>
<section class="order-detail pt-0">
    <h3 class="title-2">Order Details</h3>

    <!-- Detail list Start -->
    <ul>
        <li>
            <span>Bag total</span>
            <span>₱<span id="bag-total"><?= number_format($totalAmount, 2) ?></span></span>
        </li>

        <li>
            <span>Coupon Discount</span>
            <input type="text" id="coupon-code" placeholder="Enter coupon code" class="font-danger">
            <button id="apply-coupon-btn">Apply Coupon</button>
            <span id="coupon-discount">₱0.00</span>
        </li>

        <li>
            <span>Delivery</span>
            <span>₱<span id="delivery-fee">50.00</span></span>
        </li>

        <li>
            <span>Total Amount</span>
            <span>₱<span id="total-amount"><?= number_format($totalAmount + 50, 2) ?></span></span>
        </li>
    </ul>
    <!-- Detail list End -->
</section>
<?php
    } else {
        echo "<p>Your cart is empty.</p>";
    }

    $stmt->close();
}
?>


<script>
document.addEventListener('DOMContentLoaded', () => {
    let bagTotal = parseFloat(document.getElementById('bag-total').innerText.replace(/,/g, ''));
    const deliveryFee = 50.00;
    let couponDiscount = 0.00;

    // Function to update the total amount
    function updateTotalAmount() {
        const totalAmountElement = document.getElementById('total-amount');
        const totalAmount = (bagTotal + deliveryFee - couponDiscount).toFixed(2);
        totalAmountElement.innerText = totalAmount;
    }

    // Function to apply coupon discount
    document.getElementById('apply-coupon-btn').addEventListener('click', () => {
        const couponCode = document.getElementById('coupon-code').value;

        // Simulate a coupon check
        if (couponCode === 'DISCOUNT10') {
            couponDiscount = bagTotal * 0.1; // 10% discount
            document.getElementById('coupon-discount').innerText = `₱${couponDiscount.toFixed(2)}`;
        } else {
            couponDiscount = 0;
            document.getElementById('coupon-discount').innerText = '₱0.00';
        }

        updateTotalAmount();
    });

    // Function to update the cart quantity on the server
    function updateCartQuantity(foodId, newQuantity) {
        fetch('functions/updateCart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    food_id: foodId,
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (newQuantity > 0) {
                        // Update the price for the item in the UI
                        const priceDisplay = document.querySelector(
                                `.quantity-input[data-food-id="${foodId}"]`)
                            .closest('.media-body')
                            .querySelector('.price-display');
                        priceDisplay.innerText = (data.new_price).toFixed(2);
                        // Update the bag total
                        bagTotal = parseFloat(data.new_bag_total);
                        document.getElementById('bag-total').innerText = bagTotal.toFixed(2);
                        updateTotalAmount();
                    } else {
                        // If the quantity is zero, remove the item from the cart
                        document.querySelector(`.swipe-to-show[data-food-id="${foodId}"]`).remove();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Add event listeners to all plus and minus buttons
    document.querySelectorAll('.plus-minus i').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.getAttribute('data-food-id');
            const quantityInput = this.closest('.plus-minus').querySelector('.quantity-input');
            let currentQuantity = parseInt(quantityInput.value);

            // Adjust quantity based on whether it is a plus or minus button
            if (this.classList.contains('add') && currentQuantity < 10) {
                currentQuantity++;
            } else if (this.classList.contains('sub')) {
                currentQuantity--;
            }

            // Ensure the quantity doesn't go below 0
            if (currentQuantity < 0) currentQuantity = 0;

            // Update the input field and call the server to update the quantity
            quantityInput.value = currentQuantity;
            updateCartQuantity(foodId, currentQuantity);
        });
    });

    // Add event listeners to all delete buttons
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.getAttribute('data-food-id');
            if (confirm('Are you sure you want to remove this item from the cart?')) {
                deleteCartItem(foodId);
            }
        });
    });

});
</script>