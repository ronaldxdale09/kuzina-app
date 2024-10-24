<?php include 'includes/header.php'; ?>

<!-- Head End -->

<!-- Body Start -->

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_search.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap search-page mb-xxl">
        <!-- Search Box Start -->
        <div class="search-box">
            <i class="iconly-Search icli search"></i>
            <input class="form-control" type="search" placeholder="Search here..." />
            <i class="iconly-Voice icli mic"></i>
        </div>
        <!-- Search Box End -->

        <!-- Recent Search Section Start -->
        <section class="recent-search pb-0">
            <h1 class="font-md title-color fw-600 title-mb">Popular Search</h1>
            <ul class="custom-scroll-hidden">
                <li class="font-sm title-color"><a href="shop.html"> Vegetables</a></li>
                <li class="font-sm title-color"><a href="shop.html"> Fruits</a></li>
                <li class="font-sm title-color"><a href="shop.html"> Beauty</a></li>
                <li class="font-sm title-color"><a href="shop.html"> Fruits</a></li>
            </ul>
        </section>
        <!-- Recent Search Section End -->

        <!-- Trending Category Section Start -->
        <?php include 'components/category_rec.php'; ?>

        <!-- Recent Search Section End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->

    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->

 

    <?php include 'includes/scripts.php'; ?>
</body>
<!-- Body End -->

</html>
<!-- Html End -->