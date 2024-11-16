<?php include 'includes/header.php'; 

// Check if kitchen user is logged in and retrieve kitchen ID
$admin_id = $_COOKIE['admin_id'] ?? null;

if (!$admin_id) {
    header("Location: ../../admin.php");
    exit();
}


?>
<link rel="stylesheet" type="text/css" href="assets/css/homepage.css" />

<body>

    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_homepage.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->

    <!-- Navigation Start -->
    <?php include 'includes/sidebar.php'; ?>
    <!-- Navigation End -->


    <!-- Main Start -->
    <main class="main-wrap dashboard-page mb-xxl">
        <?php include 'components/admin.header.php'; ?>

        <!-- Order Statistics Section -->
        <?php include 'components/statistic.homepage.php'; ?>


        <!-- Revenue Chart Section -->
        <?php include 'components/statistic.orders.php'; ?>

        <!-- Popular Items Section -->







        <!-- Bottom Navigation (if needed) -->
        <nav class="bottom-nav">
            <a href="#" class="nav-item"><i class="icon-home"></i></a>
            <a href="#" class="nav-item"><i class="icon-plus"></i></a>
            <a href="#" class="nav-item"><i class="icon-order"></i></a>
            <a href="#" class="nav-item"><i class="icon-profile"></i></a>
        </nav>
    </main>

    <!-- Main End -->

    <!-- Footer Start -->

    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->

   
    <!-- Action Language End -->

    <!-- Pwa Install App Popup Start -->

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>