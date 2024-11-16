<?php include 'includes/header.php'; ?>
<!-- Head End -->

<!-- Body Start -->

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_isApproved.php'; ?>

    <!-- Skeleton loader End -->

    <style>
    .verification-details {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 12px;
        margin: 1rem 0;
        text-align: left;
    }

    .verification-details h3 {
        margin-bottom: 1rem;
        color: #333;
    }

    .verification-details ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .verification-details li {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
        color: #666;
    }

    .verification-details li i {
        color: var(--theme-color, #8a0b10);
    }

    .support-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        color: #666;
        text-decoration: none;
        margin: 0.5rem;
    }
    </style>
    <!-- Header End -->

    <!-- Sidebar Start -->

    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap error-404 mb-xxl">
        <!-- Banner Start -->
        <div class="banner-box">
            <img src="assets/images/banner/rider-waiting.png" class="img-fluid" alt="404" />
        </div>
        <!-- Banner End -->

        <!-- Error Section Start -->
        <section class="error mb-large">
            <h2 class="font-lg">Application Under Review</h2>
            <div class="status-message">
                <p class="content-color font-md">
                    Thank you for joining our delivery partner network! Your application is currently under review.
                </p>

                <div class="verification-details">
                    <h3>Next Steps:</h3>
                    <ul>
                        <li>
                            <i class='bx bx-check-shield'></i>
                            Document verification in progress
                        </li>
                        <li>
                            <i class='bx bx-phone-call'></i>
                            Expect a call for interview scheduling
                        </li>
                        <li>
                            <i class='bx bx-calendar-check'></i>
                            Face-to-face assessment appointment
                        </li>
                        <li>
                            <i class='bx bx-navigation'></i>
                            Vehicle inspection and orientation
                        </li>
                    </ul>
                </div>
                <a href="homepage.php" class="btn-solid">
                    <i class='bx bx-refresh'></i> Reload
                </a> <br>
               
            </div>

        </section>

        <!-- Error Section End -->
    </main>
    <!-- Main End -->



    <!-- Action Language Start -->
    <div class="action action-language offcanvas offcanvas-bottom" tabindex="-1" id="language"
        aria-labelledby="language">
        <div class="offcanvas-body small">
            <h2 class="m-b-title1 font-md">Select Language</h2>
            <ul class="list">
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img
                            src="assets/icons/flag/us.svg" alt="us" /> English </a>
                </li>
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img
                            src="assets/icons/flag/in.svg" alt="us" />Indian </a>
                </li>
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img
                            src="assets/icons/flag/it.svg" alt="us" />Italian</a>
                </li>
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img
                            src="assets/icons/flag/tf.svg" alt="us" /> French</a>
                </li>
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img
                            src="assets/icons/flag/cn.svg" alt="us" /> Chines</a>
                </li>
            </ul>
        </div>
    </div>
    <!-- Action Language End -->

    <!-- jquery 3.6.0 -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap js -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Lord Icon -->
    <script src="assets/js/lord-icon-2.1.0.js"></script>

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