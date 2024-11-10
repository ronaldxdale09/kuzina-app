<?php include 'includes/header.php'; ?>
<!-- Body Start -->
<link rel="stylesheet" href="assets/css/shop.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_shop.php'; ?>
    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/shop.navbar.php'; ?>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap shop-page mb-xxl">
        <!-- Catagories Tabs Start -->
        <ul class="nav nav-tab nav-pills custom-scroll-hidden" id="pills-tab" role="tablist">

            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="catagories0-tab" data-bs-toggle="pill" data-bs-target="#catagories1"
                    type="button" role="tab" aria-controls="catagories0" aria-selected="true">
                    All
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link " id="catagories1-tab" data-bs-toggle="pill" data-bs-target="#catagories1"
                    type="button" role="tab" aria-controls="catagories1" aria-selected="true">
                    Grilled & Roasted
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="catagories2-tab" data-bs-toggle="pill" data-bs-target="#catagories2"
                    type="button" role="tab" aria-controls="catagories2" aria-selected="false">
                    Vegetable-Based Stews
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="catagories3-tab" data-bs-toggle="pill" data-bs-target="#catagories3"
                    type="button" role="tab" aria-controls="catagories3" aria-selected="false">
                    Healthy Stir-Fried
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="catagories4-tab" data-bs-toggle="pill" data-bs-target="#catagories4"
                    type="button" role="tab" aria-controls="catagories4" aria-selected="false">
                    Soup-Based Meal
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="catagories5-tab" data-bs-toggle="pill" data-bs-target="#catagories5"
                    type="button" role="tab" aria-controls="catagories5" aria-selected="false">
                    Desserts & Sweets
                </button>
            </li>

            <li class="nav-item" role="presentation">
                <button class="nav-link" id="catagories6-tab" data-bs-toggle="pill" data-bs-target="#catagories6"
                    type="button" role="tab" aria-controls="catagories6" aria-selected="false">
                    Beverages
                </button>
            </li>
        </ul>

        <!-- Catagories Tabs End -->

        <!-- Search Box Start -->
        <div class="search-box">
            <div>
                <i class="iconly-Search icli search"></i>
                <input class="form-control" type="search" placeholder="Search here..." />
                <i class="iconly-Voice icli mic"></i>
            </div>
            <button class="filter font-md" type="button" data-bs-toggle="offcanvas" data-bs-target="#filter"
                aria-controls="filter">Filter</button>
        </div>
        <!-- Search Box End -->

        <!-- Tab Content Start -->
        <!-- <div class="tab-content ratio2_1" id="pills-tabContent"> -->

        <div class="tab-content" id="pills-tabContent">
            <!-- Catagories Content Start -->
            <!-- Catagories Content end -->

        </div>
        <!-- Tab Content End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <?php include 'components/shop.viewCart.php'; ?>

    <!-- Footer End -->

    <!-- Filter Offcanvas Start -->
    <div class="shop-fillter offcanvas offcanvas-bottom" tabindex="-1" id="filter" aria-labelledby="filter">
        <div class="offcanvas-header">
            <div class="catagories">
                <h5 class="title-color font-md">Category</h5>
                <button class="font-md reset">Reset</button>
            </div>
        </div>
        <div class="offcanvas-body small">
            <div class="input-box">
                <div class="select-box">
                    <select class="form-control">
                        <option>Fresh Fruits& Vegetables</option>
                        <option>Oils,Refined & Ghee</option>
                        <option>Rice, Flour & Grains</option>
                        <option>Food Cupboard</option>
                        <option>Drinks& Beverages</option>
                        <option>Instant Mixes</option>
                    </select>
                    <span><i data-feather="chevron-right"></i></span>
                </div>
            </div>

            <div class="pack-size">
                <h5 class="title-color font-md">Pack Size</h5>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="size">
                            <span class="font-sm">100g-500g</span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="size">
                            <span class="font-sm">500g-1kg</span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="size">
                            <span class="font-sm">1kg-1.5kg</span>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="size">
                            <span class="font-sm">2kg-3.5kg</span>
                        </div>
                    </div>
                </div>
            </div>

          
        </div>
        <div class="offcanvas-footer">
            <div class="btn-box">
                <button class="btn-outline font-md" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
                <button class="btn-solid font-md" data-bs-dismiss="offcanvas" aria-label="Close">Apply</button>
            </div>
        </div>
    </div>
    <!-- Filter Offcanvas End -->
    <?php include 'fetch/shop.foodlist.php'; ?>

   
    <!-- jquery 3.6.0 -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Js -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Pricing Slider js -->
    <script src="assets/js/pricing-slider.js"></script>

    <!-- Lord Icon -->
    <script src="assets/js/lord-icon-2.1.0.js"></script>

    <!-- Feather Icon -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Theme Setting js -->
    <script src="assets/js/theme-setting.js"></script>

    <!-- Script js -->
    <script src="assets/js/script.js"></script>
</body>
<!-- Body End -->

</html>
<!-- Html End -->