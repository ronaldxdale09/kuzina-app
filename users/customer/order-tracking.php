<?php 
include 'includes/header.php'; 
// Get the order ID from the URL
$order_id = $_GET['order'] ?? null;

if (!$order_id) {
    die('Order ID is missing.');
}

// Fetch order details, including kitchen and customer information
$order_sql = "SELECT 
                o.order_id, 
                o.total_amount, 
                o.order_status, 
                o.order_date, 
                k.kitchen_id, 
                CONCAT(k.fname, ' ', k.lname) AS kitchen_name, 
                k.photo AS kitchen_photo, 
                k.latitude AS kitchen_lat, 
                k.longitude AS kitchen_lng, 
                ca.street_address, 
                ca.city, 
                ca.state, 
                ca.zip_code, 
                ca.latitude AS customer_lat, 
                ca.longitude AS customer_lng
            FROM orders o
            JOIN kitchens k ON o.kitchen_id = k.kitchen_id
            JOIN user_addresses ca ON o.address_id = ca.address_id
            WHERE o.order_id = ?";
$stmt = $conn->prepare($order_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    die('Order not found.');
}

?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
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
    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>

    <?php include 'includes/sidebar.php'; ?>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap order-tracking-page">
        <div class="map-section" id="map"></div>

        <!-- Location Section Start -->
        <section class="location-section">
            <div class="time-box">
                <span class="content-color font-sm">Estimated Delivery Time</span>
                <h1>9.00am - 12.00pm</h1>
            </div>

            <div class="current-box">
                <div class="media">
                    <img src="<?php echo $order['rider_photo'] ?? 'assets/images/avatar/avatar.jpg'; ?>" alt="avatar" />
                    <div class="media-body">
                        <h2 class="font-sm title-color fw-600">
                            <?php echo $order['rider_name'] ?? 'No rider assigned'; ?></h2>
                        <span class="font-sm content-color">Courier</span>
                    </div>
                    <div class="action-box">
                        <a href="tel:<?php echo $order['rider_phone'] ?? '#'; ?>" class="bg-theme-theme"><i
                                class="iconly-Calling icli"></i></a>
                  
                    </div>
                </div>
            </div>

            <div class="kitchen-details">
                <div class="media">
                    <img src="<?php echo 'assets/images/avatar/avatar2.png'; ?>" alt="kitchen" class="kitchen-img">
                    <div class="media-body">
                        <h3 class="font-sm title-color fw-600"><?php echo $order['kitchen_name']; ?></h3>
                        <span class="font-sm content-color">Kitchen</span>
                    </div>
                </div>
            </div>


            <ul class="tracking-box">
                <li class="media">
                    <span class="bg-theme-theme"><i class="iconly-Home icli"></i></span>
                    <div class="media-body">
                        <h3 class="font-sm title-color fw-600">
                            <?php echo "{$order['street_address']}, {$order['city']}, {$order['state']} {$order['zip_code']}"; ?>
                        </h3>
                        <span class="font-sm content-color">Customer Location</span>
                    </div>
                </li>

                <li class="media">
                    <span class="bg-theme-theme"><i class="iconly-Location icli"></i></span>
                    <div class="media-body">
                        <h3 class="font-sm title-color fw-600"><?php echo "{$order['kitchen_name']} Kitchen"; ?></h3>
                        <span class="font-sm content-color">Store Location</span>
                    </div>
                </li>
            </ul>
            <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev" class="btn-solid">Order Details</a>
        </section>
        <!-- Location Section End -->
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const map = L.map('map').setView([<?php echo $order['kitchen_lat']; ?>,
            <?php echo $order['kitchen_lng']; ?>
        ], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Add markers for the kitchen and customer
        const kitchenMarker = L.marker([<?php echo $order['kitchen_lat']; ?>,
                <?php echo $order['kitchen_lng']; ?>
            ]).addTo(map)
            .bindPopup('<?php echo "{$order['kitchen_name']} Kitchen"; ?>').openPopup();

        const customerMarker = L.marker([<?php echo $order['customer_lat']; ?>,
                <?php echo $order['customer_lng']; ?>
            ]).addTo(map)
            .bindPopup('Customer Location');

        // Draw route between kitchen and customer
        const route = L.polyline([
            [<?php echo $order['kitchen_lat']; ?>, <?php echo $order['kitchen_lng']; ?>],
            [<?php echo $order['customer_lat']; ?>, <?php echo $order['customer_lng']; ?>]
        ], {
            color: 'blue',
            weight: 4,
            opacity: 0.7
        }).addTo(map);

        map.fitBounds(route.getBounds());
    });
    </script>



<?php include 'includes/scripts.php'; ?>
</body>
<!-- Body End -->

</html>
<!-- Html End -->