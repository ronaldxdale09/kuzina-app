<?php include 'includes/header.php'; 

// Check if the cookie is not set
if (!isset($_COOKIE['onboarding_seen'])) {
    // Set the cookie to indicate that the user has seen the onboarding page
    // The cookie will expire in 30 days (2592000 seconds)
    setcookie('onboarding_seen', 'true', time() + 2592000, "/");
}
?>

<!-- Body Start -->

<body>
    <!-- Main Start -->
    <main class="main-wrap onboarding-page mb-xxl">
        <!-- Banner Section Start -->
        <div class="banner">
            <img src="assets/images/banner/onboarding.png" alt="img" />
        </div>
        <!-- Banner Section Start -->

        <!-- Onboarding Section Start -->
        <section class="onboarding-slider">
            <!-- Slide 1 -->
            <div>
                <div class="content-wrap">
                    <h1 class="font-lg title-color">Healthy Meals, Delivered Fresh</h1>
                    <p class="font-md content-color">Enjoy freshly prepared, nutritious meals that are just a click
                        away. Your health journey starts here.</p>
                </div>
            </div>

            <!-- Slide 2 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Your Favorite Dishes, Anytime</h2>
                    <p class="font-md content-color">Choose from a wide selection of healthy and delicious dishes
                        crafted to satisfy your taste buds at any time.</p>
                </div>
            </div>

            <!-- Slide 3 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Nutrition Made Convenient</h2>
                    <p class="font-md content-color">We deliver balanced meals that support your active lifestyle.
                        Experience convenience without compromise.</p>
                </div>
            </div>

            <!-- Slide 4 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Stay Energized with Every Bite</h2>
                    <p class="font-md content-color">Fuel your body with wholesome ingredients and boost your energy
                        levels. Healthy eating has never been easier.</p>
                </div>
            </div>

            <!-- Slide 5 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Eco-friendly, Sustainable Choices</h2>
                    <p class="font-md content-color">Join us in supporting sustainability. Our packaging is
                        eco-friendly, and our meals are made from responsibly sourced ingredients.</p>
                </div>
            </div>
        </section>

        <!-- Onboarding Section End -->

        <a href="index.php" class="btn-solid">Sign In</a>
        <span class="content-color font-sm d-block text-center fw-600">Don't Have an account? <a href="register.php"
                class="underline font-theme">Sign In </a></span>
    </main>
    <!-- Main End -->

    <!-- jquery 3.6.0 -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap js -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Slick Slider js -->
    <script src="assets/js/slick.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/slick-custom.js"></script>

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