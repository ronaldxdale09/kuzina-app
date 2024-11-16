<?php
// Statistics queries
$queries = [
    // Total active kitchens
    "SELECT COUNT(*) FROM kitchens WHERE isApproved = 1" => 'total_kitchens',
    
    // Total active riders
    "SELECT COUNT(*) FROM delivery_riders WHERE isApproved = 1" => 'total_riders',
    
    // Total customers
    "SELECT COUNT(*) FROM customers" => 'total_customers',
    
    // Pending approvals
    "SELECT COUNT(*) FROM kitchens WHERE isApproved = 0" => 'pending_kitchens',
    
    // Today's orders and revenue
    "SELECT COUNT(*) as count, COALESCE(SUM(final_total_amount), 0) as sum 
     FROM orders WHERE DATE(order_date) = CURDATE()" => 'todays_orders'
];

$stats = [];
foreach ($queries as $query => $key) {
    $result = $conn->query($query);
    if ($key === 'todays_orders') {
        $row = $result->fetch_assoc();
        $stats['total_orders'] = $row['count'];
        $stats['total_revenue'] = $row['sum'];
    } else {
        $stats[$key] = $result->fetch_row()[0];
    }
}
?>

<div class="dashboard-stats">
    <div class="metrics-grid">
        <div class="metric" onclick="location.href='#kitchens'">
            <div class="metric-top">
                <i class='bx bx-store-alt'></i>
                <div class="value"><?= $stats['total_kitchens'] ?></div>
            </div>
            <div class="metric-bottom">Active Kitchens</div>
            <div class="hover-effect"></div>
        </div>

        <div class="metric" onclick="location.href='#riders'">
            <div class="metric-top">
                <i class='bx bx-cycling'></i>
                <div class="value"><?= $stats['total_riders'] ?></div>
            </div>
            <div class="metric-bottom">Active Riders</div>
            <div class="hover-effect"></div>
        </div>

        <div class="metric" onclick="location.href='#customers'">
            <div class="metric-top">
                <i class='bx bx-user'></i>
                <div class="value"><?= $stats['total_customers'] ?></div>
            </div>
            <div class="metric-bottom">Total Customers</div>
            <div class="hover-effect"></div>
        </div>

        <div class="metric warning" onclick="location.href='#approvals'">
            <div class="metric-top">
                <i class='bx bx-time'></i>
                <div class="value"><?= $stats['pending_kitchens'] ?></div>
            </div>
            <div class="metric-bottom">Pending Approvals</div>
            <div class="hover-effect"></div>
        </div>

        <div class="metric" onclick="location.href='#orders'">
            <div class="metric-top">
                <i class='bx bx-package'></i>
                <div class="value"><?= $stats['total_orders'] ?></div>
            </div>
            <div class="metric-bottom">Today's Orders</div>
            <div class="hover-effect"></div>
        </div>

        <div class="metric success" onclick="location.href='#revenue'">
            <div class="metric-top">
                <i class='bx bx-money'></i>
                <div class="value">₱<?= number_format($stats['total_revenue'], 2) ?></div>
            </div>
            <div class="metric-bottom">Today's Revenue</div>
            <div class="hover-effect"></div>
        </div>
    </div>
</div>

<style>
</style>