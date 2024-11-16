<?php 
include 'includes/header.php';

// Check if rider is logged in
$rider_id = $_COOKIE['rider_id'] ?? null;
if (!$rider_id) {
    header("Location: ../../rider.php");
    exit();
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/order_list.css" />

<body>
    <!-- <?php include 'skeleton/sk_menulist.php'; ?> -->
    <?php include 'navbar/main.navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Start -->
    <main class="main-wrap index-page mb-xxl">
        <!-- Orders Header -->
        <div class="orders-header">
            <h1>Available Orders</h1>
            <div class="filter-section">
                <select id="order-filter" class="custom-select">
                    <option value="all">All Orders</option>
                    <option value="nearby">Nearby First</option>
                    <option value="highest-pay">Highest Pay</option>
                </select>
            </div>
        </div>

        <!-- Orders List -->
        <!-- Orders List -->
        <div class="orders-container">
            <?php
   $query = "SELECT 
   o.order_id,
   o.order_status,
   o.total_amount,
   o.final_total_amount,
   o.order_date,
   k.fname AS kitchen_name,
   k.lname AS kitchen_lname,
   COALESCE(k.address, 'No address specified') AS pickup_address,
   COALESCE(ua.street_address, 'No delivery address') AS delivery_address,
   COALESCE(ua.city, '') AS delivery_city,
   COALESCE(ua.state, '') AS delivery_state
FROM orders o
JOIN kitchens k ON o.kitchen_id = k.kitchen_id
LEFT JOIN user_addresses ua ON o.address_id = ua.address_id
WHERE o.order_status = 'For Pickup' 
AND o.rider_id IS NULL
ORDER BY o.order_date DESC";

$stmt = $conn->prepare($query);
if (!$stmt) {
   die("Query preparation failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
   while ($order = $result->fetch_assoc()) {
       ?>
            <div class="order-card" data-order-id="<?php echo $order['order_id']; ?>">
                <div class="order-header">
                    <div class="order-info">
                        <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                        <div class="order-time">
                            <?php echo date('M d, Y h:i A', strtotime($order['order_date'])); ?>
                        </div>
                    </div>
                    <div class="order-amount">₱<?php echo number_format($order['final_total_amount'], 2); ?></div>
                </div>

                <div class="order-details">
                    <!-- Pickup Details -->
                    <div class="location-info pickup">
                        <i class='bx bx-store'></i>
                        <div class="location-text">
                            <h3><?php echo $order['kitchen_name'] . ' ' . $order['kitchen_lname']; ?></h3>
                            <p><?php echo $order['pickup_address']; ?></p>
                        </div>
                    </div>

                    <!-- Direction Arrow -->
                    <div class="direction-arrow">
                        <i class='bx bx-down-arrow-alt'></i>
                    </div>

                    <!-- Delivery Details -->
                    <div class="location-info delivery">
                        <i class='bx bx-map'></i>
                        <div class="location-text">
                            <h3>Delivery Location</h3>
                            <p>
                                <?php 
                           echo $order['delivery_address'];
                           if ($order['delivery_city'] || $order['delivery_state']) {
                               echo ', ' . $order['delivery_city'] . ' ' . $order['delivery_state'];
                           }
                           ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="order-actions">
                    <button class="btn-accept" onclick="acceptOrder(<?php echo $order['order_id']; ?>)">
                        <i class='bx bx-check-circle'></i>
                        Accept Order
                    </button>
                    <button class="btn-view" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                        <i class='bx bx-detail'></i>
                        View Details
                    </button>
                </div>
            </div>
            <?php
   }
} else {
   ?>
            <div class="no-orders">
                <i class='bx bx-package'></i>
                <h2>No Available Orders</h2>
                <p>Check back soon for new delivery requests</p>
            </div>
            <?php
}
?>
        </div>
    </main>

    <?php include 'includes/appbar.php'; ?>

    <!-- Accept Order Confirmation Modal -->
    <div class="modal" id="acceptOrderModal">
        <div class="modal-content">
            <h2>Accept Order</h2>
            <p>Are you sure you want to accept this order?</p>
            <div class="modal-actions">
                <button class="btn-outline" onclick="closeModal()">Cancel</button>
                <button class="btn-solid" onclick="confirmAcceptOrder()">Accept</button>
            </div>
        </div>
    </div>

    <?php include 'includes/scripts.php'; ?>

    <script>
    let selectedOrderId = null;

    function acceptOrder(orderId) {
        selectedOrderId = orderId;
        document.getElementById('acceptOrderModal').classList.add('show');
    }

    function closeModal() {
        document.getElementById('acceptOrderModal').classList.remove('show');
        selectedOrderId = null;
    }

    function confirmAcceptOrder() {
        if (!selectedOrderId) return;

        // Show loading state
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) loadingOverlay.style.display = 'flex';

        fetch('functions/accept_delivery.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: selectedOrderId,
                    rider_id: <?php echo $rider_id; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the order card from the list
                    const orderCard = document.querySelector(`[data-order-id="${selectedOrderId}"]`);
                    if (orderCard) {
                        orderCard.remove();
                    }

                    // Redirect to order details
                    window.location.href = `delivery_list.php`;
                } else {
                    alert(data.message || 'Failed to accept order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while accepting the order');
            })
            .finally(() => {
                if (loadingOverlay) loadingOverlay.style.display = 'none';
                closeModal();
            });
    }

    function viewOrderDetails(orderId) {
        window.location.href = `order_details.php?order_id=${orderId}`;
    }

    // Filter handling
    document.getElementById('order-filter').addEventListener('change', function() {
        const filterValue = this.value;
        const ordersContainer = document.querySelector('.orders-container');

        // Add loading state
        ordersContainer.classList.add('loading');

        fetch(`api/filter_orders.php?filter=${filterValue}`)
            .then(response => response.json())
            .then(data => {
                // Update orders list
                // Implementation depends on your data structure
            })
            .finally(() => {
                ordersContainer.classList.remove('loading');
            });
    });
    </script>
</body>

</html>