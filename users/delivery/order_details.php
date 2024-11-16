<?php 
include 'includes/header.php';

// Get rider ID from cookie
$rider_id = $_COOKIE['rider_id'] ?? null;
if (!$rider_id) {
    header("Location: ../../rider.php");
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details with customer, kitchen, and address information
$sql = "SELECT o.*, 
               CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
               c.phone AS customer_phone,
               CONCAT(k.fname, ' ', k.lname) AS kitchen_name,
               k.phone AS kitchen_phone,
               k.address AS kitchen_address,
               ua.street_address, ua.city, ua.zip_code,
               p.payment_method, p.payment_status
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN kitchens k ON o.kitchen_id = k.kitchen_id
        JOIN user_addresses ua ON o.address_id = ua.address_id
        LEFT JOIN payments p ON o.payment_id = p.payment_id
        WHERE o.order_id = ? AND (o.rider_id = ? OR o.rider_id IS NULL)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $rider_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Fetch order items
$items_sql = "SELECT oi.*, f.food_name, f.photo1, f.price 
              FROM order_items oi
              JOIN food_listings f ON oi.food_id = f.food_id
              WHERE oi.order_id = ?";

$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();
?>

<link rel="stylesheet" type="text/css" href="assets/css/delivery.details.css" />

<body>
    <?php include 'navbar/main.navbar.php'; ?>

    <main class="main-wrap order-page mb-xxl">
        <div class="delivery-page">
            <!-- Order Status Card -->
            <div class="status-card">
                <div class="order-id-status">
                    <div class="order-number">Order #<?= $order_id ?></div>
                    <div class="status-badge <?= strtolower($order['order_status']) ?>">
                        <?= $order['order_status'] ?>
                    </div>
                </div>
                <div class="order-time">
                    <i class='bx bx-time-five'></i>
                    <?= date('F d, Y h:i A', strtotime($order['order_date'])) ?>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="info-section">
                <!-- Pickup Info (Kitchen) -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon pickup">
                            <i class='bx bx-store'></i>
                        </div>
                        <h2>Pickup Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Kitchen Name</span>
                            <span class="value"><?= htmlspecialchars($order['kitchen_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Contact</span>
                            <span class="value">
                                <a href="tel:<?= htmlspecialchars($order['kitchen_phone']) ?>" class="phone-link">
                                    <i class='bx bx-phone'></i>
                                    <?= htmlspecialchars($order['kitchen_phone']) ?>
                                </a>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Address</span>
                            <span class="value">
                                <?= htmlspecialchars($order['kitchen_address']) ?>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($order['kitchen_address']) ?>" 
                                   class="map-link" target="_blank">
                                    <i class='bx bx-map'></i> View Map
                                </a>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Info (Customer) -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon delivery">
                            <i class='bx bx-map-pin'></i>
                        </div>
                        <h2>Delivery Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Customer</span>
                            <span class="value"><?= htmlspecialchars($order['customer_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Contact</span>
                            <span class="value">
                                <a href="tel:<?= htmlspecialchars($order['customer_phone']) ?>" class="phone-link">
                                    <i class='bx bx-phone'></i>
                                    <?= htmlspecialchars($order['customer_phone']) ?>
                                </a>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Delivery Address</span>
                            <span class="value">
                                <?= htmlspecialchars($order['street_address'] . ', ' . $order['city']) ?>
                                <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($order['street_address'] . ', ' . $order['city']) ?>" 
                                   class="map-link" target="_blank">
                                    <i class='bx bx-map'></i> View Map
                                </a>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-package'></i>
                        </div>
                        <h2>Order Details</h2>
                    </div>
                    <div class="card-content">
                        <div class="order-items">
                            <?php 
                            $total = 0;
                            while($item = $items_result->fetch_assoc()): 
                                $subtotal = $item['quantity'] * $item['price'];
                                $total += $subtotal;
                            ?>
                            <div class="order-item">
                                <div class="item-details">
                                    <div class="item-name"><?= htmlspecialchars($item['food_name']) ?></div>
                                    <div class="item-quantity">×<?= $item['quantity'] ?></div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>

                        <div class="order-summary">
                            <div class="summary-row total">
                                <span>Total Amount</span>
                                <span>₱<?= number_format($order['final_total_amount'], 2) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Payment Method</span>
                                <span><?= ucfirst($order['payment_method']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn-return" onclick="window.history.back()">
                    <i class='bx bx-arrow-back'></i>
                    Back
                </button>

                <?php if ($order['order_status'] == 'For Pickup'): ?>
                <button class="btn-primary" onclick="updateOrderStatus(<?= $order_id ?>, 'On the Way')">
                    <i class='bx bx-package'></i>
                    Picked Up Order
                </button>
                <?php elseif ($order['order_status'] == 'On the Way'): ?>
                <button class="btn-primary" onclick="updateOrderStatus(<?= $order_id ?>, 'Delivered')">
                    <i class='bx bx-check-circle'></i>
                    Complete Delivery
                </button>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'includes/appbar.php'; ?>
    
    <script>
    function updateOrderStatus(orderId, newStatus) {
        if (confirm('Are you sure you want to update the order status?')) {
            fetch('api/update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderId,
                    status: newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Failed to update order status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the order');
            });
        }
    }
    </script>

    <?php include 'includes/scripts.php'; ?>
</body>
</html>