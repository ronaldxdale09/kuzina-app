<?php 
include 'includes/header.php';

$tab = '';
if (isset($_GET['tab'])) {
    $tab = filter_var($_GET['tab']);
}



// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details with customer, address, and rider information
$sql = "SELECT o.*, 
               CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
               c.phone AS customer_phone,
               ua.street_address, ua.city, ua.zip_code,
               p.payment_method, p.payment_status,
               dr.first_name AS rider_first_name, 
               dr.last_name AS rider_last_name,
               dr.phone AS rider_phone
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN user_addresses ua ON o.address_id = ua.address_id
        LEFT JOIN payments p ON o.payment_id = p.payment_id
        LEFT JOIN delivery_riders dr ON o.rider_id = dr.rider_id
        WHERE o.order_id = ? AND o.kitchen_id = ?";

$stmt = $conn->prepare($sql);
$kitchen_id = $_COOKIE['kitchen_id'];
$stmt->bind_param("ii", $order_id, $kitchen_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();


// Fix for order items query - Add this after your first main order query
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
<link rel="stylesheet" type="text/css" href="assets/css/modal.css" />

<?php include 'modal/modal.order.php'; ?>

<body>
    <!-- Header Start -->
    <?php include 'navbar/shop.navbar.php'; ?>
    <!-- Header End -->

    <!-- Sidebar Start -->

    <!-- Navigation End -->
    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap order-page mb-xxl">


        <div class="delivery-page">

            <!-- Order Status Card -->
            <div class="status-card">
                <div class="order-id-status">
                    <div class="order-number">Order #<?= $order_id ?></div>
                    <div class="status-badge">On the Way</div>
                </div>
                <div class="order-time">
                    <i class='bx bx-time-five'></i>
                    <?= date('F d, Y h:i A', strtotime($order['order_date'])) ?>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="info-section">
                <!-- Customer Info -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-user'></i>
                        </div>
                        <h2>Customer Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Name</span>
                            <span class="value"><?= htmlspecialchars($order['customer_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Phone</span>
                            <span class="value">
                                <a
                                    href="tel:<?= htmlspecialchars($order['customer_phone']) ?>"><?= htmlspecialchars($order['customer_phone']) ?></a>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Address</span>
                            <span
                                class="value"><?= htmlspecialchars($order['street_address'] . ', ' . $order['city']) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Delivery Info -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-cycling'></i>
                        </div>
                        <h2>Delivery Information</h2>
                    </div>
                    <div class="card-content">
                        <?php if ($order['rider_first_name']): ?>
                        <div class="info-item">
                            <span class="label">Rider</span>
                            <span
                                class="value"><?= htmlspecialchars($order['rider_first_name'] . ' ' . $order['rider_last_name']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Contact</span>
                            <span class="value">
                                <a
                                    href="tel:<?= htmlspecialchars($order['rider_phone']) ?>"><?= htmlspecialchars($order['rider_phone']) ?></a>
                            </span>
                        </div>
                        <?php else: ?>
                        <div class="no-rider">
                            <i class='bx bx-error-circle'></i>
                            <p>No rider assigned yet</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-cart'></i>
                        </div>
                        <h2>Order Items</h2>
                    </div>
                    <div class="card-content">
                        <div class="order-items">
                            <?php foreach ($items_result as $item): 
                        $subtotal = $item['quantity'] * $item['price'];
                    ?>
                            <div class="order-item">
                                <div class="item-image">
                                    <?php if ($item['photo1']): ?>
                                    <img src="../../uploads/<?= htmlspecialchars($item['photo1']) ?>"
                                        alt="<?= htmlspecialchars($item['food_name']) ?>">
                                    <?php else: ?>
                                    <div class="placeholder-image">
                                        <i class='bx bx-image'></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-details">
                                    <div class="item-name"><?= htmlspecialchars($item['food_name']) ?></div>
                                    <div class="item-meta">
                                        <div class="quantity">×<?= $item['quantity'] ?></div>
                                        <div class="price">₱<?= number_format($item['price'], 2) ?></div>
                                    </div>
                                </div>
                                <div class="item-total">
                                    ₱<?= number_format($subtotal, 2) ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-summary">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span>₱<?= number_format($order['total_amount'], 2) ?></span>
                            </div>
                            <?php if ($order['discount_amount'] > 0): ?>
                            <div class="summary-row discount">
                                <span>Discount</span>
                                <span>-₱<?= number_format($order['discount_amount'], 2) ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="summary-row total">
                                <span>Total</span>
                                <span>₱<?= number_format($order['final_total_amount'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Info -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-credit-card'></i>
                        </div>
                        <h2>Payment Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Method</span>
                            <span class="value"><?= ucfirst($order['payment_method']) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Status</span>
                            <span class="value">
                                <span class="payment-badge <?= strtolower($order['payment_status']) ?>">
                                    <?= ucfirst($order['payment_status']) ?>
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn-return" onclick="window.location.href='order_list.php?tab=2'">
                    <i class='bx bx-arrow-back'></i>
                    Return to List
                </button>
                <button class="btn-secondary" onclick="window.print()">
                    <i class='bx bx-printer'></i>
                    Print Order
                </button>
                <button class="btn-primary" onclick="markAsDelivered(<?= $order_id ?>)">
                    <i class='bx bx-check-circle'></i>
                    Mark as Delivered
                </button>
            </div>
        </div>
    </main>
    <!-- Main End -->


    <?php include 'includes/appbar.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>

</html>