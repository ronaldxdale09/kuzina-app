<?php
// Get order statistics
$orderQueries = [
    // Pending orders
    "SELECT COUNT(*) FROM orders WHERE order_status = 'Pending'" => 'pending_orders',
    
    // Active orders (Confirmed, Preparing, For Pickup, On the Way)
    "SELECT COUNT(*) FROM orders 
     WHERE order_status IN ('Confirmed', 'Preparing', 'For Pickup', 'On the Way')" => 'active_orders',
    
    // Completed orders today
    "SELECT COUNT(*) FROM orders 
     WHERE order_status = 'Delivered' 
     AND DATE(updated_at) = CURDATE()" => 'completed_orders',
    
    // Cancelled orders today
    "SELECT COUNT(*) FROM orders 
     WHERE order_status = 'Cancelled' 
     AND DATE(updated_at) = CURDATE()" => 'cancelled_orders',
    
    // Order success rate (Delivered vs Total Orders)
    "SELECT 
        COUNT(CASE WHEN order_status = 'Delivered' THEN 1 END) * 100.0 / COUNT(*) 
     FROM orders 
     WHERE DATE(order_date) = CURDATE()" => 'success_rate'
];

$stats = [];
foreach ($orderQueries as $query => $key) {
    $result = $conn->query($query);
    $stats[$key] = $result->fetch_row()[0];
}

// Round success rate to 2 decimal places
$stats['success_rate'] = number_format($stats['success_rate'] ?? 0, 2);
?>
<div class="order-section">
    <div class="section-header">
        <h2>Order Management</h2>
        <div class="header-stats">
            <span>Total Orders Today: <?= array_sum([$stats['pending_orders'], $stats['active_orders'], $stats['completed_orders'], $stats['cancelled_orders']]) ?></span>
        </div>
    </div>

    <div class="order-metrics">
        <div class="metric-container pending">
            <div class="metric-header">
                <i class='bx bx-time-five'></i>
                <h3>Pending</h3>
            </div>
            <div class="metric-value"><?= $stats['pending_orders'] ?></div>
        </div>

        <div class="metric-container active">
            <div class="metric-header">
                <i class='bx bx-package'></i>
                <h3>Active</h3>
            </div>
            <div class="metric-value"><?= $stats['active_orders'] ?></div>
        </div>

        <div class="metric-container completed">
            <div class="metric-header">
                <i class='bx bx-check-circle'></i>
                <h3>Completed</h3>
            </div>
            <div class="metric-value"><?= $stats['completed_orders'] ?></div>
        </div>

        <div class="metric-container cancelled">
            <div class="metric-header">
                <i class='bx bx-x-circle'></i>
                <h3>Cancelled</h3>
            </div>
            <div class="metric-value"><?= $stats['cancelled_orders'] ?></div>
        </div>
    </div>

    <div class="success-rate">
        <div class="rate-label">Success Rate</div>
        <div class="rate-value"><?= $stats['success_rate'] ?>%</div>
        <div class="progress-bar">
            <div class="progress" style="width: <?= $stats['success_rate'] ?>%"></div>
        </div>
    </div>
</div>
