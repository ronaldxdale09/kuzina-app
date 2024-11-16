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

// Updated SQL query to include coordinates
$sql = "SELECT o.*, 
               CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
               c.phone AS customer_phone,
               CONCAT(k.fname, ' ', k.lname) AS kitchen_name,
               k.phone AS kitchen_phone,
               k.address AS kitchen_address,
               ua.street_address, 
               ua.city, 
               ua.zip_code,
               ua.latitude AS customer_lat,
               ua.longitude AS customer_lng,
               p.payment_method, 
               p.payment_status
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        JOIN kitchens k ON o.kitchen_id = k.kitchen_id
        JOIN user_addresses ua ON o.address_id = ua.address_id
        LEFT JOIN payments p ON o.payment_id = p.payment_id
        WHERE o.order_id = ? 
        AND (o.rider_id = ? OR o.rider_id IS NULL)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $rider_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Validate coordinates
if (!$order || 
    !is_numeric($order['customer_lat']) || 
    !is_numeric($order['customer_lng'])) {
    
    // Log error for debugging
    error_log("Invalid or missing coordinates for order ID: $order_id");
    
    // Set default coordinates for Zamboanga City as fallback
    $default_lat = 6.9214;  // Default latitude for Zamboanga City
    $default_lng = 122.0790; // Default longitude for Zamboanga City
    
    if (!is_numeric($order['customer_lat'])) $order['customer_lat'] = $default_lat;
    if (!is_numeric($order['customer_lng'])) $order['customer_lng'] = $default_lng;
}

// Fetch order items
$items_sql = "SELECT oi.*, f.food_name, f.photo1, f.price 
              FROM order_items oi
              JOIN food_listings f ON oi.food_id = f.food_id
              WHERE oi.order_id = ?";

$items_stmt = $conn->prepare($items_sql);
$items_stmt->bind_param("i", $order_id);
$items_stmt->execute();
$items_result = $items_stmt->get_result();

// Add JavaScript to make coordinates available to the map
?>

