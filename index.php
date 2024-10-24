<?php include 'includes/header.php';?>
<!-- Body Start -->
<link rel="stylesheet" href="assets/css/login.css">

<body>
    <div class="bg-pattern-wrap ratio2_1">
        <!-- Background Image -->
        <div class="bg-patter">
            <img src="assets/images/banner/bg-pattern2.png" class="bg-img" alt="pattern" />
        </div>
    </div>

    <!-- Main Start -->
    <main class="main-wrap login-page mb-xxl">
        <img class="logo" src="assets/images/logo/logo-w2.png" alt="logo" />
        <img class="logo logo-w" src="assets/images/logo/logo-w2.png" alt="logo" />
        <p class="font-sm content-color">Explore a wide range of nutritious, homemade recipes crafted by home cooks.
            Enjoy wholesome, balanced meals made with fresh ingredients and love.</p>

        <section class="login-section p-0">
            <!-- Login Form Start -->
            <form id="login-form" class="custom-form" method="POST">
                <h1 class="font-md title-color fw-600">Login Account</h1>

                <!-- Phone Number Input start -->
                <div class="input-box">
                    <input type="number" id="phone" name="phone" placeholder="Phone Number" required
                        class="form-control" autocomplete="off" />
                    <i class="iconly-Call icli"></i>
                </div>
                <!-- Phone Number Input End -->

                <!-- Password Input start -->
                <div class="input-box">
                    <input type="password" id="password" name="password" placeholder="Password" required
                        class="form-control" />
                    <i class="iconly-Hide icli showHidePassword"></i>
                </div>
                <!-- Password Input End -->

                <button type="submit" id="login-submit" class="btn-solid">Sign in</button>

                <span class="content-color font-sm d-block text-center fw-600">
                    If you are new, <a href="register.php" class="underline">Create Now</a>
                </span>
            </form>
            <!-- Login Form End -->


        </section>


        <!-- Login Section End -->
    </main>
    <!-- Main End -->

    <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev"
        class="footer d-block text-center font-md title-color text-decoration-underline">Continue as
        guest</a>


    <div class="action action-confirmation offcanvas offcanvas-bottom" tabindex="-1" id="underDev"
        aria-labelledby="confirmation">
        <div class="offcanvas-body small">
            <div class="confirmation-box">
                <h2>Feature Under Development</h2>
                <p class="font-sm content-color">We apologize for the inconvenience, but this feature or module is
                    currently under development. Please check back soon for updates or contact support for further
                    assistance.</p>
                <div class="btn-box">
                    <button class="btn-outline" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Main End -->
    <div id="loading-overlay" style="display: none;">
        <div id="loading-spinner">
            <img src="assets/loader/loader5.gif" alt="Loading...">
        </div>
    </div>


    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span id="closeErrorModal" class="close">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage"></p>
        </div>
    </div>

    <script>
    document.getElementById('login-submit').addEventListener('click', function(e) {
        e.preventDefault();

        // Show the loader
        document.getElementById('loading-overlay').style.display = 'flex'; // Show the loading overlay

        // Prepare form data
        const formData = new FormData(document.getElementById('login-form'));

        // Perform AJAX request for login
        fetch('functions/login.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Ensure the loader is visible for at least 3 seconds
                setTimeout(() => {
                    document.getElementById('loading-overlay').style.display =
                        'none'; // Hide loader

                    if (data.success) {
                        // Redirect to homepage
                        window.location.href = 'users/customer/homepage.php';
                    } else {
                        // Show error message if login failed
                        showErrorModal(data.message);
                    }
                }, 2500); // Minimum 3 seconds delay
            })
            .catch(error => {
                setTimeout(() => {
                    document.getElementById('loading-overlay').style.display =
                        'none'; // Hide loader
                    console.error('Error:', error);
                    showErrorModal('An error occurred during login. Please try again.');
                }, 2500); // Minimum 3 seconds delay
            });
    });

    // Show error modal with custom message
    function showErrorModal(message) {
        const errorModal = document.getElementById('errorModal');
        document.getElementById('errorMessage').innerText = message;
        errorModal.classList.add('show'); // Show error modal
    }

    // Close the error modal
    document.getElementById('closeErrorModal').onclick = function() {
        document.getElementById('errorModal').classList.remove('show');
    };
    </script>
    <!-- jquery 3.6.0 -->
    <?php include 'includes/script.php';?>

</body>
<!-- Body End -->

</html>
<!-- Html End -->