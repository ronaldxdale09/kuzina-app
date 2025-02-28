<?php include 'includes/header.php';

// Redirect to login if not logged in
if (!isset($customer_id) || empty($customer_id)) {
    header("Location: login.php");
    exit();
}

// Enhanced function to get orders by status with better error handling
function getOrdersByStatus($conn, $customer_id, $statuses)
{
    try {
        $statusPlaceholders = str_repeat('?,', count($statuses) - 1) . '?';
        $query = "SELECT 
            o.order_id,
            o.order_status,
            o.order_date,
            o.total_amount,
            o.final_total_amount,
            o.delivery_fee,
            COUNT(oi.order_item_id) as total_items,
            COALESCE(ua.street_address, 'No address specified') as delivery_address,
            COALESCE(ua.city, '') as city,
            COALESCE(ua.state, '') as state,
            k.kitchen_id,
            k.fname AS kitchen_fname,
            k.lname AS kitchen_lname,
            GROUP_CONCAT(DISTINCT r.rating) as ratings,
            GROUP_CONCAT(DISTINCT r.food_id) as rated_foods
        FROM orders o
        LEFT JOIN order_items oi ON o.order_id = oi.order_id
        LEFT JOIN user_addresses ua ON o.address_id = ua.address_id
        LEFT JOIN kitchens k ON o.kitchen_id = k.kitchen_id
        LEFT JOIN reviews r ON o.order_id = r.order_id AND r.customer_id = o.customer_id
        WHERE o.customer_id = ? 
        AND o.order_status IN ($statusPlaceholders)
        GROUP BY o.order_id
        ORDER BY o.order_date DESC";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }

        $params = array_merge([$customer_id], $statuses);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);

        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        return $stmt->get_result();
    } catch (Exception $e) {
        error_log("Error in getOrdersByStatus: " . $e->getMessage());
        return false;
    }
}

// Function to get order items for display
function getOrderItems($conn, $order_id, $limit = 3)
{
    try {
        $query = "SELECT oi.order_item_id, oi.quantity, f.food_name, f.food_id, f.photo1
                 FROM order_items oi
                 JOIN food_listings f ON oi.food_id = f.food_id
                 WHERE oi.order_id = ?
                 LIMIT ?";

        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Query preparation failed: " . $conn->error);
        }

        $stmt->bind_param("ii", $order_id, $limit);

        if (!$stmt->execute()) {
            throw new Exception("Query execution failed: " . $stmt->error);
        }

        return $stmt->get_result();
    } catch (Exception $e) {
        error_log("Error in getOrderItems: " . $e->getMessage());
        return false;
    }
}

// Function to check if all food items in an order have been reviewed
function isOrderFullyReviewed($conn, $order_id, $customer_id)
{
    try {
        // Count total food items in the order
        $totalItemsQuery = "SELECT COUNT(DISTINCT oi.food_id) as total_foods
                           FROM order_items oi
                           WHERE oi.order_id = ?";
        $totalStmt = $conn->prepare($totalItemsQuery);
        $totalStmt->bind_param("i", $order_id);
        $totalStmt->execute();
        $totalResult = $totalStmt->get_result()->fetch_assoc();
        $totalFoods = $totalResult['total_foods'];

        // Count reviewed food items
        $reviewedQuery = "SELECT COUNT(DISTINCT food_id) as reviewed_foods
                         FROM reviews
                         WHERE order_id = ? AND customer_id = ?";
        $reviewedStmt = $conn->prepare($reviewedQuery);
        $reviewedStmt->bind_param("ii", $order_id, $customer_id);
        $reviewedStmt->execute();
        $reviewedResult = $reviewedStmt->get_result()->fetch_assoc();
        $reviewedFoods = $reviewedResult['reviewed_foods'];

        return $totalFoods <= $reviewedFoods;
    } catch (Exception $e) {
        error_log("Error in isOrderFullyReviewed: " . $e->getMessage());
        return false;
    }
}
?>

<link rel="stylesheet" href="assets/css/order-history.css">


<body>
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();"><i
                    class="iconly-Arrow-Left-Square icli"></i></a>
            <h1 class="title-color font-md">Order History</h1>
        </div>
        <div class="avatar-wrap">
            <a href="homepage.php">
                <i class="iconly-Home icli"></i>
            </a>
        </div>
    </header>
    <!-- Main content -->
    <main class="main-wrap">
        <!-- Tab Navigation -->
        <ul class="nav nav-tab" id="ordersTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="processing-tab" data-bs-toggle="pill" data-bs-target="#processing" type="button" role="tab" aria-selected="true">
                    Processing
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed" type="button" role="tab" aria-selected="false">
                    Completed
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cancelled-tab" data-bs-toggle="pill" data-bs-target="#cancelled" type="button" role="tab" aria-selected="false">
                    Cancelled
                </button>
            </li>
        </ul>