<link rel="stylesheet" type="text/css" href="assets/css/delivery.details.css" />
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Leaflet Routing Machine CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

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

            <!-- First, update your HTML button to use a class instead of inline onclick -->
            <div class="info-section">
                <div class="info-card">

                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-map-alt'></i>
                        </div>
                        <h2>Delivery Route</h2>
                    </div>
                    <div class="card-content">
                        <button class="view-map-btn show-map-btn">
                            <i class='bx bx-map'></i>
                            View Delivery Route
                        </button>
                    </div>
                </div>
            </div>
            <!-- Map container -->
            <div id="map-container">
                <div id="delivery-map"></div>
                <div class="map-controls">
                    <button class="map-button get-location-btn">
                        <i class='bx bx-current-location'></i>
                        My Location
                    </button>
                    <button class="map-button close-map hide-map-btn">
                        <i class='bx bx-x'></i>
                        Close
                    </button>
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


    <!-- Modal Template -->
    <div id="modalContainer" class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"></h3>
                <button onclick="closeModal()" class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p id="modalMessage"></p>
            </div>
            <div class="modal-footer">
                <button onclick="closeModal()" class="cancel-btn">Cancel</button>
                <button onclick="confirmModal()" class="confirm-btn">Confirm</button>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Leaflet Routing Machine JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-routing-machine/3.2.12/leaflet-routing-machine.min.js">
    </script>
    <script>
    // Config object for app settings
    const CONFIG = {
        updateInterval: 30000,
        locationOptions: {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        },
        mapDefaults: {
            zoom: 15,
            maxZoom: 19
        }
    };

    // Marker icons configuration
    const MARKER_ICONS = {
        user: L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: #2196F3; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center;"><i class='bx bx-cycling' style='color: white; font-size: 14px;'></i></div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        }),
        delivery: L.divIcon({
            className: 'custom-div-icon',
            html: `<div style="background-color: #ff5722; width: 24px; height: 24px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.2); display: flex; align-items: center; justify-content: center;"><i class='bx bx-map-pin' style='color: white; font-size: 14px;'></i></div>`,
            iconSize: [24, 24],
            iconAnchor: [12, 12]
        })
    };

    // Modal functions
    function showModal(title, message, onConfirm = null) {
        const modal = document.getElementById('modalContainer');
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        modal.style.display = 'flex';

        window.confirmModal = () => {
            if (onConfirm) onConfirm();
            closeModal();
        };
    }

    function closeModal() {
        document.getElementById('modalContainer').style.display = 'none';
    }

    // Core tracking functions
    function initMap(container, deliveryCoords) {
        const map = L.map(container, {
            zoomControl: false,
            attributionControl: false
        }).setView([deliveryCoords.lat, deliveryCoords.lng], CONFIG.mapDefaults.zoom);

        L.control.zoom({
            position: 'bottomright'
        }).addTo(map);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: CONFIG.mapDefaults.maxZoom
        }).addTo(map);

        return map;
    }

    function getCurrentLocation() {
        return new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(
                position => resolve(position.coords),
                error => reject(error),
                CONFIG.locationOptions
            );
        });
    }

    function updateRiderLocation(orderId, coords) {
        return fetch('api/update_rider_location.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_id: orderId,
                current_lat: coords.latitude,
                current_lng: coords.longitude
            })
        }).catch(error => showModal('Error', 'Failed to update location: ' + error.message));
    }

    function updateOrderStatus(orderId, newStatus, coords) {
        return fetch('functions/update_order_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_id: orderId,
                status: newStatus,
                current_lat: coords.latitude,
                current_lng: coords.longitude
            })
        }).then(response => response.json());
    }

    function initDeliveryTracking(deliveryCoords, orderId) {
        let map = null;
        let userMarker = null;
        let deliveryMarker = null;
        let routingControl = null;
        let updateInterval = null;

        function showMap() {
            const mapContainer = document.getElementById('map-container');
            if (!mapContainer) return;

            mapContainer.style.display = 'block';

            if (!map) {
                map = initMap('delivery-map', deliveryCoords);
                addDeliveryMarker();

                // Add CSS fixes for controls
                const mapControls = document.querySelector('.map-controls');
                if (mapControls) {
                    mapControls.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
            z-index: 9999;
            background: white;
            padding: 8px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        `;
                }

                const allMapButtons = document.querySelectorAll('.map-button');
    allMapButtons.forEach(button => {
        button.style.cssText = `
            background: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            color: #333;
            z-index: 9999;
            min-width: 120px;
            justify-content: center;
            transition: all 0.2s ease;
            &:active {
                transform: scale(0.98);
            }
        `;
    });

                const closeButton = document.querySelector('.close-map');
                if (closeButton) {
                    closeButton.style.background = '#8B0000';
                    closeButton.style.color = 'white';
                }
            }

            map.invalidateSize();
        }

        function hideMap() {
            const mapContainer = document.getElementById('map-container');
            if (mapContainer) mapContainer.style.display = 'none';
            clearInterval(updateInterval);
        }

        function addDeliveryMarker() {
            deliveryMarker = L.marker([deliveryCoords.lat, deliveryCoords.lng], {
                icon: MARKER_ICONS.delivery
            }).addTo(map);

            deliveryMarker.bindPopup(`
            <div style="padding: 8px; font-size: 14px; line-height: 1.4;">
                <strong style="display: block; margin-bottom: 5px;">${deliveryCoords.name}</strong>
                ${deliveryCoords.address}<br>
                <a href="tel:${deliveryCoords.phone}" style="color: #2196F3; text-decoration: none; display: flex; align-items: center; gap: 5px; margin-top: 5px;">
                    <i class='bx bx-phone'></i> ${deliveryCoords.phone}
                </a>
            </div>
        `).openPopup();
        }

        function updateRoute(userLat, userLng) {
            if (routingControl) map.removeControl(routingControl);

            routingControl = L.Routing.control({
                waypoints: [
                    L.latLng(userLat, userLng),
                    L.latLng(deliveryCoords.lat, deliveryCoords.lng)
                ],
                routeWhileDragging: false,
                showAlternatives: false,
                fitSelectedRoutes: false,
                show: false,
                lineOptions: {
                    styles: [{
                        color: '#2196F3',
                        opacity: 0.8,
                        weight: 4
                    }]
                },
                createMarker: () => null
            }).addTo(map);

            routingControl.on('routesfound', handleRouteFound);
        }

        function handleRouteFound(e) {
            const summary = e.routes[0].summary;
            const distance = (summary.totalDistance / 1000).toFixed(1);
            const minutes = Math.round(summary.totalTime / 60);

            if (userMarker) {
                userMarker.bindPopup(`
                <div style="padding: 8px; font-size: 14px; line-height: 1.4;">
                    <strong>Your Location</strong><br>
                    Distance: ${distance} km<br>
                    Est. Time: ${minutes} min
                </div>
            `).openPopup();
            }

            document.querySelectorAll('.leaflet-routing-container')
                .forEach(container => container.style.display = 'none');
        }

        async function startLocationUpdates() {
            try {
                const coords = await getCurrentLocation();
                updateUserMarker(coords);
                updateRoute(coords.latitude, coords.longitude);
                fitMapBounds(coords);

                if (updateInterval) clearInterval(updateInterval);
                updateInterval = setInterval(async () => {
                    const newCoords = await getCurrentLocation();
                    updateUserMarker(newCoords);
                    updateRoute(newCoords.latitude, newCoords.longitude);
                    updateRiderLocation(orderId, newCoords);
                }, CONFIG.updateInterval);
            } catch (error) {
                showModal('Error', 'Unable to get your location. Please check your GPS settings.');
            }
        }

        function updateUserMarker(coords) {
            if (userMarker) {
                userMarker.setLatLng([coords.latitude, coords.longitude]);
            } else {
                userMarker = L.marker([coords.latitude, coords.longitude], {
                    icon: MARKER_ICONS.user
                }).addTo(map);
            }
        }

        function fitMapBounds(coords) {
            const bounds = L.latLngBounds([
                [coords.latitude, coords.longitude],
                [deliveryCoords.lat, deliveryCoords.lng]
            ]);
            map.fitBounds(bounds, {
                padding: [50, 50],
                maxZoom: 16
            });
        }

        // Event listeners
        document.querySelector('.show-map-btn')?.addEventListener('click', showMap);
        document.querySelector('.hide-map-btn')?.addEventListener('click', hideMap);
        document.querySelector('.get-location-btn')?.addEventListener('click', startLocationUpdates);
    }

    // Start the application
    document.addEventListener('DOMContentLoaded', function() {
        const deliveryCoords = {
            lat: <?= $order['customer_lat'] ?? 'null' ?>,
            lng: <?= $order['customer_lng'] ?? 'null' ?>,
            address: '<?= htmlspecialchars($order['street_address'] ?? '') ?>',
            name: '<?= htmlspecialchars($order['customer_name'] ?? '') ?>',
            phone: '<?= htmlspecialchars($order['customer_phone'] ?? '') ?>'
        };

        if (!deliveryCoords.lat || !deliveryCoords.lng) {
            console.error('Invalid delivery coordinates');
            return;
        }

        initDeliveryTracking(deliveryCoords, <?= $order_id ?>);

        // Global functions
        window.updateOrderStatus = function(orderId, newStatus) {
            if (!orderId || !newStatus) return;

            navigator.geolocation.getCurrentPosition(
                position => {
                    showModal(
                        'Confirm Status Update',
                        `Are you sure you want to update the order status to ${newStatus}?`,
                        async () => {
                            try {
                                const response = await fetch(
                                    'functions/update_order_status.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            order_id: orderId,
                                            status: newStatus,
                                            current_lat: position.coords.latitude,
                                            current_lng: position.coords.longitude
                                        })
                                    });

                                const result = await response.json();
                                if (result.success) {
                                    showModal('Success',
                                        `Order status updated to ${newStatus}`);
                                    setTimeout(() => window.location.reload(), 1500);
                                } else {
                                    showModal('Error', 'Failed to update order status: ' +
                                        result.message);
                                }
                            } catch (error) {
                                showModal('Error',
                                    'An error occurred while updating the order');
                            }
                        }
                    );
                },
                error => showModal('Error',
                    'Unable to get your location. Please check your GPS settings.'), {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        };
    });
    </script>

    <?php include 'includes/scripts.php'; ?>
</body>

</html>