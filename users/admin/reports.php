<?php
include 'includes/header.php';

// Fetch overall statistics
$stats_query = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM orders) as total_orders,
        (SELECT COUNT(*) FROM customers) as total_customers,
        (SELECT COUNT(*) FROM kitchens) as total_kitchens,
        (SELECT COUNT(*) FROM delivery_riders) as total_riders,
        (SELECT COALESCE(SUM(final_total_amount), 0) FROM orders WHERE order_status = 'Delivered') as total_revenue
");
$stats = $stats_query->fetch_assoc();

// Get monthly orders data
$monthly_orders = $conn->query("
    SELECT 
        DATE_FORMAT(order_date, '%Y-%m') as month,
        COUNT(*) as order_count,
        SUM(final_total_amount) as revenue
    FROM orders 
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6
");

// Get top performing kitchens
$top_kitchens = $conn->query("
    SELECT 
        k.fname,
        k.lname,
        COUNT(o.order_id) as order_count,
        SUM(o.final_total_amount) as total_revenue
    FROM kitchens k
    LEFT JOIN orders o ON k.kitchen_id = o.kitchen_id
    WHERE o.order_status = 'Delivered'
    GROUP BY k.kitchen_id
    ORDER BY total_revenue DESC
    LIMIT 5
");

// Get top selling foods
$top_foods = $conn->query("
    SELECT 
        f.food_name,
        COUNT(oi.order_item_id) as order_count,
        SUM(oi.quantity) as total_quantity
    FROM food_listings f
    LEFT JOIN order_items oi ON f.food_id = oi.food_id
    LEFT JOIN orders o ON oi.order_id = o.order_id
    WHERE o.order_status = 'Delivered'
    GROUP BY f.food_id
    ORDER BY total_quantity DESC
    LIMIT 5
");
?>
<!-- Header Start -->
<?php include 'navbar/main.navbar.php'; ?>
<!-- Navigation Start -->
<?php include 'includes/sidebar.php'; ?>
<body>
    <div class="container-fluid py-4">
        <h2 class="mb-4">Dashboard Reports</h2>

        <!-- Overall Statistics -->
        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Orders</h6>
                        <div class="stat-value"><?php echo number_format($stats['total_orders']); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Revenue</h6>
                        <div class="stat-value">₱<?php echo number_format($stats['total_revenue'], 2); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Total Customers</h6>
                        <div class="stat-value"><?php echo number_format($stats['total_customers']); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted">Active Kitchens</h6>
                        <div class="stat-value"><?php echo number_format($stats['total_kitchens']); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Monthly Revenue Chart -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Revenue</h5>
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Kitchens -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Top Performing Kitchens</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Kitchen Name</th>
                                        <th>Orders</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($kitchen = $top_kitchens->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $kitchen['fname'] . ' ' . $kitchen['lname']; ?></td>
                                        <td><?php echo number_format($kitchen['order_count']); ?></td>
                                        <td>₱<?php echo number_format($kitchen['total_revenue'], 2); ?></td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Foods -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Top Selling Foods</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Food Name</th>
                                <th>Orders</th>
                                <th>Total Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($food = $top_foods->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $food['food_name']; ?></td>
                                <td><?php echo number_format($food['order_count']); ?></td>
                                <td><?php echo number_format($food['total_quantity']); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table> 
                </div><br><br><br>
            </div>
        </div>
    </div>


    <?php include 'includes/appbar.php'; ?>
    <?php include 'includes/scripts.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Initialize Revenue Chart
        const monthlyData = <?php 
            $chartData = [];
            while($row = $monthly_orders->fetch_assoc()) {
                $chartData[] = [
                    'month' => date('M Y', strtotime($row['month'] . '-01')),
                    'revenue' => $row['revenue']
                ];
            }
            echo json_encode(array_reverse($chartData));
        ?>;

        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: monthlyData.map(row => row.month),
                datasets: [{
                    label: 'Monthly Revenue',
                    data: monthlyData.map(row => row.revenue),
                    borderColor: '#502121',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>