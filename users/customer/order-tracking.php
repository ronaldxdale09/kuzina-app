<?php 
include 'includes/header.php'; 
// Get the order ID from the URL
$order_id = $_GET['order'] ?? null;

if (!$order_id) {
    echo displayError("No order ID provided");
    exit();
}

$order_sql = "SELECT 
    o.order_id, 
    o.order_status,
    o.order_date,
    o.total_amount,
    o.final_total_amount,
    o.discount_amount,
    
    -- Kitchen details
    k.kitchen_id, 
    k.fname AS kitchen_name,
    k.lname AS kitchen_lname,
    k.photo AS kitchen_photo,
    k.phone AS kitchen_phone,
    k.latitude AS kitchen_lat, 
    k.longitude AS kitchen_lng,
    
    -- Rider details
    dr.rider_id,
    CONCAT(dr.first_name, ' ', dr.last_name) AS rider_name,
    dr.phone AS rider_phone,
    dr.profile_photo AS rider_photo,
    
    -- Delivery details
    COALESCE(dd.pickup_address, '') as pickup_address,
    COALESCE(dd.delivery_address, '') as delivery_address,
    COALESCE(dd.delivery_status, '') as delivery_status,
    COALESCE(dd.estimated_delivery_time, 
             DATE_ADD(o.order_date, INTERVAL 30 MINUTE)) as expected_delivery_time,
    
    -- User address details
    ua.street_address, 
    ua.city, 
    ua.state, 
    ua.zip_code,
    ua.latitude AS customer_lat, 
    ua.longitude AS customer_lng,
    
    -- Review check
    (SELECT COUNT(*) FROM reviews r 
     WHERE r.kitchen_id = k.kitchen_id) as review_count
            
FROM orders o
JOIN kitchens k ON o.kitchen_id = k.kitchen_id
JOIN user_addresses ua ON o.address_id = ua.address_id
LEFT JOIN delivery_riders dr ON o.rider_id = dr.rider_id
LEFT JOIN delivery_details dd ON o.order_id = dd.order_id
WHERE o.order_id = ?";

try {
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo displayError("Order #$order_id not found.");
        exit();
    }
    
    $order = $result->fetch_assoc();
    $stmt->close();
    
    // Get order items
    $items_sql = "SELECT 
        oi.quantity,
        oi.price as unit_price,
        fl.food_name AS item_name,
        fl.description AS item_description,
        fl.photo1 AS item_photo,
        fl.category
    FROM order_items oi
    JOIN food_listings fl ON oi.food_id = fl.food_id
    WHERE oi.order_id = ?
    ORDER BY fl.category";
    
    $stmt = $conn->prepare($items_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $items_result = $stmt->get_result();
    $order_items = [];
    
    while ($item = $items_result->fetch_assoc()) {
        $order_items[] = $item;
    }
    $stmt->close();
    

    
  
    
} catch (Exception $e) {
    error_log("Error in order tracking: " . $e->getMessage());
    echo displayError("Error retrieving order details. Please try again later.");
    exit();
}

function displayError($message) {
    return "
    <div style='
        padding: 20px;
        margin: 20px;
        background-color: #fee2e2;
        border: 1px solid #ef4444;
        border-radius: 8px;
        color: #991b1b;
        text-align: center;
    '>
        <h3 style='margin: 0 0 10px 0;'>Error</h3>
        <p style='margin: 0;'>$message</p>
    </div>";
}
function getDeliveryStatusInfo($order) {
    $status = $order['order_status'];
    $current_time = time();
    
    // Calculate base delivery window
    $min_time = 10; // minimum minutes
    $max_time = 20; // maximum minutes
    
    switch ($status) {
        case 'Pending':
            return [
                'message' => 'Order Received',
                'time' => 'Waiting for kitchen to confirm'
            ];
            
        case 'Preparing':
            return [
                'message' => 'Kitchen is preparing your order',
                'time' => "$min_time-$max_time minutes"
            ];
            
        case 'For Pickup':
            return [
                'message' => 'Waiting for rider to pickup',
                'time' => 'Rider is on the way to pickup'
            ];
            
        case 'On the Way':
            // Calculate estimated arrival time based on distance
            $distance = calculateDistance(
                $order['kitchen_lat'],
                $order['kitchen_lng'],
                $order['customer_lat'],
                $order['customer_lng']
            );
            
            // Estimate 2 minutes per kilometer
            $estimated_minutes = ceil($distance * 2);
            $max_minutes = ceil($estimated_minutes * 1.5); // Add 50% buffer
            
            return [
                'message' => 'Food is on the way',
                'time' => "$estimated_minutes-$max_minutes minutes"
            ];
            
        case 'Delivered':
            return [
                'message' => 'Order Completed',
                'time' => 'Delivered'
            ];
            
        case 'Cancelled':
            return [
                'message' => 'Order Cancelled',
                'time' => 'Cancelled'
            ];
            
        default:
            return [
                'message' => 'Processing Order',
                'time' => 'Calculating time...'
            ];
    }
}


