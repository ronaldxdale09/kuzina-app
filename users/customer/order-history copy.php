<?php include 'includes/header.php'; 

if (!$customer_id) {
    header("Location: login.php");
    exit();
}


// Modified order history query to include kitchen_id and review information
function getOrdersByStatus($conn, $customer_id, $statuses) {
  $statusPlaceholders = str_repeat('?,', count($statuses) - 1) . '?';
  $query = "SELECT 
      o.order_id,
      o.order_status,
      o.order_date,
      o.total_amount,
      o.final_total_amount,
      COUNT(oi.order_item_id ) as total_items,
      COALESCE(ua.street_address, 'No address specified') as delivery_address,
      COALESCE(ua.city, '') as city,
      COALESCE(ua.state, '') as state,
      k.kitchen_id,
      k.fname AS kitchen_name,
      k.lname AS kitchen_lname,
      r.rating,
      r.comment
  FROM orders o
  LEFT JOIN order_items oi ON o.order_id = oi.order_id
  LEFT JOIN user_addresses ua ON o.address_id = ua.address_id
  LEFT JOIN kitchens k ON o.kitchen_id = k.kitchen_id
  LEFT JOIN reviews r ON k.kitchen_id = r.kitchen_id AND r.customer_id = o.customer_id
  WHERE o.customer_id = ? 
  AND o.order_status IN ($statusPlaceholders)
  GROUP BY o.order_id
  ORDER BY o.order_date DESC";

  $stmt = $conn->prepare($query);
  
  $params = array_merge([$customer_id], $statuses);
  $types = str_repeat('s', count($params));
  $stmt->bind_param($types, ...$params);
  
  $stmt->execute();
  return $stmt->get_result();
}
?>
<!-- Head End -->

<!-- Body Start -->
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<link rel="stylesheet" href="assets/css/order-history.css">

