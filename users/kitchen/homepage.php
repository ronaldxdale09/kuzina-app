<?php include 'includes/header.php'; 

// Check if kitchen user is logged in and retrieve kitchen ID
$kitchen_id = $_COOKIE['kitchen_id'] ?? null;

if (!$kitchen_id) {
    header("Location: ../../kitchen_login.php");
    exit();
}

// Query to check if the kitchen is approved
$stmt = $conn->prepare("SELECT isApproved FROM kitchens WHERE kitchen_id = ? AND isApproved = 1");
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$stmt->store_result();

// Redirect if the kitchen is not approved
if ($stmt->num_rows === 0) {
    header("Location: isApproved.php");
    exit();
}

// Close the statement and connection
$stmt->close();
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

        <!-- Order Statistics Section -->
        <?php include 'components/statistic.homepage.php'; ?>


        <!-- Revenue Chart Section -->
        <?php include 'components/chart.homepage.php'; ?>


        <!-- Reviews Section -->
        <section class="reviews-section">
            <div class="reviews-summary">
                <div class="review-score">
                    <span class="star">★</span>
                    <h4>4.9</h4>
                </div>
                <p class="total-reviews">Total 20 Reviews</p>
                <a href="#" class="review-link">See All Reviews</a>
            </div>
        </section>

        <!-- Popular Items Section -->
      



        <!-- Delivery Performance Section -->
        <section class="delivery-performance-section">
            <div class="section-header">
                <h4>Delivery Performance</h4>
                <a href="#" class="see-all">See Details</a>
            </div>
            <div class="performance-metrics">
                <div class="metric-item">
                    <h3>30 </h3>
                    <p> (Minutes) Average Delivery Time</p>
                </div>
                <div class="metric-item">
                    <h3>10</h3>
                    <p>Orders In Transit</p>
                </div>
            </div>
        </section>


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