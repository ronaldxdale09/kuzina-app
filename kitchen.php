<?php include 'includes/header.php';
// Place this at the very top of your first PHP file (e.g., index.php or config.php)
error_reporting(0); // Disable all error reporting
// OR
error_reporting(E_ERROR | E_PARSE); // Show only fatal errors and parse errors
// OR
ini_set('display_errors', 0); // Hide all errors from display

// Check if the 'kitchen_user_id' cookie is set and not empty
if (isset($_COOKIE['kitchen_id']) && !empty($_COOKIE['kitchen_id'])) {
    // Redirect to Kitchen dashboard
    header("Location: users/kitchen/homepage.php");
    exit();
}

?>
<!-- Body Start -->
<link rel="stylesheet" href="assets/css/login.css">


<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<body>
    <div class="bg-pattern-wrap ratio2_1">

    </div>

    <!-- Main Start -->
    <main class="main-wrap login-page login mb-xxl">
        <div class="header">
            <img src="assets/images/banner/bg-pattern2.png" class="bg-img" alt="pattern" />
            <br>
            <div class="header-content">
                <div class="badge">
                    <i class='bx bx-restaurant'></i>
                    <span>Kitchen Portal</span>
                </div>
            </div>
        </div>

        <section class="login-section p-0">
            <!-- Kitchen Info Card -->
            <div class="info-card">
                <img class="logo" style="margin-bottom:20px" src="assets/images/logo/logo-w2.png" alt="logo" /> <br>

                <h2>Kitchen Dashboard Access</h2>
                <p>Manage your kitchen operations, orders, and menu all in one place</p>
            </div>

            <!-- Login Form Start -->
            <form id="login-form" class="custom-form form" method="POST">

                <div class="icon-wrapper">
                    <i class='bx bx-restaurant'></i>
                </div>
                <h1 class="font-md title-color fw-600">Kitchen Partner Login</h1>

                <div class="form-group-wrapper">
                    <!-- Username Input -->
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
                </div>

                <button type="submit" id="login-submit" class="btn-solid btn">
                    <i class='bx bx-lock-open-alt'></i>
                    Sign In
                </button>

                <!-- Help Links -->
                <div class="help-links">
                    <a href="#" class="help-link">
                        <i class='bx bx-help-circle'></i>
                        Forgot Password?
                    </a>
                    <a href="#" class="help-link">
                        <i class='bx bx-support'></i>
                        Kitchen Support
                    </a>
                </div>

                <!-- Join as Kitchen Partner -->
                <div class="join-section">
                    <h3>Not a Kitchen Partner Yet?</h3>
                    <a href="k_registration.php" class="btn-join join">
                        <i class='bx bx-restaurant'></i>
                        Become a Kitchen Partner
                    </a>
                </div>

                <!-- Portal Selection -->
                <div class="portal-selection">
                    <span>Looking for a different portal?</span>
                    <div class="portal-buttons">
                        <a href="index.php" class="portal-btn customer">
                            <i class='bx bx-user'></i>
                            Customer Portal
                        </a>
                        <a href="rider.php" class="portal-btn rider">
                            <i class='bx bx-cycling'></i>
                            Rider Portal
                        </a>
                    </div>
                </div>
            </form>
        </section>
    </main>

    <!-- Main End -->
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
        fetch('functions/kitchen.login.php', {
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
                        window.location.href = 'users/kitchen/homepage.php';
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




    <script>
    function requestGeolocationPermission() {
        // Check if geolocation is supported by the browser
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    // Success callback - user granted permission
                    console.log("Latitude:", position.coords.latitude);
                    console.log("Longitude:", position.coords.longitude);
                    // You can use the coordinates here, such as sending them to your server or displaying on a map
                },
                (error) => {
                    // Error callback - user denied permission or other error
                    if (error.code === error.PERMISSION_DENIED) {
                        alert("Permission denied. We need access to your location to provide better services.");
                    } else {
                        alert("An error occurred while retrieving location.");
                    }
                }
            );
        } else {
            // Geolocation not supported
            alert("Geolocation is not supported by your browser.");
        }
    }
    requestGeolocationPermission();

    // Call the function to prompt for geolocation permission
    </script>

    <?php include 'includes/script.php';?>
</body>

</html>