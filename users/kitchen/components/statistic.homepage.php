<?php
// Assume $kitchen_id is set (replace this line if it's sourced differently)
$kitchen_id = $_COOKIE['kitchen_id'] ?? null;

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

    <div class="statistics-box">
        <a href="order_list.php?tab=1" class="stat-item">
            <div class="stat-icon">
                <i class='bx bx-package'></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $runningOrdersCount; ?></h3>
                <p>Running Orders</p>
            </div>
        </a>
        <a href="order_list.php" class="stat-item">
            <div class="stat-icon">
                <i class='bx bx-receipt'></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $orderRequestsCount; ?></h3>
                <p>Order Requests</p>
            </div>
        </a>
    </div>
<br>
<style>
.statistics-section {
    margin: 20px 0;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
}

.stat-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    background: #fcfcfc;
}

.stat-item:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    margin-right: 15px;
    border-radius: 10px;
    background: rgba(80, 33, 33, 0.1);
}

.stat-icon i {
    font-size: 24px;
    color: #502121;
}

.stat-content {
    flex: 1;
}

.stat-content h3 {
    font-size: 24px;
    color: #502121;
    margin: 0 0 5px 0;
}

.stat-content p {
    font-size: 14px;
    color: #666;
    margin: 0;
}

@media (max-width: 768px) {
    .statistics-box {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Add ripple effect
document.querySelectorAll('.stat-item').forEach(item => {
    item.addEventListener('click', function(e) {
        const rect = item.getBoundingClientRect();
        const ripple = document.createElement('div');
        const diameter = Math.max(rect.width, rect.height);
        
        ripple.style.width = ripple.style.height = `${diameter}px`;
        ripple.style.left = `${e.clientX - rect.left - diameter/2}px`;
        ripple.style.top = `${e.clientY - rect.top - diameter/2}px`;
        ripple.classList.add('ripple');
        
        item.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});
</script>

<style>
/* Ripple effect styles */
.stat-item {
    position: relative;
    overflow: hidden;
}

.ripple {
    position: absolute;
    background: rgba(80, 33, 33, 0.2);
    border-radius: 50%;
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
}

@keyframes ripple {
    to {
        transform: scale(4);
        opacity: 0;
    }
}
</style>