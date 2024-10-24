<?php include 'includes/header.php'; ?>
<link rel="stylesheet" type="text/css" href="assets/css/homepage.css" />

<body>

    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_homepage.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'includes/top_header.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->
    <a href="javascript:void(0)" class="overlay-sidebar"></a>
    <aside class="header-sidebar">
        <div class="wrap">
            <div class="user-panel">
                <div class="media">
                    <a href="account.html"> <img src="assets/images/avatar/avatar.jpg" alt="avatar" /></a>
                    <div class="media-body">
                        <a href="account.html" class="title-color font-sm">Andrea Joanne
                            <span class="content-color font-xs">andreajoanne@gmail.com</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation Start -->
            <?php include 'includes/navbar.php'; ?>
            <!-- Navigation End -->
        </div>

        <div class="contact-us">
            <span class="title-color">Contact Support</span>
            <p class="content-color font-xs">If you have any problem,queries or questions feel free to reach out</p>
            <a href="javascript:void(0)" class="btn-solid"> Contact Us </a>
        </div>
    </aside>
    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap dashboard-page mb-xxl">

        <!-- Order Statistics Section -->
        <section class="statistics-section">
            <div class="statistics-box">
                <div class="stat-item">
                    <i class='bx bx-package'></i> <!-- Icon for Running Orders -->
                    <h3>20</h3>
                    <p>Running Orders</p>
                </div>
                <div class="stat-item">
                    <i class='bx bx-receipt'></i> <!-- Icon for Order Requests -->
                    <h3>05</h3>
                    <p>Order Requests</p>
                </div>
            </div>
        </section>


        <!-- Revenue Chart Section -->
        <section class="revenue-section">
            <div class="revenue-top">
                <h4>Total Revenue</h4>
                <div class="revenue-filter">
                    <select id="revenue-duration" class="custom-select">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
            </div>
            <div class="revenue-total">
                <h3>Php 2,402</h3>
                <a href="#" class="details-link">See Details</a>
            </div>
            <!-- Chart Placeholder -->
            <div class="chart-wrap">
                <canvas id="revenueChart"></canvas>
            </div>
        </section>

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
        <section class="popular-items-section">
            <div class="section-header">
                <h4>Popular Items This Week</h4>
                <a href="#" class="see-all">See All</a>
            </div>
            <div class="popular-items">
                <?php
        // Query to fetch popular items (you can customize the logic for 'popular' as needed)
        $sql = "SELECT food_name, photo1 FROM food_listings WHERE available = 1 LIMIT 5";
        $result = $conn->query($sql);

        // Check if results are available
        if ($result->num_rows > 0) {
            // Output data for each row
            while ($row = $result->fetch_assoc()) {
                // Use the food name and image dynamically
                echo '<div class="item-card">';
                echo '<img src="../../uploads/' . htmlspecialchars($row['photo1']) . '" alt="' . htmlspecialchars($row['food_name']) . '">';
                echo '<p class="item-name">' . htmlspecialchars($row['food_name']) . '</p>';
                echo '</div>';
            }
        } else {
            echo "<p>No popular items found.</p>";
        }

        // Close the connection
        $conn->close();
        ?>
            </div>
        </section>
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
                <div class="metric-item">
                    <h3>2</h3>
                    <p>Canceled Orders</p>
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

    <!-- Chart.js for Revenue Graph -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    var ctx = document.getElementById('revenueChart').getContext('2d');
    var revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['10AM', '12PM', '2PM', '4PM', '6PM', '8PM'],
            datasets: [{
                label: 'Revenue',
                data: [203, 400, 150, 320, 500, 350],
                borderColor: '#FF6B6B',
                fill: false
            }]
        },
        options: {
            scales: {
                x: {
                    display: true
                },
                y: {
                    display: true,
                    beginAtZero: true
                }
            }
        }
    });
    </script>


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