<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" href="assets/css/homepage.css">

<body>

    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_homepage.php'; ?>
    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->

    <!-- Navigation Start -->
    <?php include 'includes/sidebar.php'; ?>
    <!-- Navigation End -->

    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap index-page mb-xxl">
        <!-- Search Box Start -->
        <div class="search-box">
            <i class="iconly-Search icli search"></i>
            <input class="form-control" type="search" placeholder="Search here..." />
            <i class="iconly-Voice icli mic"></i>
        </div>
        <!-- Search Box End -->


        <!-- Shop By Category Start -->
        <?php include 'components/category_rec.php'; ?>

        <!-- Shop By Category End -->

        <?php include 'components/random.product.php'; ?>
        <?php include 'components/recommendation.php'; ?>



        <!-- Everyday Essentials Start -->
        <section class="low-price-section pt-4">
            <div class="top-content">
                <div>
                    <h4 class="title-color">Everyday Essentials</h4>
                    <p class="content-color">Best price ever of all the time</p>
                </div>
                <a href="shop.html" class="font-theme">See More</a>
            </div>

            <div class="product-slider">
                <?php include 'fetch/homepage.fetch.php'; ?>

            </div>

        </section>


        <!-- Everyday Essentials End -->


        <!-- Lowest Price 2 Start -->
        <section class="low-price-section pt-0">
            <div class="top-content">
                <div>
                    <h4 class="title-color">Peoples Favorite!</h4>
                    <p class="content-color">Pay less, Get More</p>
                </div>
                <a href="shop.html" class="font-theme">See More</a>
            </div>

            <div class="product-slider">
                <?php
               
               fetch_and_render_random_products($conn)
               ?>
            </div>
        </section>
        <!-- Lowest Price 2 End -->

        <!-- Question section Start -->
        <section class="question-section pt-0">
            <h5>Didn’t find what you were looking for?</h5>
            <a href="shop.php" class="btn-solid">Browse Category</a>
        </section>
        <!-- Question section End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->

    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->

    <!-- Action Language Start -->
    <div class="action action-language offcanvas offcanvas-bottom" tabindex="-1" id="language"
        aria-labelledby="language">
        <div class="offcanvas-body small">
            <h2 class="m-b-title1 font-md">Select Language</h2>

            <ul class="list">
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img
                            src="assets/icons/flag/us.svg" alt="us" /> English </a>
                </li>

            </ul>
        </div>
    </div>
    <!-- Action Language End -->

    <!-- Pwa Install App Popup Start -->

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>