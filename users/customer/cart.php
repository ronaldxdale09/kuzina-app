<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" href="assets/css/cart.css">

<!-- Head End -->

<!-- Body Start -->

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_cart.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();">
                <i class="iconly-Arrow-Left-Square icli"></i>
            </a>
            <h1 class="title-color font-md">My Cart <span class="font-sm content-color"></span></h1>
        </div>
        <div class="avatar-wrap">
            <a href="homepage.php">
                <i class="iconly-Home icli"></i>
            </a>
        </div>
    </header>
    <!-- Header End -->


    <!-- Main Start -->
    <main class="main-wrap cart-page mb-xxl">
        <?php       
            include 'components/cart.address.php';
            ?> <br>

        <!-- Cart Item Section Start  -->
        <div class="cart-item-wrap pt-0">
            <?php       
            include 'components/cart.items.php';
            include 'components/cart.order.details.php';
            ?>
        </div>
        <!-- Cart Item Section End  -->



        <!-- Order Detail Start -->

        <!-- Order Detail End -->
    </main>

    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-wrap footer-button">
        <a href="payment.php" class="font-md">Proceed to Checkout</a>
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
            <span class="font-sm">on order above PHP 250.00</span>
            <div class="code">
                <span class="font-sm">Code: <strong> KUZINA</strong></span>
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
                    other offer or discounts or promotions offered by KUZINA.
                </li>
            </ol>
        </div>
    </div>
    <!-- Offer Offcanvas End -->

    <!-- Swiper Js -->
    <script src="assets/js/jquery-swipe-1.11.3.min.js"></script>
    <script src="assets/js/jquery.mobile-1.4.5.min.js"></script>

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>
<!-- Html End -->