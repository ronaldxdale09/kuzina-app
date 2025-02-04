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
                <button class="nav-link active" id="category-all-tab" data-bs-toggle="pill" type="button" role="tab">
                    All
                </button>
            </li>
            <?php
            // Fetch categories from database
            $category_sql = "SELECT category_id, name, icon FROM food_categories WHERE is_active = 1 ORDER BY name";
            $category_result = $conn->query($category_sql);

            if ($category_result && $category_result->num_rows > 0) {
                while ($category = $category_result->fetch_assoc()) {
                    echo '<li class="nav-item" role="presentation">';
                    echo '<button class="nav-link" id="category-' . $category['category_id'] . '-tab" data-bs-toggle="pill" type="button" role="tab">';
                    echo '<i class="' . htmlspecialchars($category['icon']) . '"></i> ';
                    echo htmlspecialchars($category['name']);
                    echo '</button>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
        <!-- Catagories Tabs End -->

        <!-- Search Box Start -->
        <div class="search-box">
            <div>
                <i class="bx bx-search icli search"></i>
                <input class="form-control" type="search" id="searchInput" placeholder="Search here..." />
            </div>
            <button class="filter font-md" type="button" data-bs-toggle="offcanvas" data-bs-target="#filter">Filter</button>
        </div>
 
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