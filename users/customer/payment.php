<?php include 'includes/header.php'; ?>
<?php include 'modal/modal.payment.php'; ?>

<!-- Body Start -->
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" href="assets/css/modal.css">

<!-- Body Start -->
<style>
#confirm-payment {
    width: 100%;
    max-width: 100%;
    display: block;
    padding: 12px 0;
    font-size: 16px;
    text-align: center;
    /* Optional: space from elements above */
}
</style>

<body>
    <!-- Header Start -->
    <header class="header">
        <div class="logo-wrap">
            <a href="homepage.php"><i class="iconly-Arrow-Left-Square icli"></i></a>
            <h1 class="title-color font-md">Add Payment Method</h1>
        </div>
    </header>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap payment-page mb-xxl">

        <?php include 'components/cart.address.php'; ?>
        <!-- Payment Section Start -->
        <section class="payment-section">
            <div class="accordion" id="accordionExample">
                <!-- Wallet Payment Options -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button font-md title-color collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                            aria-controls="collapseThree">
                            E-Wallet
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <ul class="filter-row">
                                <li class="filter-col" data-payment-method="gcash">
                                    GCash (Demo) <span class="check"><img src="assets/icons/svg/active.svg"
                                            alt="active" /></span>
                                </li>
                                <li class="filter-col" data-payment-method="maya">
                                    Maya (Demo)<span class="check"><img src="assets/icons/svg/active.svg"
                                            alt="active" /></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Cash on Delivery Option -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingfour">
                        <button class="accordion-button font-md title-color collapsed" type="button"
                            data-bs-toggle="collapse" data-bs-target="#collapsefour" aria-expanded="false"
                            aria-controls="collapsefour">
                            Cash on Delivery
                        </button>
                    </h2>
                    <div id="collapsefour" class="accordion-collapse collapse show" aria-labelledby="headingfour"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body cash">
                            <ul class="filter-row">
                                <!-- Preselect Cash on Delivery by adding the 'active' class -->
                                <li class="filter-col active" data-payment-method="cod">
                                    Cash on Delivery<span class="check"><img src="assets/icons/svg/active.svg"
                                            alt="active" /></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Payment Section End -->

        <?php include 'fetch/payment.order.details.php'; ?>

        <!-- Order Detail End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-wrap footer-button">
        <button id="confirm-payment" class="font-md btn-solid">Confirm Payment</button>

    </footer>
    <!-- Footer End -->

    <script>
        
    </script>
    <!-- PAYMONGO -->
    <script src="api/paymongo.js"></script>


    <!-- jquery 3.6.0 -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Js -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Date Picker  js -->
    <script src="assets/js/date-picker/datepicker.js"></script>
    <script src="assets/js/date-picker/datepicker.en.js"></script>
    <script src="assets/js/date-picker/datepicker.custom.js"></script>

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