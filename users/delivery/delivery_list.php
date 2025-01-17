<?php 
include 'includes/header.php';

// Check if rider is logged in
$rider_id = $_COOKIE['rider_id'] ?? null;
if (!$rider_id) {
    header("Location: ../../rider.php");
    exit();
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/delivery_list.css" />
<link rel="stylesheet" type="text/css" href="assets/css/modal.css" />

<body>
    <?php include 'navbar/main.navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-wrap index-page mb-xxl">
        <!-- Delivery List Header -->
        <div class="orders-header">
            <h1>My Deliveries</h1>
            <div class="filter-section">
                <select id="order-filter" class="custom-select">
                    <option value="all">All Deliveries</option>
                    <option value="active" selected>Active Deliveries</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
        </div>

        <!-- Deliveries List -->
        <div class="orders-container">
            <?php
            // Query for rider's deliveries
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
                COALESCE(ua.state, '') AS delivery_state,
                c.first_name AS customer_fname,
                c.last_name AS customer_lname,
                c.phone AS customer_phone
            FROM orders o
            JOIN kitchens k ON o.kitchen_id = k.kitchen_id
            JOIN customers c ON o.customer_id = c.customer_id
            LEFT JOIN user_addresses ua ON o.address_id = ua.address_id
            WHERE o.rider_id = ?
            AND o.order_status IN ('For Pickup', 'On the Way', 'Delivered')
            ORDER BY 
                CASE 
                    WHEN o.order_status = 'For Pickup' THEN 1
                    WHEN o.order_status = 'On the Way' THEN 2
                    WHEN o.order_status = 'Delivered' THEN 3
                END,
                o.order_date DESC";

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $rider_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($order = $result->fetch_assoc()) {
                    ?>
            <div class="order-card <?php echo strtolower($order['order_status']); ?>"
                data-order-id="<?php echo $order['order_id']; ?>"
                data-status="<?php echo strtolower($order['order_status']); ?>">
                <div class="order-header">
                    <div class="order-info">
                        <div class="order-id">Order #<?php echo $order['order_id']; ?></div>
                        <div class="status-badge <?php echo strtolower($order['order_status']); ?>">
                            <?php echo $order['order_status']; ?>
                        </div>
                    </div>
                    <div class="order-amount">₱<?php echo number_format($order['final_total_amount'], 2); ?></div>
                </div>

                <div class="order-details">
                    <!-- Customer Details -->
                    <div class="location-info customer">
                        <i class='bx bx-user'></i>
                        <div class="location-text">
                            <h3><?php echo $order['customer_fname'] . ' ' . $order['customer_lname']; ?></h3>
                            <p>
                                <a href="tel:<?php echo $order['customer_phone']; ?>">
                                    <i class='bx bx-phone'></i> <?php echo $order['customer_phone']; ?>
                                </a>
                            </p>
                        </div>
                    </div>

                    <!-- Pickup Details -->
                    <div class="location-info pickup">
                        <i class='bx bx-store'></i>
                        <div class="location-text">
                            <h3><?php echo $order['kitchen_name'] . ' ' . $order['kitchen_lname']; ?></h3>
                            <p><?php echo $order['pickup_address']; ?></p>
                        </div>
                    </div>

                    <!-- Delivery Details -->
                    <div class="location-info delivery">
                        <i class='bx bx-map'></i>
                        <div class="location-text">
                            <h3>Delivery Location</h3>
                            <p><?php 
                                        echo $order['delivery_address'];
                                        if ($order['delivery_city'] || $order['delivery_state']) {
                                            echo ', ' . $order['delivery_city'] . ' ' . $order['delivery_state'];
                                        }
                                    ?></p>
                        </div>
                    </div>
                </div>

                <div class="order-actions">
                    <?php if ($order['order_status'] == 'For Pickup'): ?>
                    <button class="btn-primary" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'On the Way')">
                        <i class='bx bx-package'></i>
                        Picked Up
                    </button>
                    <?php elseif ($order['order_status'] == 'On the Way'): ?>
                    <button class="btn-primary" onclick="updateStatus(<?php echo $order['order_id']; ?>, 'Delivered')">
                        <i class='bx bx-check-circle'></i>
                        Complete Delivery
                    </button>
                    <?php endif; ?>

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
                <i class='bx bx-cycling'></i>
                <h2>No Deliveries Yet</h2>
                <p>Accept orders to start delivering</p>
                <a href="order_list.php" class="btn-primary">
                    <i class='bx bx-package'></i>
                    Find Orders
                </a>
            </div>

            <?php
            }
            ?>
        </div>
        <!-- Add this right after the orders-container div -->
        <div id="no-active-orders" class="no-orders" style="display: none;">
            <i class='bx bx-cycling'></i>
            <h2>No Active Deliveries</h2>
            <p>All current deliveries have been completed</p>
            <a href="order_list.php" class="btn-primary">
            <i class='bx bx-filter'></i>

                Find New Orders
            </a>
        </div>
    </main>


    <!-- Status Update Confirmation Modal -->
    <div class="modal" id="confirmationModal">
        <div class="modal-content">
            <span class="close-modal" id="closeConfirmModal">&times;</span>
            <div class="modal-item-info">
                <h2>Confirm Status Update</h2>
                <p id="confirmationMessage"></p>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" id="cancelStatusUpdate">Cancel</button>
                <button class="btn-confirm" id="confirmStatusUpdate">Confirm</button>
            </div>
        </div>
    </div>
    <!-- Status Update Confirmation Modal -->
    <div class="modal" id="confirmationModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-item-info">
                <h2>Confirm Status Update</h2>
                <p id="confirmationMessage"></p>
                <div class="modal-actions">
                    <button class="btn-cancel" id="cancelStatusUpdate">Cancel</button>
                    <button class="btn-confirm" id="confirmStatusUpdate">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Notification Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-item-info">
                <h2>Success!</h2>
                <p id="successMessage"></p>
                <div class="modal-actions">
                    <button class="btn-confirm" id="successModalConfirm">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Notification Modal -->
    <div class="modal" id="errorModal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-item-info">
                <h2>Error</h2>
                <p id="errorMessage"></p>
                <div class="modal-actions">
                    <button class="btn-confirm" id="errorModalConfirm">OK</button>
                </div>
            </div>
        </div>
    </div>


    <?php include 'includes/appbar.php'; ?>

    <script>
    // Modal handling functions
    function showModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('show');
    }

    function hideModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('show');
        }
    }

    // Add event listeners for close buttons
    document.querySelectorAll('.close-modal').forEach(button => {
        button.onclick = function() {
            this.closest('.modal').classList.remove('show');
        };
    });

    // Variables to store pending status update
    let pendingOrderId = null;
    let pendingStatus = null;

    function updateStatus(orderId, newStatus) {
        pendingOrderId = orderId;
        pendingStatus = newStatus;

        // Show confirmation modal
        document.getElementById('confirmationMessage').textContent =
            `Are you sure you want to mark this order as ${newStatus}?`;
        showModal('confirmationModal');
    }

    // Confirmation modal buttons
    document.getElementById('confirmStatusUpdate').onclick = function() {
        hideModal('confirmationModal');

        if (pendingOrderId && pendingStatus) {
            fetch('functions/update_delivery.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: pendingOrderId,
                        status: pendingStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('successMessage').textContent =
                            `Order status has been updated to ${pendingStatus}`;
                        showModal('successModal');
                    } else {
                        document.getElementById('errorMessage').textContent =
                            data.message || 'Failed to update order status';
                        showModal('errorModal');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('errorMessage').textContent =
                        'An error occurred while updating the status';
                    showModal('errorModal');
                });
        }
    }

    document.getElementById('cancelStatusUpdate').onclick = function() {
        hideModal('confirmationModal');
        pendingOrderId = null;
        pendingStatus = null;
    }

    document.getElementById('successModalConfirm').onclick = function() {
        hideModal('successModal');
        location.reload();
    }

    document.getElementById('errorModalConfirm').onclick = function() {
        hideModal('errorModal');
    }

    // Original view details function
    function viewOrderDetails(orderId) {
        window.location.href = `delivery_details.php?order_id=${orderId}`;
    }

    // Function to filter orders
    function filterOrders(filterValue) {
        const cards = document.querySelectorAll('.order-card');
        const noActiveOrders = document.getElementById('no-active-orders');
        let visibleCards = 0;

        cards.forEach(card => {
            const status = card.dataset.status;

            if (filterValue === 'all') {
                card.style.display = 'block';
                visibleCards++;
            } else if (filterValue === 'active' && (status === 'for pickup' || status === 'on the way')) {
                card.style.display = 'block';
                visibleCards++;
            } else if (filterValue === 'completed' && status === 'delivered') {
                card.style.display = 'block';
                visibleCards++;
            } else {
                card.style.display = 'none';
            }
        });

        // Show "no active orders" message if no cards are visible for active filter
        if (filterValue === 'active' && visibleCards === 0) {
            noActiveOrders.style.display = 'block';
        } else {
            noActiveOrders.style.display = 'none';
        }
    }

    // Apply filter on page load
    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('order-filter');
        filterOrders(select.value);
    });

    // Apply filter when selection changes
    document.getElementById('order-filter').addEventListener('change', function() {
        filterOrders(this.value);
    });
    </script>
</body>

</html>