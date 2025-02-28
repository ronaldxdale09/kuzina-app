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
        <br>
        <!-- Search Box Start -->
        <form id="homeSearchForm" action="shop.php" method="get" class="m-0 p-0">
            <div class="search-box">
                <i class="iconly-Search icli search" id="searchSubmitIcon"></i>
                <input class="form-control" type="search" name="search" placeholder="Search here..." />
            </div>
        </form>
        <!-- Search Box End -->


        <!-- Shop By Category Start -->
        <?php include 'components/category_rec.php'; ?>

        <!-- Shop By Category End -->

        <?php include 'components/recommendation.php'; ?>



        <!-- Everyday Essentials Start -->
        <section class="low-price-section pt-4">
            <div class="top-content">
                <div>
                    <h4 class="title-color">Everyday Essentials</h4>
                    <p class="content-color">Best price ever of all the time</p>
                </div>
                <a href="shop.php" class="font-theme">See More</a>
            </div>

            <?php include 'components/homepage.kitchenListing.php'; ?>


        </section>


        <!-- Everyday Essentials End -->


        <!-- Lowest Price 2 Start -->
        <section class="low-price-section pt-0">
            <div class="top-content">
                <div>
                    <h4 class="title-color">Peoples Favorite!</h4>
                    <p class="content-color">Pay less, Get More</p>
                </div>
                <a href="shop.php" class="font-theme">See More</a>
            </div>

            <div class="product-slider">
                <?php include 'components/random.product.php'; ?>

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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get the form and search icon
            const searchForm = document.getElementById('homeSearchForm');
            const searchIcon = document.getElementById('searchSubmitIcon');

            // Make the search icon clickable
            if (searchIcon) {
                searchIcon.style.cursor = 'pointer';
                searchIcon.addEventListener('click', function() {
                    searchForm.submit();
                });
            }

            // Prevent empty form submission
            searchForm.addEventListener('submit', function(event) {
                const searchInput = this.querySelector('input[type="search"]');
                if (!searchInput.value.trim()) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
<!-- Body End -->

</html>