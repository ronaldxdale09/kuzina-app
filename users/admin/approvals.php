<?php 
include 'includes/header.php';

$tab = '';
if (isset($_GET['tab'])) {
    $tab = filter_var($_GET['tab']);
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/approval.css" />
<link rel="stylesheet" type="text/css" href="assets/css/modal.css" />
<link rel="stylesheet" type="text/css" href="assets/css/modal.approval.css" />


<body>
    <?php include 'navbar/main.navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-wrap approval-page mb-xxl">
        <div class="approval-list-page section-b-t">
            <ul class="nav nav-tabs" id="approvalTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '') ? 'active' : ''; ?>" id="kitchen-tab"
                        data-bs-toggle="tab" href="#kitchen" role="tab"
                        aria-selected="<?php echo ($tab == '') ? 'true' : 'false'; ?>">
                        <i class='bx bx-store'></i>
                        <span>Kitchen Applications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '1') ? 'active' : ''; ?>" id="rider-tab" data-bs-toggle="tab"
                        href="#rider" role="tab" aria-selected="<?php echo ($tab == '1') ? 'true' : 'false'; ?>">
                        <i class='bx bx-cycling'></i>
                        <span>Rider Applications</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '2') ? 'active' : ''; ?>" id="foodlist-tab"
                        data-bs-toggle="tab" href="#foodlist" role="tab"
                        aria-selected="<?php echo ($tab == '2') ? 'true' : 'false'; ?>">
                        <i class='bx bx-food-menu'></i>
                        <span>Food Listing</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content container">
                <div class="tab-pane fade <?php echo ($tab == '') ? 'show active' : ''; ?>" id="kitchen" role="tabpanel"
                    aria-labelledby="kitchen-tab">
                    <?php include 'approval/kitchen.approval.php'; ?>
                </div>

                <div class="tab-pane fade <?php echo ($tab == '1') ? 'show active' : ''; ?>" id="rider" role="tabpanel"
                    aria-labelledby="rider-tab">
                    <?php include 'approval/rider.approval.php'; ?>
                </div>

                <div class="tab-pane fade <?php echo ($tab == '2') ? 'show active' : ''; ?>" id="foodlist"
                    role="tabpanel" aria-labelledby="foodlist-tab">
                    <?php include 'approval/food.approval.php'; ?>
                    <!-- Uncommented this line -->
                </div>
            </div>
        </div>
    </main>


    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('.nav-link');

        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('href').replace('#', '');
                let tabParam = '';

                switch (tabId) {
                    case 'kitchen':
                        tabParam = '';
                        break;
                    case 'rider':
                        tabParam = '1';
                        break;
                    case 'foodlist':
                        tabParam = '2';
                        break;
                }

                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', tabParam);
                window.history.pushState({}, '', newUrl);

                const tab = new bootstrap.Tab(this);
                tab.show();
            });
        });

        window.addEventListener('popstate', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const activeTab = urlParams.get('tab') || '';

            let tabElement;
            switch (activeTab) {
                case '':
                    tabElement = document.querySelector('#kitchen-tab');
                    break;
                case '1':
                    tabElement = document.querySelector('#rider-tab');
                    break;
                case '2':
                    tabElement = document.querySelector('#approved-tab');
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