try {
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo displayError("Order #$order_id not found.");
        exit();
    }
    
    $order = $result->fetch_assoc();
    $stmt->close();
    
    // Get delivery status information
    $delivery_info = getDeliveryStatusInfo($order);
    $order['status_message'] = $delivery_info['message'];
    $order['delivery_time'] = $delivery_info['time'];
    
    // Insert or update delivery details if status is On the Way
    if ($order['order_status'] == 'On the Way') {
        $distance = calculateDistance(
            $order['kitchen_lat'],
            $order['kitchen_lng'],
            $order['customer_lat'],
            $order['customer_lng']
        );
        $estimated_minutes = ceil($distance * 2);
        
        $update_sql = "INSERT INTO delivery_details 
                      (order_id, estimated_delivery_time, delivery_status) 
                      VALUES (?, DATE_ADD(NOW(), INTERVAL ? MINUTE), 'On the Way')
                      ON DUPLICATE KEY UPDATE 
                      estimated_delivery_time = VALUES(estimated_delivery_time),
                      delivery_status = VALUES(delivery_status)";
        
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ii", $order_id, $estimated_minutes);
        $stmt->execute();
        $stmt->close();
    }
    
} catch (Exception $e) {
    error_log("Error in order tracking: " . $e->getMessage());
    echo displayError("Error retrieving order details. Please try again later.");
    exit();
}


function calculateDistance($lat1, $lon1, $lat2, $lon2) {
    $earth_radius = 6371; // km
    
    $lat1 = deg2rad(floatval($lat1));
    $lon1 = deg2rad(floatval($lon1));
    $lat2 = deg2rad(floatval($lat2));
    $lon2 = deg2rad(floatval($lon2));
    
    $delta_lat = $lat2 - $lat1;
    $delta_lon = $lon2 - $lon1;
    
    $a = sin($delta_lat/2) * sin($delta_lat/2) +
         cos($lat1) * cos($lat2) * 
         sin($delta_lon/2) * sin($delta_lon/2);
         
    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    
    return $earth_radius * $c;
}
?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" href="assets/css/order-tracking.css">

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>


<style>
.kitchen-img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
}
</style>
<!-- Body Start -->

