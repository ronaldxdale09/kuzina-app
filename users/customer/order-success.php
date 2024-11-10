<?php include 'includes/header.php';


// Fetch the order details using a unique order identifier, such as order_id from a previous session or redirect
$order_id = $_GET['order_id'] ?? null; // Assuming order_id is passed in the URL

if ($order_id) {
    // Query the order details
    $sql = "SELECT o.order_id, o.order_date, o.total_amount, o.final_total_amount, 
                   o.discount_amount, a.street_address, a.city, a.state, a.zip_code 
            FROM orders o
            LEFT JOIN user_addresses a ON o.address_id = a.address_id
            WHERE o.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if ($order) {
        // Variables for HTML injection
        $orderDate = date("D, d M, H:i", strtotime($order['order_date']));
        $orderID = htmlspecialchars($order['order_id']);
        $totalAmount = number_format($order['total_amount'], 2);
        $finalTotalAmount = number_format($order['final_total_amount'], 2);
        $discountAmount = number_format($order['discount_amount'], 2);
    } else {
        echo "Order not found.";
        exit;
    }
} else {
    echo "Order ID missing.";
    exit;
}


?>
<!-- Head End -->

<!-- Body Start -->

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_order-sucess.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->

    <?php include 'navbar/main.navbar.php'; ?>
    <!-- Header End -->

    <!-- Sidebar Start -->
    <?php include 'includes/sidebar.php'; ?>
    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap order-success-page mb-xxl">
        <!-- Banner Section Start -->
        <section class="banner-section">
            <div class="banner-wrap">
                <img src="assets/svg/order-success.svg" alt="order-success" />
            </div>

            <div class="content-wrap">
                <h1 class="font-lg title-color">Thank you for your order!</h1>
                <p class="font-sm content-color">Your order has been placed successfully. Your order ID is
                    #<?php echo $orderID; ?></p>
            </div>
        </section>
        <!-- Banner Section End -->

        <!-- Order Id Section Start -->
        <section class="order-id-section">
            <div class="media">
                <span><i class="iconly-Calendar icli"></i></span>
                <div class="media-body">
                    <h2 class="font-sm color-title">Order Date</h2>
                    <span class="content-color"><?php echo $orderDate; ?></span>
                </div>
            </div>

            <div class="media">
                <span><i class="iconly-Document icli"></i></span>
                <div class="media-body">
                    <h2 class="font-sm color-title">Order ID</h2>
                    <span class="content-color">#<?php echo $orderID; ?></span>
                </div>
            </div>
        </section>
        <!-- Order Id Section End -->

        <!-- Order Detail Start -->
        <section class="order-detail">
            <h3 class="title-2">Order Details</h3>
            <!-- Product Detail Start -->
            <ul>
                <li>
                    <span>Bag total</span>
                    <span>PHP <?php echo $totalAmount; ?></span>
                </li>



                <li>
                    <span>Delivery</span>
                    <span>PHP 50.00</span> <!-- Adjust this if you have a delivery charge -->
                </li>

                <li>
                    <span>Total Amount</span>
                    <span>PHP <?php echo $finalTotalAmount; ?></span>
                </li>
            </ul>
            <!-- Product Detail End -->
        </section>
        <!-- Order Detail End -->
    </main>

    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-wrap footer-button">
        <a href="order-tracking.php?order=<?php echo $orderID?>" class="font-md">Track Package on Map</a>
    </footer>
    <!-- Footer End -->

    <!-- Action Language Start -->

    <!-- Action Language End -->

    <?php include 'includes/scripts.php'; ?>
</body>
<!-- Body End -->

</html>
<!-- Html End -->