<?php include 'includes/header.php'; 
// Place this at the very top of your first PHP file (e.g., index.php or config.php)
error_reporting(0); // Disable all error reporting
// OR
error_reporting(E_ERROR | E_PARSE); // Show only fatal errors and parse errors
// OR
ini_set('display_errors', 0); // Hide all errors from display

// Get kitchen ID from cookie name 'kitchen_user_id'
$kitchen_id = $_COOKIE['kitchen_id'] ?? $_SESSION['kitchen_id'] ?? null;

// Simple approval check
$stmt = $conn->prepare("SELECT isApproved FROM kitchens WHERE kitchen_id = ?");
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$result = $stmt->get_result();
$kitchen = $result->fetch_assoc();

if (!$kitchen || $kitchen['isApproved'] != 1) {
    header("Location: isApproved.php");
    exit();
}
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

        <?php include 'components/kitchen.balance.php'; ?>

        <!-- Revenue Chart Section -->
        <?php include 'components/chart.homepage.php'; ?>


        <!-- Reviews Section -->
        <?php include 'components/homepage.review.php'; ?>


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
  

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>