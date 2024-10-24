<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<!-- Head End -->

<!-- Body Start -->

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_cart.php'; ?>

    <!-- Skeleton loader End -->
    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header Start -->
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();">
                <i class="iconly-Arrow-Left-Square icli"></i>
            </a>
            <h1 class="title-color font-md">My Cart <span class="font-sm content-color">(4 Items)</span></h1>
        </div>
        <div class="avatar-wrap">
            <a href="index.html">
                <i class="iconly-Home icli"></i>
            </a>
        </div>
    </header>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap cart-page mb-xxl">
        <!-- Cart Item Section Start  -->
        <div class="cart-item-wrap pt-0">
            <?php             
            $customer_id = 1;

            include 'fetch/cart.items.php';
            include 'fetch/cart.order.details.php';
            ?>
        </div>
        <!-- Cart Item Section End  -->

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
                        <a href="javascript:void(0)">SCD450</a>
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

        <!-- Order Detail Start -->

        <!-- Order Detail End -->
    </main>

    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-wrap footer-button">
        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev" class="font-md">Proceed to Checkout</a>
    </footer>
    <!-- Footer End -->

    <!-- Action confirmation Start -->
    <div class="action action-confirmation offcanvas offcanvas-bottom" tabindex="-1" id="confirmation"
        aria-labelledby="confirmation">
        <div class="offcanvas-body small">
            <div class="confirmation-box">
                <h2>Are You Sure?</h2>
                <p class="font-sm content-color">The permission for the use/group, preview is inherited from the object,
                    Modifiying it for this object will create a new permission for this object</p>
                <div class="btn-box">
                    <button class="btn-outline" data-bs-dismiss="offcanvas" aria-label="Close">Cancel</button>
                    <button class="btn-solid d-block" data-bs-dismiss="offcanvas" aria-label="Close">Remove</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Action Confirmation End -->

    <!-- Offer Offcanvas Start -->
    <div class="offcanvas offer-offcanvas offcanvas-bottom" tabindex="-1" id="offer-1" aria-labelledby="offer-1Label">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title font-lg" id="offer-1Label">Flat 50% off</h5>
            <span class="font-sm">on order above $250.00</span>
            <div class="code">
                <span class="font-sm">Code: <strong> SCD450</strong></span>
                <button class="btn-outline" data-bs-dismiss="offcanvas" aria-label="Close">Copy Code</button>
            </div>
        </div>
        <div class="offcanvas-body small">
            <h6 class="font-md content-color">Terms & conditions</h6>
            <ol>
                <li class="font-sm content-color">
                    Information on how to participate forms part of these Terms & Conditions. By participating,
                    claimants agree to be bound by these Terms & Conditions. Claimants must comply with these Terms
                    & Conditions for a coupon to be valid.
                </li>
                <li class="font-sm content-color">
                    Each claimant is entitled to one coupon per accommodation establishment. Coupons are not
                    transferable and are not redeemable for cash and cannot be combined with any other coupons or any
                    other offer or discounts or promotions offered by Quovai.
                </li>
            </ol>
        </div>
    </div>
    <!-- Offer Offcanvas End -->


    <script>
    document.addEventListener('DOMContentLoaded', () => {
        let bagTotal = parseFloat(document.getElementById('bag-total').innerText.replace(/,/g, '')) || 0;
        const deliveryFee = 50.00;
        let couponDiscount = 0.00;

        // Function to update the total amount
        function updateTotalAmount() {
            const totalAmountElement = document.getElementById('total-amount');
            const totalAmount = (bagTotal + deliveryFee - couponDiscount).toFixed(2);
            if (totalAmountElement) {
                totalAmountElement.innerText = totalAmount;
            } else {
                console.error("Total amount element not found.");
            }
        }

        // Function to update the bag total in real-time
        function updateBagTotal(newBagTotal) {
            bagTotal = newBagTotal;
            const bagTotalElement = document.getElementById('bag-total');
            if (bagTotalElement) {
                bagTotalElement.innerText = bagTotal.toFixed(2); // Update bag total
            } else {
                console.error("Bag total element not found.");
            }
            updateTotalAmount();
        }

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
                        const priceDisplay = document.querySelector(
                                `.quantity-input[data-food-id="${foodId}"]`)
                            ?.closest('.media-body')
                            ?.querySelector('.price-display');

                        if (priceDisplay) {
                            priceDisplay.innerText = (data.new_price).toFixed(2);
                        } else {
                            console.error("Price display element not found for food ID:", foodId);
                        }

                        updateBagTotal(data.new_bag_total);
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Add event listeners to plus and minus buttons
        document.querySelectorAll('.plus-minus i').forEach(button => {
            button.addEventListener('click', function() {
                const foodId = this.getAttribute('data-food-id');
                const quantityInput = this.closest('.plus-minus').querySelector(
                    '.quantity-input');

                if (quantityInput) {
                    let currentQuantity = parseInt(quantityInput.value);

                    if (this.classList.contains('add') && currentQuantity < 10) {
                        currentQuantity++; // Increase quantity by 1
                    } else if (this.classList.contains('sub') && currentQuantity > 1) {
                        currentQuantity--; // Decrease quantity by 1
                    }

                    if (currentQuantity < 1) {
                        currentQuantity = 1;
                    }

                    quantityInput.value = currentQuantity;
                    updateCartQuantity(foodId, currentQuantity);
                } else {
                    console.error('Quantity input not found for food ID:', foodId);
                }
            });
        });

        // Function to apply coupon discount
        document.getElementById('apply-coupon-btn').addEventListener('click', () => {
            const couponCode = document.getElementById('coupon-code').value;

            if (couponCode === 'DISCOUNT10') {
                couponDiscount = bagTotal * 0.1; // 10% discount
                document.getElementById('coupon-discount').innerText = `₱${couponDiscount.toFixed(2)}`;
            } else {
                couponDiscount = 0;
                document.getElementById('coupon-discount').innerText = '₱0.00';
            }

            updateTotalAmount();
        });
    });
    </script>

<?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>
<!-- Html End -->