<?php
// Start output buffering at the very beginning
ob_start();

// Include database connection and functions
include 'includes/header.php';

// Authentication and Verification Check
function checkRiderAuth() {
    global $conn;
    
    // Check if rider is logged in
    $rider_id = $_COOKIE['rider_id'] ?? null;
    
    if (!$rider_id) {
        ob_end_clean(); // Clear output buffer
        header("Location: ../../rider.php");
        exit();
    }
    
    // Check rider approval status
    $stmt = $conn->prepare("SELECT isApproved FROM delivery_riders WHERE rider_id = ? AND isApproved = 1");
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows === 0) {
        ob_end_clean(); // Clear output buffer
        header("Location: isApproved.php");
        exit();
    }
    
    $stmt->close();
    return $rider_id;
}

// Perform authentication check
$rider_id = checkRiderAuth();

?>

<link rel="stylesheet" type="text/css" href="assets/css/homepage.css" />

<body>
    <!-- Skeleton loader -->
    <?php include 'skeleton/sk_homepage.php'; ?>

    <!-- Header -->
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-wrap dashboard-page mb-xxl">
        <!-- Delivery Statistics Section -->
            <?php include 'components/delivery.statistic.php'; ?>
            <?php include 'components/rider.balance.php'; ?>

        <!-- Earnings Chart Section -->
            <?php include 'components/earnings_chart.php'; ?>

  
     

    </main>

    <!-- App Bar -->
    <?php include 'includes/appbar.php'; ?>

    <!-- Language Selection -->
    <div class="action action-language offcanvas offcanvas-bottom" tabindex="-1" id="language">
        <div class="offcanvas-body small">
            <h2 class="m-b-title1 font-md">Select Language</h2>
            <ul class="list">
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close">
                        <img src="assets/icons/flag/us.svg" alt="us" /> English
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Scripts -->
    <?php include 'includes/scripts.php'; ?>

</body>
</html>