<body>
    <!-- Skeleton loader Start -->

    <!-- Skeleton loader End -->

    <!-- Header Start -->
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
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap order-history mb-xxl">
        <!-- Categories Tabs Start -->
        <ul class="nav nav-tab nav-pills custom-scroll-hidden" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="processing-tab" data-bs-toggle="pill" data-bs-target="#processing"
                    type="button" role="tab" aria-controls="processing" aria-selected="true">
                    <i class='bx bx-loader-circle'></i>
                    <span>Processing</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed"
                    type="button" role="tab" aria-controls="completed" aria-selected="false">
                    <i class='bx bx-check-circle'></i>
                    <span>Completed</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cancelled-tab" data-bs-toggle="pill" data-bs-target="#cancelled"
                    type="button" role="tab" aria-controls="cancelled" aria-selected="false">
                    <i class='bx bx-x-circle'></i>
                    <span>Cancelled</span>
                </button>
            </li>
        </ul>

        <!-- Tab Content Start -->
        <section class="tab-content ratio2_1" id="pills-tabContent">
            <!-- Processing Orders Tab -->
            <div class="tab-pane fade show active" id="processing" role="tabpanel" aria-labelledby="processing-tab">
                <?php
            $processingStatuses = ['Pending', 'Preparing', 'For Pickup', 'On the Way'];
            $processingOrders = getOrdersByStatus($conn, $customer_id, $processingStatuses);
            
            if ($processingOrders->num_rows > 0) {
                while ($order = $processingOrders->fetch_assoc()) {
                    ?>
                <div class="order-box">
                    <div class="order-header">
                        <div class="order-info">
                            <h2 class="font-sm title-color">
                                ID: #<?php echo $order['order_id']; ?>,
                                Dt: <?php echo date('d M, Y', strtotime($order['order_date'])); ?>
                            </h2>
                            <div
                                class="status-badge <?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>">
                                <?php echo $order['order_status']; ?>
                            </div>
                        </div>
                        <div class="price-tag">₱<?php echo number_format($order['final_total_amount'], 2); ?></div>
                    </div>

                    <div class="order-details">
                        <p class="font-xs content-color">
                            <?php echo $order['delivery_address']; ?>
                            <?php if ($order['city'] || $order['state']) echo ", {$order['city']} {$order['state']}"; ?>
                        </p>
                        <span class="content-color font-xs">
                            Items: <span class="font-theme"><?php echo $order['total_items']; ?></span>
                        </span>
                    </div>

                    <div class="order-actions">
                        <?php if ($order['order_status'] != 'Cancelled' && $order['order_status'] != 'Completed'): ?>
                        <button class="btn-track-order" onclick="trackOrder(<?php echo $order['order_id']; ?>)">
                            <i class='bx bx-map'></i>
                            Track Order
                        </button>
                        <?php endif; ?>

                        <button class="btn-view-details" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                            <i class='bx bx-detail'></i>
                            View Details
                        </button>
                    </div>
                </div>
                <?php
                }
            } else {
                echo '<div class="no-orders-message">No processing orders found.</div>';
            }
            ?>
            </div>

            <!-- Completed Orders Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <?php
            $completedOrders = getOrdersByStatus($conn, $customer_id, ['Delivered']);
            
            if ($completedOrders->num_rows > 0) {
                while ($order = $completedOrders->fetch_assoc()) {
                    ?>
                <div class="order-box">
                    <div class="media">
                        <a href="order-tracking.php?id=<?php echo $order['order_id']; ?>" class="content-box">
                            <h2 class="font-sm title-color">
                                ID: #<?php echo $order['order_id']; ?>,
                                Dt: <?php echo date('d M, Y', strtotime($order['order_date'])); ?>
                            </h2>
                            <p class="font-xs content-color">
                                <?php echo $order['delivery_address']; ?>
                                <?php if ($order['city'] || $order['state']) echo ", {$order['city']} {$order['state']}"; ?>
                            </p>
                            <span class="content-color font-xs">
                                Paid: <span
                                    class="font-theme">₱<?php echo number_format($order['final_total_amount'], 2); ?></span>,
                                Items: <span class="font-theme"><?php echo $order['total_items']; ?></span>
                            </span>
                        </a>
                    </div>
                    <div class="bottom-content">
                        <a href="kitchen.php?id=<?php echo $order['kitchen_id']; ?>" class="title-color font-sm fw-600">
                            Order Again
                        </a>
                        <?php if ($order['rating'] == 0): ?>
                        <button class="give-rating content-color font-sm"
                            onclick="showRatingModal(<?php echo $order['order_id']; ?>)">
                            Rate & Review Order
                        </button>
                        <?php else: ?>
                        <div class="rating">
                            <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $order['rating']) {
                                            echo '<i class="feather-star filled"></i>';
                                        } else {
                                            echo '<i class="feather-star"></i>';
                                        }
                                    }
                                    ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php
                }
            } else {
                echo '<div class="no-orders-message">No completed orders found.</div>';
            }
            ?>
            </div>

            <!-- Cancelled Orders Tab -->
            <div class="tab-pane fade" id="cancelled" role="tabpanel" aria-labelledby="cancelled-tab">
                <?php
            $cancelledOrders = getOrdersByStatus($conn, $customer_id, ['Cancelled']);
            
            if ($cancelledOrders->num_rows > 0) {
                while ($order = $cancelledOrders->fetch_assoc()) {
                    ?>
                <div class="order-box cancelled">
                    <div class="media">
                        <a href="order-tracking.php?id=<?php echo $order['order_id']; ?>" class="content-box">
                            <h2 class="font-sm title-color">
                                ID: #<?php echo $order['order_id']; ?>,
                                Dt: <?php echo date('d M, Y', strtotime($order['order_date'])); ?>
                            </h2>
                            <p class="font-xs content-color">
                                <?php echo $order['delivery_address']; ?>
                                <?php if ($order['city'] || $order['state']) echo ", {$order['city']} {$order['state']}"; ?>
                            </p>
                            <span class="content-color font-xs">
                                Amount: <span
                                    class="font-theme">₱<?php echo number_format($order['final_total_amount'], 2); ?></span>,
                                Items: <span class="font-theme"><?php echo $order['total_items']; ?></span>
                            </span>
                        </a>
                    </div>
                </div>
                <?php
                }
            } else {
                echo '<div class="no-orders-message">No cancelled orders found.</div>';
            }
            ?>
            </div>
        </section>
    </main>

    <!-- Rating Modal -->
    <div class="modal" id="ratingModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-item-info">
                <h2>Rate Your Order</h2>
                <div class="rating-input">
                    <div class="stars">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <i class="feather-star" data-rating="<?php echo $i; ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <textarea id="review-text" placeholder="Write your review (optional)"></textarea>
                </div>
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="closeRatingModal()">Cancel</button>
                    <button class="btn-confirm" onclick="submitRating()">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main End -->
    <script>
    // Track order and view details functions
    function trackOrder(orderId) {
        // Redirect to tracking page
        window.location.href = `order-tracking.php?order=${orderId}`;
    }

    function viewOrderDetails(orderId) {
        window.location.href = `order-tracking.php?order=${orderId}`;
    }

    // Tab switching functionality
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
                const targetId = this.getAttribute('data-bs-target').slice(1);
                const targetContent = document.getElementById(targetId);
                targetContent.classList.add('show', 'active');
            });
        });
    });

    // Add smooth loading transition
    function showLoadingState() {
        const orderBoxes = document.querySelectorAll('.order-box');
        orderBoxes.forEach(box => box.classList.add('loading'));
    }

    function hideLoadingState() {
        const orderBoxes = document.querySelectorAll('.order-box');
        orderBoxes.forEach(box => box.classList.remove('loading'));
    }

    // Enhanced tab switching with loading state
    function switchTab(tabId) {
        showLoadingState();

        setTimeout(() => {
            const targetButton = document.querySelector(`[data-bs-target="#${tabId}"]`);
            if (targetButton) {
                targetButton.click();
            }
            hideLoadingState();
        }, 300);
    }

    // Add swipe functionality for mobile
    let touchStartX = 0;
    let touchEndX = 0;

    document.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });

    document.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
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

    // Add scroll to top when switching tabs
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // Handle order card clicks
    document.addEventListener('click', function(e) {
        const orderBox = e.target.closest('.order-box');
        if (orderBox && !e.target.closest('.order-actions')) {
            const orderId = orderBox.dataset.orderId;
            viewOrderDetails(orderId);
        }
    });

    // Add error handling for tracking and viewing details
    function handleError(error) {
        console.error('Error:', error);
        showErrorModal('An error occurred. Please try again later.');
    }

    function showErrorModal(message) {
        // Create and show error modal
        const modalHTML = `
        <div class="modal show" id="errorModal">
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <div class="modal-item-info">
                    <h2>Error</h2>
                    <p>${message}</p>
                    <div class="modal-actions">
                        <button class="btn-confirm" onclick="closeErrorModal()">OK</button>
                    </div>
                </div>
            </div>
        </div>
    `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        document.getElementById('errorModal').classList.add('show');
    }

    function closeErrorModal() {
        const modal = document.getElementById('errorModal');
        if (modal) {
            modal.remove();
        }
    }

    // Update real-time order status (if you have WebSocket or want to poll)
    function initializeRealTimeUpdates() {
        setInterval(() => {
            const processingOrders = document.querySelectorAll('#processing .order-box');
            processingOrders.forEach(order => {
                const orderId = order.dataset.orderId;
                checkOrderStatus(orderId);
            });
        }, 30000); // Check every 30 seconds
    }

    function checkOrderStatus(orderId) {
        fetch(`check-order-status.php?order_id=${orderId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status) {
                    updateOrderStatus(orderId, data.status);
                }
            })
            .catch(error => console.error('Error checking order status:', error));
    }

    function updateOrderStatus(orderId, newStatus) {
        const orderBox = document.querySelector(`.order-box[data-order-id="${orderId}"]`);
        if (orderBox) {
            const statusBadge = orderBox.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = `status-badge ${newStatus.toLowerCase().replace(' ', '-')}`;
                statusBadge.textContent = newStatus;

                // Update tracking button visibility
                const trackButton = orderBox.querySelector('.btn-track-order');
                if (newStatus === 'Completed' || newStatus === 'Cancelled') {
                    trackButton?.remove();
                }
            }
        }
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initializeRealTimeUpdates();
    });
    </script>

    <?php include 'includes/scripts.php'; ?>
</body>
<!-- Body End -->

</html>
<!-- Html End -->