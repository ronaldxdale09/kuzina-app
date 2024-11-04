<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<!-- Head End -->

  <!-- Body Start -->
  <body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_cart.php'; ?>

    <!-- Skeleton loader End -->
    <!-- Header Start -->
    <?php include 'includes/top_header.php'; ?>

    <!-- Header Start -->
    <header class="header">
      <div class="logo-wrap">
        <a href="homepage.php"><i class="iconly-Arrow-Left-Square icli"></i></a>
        <h1 class="title-color font-md">My Cart <span class="font-sm content-color">(4 Items)</span></h1>
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
      <!-- Cart Item Section Start  -->
      <div class="cart-item-wrap pt-0">
        <div class="swipe-to-show">
          <div class="product-list media">
            <a href="product.html"><img src="assets/images/product/8.png" alt="offer" /></a>
            <div class="media-body">
              <a href="product.html" class="font-sm"> Assorted Capsicum Combo </a>
              <span class="content-color font-xs">500g</span>
              <span class="title-color font-sm">$25.00 <span class="badges-round bg-theme-theme font-xs">50% off</span></span>
              <div class="plus-minus">
                <i class="sub" data-feather="minus"></i>
                <input type="number" value="1" min="0" max="10" />
                <i class="add" data-feather="plus"></i>
              </div>
            </div>
          </div>
          <div class="delete-button" data-bs-toggle="offcanvas" data-bs-target="#confirmation" aria-controls="confirmation">
            <i data-feather="trash"></i>
          </div>
        </div>

        <div class="swipe-to-show active">
          <div class="product-list media">
            <a href="product.html"><img src="assets/images/product/6.png" alt="offer" /></a>
            <div class="media-body">
              <a href="product.html" class="font-sm"> Assorted Capsicum Combo </a>
              <span class="content-color font-xs">500g</span>
              <span class="title-color font-sm">$25.00 <span class="badges-round bg-theme-theme font-xs">50% off</span></span>
              <div class="plus-minus">
                <i class="sub" data-feather="minus"></i>
                <input type="number" value="1" min="0" max="10" />
                <i class="add" data-feather="plus"></i>
              </div>
            </div>
          </div>
          <div class="delete-button" data-bs-toggle="offcanvas" data-bs-target="#confirmation" aria-controls="confirmation">
            <i data-feather="trash"></i>
          </div>
        </div>

        <div class="swipe-to-show">
          <div class="product-list media">
            <a href="product.html"><img src="assets/images/product/7.png" alt="offer" /></a>
            <div class="media-body">
              <a href="product.html" class="font-sm"> Assorted Capsicum Combo </a>
              <span class="content-color font-xs">500g</span>
              <span class="title-color font-sm">$25.00 </span>
              <div class="plus-minus">
                <i class="sub" data-feather="minus"></i>
                <input type="number" value="1" min="0" max="10" />
                <i class="add" data-feather="plus"></i>
              </div>
            </div>
          </div>
          <div class="delete-button" data-bs-toggle="offcanvas" data-bs-target="#confirmation" aria-controls="confirmation">
            <i data-feather="trash"></i>
          </div>
        </div>

        <div class="swipe-to-show">
          <div class="product-list media">
            <a href="product.html"><img src="assets/images/product/11.png" alt="offer" /></a>
            <div class="media-body">
              <a href="product.html" class="font-sm"> Assorted Capsicum Combo </a>
              <span class="content-color font-xs">500g</span>
              <span class="title-color font-sm">$25.00 <span class="badges-round bg-theme-theme font-xs">50% off</span></span>
              <div class="plus-minus">
                <i class="sub" data-feather="minus"></i>
                <input type="number" value="1" min="0" max="10" />
                <i class="add" data-feather="plus"></i>
              </div>
            </div>
          </div>
          <div class="delete-button" data-bs-toggle="offcanvas" data-bs-target="#confirmation" aria-controls="confirmation">
            <i data-feather="trash"></i>
          </div>
        </div>
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
              <span class="content-color">on order above $250.00</span>
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
      <section class="order-detail pt-0">
        <h3 class="title-2">Order Details</h3>

        <!-- Detail list Start -->
        <ul>
          <li>
            <span>Bag total</span>
            <span>$220.00</span>
          </li>

          <li>
            <span>Bag savings</span>
            <span class="font-theme">-$20.00</span>
          </li>

          <li>
            <span>Coupon Discount</span>
            <a href="offer.html" class="font-danger">Apply Coupon</a>
          </li>

          <li>
            <span>Delivery</span>
            <span>$50.00</span>
          </li>
          <li>
            <span>Total Amount</span>
            <span>$270.00</span>
          </li>
        </ul>
        <!-- Detail list End -->
      </section>
      <!-- Order Detail End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-wrap footer-button">
      <a href="address1.html" class="font-md">Proceed to Checkout</a>
    </footer>
    <!-- Footer End -->

    <!-- Action confirmation Start -->
    <div class="action action-confirmation offcanvas offcanvas-bottom" tabindex="-1" id="confirmation" aria-labelledby="confirmation">
      <div class="offcanvas-body small">
        <div class="confirmation-box">
          <h2>Are You Sure?</h2>
          <p class="font-sm content-color">The permission for the use/group, preview is inherited from the object, Modifiying it for this object will create a new permission for this object</p>
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
            Information on how to participate forms part of these Terms & Conditions. By participating, claimants agree to be bound by these Terms & Conditions. Claimants must comply with these Terms
            & Conditions for a coupon to be valid.
          </li>
          <li class="font-sm content-color">
            Each claimant is entitled to one coupon per accommodation establishment. Coupons are not transferable and are not redeemable for cash and cannot be combined with any other coupons or any
            other offer or discounts or promotions offered by Quovai.
          </li>
        </ol>
      </div>
    </div>
    <!-- Offer Offcanvas End -->


    <?php include 'includes/scripts.php'; ?>

  </body>
  <!-- Body End -->
</html>
<!-- Html End -->
