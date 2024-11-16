<?php
// Get rider_id from cookie
$rider_id = $_COOKIE['rider_id'] ?? null;

if ($rider_id) {
    // Active deliveries count
    $activeDeliveriesQuery = "SELECT COUNT(*) FROM orders 
        WHERE rider_id = ? AND order_status IN ('For Pickup', 'On the Way')";
    $stmt = $conn->prepare($activeDeliveriesQuery);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $stmt->bind_result($activeDeliveries);
    $stmt->fetch();
    $stmt->close();

    // Today's earnings
    $todayEarningsQuery = "SELECT COALESCE(SUM(amount), 0) FROM rider_earnings 
        WHERE rider_id = ? AND DATE(earning_date) = CURDATE()";
    $stmt = $conn->prepare($todayEarningsQuery);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $stmt->bind_result($todayEarnings);
    $stmt->fetch();
    $stmt->close();

    // Today's completed deliveries
    $completedTodayQuery = "SELECT COUNT(*) FROM orders 
        WHERE rider_id = ? 
        AND order_status = 'Delivered' 
        AND DATE(updated_at) = CURDATE()";
    $stmt = $conn->prepare($completedTodayQuery);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $stmt->bind_result($completedToday);
    $stmt->fetch();
    $stmt->close();

    // Average delivery time today
    $avgTimeQuery = "SELECT 
        AVG(TIMESTAMPDIFF(MINUTE, o.order_date, o.updated_at)) 
        FROM orders o
        WHERE o.rider_id = ? 
        AND o.order_status = 'Delivered' 
        AND DATE(o.updated_at) = CURDATE()";
    $stmt = $conn->prepare($avgTimeQuery);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $stmt->bind_result($avgDeliveryTime);
    $stmt->fetch();
    $stmt->close();

} else {
    $activeDeliveries = 0;
    $todayEarnings = 0;
    $completedToday = 0;
    $avgDeliveryTime = 0;
}

// Format values
$formattedEarnings = number_format($todayEarnings ?? 0, 2);
$avgDeliveryTime = round($avgDeliveryTime ?? 0);
?>

<section class="statistics-section">
    <div class="statistics-box">
        <div class="stat-item">
            <div class="stat-icon active">
                <i class='bx bx-cycling'></i>
            </div>
            <div class="stat-info">
                <h3><?= $activeDeliveries ?></h3>
                <p>Active Deliveries</p>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon earnings">
                <i class='bx bx-money'></i>
            </div>
            <div class="stat-info">
                <h3>₱<?= $formattedEarnings ?></h3>
                <p>Today's Earnings</p>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon completed">
                <i class='bx bx-check-circle'></i>
            </div>
            <div class="stat-info">
                <h3><?= $completedToday ?></h3>
                <p>Completed Today</p>
            </div>
        </div>

        <div class="stat-item">
            <div class="stat-icon time">
                <i class='bx bx-time'></i>
            </div>
            <div class="stat-info">
                <h3><?= $avgDeliveryTime ?> min</h3>
                <p>Avg Delivery Time</p>
            </div>
        </div>
    </div>
</section>