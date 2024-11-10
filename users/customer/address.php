<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<!-- Body Start -->

<body>
    <!-- Header Start -->
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();">
                <i class="iconly-Arrow-Left-Square icli"></i>
            </a>
            <h1 class="title-color font-md">Select delivery address</h1>
        </div>
    </header>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap address2-page mb-xxl">
        <!-- Address Section Start -->
        <section class="pt-0">
            <button class="d-block btn-outline-grey" data-bs-toggle="offcanvas" data-bs-target="#add-address"
                aria-controls="add-address">+ Add New Address</button>

            <div id="address-list">
                <?php include 'fetch/address.select.php'; ?>
            </div>
        </section>
        <!-- Address Section End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-wrap footer-button">
        <a href="javascript:void(0);" id="confirm-address" class="font-md">Select Address</a>
    </footer>
    <!-- Footer End -->

    <!-- Add New Address Off Canvas Start -->
    <?php    include 'modal/modal.address.php'; ?>



    <!-- Add New Address Off Canvas End -->

    <!-- jquery 3.6.0 -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Js -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Feather Icon -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Theme Setting js -->
    <script src="assets/js/theme-setting.js"></script>

    <!-- Script js -->
    <script src="assets/js/script.js"></script>
</body>
<!-- Body End -->

</html>
<!-- Html End -->