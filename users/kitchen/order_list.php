<?php 
include 'includes/header.php';

$tab = '';
if (isset($_GET['tab'])) {
    $tab = filter_var($_GET['tab']);
}




?>

<link rel="stylesheet" type="text/css" href="assets/css/order_list.css" />
<link rel="stylesheet" type="text/css" href="assets/css/modal.css" />
<link rel="stylesheet" type="text/css" href="assets/css/modal.order.css" />

<?php include 'modal/modal.order.php'; ?>

<body>
    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>
    <!-- Header End -->
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_orders.php'; ?>

    <!-- Sidebar Start -->

    <!-- Navigation Start -->
    <?php include 'includes/sidebar.php'; ?>
    <!-- Navigation End -->
    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap order-page mb-xxl">
        <div class=" orderlist-page section-b-t">

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '') ? 'active' : ''; ?>" id="confirmed-tab"
                        data-bs-toggle="tab" href="#confirmed" role="tab"
                        aria-selected="<?php echo ($tab == '') ? 'true' : 'false'; ?>">
                        Confirmed Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '1') ? 'active' : ''; ?>" id="preparing-tab"
                        data-bs-toggle="tab" href="#preparing" role="tab"
                        aria-selected="<?php echo ($tab == '1') ? 'true' : 'false'; ?>">
                        Preparing Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '2') ? 'active' : ''; ?>" id="delivery-tab"
                        data-bs-toggle="tab" href="#delivery" role="tab"
                        aria-selected="<?php echo ($tab == '2') ? 'true' : 'false'; ?>">
                        Delivery
                    </a>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content container">
                <!-- Confirmed Orders Tab -->
                <div class="tab-pane fade <?php echo ($tab == '') ? 'show active' : ''; ?>" id="confirmed"
                    role="tabpanel" aria-labelledby="confirmed-tab">
                    <?php include 'order/confirmed.order.php'; ?>
                </div>

                <!-- Preparing Orders Tab -->
                <div class="tab-pane fade <?php echo ($tab == '1') ? 'show active' : ''; ?>" id="preparing"
                    role="tabpanel" aria-labelledby="preparing-tab">
                    <?php include 'order/preparing.order.php'; ?>
                </div>

                <!-- Delivery Tab -->
                <div class="tab-pane fade <?php echo ($tab == '2') ? 'show active' : ''; ?>" id="delivery"
                    role="tabpanel" aria-labelledby="delivery-tab">
                    <?php include 'order/delivery.order.php'; ?>
                </div>
            </div>
        </div>
    </main>
    <!-- Main End -->

    <!-- Add this JavaScript at the bottom of your page -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all tab links
        const tabLinks = document.querySelectorAll('.nav-link');

        // Add click event listener to each tab
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                // Get the tab parameter from the href attribute
                const tabId = this.getAttribute('href').replace('#', '');
                let tabParam = '';

                // Set the appropriate tab parameter
                switch (tabId) {
                    case 'confirmed':
                        tabParam = '';
                        break;
                    case 'preparing':
                        tabParam = '1';
                        break;
                    case 'delivery':
                        tabParam = '2';
                        break;
                }

                // Update URL with the new tab parameter
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', tabParam);
                window.history.pushState({}, '', newUrl);

                // Show the selected tab
                const tab = new bootstrap.Tab(this);
                tab.show();
            });
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || '';

            // Find and activate the correct tab
            let tabElement;
            switch (activeTab) {
                case '':
                    tabElement = document.querySelector('#confirmed-tab');
                    break;
                case '1':
                    tabElement = document.querySelector('#preparing-tab');
                    break;
                case '2':
                    tabElement = document.querySelector('#delivery-tab');
                    break;
            }

            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        });
    });
    </script>
    <?php include 'includes/appbar.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>

</html>