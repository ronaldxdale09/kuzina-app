<?php

// Assume $kitchen_id is set (replace this line if it's sourced differently)
$kitchen_id = $_COOKIE['kitchen_user_id'] ?? null; 

if ($kitchen_id) {
    // Query to count running orders specific to the kitchen
    $runningOrdersQuery = "SELECT COUNT(*) FROM orders WHERE kitchen_id = ? AND order_status IN ('On the Way', 'Preparing')";
    $stmt = $conn->prepare($runningOrdersQuery);
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $stmt->bind_result($runningOrdersCount);
    $stmt->fetch();
    $stmt->close();

    // Query to count order requests specific to the kitchen
    $orderRequestsQuery = "SELECT COUNT(*) FROM orders WHERE kitchen_id = ? AND order_status IN ('Pending', 'Confirmed')";
    $stmt = $conn->prepare($orderRequestsQuery);
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $stmt->bind_result($orderRequestsCount);
    $stmt->fetch();
    $stmt->close();
} else {
    // Default values if no kitchen_id is provided
    $runningOrdersCount = 0;
    $orderRequestsCount = 0;
}

?>

<section class="statistics-section">
    <div class="statistics-box">
        <div class="stat-item">
            <i class='bx bx-package'></i> <!-- Icon for Running Orders -->
            <h3><?php echo $runningOrdersCount; ?></h3>
            <p>Running Orders</p>
        </div>
        <div class="stat-item">
            <i class='bx bx-receipt'></i> <!-- Icon for Order Requests -->
            <h3><?php echo $orderRequestsCount; ?></h3>
            <p>Order Requests</p>
        </div>
    </div>
</section>
