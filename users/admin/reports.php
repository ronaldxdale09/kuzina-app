<?php
include 'includes/header.php';

// Handle AJAX requests first, before any output
if(isset($_POST['action'])) {
    
    header('Content-Type: application/json');
    $period = $_POST['period'] ?? 'month';
    
    $date_filter = getDateRangeFilter($period);
    
    try {
        switch($_POST['action']) {
            case 'fetch_stats':
                $stats_query = $conn->query("
                    SELECT 
                        (SELECT COUNT(*) FROM orders WHERE $date_filter) as total_orders,
                        (SELECT COUNT(*) FROM customers) as total_customers,
                        (SELECT COUNT(*) FROM kitchens) as total_kitchens,
                        (SELECT COALESCE(SUM(final_total_amount), 0) FROM orders 
                         WHERE order_status = 'Delivered' AND $date_filter) as total_revenue
                ");
                echo json_encode($stats_query->fetch_assoc());
                break;
                
            case 'fetch_chart':
                $chart_query = $conn->query("
                    SELECT 
                        DATE_FORMAT(order_date, '%Y-%m-%d') as date,
                        COUNT(*) as order_count,
                        SUM(final_total_amount) as revenue
                    FROM orders 
                    WHERE $date_filter
                    GROUP BY DATE(order_date)
                    ORDER BY date DESC
                    LIMIT 30
                ");
                
                $chart_data = array();
                while($row = $chart_query->fetch_assoc()) {
                    $chart_data[] = $row;
                }
                echo json_encode($chart_data);
                break;
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}



// Helper function for date filtering
function getDateRangeFilter($period = 'month')
{
    $today = date('Y-m-d');
    switch ($period) {
        case 'today':
            return "DATE(order_date) = '$today'";
        case 'week':
            $week_start = date('Y-m-d', strtotime('monday this week'));
            return "DATE(order_date) >= '$week_start'";
        case 'month':
            $month_start = date('Y-m-01');
            return "DATE(order_date) >= '$month_start'";
        case 'year':
            $year_start = date('Y-01-01');
            return "DATE(order_date) >= '$year_start'";
        default:
            return "1=1"; // All time
    }
}

// Initial data load
$default_filter = getDateRangeFilter('month');

// Fetch overall statistics
$stats_query = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM orders WHERE $default_filter) as total_orders,
        (SELECT COUNT(*) FROM customers) as total_customers,
        (SELECT COUNT(*) FROM kitchens) as total_kitchens,
        (SELECT COALESCE(SUM(final_total_amount), 0) FROM orders 
         WHERE order_status = 'Delivered' AND $default_filter) as total_revenue
");
$stats = $stats_query->fetch_assoc();

// Get monthly orders data
$monthly_orders = $conn->query("
    SELECT 
        DATE_FORMAT(order_date, '%Y-%m-%d') as date,
        COUNT(*) as order_count,
        SUM(final_total_amount) as revenue
    FROM orders 
    WHERE $default_filter
    GROUP BY DATE(order_date)
    ORDER BY date DESC
    LIMIT 30
");

// Get top performing kitchens
$top_kitchens = $conn->query("
    SELECT 
        k.fname, k.lname,
        COUNT(o.order_id) as order_count,
        SUM(o.final_total_amount) as total_revenue
    FROM kitchens k
    LEFT JOIN orders o ON k.kitchen_id = o.kitchen_id
    WHERE o.order_status = 'Delivered' AND $default_filter
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
    WHERE o.order_status = 'Delivered' AND $default_filter
    GROUP BY f.food_id
    ORDER BY total_quantity DESC
    LIMIT 5
");
?>

<link rel="stylesheet" type="text/css" href="assets/css/report.css" />

<?php include 'navbar/main.navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<body>
    <div class="container-fluid py-2">
        <div class="header-section">
            <h2 class="dashboard-title">Dashboard Reports</h2>
            <div class="filter-section">
                <select id="timeFilter" class="time-filter">
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month" selected>This Month</option>
                    <option value="year">This Year</option>
                    <option value="all">All Time</option>
                </select>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-label">Total Orders</span>
                <span class="stat-number" data-stat="orders"><?php echo number_format($stats['total_orders']); ?></span>
            </div>

            <div class="stat-card">
                <span class="stat-label">Total Revenue</span>
                <span class="stat-number"
                    data-stat="revenue">₱<?php echo number_format($stats['total_revenue'], 2); ?></span>
            </div>

            <div class="stat-card">
                <span class="stat-label">Total Customers</span>
                <span class="stat-number"
                    data-stat="customers"><?php echo number_format($stats['total_customers']); ?></span>
            </div>

            <div class="stat-card">
                <span class="stat-label">Active Kitchens</span>
                <span class="stat-number"
                    data-stat="kitchens"><?php echo number_format($stats['total_kitchens']); ?></span>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-grid">
            <!-- Monthly Revenue Chart -->
            <div class="chart-card">
                <h5 class="card-title">Revenue Trend</h5>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Top Kitchens -->
            <div class="chart-card">
                <h5 class="card-title">Top Performing Kitchens</h5>
                <div class="table-responsive">
                    <table class="table" id="kitchensTable">
                        <thead>
                            <tr>
                                <th>Kitchen Name</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($kitchen = $top_kitchens->fetch_assoc()): ?>
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

        <!-- Top Selling Foods -->
        <div class="chart-card">
            <h5 class="card-title">Top Selling Foods</h5>
            <div class="table-responsive">
                <table class="table" id="foodsTable">
                    <thead>
                        <tr>
                            <th>Food Name</th>
                            <th>Orders</th>
                            <th>Total Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($food = $top_foods->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $food['food_name']; ?></td>
                            <td><?php echo number_format($food['order_count']); ?></td>
                            <td><?php echo number_format($food['total_quantity']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php include 'includes/appbar.php'; ?>
    <?php include 'includes/scripts.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    let revenueChart;

    // Initialize charts when document is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeRevenueChart();

        // Add event listener for time filter
        document.getElementById('timeFilter').addEventListener('change', function() {
            updateDashboard(this.value);
        });
    });

    function initializeRevenueChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    data: [],
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

        // Load initial data
        updateDashboard('month');
    }

    function updateDashboard(period) {
    // Update stats
    fetch('reports.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=fetch_stats&period=${period}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        updateStats(data);
    })
    .catch(error => {
        console.error('Error updating stats:', error);
    });

    // Update chart
    fetch('reports.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=fetch_chart&period=${period}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        updateChart(data);
    })
    .catch(error => {
        console.error('Error updating chart:', error);
    });
}

    // Add loading state CSS
    document.head.insertAdjacentHTML('beforeend', `
    <style>
        .loading {
            opacity: 0.6;
            pointer-events: none;
            position: relative;
        }
        .loading::after {
            content: "Loading...";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 14px;
            color: #502121;
        }
    </style>
`);

    function updateStats(data) {
        document.querySelector('[data-stat="orders"]').textContent =
            Number(data.total_orders).toLocaleString();
        document.querySelector('[data-stat="revenue"]').textContent =
            '₱' + Number(data.total_revenue).toLocaleString(undefined, {
                minimumFractionDigits: 2
            });
        document.querySelector('[data-stat="customers"]').textContent =
            Number(data.total_customers).toLocaleString();
        document.querySelector('[data-stat="kitchens"]').textContent =
            Number(data.total_kitchens).toLocaleString();
    }

    function updateChart(data) {
        revenueChart.data.labels = data.map(item => formatDate(item.date));
        revenueChart.data.datasets[0].data = data.map(item => item.revenue);
        revenueChart.update();
    }

    function updateKitchenTable(data) {
        const tbody = document.querySelector('#kitchensTable tbody');
        tbody.innerHTML = data.map(kitchen => `
                <tr>
                    <td>${kitchen.fname} ${kitchen.lname}</td>
                    <td>${Number(kitchen.order_count).toLocaleString()}</td>
                    <td>₱${Number(kitchen.total_revenue).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                </tr>
            `).join('');
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    }
    </script>