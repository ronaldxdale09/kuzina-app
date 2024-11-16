<?php include 'includes/header.php'; 

// Check if the cookie is not set
if (!isset($_COOKIE['onboarding_journal'])) {
    // Set the cookie to indicate that the user has seen the onboarding page
    // The cookie will expire in 30 days (2592000 seconds)
    setcookie('onboarding_journal', 'true', time() + 2592000, "/");
}
?>

<!-- Body Start -->

<body>
    <!-- Main Start -->
    <main class="main-wrap onboarding-page mb-xxl">
        <!-- Banner Section Start -->
        <div class="banner">
            <img src="assets/images/banner/journal-banner.jpg" alt="Track Your Meals and Progress" />
        </div>
        <!-- Banner Section End -->

        <!-- Onboarding Section Start -->
        <section class="onboarding-slider">
            <!-- Slide 1 -->
            <div>
                <div class="content-wrap">
                    <h1 class="font-lg title-color">Track Your Meals Effortlessly</h1>
                    <p class="font-md content-color">Stay on top of your health journey with our easy-to-use meal
                        tracking feature.</p>
                </div>
            </div>

            <!-- Slide 2 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Personalized Nutrition Insights</h2>
                    <p class="font-md content-color">Understand your eating habits and get tailored recommendations to
                        meet your health goals.</p>
                </div>
            </div>

            <!-- Slide 3 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Set and Achieve Goals</h2>
                    <p class="font-md content-color">Define daily calorie, protein, carb, and fat goals. Monitor your
                        progress with real-time analytics.</p>
                </div>
            </div>

            <!-- Slide 4 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Seamless Integration</h2>
                    <p class="font-md content-color">Automatically sync meals from your orders into your journal for
                        hassle-free tracking.</p>
                </div>
            </div>

            <!-- Slide 5 -->
            <div>
                <div class="content-wrap">
                    <h2 class="font-lg title-color">Celebrate Your Progress</h2>
                    <p class="font-md content-color">Stay motivated by tracking achievements and visualizing your health
                        improvements over time.</p>
                </div>
            </div>
        </section>
        <!-- Onboarding Section End -->

        <!-- Call to Action -->
        <a href="journal.php" class="btn-solid">Go to My Journal</a>
  
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