<body>
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();"><i
                    class="iconly-Arrow-Left-Square icli"></i></a>
            <h1 class="title-color font-md">Order Tracking</h1>
        </div>
        <div class="avatar-wrap">
            <a href="homepage.php">
                <i class="iconly-Home icli"></i>
            </a>
        </div>
    </header>
    <!-- Header End -->

    <!-- Updated HTML Structure -->
    <main class="order-tracking-page">
        <div class="map-section" id="map"></div>

        <div class="tracking-content">
            <div class="status-timeline">
                <div class="status-line"></div>
                <?php
        $statuses = [
            [
                'icon' => 'bx-receipt',
                'text' => 'Order Placed',
                'active' => true,
                'color' => '#D22701'
            ],
            [
                'icon' => 'bx-dish',
                'text' => 'Preparing',
                'active' => $order['order_status'] == 'Preparing',
                'color' => '#64748b'
            ],
            [
                'icon' => 'bx-package',
                'text' => 'Ready for Pickup',
                'active' => $order['order_status'] == 'For Pickup',
                'color' => '#64748b'
            ],
            [
                'icon' => 'bx-cycling',
                'text' => 'On the Way',
                'active' => $order['order_status'] == 'On the Way',
                'color' => '#D22701'
            ],
            [
                'icon' => 'bx-check-circle',
                'text' => 'Delivered',
                'active' => $order['order_status'] == 'Delivered',
                'color' => '#64748b'
            ]
        ];
        
        foreach ($statuses as $index => $status) {
            // Determine if previous steps should be active
            $isActive = false;
            if ($order['order_status'] == 'Delivered') {
                $isActive = true;
            } elseif ($order['order_status'] == 'On the Way' && $index <= 3) {
                $isActive = true;
            } elseif ($order['order_status'] == 'For Pickup' && $index <= 2) {
                $isActive = true;
            } elseif ($order['order_status'] == 'Preparing' && $index <= 1) {
                $isActive = true;
            } elseif ($index == 0) {
                $isActive = true;
            }

            echo '<div class="status-step">
                    <div class="status-icon ' . ($isActive ? 'active' : '') . '">
                        <i class="bx ' . $status['icon'] . '"></i>
                    </div>
                    <span class="status-text ' . ($isActive ? 'active' : '') . '">' . 
                        $status['text'] . 
                    '</span>
                </div>';
        }
        ?>
            </div>

            <section class="location-section">
                <div class="delivery-status">
                    <div class="time-box <?php echo strtolower(str_replace(' ', '-', $order['order_status'])); ?>">
                        <span class="status-label"><?php echo htmlspecialchars($order['status_message']); ?></span>
                        <h1 class="time-display"><?php echo htmlspecialchars($order['delivery_time']); ?></h1>
                    </div>
                </div>

                <?php if ($order['rider_id']): ?>
                <div class="delivery-card rider-card">
                    <div class="card-header">
                        <h3>Delivery Rider</h3>
                    </div>
                    <div class="card-content">
                        <div class="profile-section">
                            <img src="../../uploads/riders/<?php echo $order['rider_photo'] ?? 'assets/images/avatar/avatar2.jpg'; ?>"
                                alt="Rider" class="profile-img">
                            <div class="profile-info">
                                <h4><?php echo $order['rider_name']; ?></h4>
                                <span>Your Delivery Partner</span>
                            </div>
                            <a href="tel:<?php echo $order['rider_phone']; ?>" class="action-button">
                                <i class='bx bx-phone'></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="delivery-card kitchen-card">
                    <div class="card-header">
                        <h3>Kitchen Details</h3>
                    </div>
                    <div class="card-content">
                        <div class="profile-section">
                            <img src="assets/images/avatar/avatar2.png" alt="Kitchen" class="profile-img">
                            <div class="profile-info">
                                <h4><?php echo $order['kitchen_name']; ?></h4>
                                <span>Preparing Your Order</span>
                            </div>
                            <a href="tel:<?php echo $order['kitchen_phone']; ?>" class="action-button">
                                <i class='bx bx-phone'></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="delivery-card address-card">
                    <div class="card-header">
                        <h3>Delivery Information</h3>
                    </div>
                    <div class="card-content">
                        <div class="address-item">
                            <i class='bx bx-home'></i>
                            <div class="address-details">
                                <h4>Delivery Address</h4>
                                <p><?php echo "{$order['street_address']}, {$order['city']}, {$order['state']} {$order['zip_code']}"; ?>
                                </p>
                            </div>
                        </div>
                        <div class="address-item">
                            <i class='bx bx-store'></i>
                            <div class="address-details">
                                <h4>Pickup Location</h4>
                                <p><?php echo "{$order['kitchen_name']} Kitchen"; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <button class="view-details-btn" onclick="showOrderDetails()">
                    <i class='bx bx-receipt'></i>
                    View Order Details
                </button>
            </section>
        </div>
        <div class="offcanvas offcanvas-bottom order-details" tabindex="-1" id="orderDetailsModal"
            aria-labelledby="orderDetailsLabel">
            <div class="offcanvas-header">
                <h5 class="title-color font-md fw-600">Order #<?php echo $order['order_id']; ?></h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close">
                    <i class='bx bx-x'></i>
                </button>
            </div>

            <div class="offcanvas-body small">
                <!-- Order Items -->
                <div class="order-items">
                    <?php
            $stmt = $conn->prepare($items_sql);
            $stmt->bind_param("i", $order_id);
            $stmt->execute();
            $items_result = $stmt->get_result();
            
            while ($item = $items_result->fetch_assoc()) {
                echo '<div class="order-item">
                        <div class="item-details">
                            <span class="quantity">' . $item['quantity'] . 'x</span>
                            <span class="item-name">' . $item['item_name'] . '</span>
                        </div>
                        <span class="item-price">₱' . number_format($item['quantity'] * $item['unit_price'], 2) . '</span>
                    </div>';
            }
            ?>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>₱<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Delivery Fee</span>
                        <span>₱<?php echo number_format($order['final_total_amount'] - $order['total_amount'], 2); ?></span>
                    </div>
                    <div class="summary-item total">
                        <span>Total</span>
                        <span class="total-amount">₱<?php echo number_format($order['final_total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="offcanvas-footer">
                <div class="btn-box">
                    <button class="btn-outline font-md" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
                    <?php if ($order['order_status'] != 'Delivered' && $order['order_status'] != 'Cancelled'): ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>


    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

    <script>
    // Global map variables
    let map, routingControl, riderMarker;

    // Map initialization
    function initializeMap(kitchenLat, kitchenLng, customerLat, customerLng, kitchenName) {
        map = L.map('map').setView([kitchenLat, kitchenLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        const icons = {
            kitchen: createIcon('bx-store', '#D22701'),
            customer: createIcon('bx-home', '#4E0707'),
            rider: createIcon('bx-cycling', '#FF670E')
        };

        // Add markers
        const kitchenMarker = L.marker([kitchenLat, kitchenLng], {
            icon: icons.kitchen
        }).addTo(map).bindPopup(kitchenName);

        const customerMarker = L.marker([customerLat, customerLng], {
            icon: icons.customer
        }).addTo(map).bindPopup('Delivery Location');

        initializeRouting(kitchenLat, kitchenLng, customerLat, customerLng);
        fitMapBounds(kitchenLat, kitchenLng, customerLat, customerLng);
    }

    function createIcon(iconClass, color) {
        return L.divIcon({
            html: `<i class="bx ${iconClass}" style="font-size: 24px; color: ${color};"></i>`,
            className: 'custom-div-icon',
            iconSize: [30, 30],
            iconAnchor: [15, 15]
        });
    }

    function initializeRouting(kitchenLat, kitchenLng, customerLat, customerLng) {
        routingControl = L.Routing.control({
            waypoints: [
                L.latLng(kitchenLat, kitchenLng),
                L.latLng(customerLat, customerLng)
            ],
            lineOptions: {
                styles: [{
                    color: '#FF670E',
                    weight: 6,
                    opacity: 0.7,
                    dashArray: '10, 10'
                }],
                addWaypoints: false
            },
            createMarker: () => null,
            show: false,
            routeWhileDragging: false
        }).addTo(map);
    }

    function fitMapBounds(kitchenLat, kitchenLng, customerLat, customerLng) {
        const bounds = L.latLngBounds(
            [kitchenLat, kitchenLng],
            [customerLat, customerLng]
        );
        map.fitBounds(bounds, {
            padding: [50, 50]
        });
    }

    // Delivery status tracking
    class DeliveryTracker {
        constructor(orderId, initialStatus, kitchenLat, kitchenLng, customerLat, customerLng) {
            this.orderId = orderId;
            this.currentStatus = initialStatus;
            this.coords = {
                kitchen: [kitchenLat, kitchenLng],
                customer: [customerLat, customerLng]
            };
            this.statusChecker = null;
            this.statusMessages = {
                'Preparing': 'Kitchen is now preparing your order',
                'For Pickup': 'Dispatch in Progress - Driver proceeding to restaurant',
                'On the Way': 'Order picked up - Out for delivery',
                'Delivered': 'Order has been delivered successfully'
            };
            this.hasShownDeliveredModal = false;
        }

        start() {
            this.checkStatus();
            this.statusChecker = setInterval(() => this.checkStatus(), 5000);
        }
        async checkStatus() {
            try {
                const response = await fetch(`api/check_delivery_status.php?order=${this.orderId}`);
                const data = await response.json();

                if (!data.success) {
                    showModal('errorModal', data.message);
                    return;
                }

                if (data.status !== this.currentStatus) {
                    this.currentStatus = data.status;
                    this.updateUI(data);
                    this.showNotification(data.status);

                    if (data.status === 'Delivered' && !this.hasShownDeliveredModal) {
                        this.stop();
                        this.hasShownDeliveredModal = true;
                        this.showDeliveredMessage();
                    }
                }

                if (data.route) {
                    this.updateRiderLocation(
                        data.route.current.lat,
                        data.route.current.lng,
                        data.route.start,
                        data.route.end
                    );

                    const lastUpdate = new Date(data.route.current.last_updated);
                    const minutesAgo = Math.floor((new Date() - lastUpdate) / 60000);

                    if (minutesAgo > 5) {
                        console.warn(`Rider location is ${minutesAgo} minutes old`);
                    }
                }

                if (data.estimated_time) {
                    this.updateEstimatedTime(data.estimated_time);
                }

            } catch (error) {
                console.error('Status check failed:', error);
                showModal('errorModal', 'Failed to check delivery status');
            }
        }
        updateUI(data) {
            this.updateStatusTimeline(data.status);
            if (data.estimated_time) this.updateEstimatedTime(data.estimated_time);

            const elements = {
                status: document.querySelector('.status-label'),
                time: document.querySelector('.time-display'),
                timeBox: document.querySelector('.time-box')
            };

            if (elements.status && data.status_message) {
                elements.status.textContent = data.status_message;
            }
            if (elements.time && data.delivery_time) {
                elements.time.textContent = data.delivery_time;
            }
            if (elements.timeBox) {
                elements.timeBox.className = `time-box ${data.status.toLowerCase().replace(' ', '-')}`;
            }
        }

        updateRiderLocation(lat, lng) {
            const newPosition = [lat, lng];

            if (!riderMarker) {
                riderMarker = L.marker(newPosition, {
                    icon: createIcon('bx-cycling', '#FF670E')
                }).addTo(map);
            } else {
                this.animateMarker(riderMarker, newPosition);
            }

            if (this.currentStatus === 'On the Way') {
                this.updateRoute(newPosition);
            }
        }

        animateMarker(marker, newPosition) {
            const oldLatLng = marker.getLatLng();
            const frames = 20;
            let i = 0;

            const animate = () => {
                if (i < frames) {
                    i++;
                    const lat = oldLatLng.lat + (newPosition[0] - oldLatLng.lat) * (i / frames);
                    const lng = oldLatLng.lng + (newPosition[1] - oldLatLng.lng) * (i / frames);
                    marker.setLatLng([lat, lng]);
                    requestAnimationFrame(animate);
                }
            };
            animate();
        }

        updateRoute(riderPosition) {
            if (!routingControl) return;
            routingControl.setWaypoints([
                L.latLng(...this.coords.kitchen),
                L.latLng(...riderPosition),
                L.latLng(...this.coords.customer)
            ]);
        }

        showNotification(status) {
            if (!this.statusMessages[status]) return;

            const notification = document.createElement('div');
            notification.id = 'statusNotification';
            notification.className = 'status-notification';
            notification.innerHTML = `
            <div class="notification-content">
                <i class='bx bx-bell'></i>
                <span>${this.statusMessages[status]}</span>
            </div>
        `;

            document.getElementById('statusNotification')?.remove();
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => notification.remove(), 500);
            }, 5000);
        }

        stop() {
            if (this.statusChecker) {
                clearInterval(this.statusChecker);
                this.statusChecker = null;
            }
        }

        updateStatusTimeline(status) {
            const statusOrder = ['Order Placed', 'Preparing', 'Ready for Pickup', 'On the Way', 'Delivered'];
            document.querySelectorAll('.status-step').forEach((step, index) => {
                const icon = step.querySelector('.status-icon');
                if (statusOrder.indexOf(status) >= index) {
                    icon.classList.add('active');
                }
            });
        }

        updateEstimatedTime(time) {
            const timeDisplay = document.querySelector('.time-box h1');
            if (timeDisplay && time) {
                timeDisplay.textContent = time;
            }
        }

        showDeliveredMessage() {
            const existingModal = document.getElementById('deliveredModal');
            if (existingModal) {
                existingModal.remove();
            }

            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.id = 'deliveredModal';
            modal.style.display = 'flex';
            modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-item-info">
                    <h2>Order Delivered!</h2>
                    <i class='bx bx-check-circle' style="color: #16a34a; font-size: 48px;"></i>
                    <p>Your order has been delivered successfully.</p>
                    <div class="modal-actions">
                        <button onclick="window.location.href='homepage.php?order_id=${this.orderId}'" class="btn-confirm">
                            Rate Your Experience
                        </button>
                    </div>
                </div>
            </div>
        `;
            document.body.appendChild(modal);
        }

    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
        // Initialize map with PHP variables
        initializeMap(
            <?php echo $order['kitchen_lat']; ?>,
            <?php echo $order['kitchen_lng']; ?>,
            <?php echo $order['customer_lat']; ?>,
            <?php echo $order['customer_lng']; ?>,
            '<?php echo addslashes($order["kitchen_name"]); ?>'
        );

        // Start delivery tracking
        const tracker = new DeliveryTracker(
            <?php echo $order_id; ?>,
            '<?php echo $order['order_status']; ?>',
            <?php echo $order['kitchen_lat']; ?>,
            <?php echo $order['kitchen_lng']; ?>,
            <?php echo $order['customer_lat']; ?>,
            <?php echo $order['customer_lng']; ?>
        );
        tracker.start();
    });

    // Order details modal functions
    function showOrderDetails() {
        const orderModal = new bootstrap.Offcanvas(document.getElementById('orderDetailsModal'));
        orderModal.show();
    }

    function hideOrderDetails() {
        const orderModal = bootstrap.Offcanvas.getInstance(document.getElementById('orderDetailsModal'));
        if (orderModal) {
            orderModal.hide();
        }
    }
    </script>



    <?php include 'includes/scripts.php'; ?>
</body>
<!-- Body End -->

</html>
<!-- Html End -->