<br>


        <!-- Tab Content -->
        <div class="tab-content" id="orderTabsContent">
            <!-- Processing Orders Tab -->
            <div class="tab-pane fade show active" id="processing" role="tabpanel">
                <?php
                $processingStatuses = ['Pending', 'Confirmed', 'Preparing', 'For Pickup', 'On the Way'];
                $processingOrders = getOrdersByStatus($conn, $customer_id, $processingStatuses);

                if ($processingOrders && $processingOrders->num_rows > 0) {
                    while ($order = $processingOrders->fetch_assoc()) {
                        $orderItems = getOrderItems($conn, $order['order_id'], 4);

                        // Get the first letter of kitchen name for avatar
                        $kitchenInitial = !empty($order['kitchen_fname']) ? strtoupper(substr($order['kitchen_fname'], 0, 1)) : 'K';
                        $kitchenName = $order['kitchen_fname'] . ' ' . $order['kitchen_lname'];
                ?>
                        <div class="order-box" data-order-id="<?php echo $order['order_id']; ?>">
                            <div class="kitchen-info">
                                <div class="kitchen-avatar"><?php echo $kitchenInitial; ?></div>
                                <span class="kitchen-name"><?php echo htmlspecialchars($kitchenName); ?></span>
                            </div>

                            <div class="order-header">
                                <div class="order-info">
                                    <h2 class="font-sm title-color">
                                        Order #<?php echo $order['order_id']; ?>
                                    </h2>
                                    <time datetime="<?php echo $order['order_date']; ?>" class="font-xs content-color">
                                        <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?>
                                    </time>
                                    <div class="status-badge <?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>">
                                        <?php echo $order['order_status']; ?>
                                    </div>
                                </div>
                                <div class="price-tag">₱<?php echo number_format($order['final_total_amount'], 2); ?></div>
                            </div>

                            <div class="order-details">
                                <p class="font-xs content-color">
                                    <i class='bx bx-map-pin'></i>
                                    <?php echo htmlspecialchars($order['delivery_address']); ?>
                                    <?php if ($order['city'] || $order['state']) echo ", " . htmlspecialchars($order['city'] . ' ' . $order['state']); ?>
                                </p>

                                <div class="order-items">
                                    <?php
                                    if ($orderItems && $orderItems->num_rows > 0) {
                                        $itemsShown = 0;
                                        while ($item = $orderItems->fetch_assoc()) {
                                            $itemsShown++;
                                            $imagePath = !empty($item['photo1']) ? '../../uploads/' . $item['photo1'] : 'assets/images/placeholder-food.jpg';
                                    ?>
                                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="item-thumb">
                                        <?php
                                        }

                                        $remainingItems = $order['total_items'] - $itemsShown;
                                        if ($remainingItems > 0) {
                                        ?>
                                            <div class="item-count">+<?php echo $remainingItems; ?></div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="order-actions">
                                <?php if (in_array($order['order_status'], ['Confirmed', 'Preparing', 'For Pickup', 'On the Way'])) { ?>
                                    <button class="btn-track-order" onclick="trackOrder(<?php echo $order['order_id']; ?>)">
                                        <i class='bx bx-map'></i> Track Order
                                    </button>
                                <?php } ?>

                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="no-orders-message">
                        <i class='bx bx-package'></i>
                        <h3>No Active Orders</h3>
                        <p>Your processing orders will appear here.</p>
                    </div>
                <?php
                }
                ?>
            </div>

            <!-- Completed Orders Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                <?php
                $completedOrders = getOrdersByStatus($conn, $customer_id, ['Delivered']);

                if ($completedOrders && $completedOrders->num_rows > 0) {
                    while ($order = $completedOrders->fetch_assoc()) {
                        $orderItems = getOrderItems($conn, $order['order_id'], 3);
                        $isFullyReviewed = isOrderFullyReviewed($conn, $order['order_id'], $customer_id);

                        // Get the first letter of kitchen name for avatar
                        $kitchenInitial = !empty($order['kitchen_fname']) ? strtoupper(substr($order['kitchen_fname'], 0, 1)) : 'K';
                        $kitchenName = $order['kitchen_fname'] . ' ' . $order['kitchen_lname'];
                ?>
                        <div class="order-box" data-order-id="<?php echo $order['order_id']; ?>">
                            <div class="kitchen-info">
                                <div class="kitchen-avatar"><?php echo $kitchenInitial; ?></div>
                                <span class="kitchen-name"><?php echo htmlspecialchars($kitchenName); ?></span>
                            </div>

                            <div class="order-header">
                                <div class="order-info">
                                    <h2 class="font-sm title-color">
                                        Order #<?php echo $order['order_id']; ?>
                                    </h2>
                                    <time datetime="<?php echo $order['order_date']; ?>" class="font-xs content-color">
                                        <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?>
                                    </time>
                                    <div class="status-badge delivered">Delivered</div>
                                </div>
                                <div class="price-tag">₱<?php echo number_format($order['final_total_amount'], 2); ?></div>
                            </div>

                            <div class="order-details">
                                <p class="font-xs content-color">
                                    <i class='bx bx-map-pin'></i>
                                    <?php echo htmlspecialchars($order['delivery_address']); ?>
                                    <?php if ($order['city'] || $order['state']) echo ", " . htmlspecialchars($order['city'] . ' ' . $order['state']); ?>
                                </p>

                                <div class="order-items">
                                    <?php
                                    if ($orderItems && $orderItems->num_rows > 0) {
                                        $itemsShown = 0;
                                        while ($item = $orderItems->fetch_assoc()) {
                                            $itemsShown++;
                                            $imagePath = !empty($item['photo1']) ? '../../uploads/' . $item['photo1'] : 'assets/images/placeholder-food.jpg';
                                    ?>
                                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="item-thumb">
                                        <?php
                                        }

                                        $remainingItems = $order['total_items'] - $itemsShown;
                                        if ($remainingItems > 0) {
                                        ?>
                                            <div class="item-count">+<?php echo $remainingItems; ?></div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="order-actions">
                                <a href="kitchen.php?id=<?php echo $order['kitchen_id']; ?>" class="btn-order-again">
                                    <i class='bx bx-shopping-bag'></i> Order Again
                                </a>

                                <?php if (!$isFullyReviewed) { ?>
                                    <a href="order-review.php?order=<?php echo $order['order_id']; ?>" class="btn-review">
                                        <i class='bx bx-star'></i> Review
                                    </a>
                                <?php } else { ?>
                                    <a href="order-review.php?order=<?php echo $order['order_id']; ?>&view=1" class="btn-view-review">
                                        <i class='bx bx-comment-detail'></i> View Review
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="no-orders-message">
                        <i class='bx bx-check-double'></i>
                        <h3>No Completed Orders</h3>
                        <p>Your completed orders will appear here.</p>
                    </div>
                <?php
                }
                ?>
            </div>

            <!-- Cancelled Orders Tab -->
            <div class="tab-pane fade" id="cancelled" role="tabpanel">
                <?php
                $cancelledOrders = getOrdersByStatus($conn, $customer_id, ['Cancelled']);

                if ($cancelledOrders && $cancelledOrders->num_rows > 0) {
                    while ($order = $cancelledOrders->fetch_assoc()) {
                        $orderItems = getOrderItems($conn, $order['order_id'], 3);

                        // Get the first letter of kitchen name for avatar
                        $kitchenInitial = !empty($order['kitchen_fname']) ? strtoupper(substr($order['kitchen_fname'], 0, 1)) : 'K';
                        $kitchenName = $order['kitchen_fname'] . ' ' . $order['kitchen_lname'];
                ?>
                        <div class="order-box" data-order-id="<?php echo $order['order_id']; ?>">
                            <div class="kitchen-info">
                                <div class="kitchen-avatar"><?php echo $kitchenInitial; ?></div>
                                <span class="kitchen-name"><?php echo htmlspecialchars($kitchenName); ?></span>
                            </div>

                            <div class="order-header">
                                <div class="order-info">
                                    <h2 class="font-sm title-color">
                                        Order #<?php echo $order['order_id']; ?>
                                    </h2>
                                    <time datetime="<?php echo $order['order_date']; ?>" class="font-xs content-color">
                                        <?php echo date('M d, Y - h:i A', strtotime($order['order_date'])); ?>
                                    </time>
                                    <div class="status-badge cancelled">Cancelled</div>
                                </div>
                                <div class="price-tag">₱<?php echo number_format($order['final_total_amount'], 2); ?></div>
                            </div>

                            <div class="order-details">
                                <p class="font-xs content-color">
                                    <i class='bx bx-map-pin'></i>
                                    <?php echo htmlspecialchars($order['delivery_address']); ?>
                                    <?php if ($order['city'] || $order['state']) echo ", " . htmlspecialchars($order['city'] . ' ' . $order['state']); ?>
                                </p>

                                <div class="order-items">
                                    <?php
                                    if ($orderItems && $orderItems->num_rows > 0) {
                                        $itemsShown = 0;
                                        while ($item = $orderItems->fetch_assoc()) {
                                            $itemsShown++;
                                            $imagePath = !empty($item['photo1']) ? '../../uploads/' . $item['photo1'] : 'assets/images/placeholder-food.jpg';
                                    ?>
                                            <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="item-thumb">
                                        <?php
                                        }

                                        $remainingItems = $order['total_items'] - $itemsShown;
                                        if ($remainingItems > 0) {
                                        ?>
                                            <div class="item-count">+<?php echo $remainingItems; ?></div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                            <div class="order-actions">
                                <a href="kitchen.php?id=<?php echo $order['kitchen_id']; ?>" class="btn-order-again">
                                    <i class='bx bx-shopping-bag'></i> Order Again
                                </a>

                                <button class="btn-view-details" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                    <i class='bx bx-detail'></i> Details
                                </button>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="no-orders-message">
                        <i class='bx bx-x-circle'></i>
                        <h3>No Cancelled Orders</h3>
                        <p>Your cancelled orders will appear here.</p>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </main>

    <script>
        // Track order and view details functions
        function trackOrder(orderId) {
            window.location.href = `order-tracking.php?order=${orderId}`;
        }

        function viewOrderDetails(orderId) {
            window.location.href = `order-details.php?order=${orderId}`;
        }

        // Tab switching with smooth transitions
        document.addEventListener('DOMContentLoaded', function() {
            // Get all tab buttons and content
            const tabButtons = document.querySelectorAll('.nav-link');
            const tabContents = document.querySelectorAll('.tab-pane');

            // Add click event listeners to tab buttons
            tabButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all buttons and content
                    tabButtons.forEach(btn => {
                        btn.classList.remove('active');
                        btn.setAttribute('aria-selected', 'false');
                    });

                    tabContents.forEach(content => {
                        content.classList.remove('show', 'active');
                    });

                    // Add active class to clicked button and its content
                    this.classList.add('active');
                    this.setAttribute('aria-selected', 'true');

                    // Get target content id and activate it
                    const targetId = this.getAttribute('data-bs-target').substring(1);
                    const targetContent = document.getElementById(targetId);
                    setTimeout(() => {
                        targetContent.classList.add('show', 'active');
                    }, 150);
                });
            });

            // Pull to refresh functionality
            let touchStartY = 0;
            let touchEndY = 0;
            const refreshThreshold = 100;
            const refreshIndicator = document.getElementById('refreshIndicator');

            document.addEventListener('touchstart', e => {
                touchStartY = e.touches[0].clientY;
                if (window.scrollY === 0) {
                    refreshIndicator.classList.add('visible');
                }
            });

            document.addEventListener('touchmove', e => {
                if (window.scrollY > 0) return;

                touchEndY = e.touches[0].clientY;
                const distance = touchEndY - touchStartY;

                if (distance > 0 && distance < refreshThreshold) {
                    refreshIndicator.style.transform = `translateY(${distance - 60}px)`;
                }
            });

            document.addEventListener('touchend', e => {
                if (window.scrollY > 0) return;

                const distance = touchEndY - touchStartY;

                if (distance > refreshThreshold) {
                    // Show loading state
                    refreshIndicator.innerHTML = '<i class="bx bx-loader-alt"></i> Refreshing...';

                    // Reload the page
                    setTimeout(() => {
                        window.location.reload();
                    }, 800);
                } else {
                    // Reset indicator position
                    refreshIndicator.style.transform = 'translateY(-60px)';
                    setTimeout(() => {
                        refreshIndicator.classList.remove('visible');
                    }, 300);
                }
            });

            // Add swipe functionality for tab switching
            let touchStartX = 0;
            let touchEndX = 0;

            document.addEventListener('touchstart', e => {
                touchStartX = e.touches[0].clientX;
            });

            document.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].clientX;
                handleSwipe();
            });

            function handleSwipe() {
                const SWIPE_THRESHOLD = 50;
                const tabButtons = Array.from(document.querySelectorAll('.nav-link'));
                const currentActiveIndex = tabButtons.findIndex(btn => btn.classList.contains('active'));

                if (touchEndX < touchStartX - SWIPE_THRESHOLD && currentActiveIndex < tabButtons.length - 1) {
                    // Swipe left - go to next tab
                    tabButtons[currentActiveIndex + 1].click();
                }

                if (touchEndX > touchStartX + SWIPE_THRESHOLD && currentActiveIndex > 0) {
                    // Swipe right - go to previous tab
                    tabButtons[currentActiveIndex - 1].click();
                }
            }
        });
    </script>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>