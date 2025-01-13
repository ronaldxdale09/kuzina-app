<?php 
include 'includes/header.php'; 

// Check if splash screen has been shown
$showSplash = !isset($_SESSION['splash_shown']);
if (!isset($_SESSION['splash_shown'])) {
    $_SESSION['splash_shown'] = true;
}
?>

<link rel="stylesheet" href="assets/css/login.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<!-- Splash Screen Styles -->
<style>
#initial-splash-screen {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: #ffffff;
    display: <?php echo $showSplash ? 'flex': 'none';
    ?>;
    flex-direction: column;
    align-items: center;
    z-index: 99999;
}
</style>


<body>
    <!-- Initial Splash Screen -->
    <?php if ($showSplash): ?>
    <div id="initial-splash-screen">
        <div class="splash-logo-container">
            <img src="assets/splash/logo.png" alt="Logo" id="splash-logo">
        </div>
        <div class="splash-animation-container">
            <img src="assets/splash/splash.gif" alt="Loading Animation" id="splash-animation">
        </div>
        <div class="splash-progress-container">
            <div class="splash-progress-bar">
                <div class="splash-progress" id="splash-progress"></div>
            </div>
            <div class="splash-progress-text" id="splash-progress-text">Loading assets... 0%</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Main Content -->
    <div id="main-content" style="display: <?php echo $showSplash ? 'none' : 'block'; ?>">
        <main class="main-wrap login-page login mb-xxl">
            <div class="header">
                <img src="assets/images/banner/bg-pattern2.png" class="bg-img" alt="pattern" />
                <br>
                <div class="header-content">
                    <div class="badge">
                        <i class='bx bx-restaurant'></i>
                        <span>Customer Portal</span>
                    </div>
                </div>
            </div>

            <section class="login-section p-0">
                <div class="info-card">
                    <img class="logo" style="margin-bottom:20px" src="assets/images/logo/logo-w2.png" alt="logo" /> <br>
                    <h2>Welcome to Kuzina</h2>
                    <p class="font-sm content-color">Explore a wide range of nutritious, homemade recipes crafted by
                        home cooks.
                        Enjoy wholesome, balanced meals made with fresh ingredients and love.</p>
                </div>

                <form id="login-form" class="custom-form form" method="POST">
                    <h1 class="font-md title-color fw-600">Customer Login</h1>

                    <div class="form-group-wrapper">
                        <div class="input-box">
                            <input type="number" id="phone" name="phone" placeholder="Phone Number" required
                                class="form-control" autocomplete="off" />
                            <i class="iconly-Call icli"></i>
                        </div>

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

                    <span class="content-color font-sm d-block text-center fw-600">
                        If you are new, <a href="register.php" class="underline">Create Now</a>
                    </span>

                    <div class="help-links">
                        <a href="#" class="help-link">
                            <i class='bx bx-help-circle'></i>
                            Forgot Password?
                        </a>
                        <a href="#" class="help-link">
                            <i class='bx bx-support'></i>
                            Support
                        </a>
                    </div>

                    <div class="portal-selection">
                        <span>Looking for a different portal?</span>
                        <div class="portal-buttons">
                            <a href="kitchen.php" class="portal-btn customer">
                                <i class='bx bx-user'></i>
                                Kitchen Portal
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

        <!-- Login Loading Overlay -->
        <div id="login-loading-overlay">
            <div id="login-loading-spinner">
                <img src="assets/loader/loader5.gif" alt="Loading...">
            </div>
        </div>

        <!-- Error Modal -->
        <div id="error-modal" class="error-modal">
            <div class="error-modal-content">
                <span id="error-close" class="error-close">&times;</span>
                <h2>Error</h2>
                <p id="error-message"></p>
            </div>
        </div>

        <!-- Under Development Modal -->
        <div class="action action-confirmation offcanvas offcanvas-bottom" tabindex="-1" id="underDev"
            aria-labelledby="confirmation">
            <div class="offcanvas-body small">
                <div class="confirmation-box">
                    <h2>Feature Under Development</h2>
                    <p class="font-sm content-color">We apologize for the inconvenience, but this feature is
                        currently under development. Please check back soon for updates.</p>
                    <div class="btn-box">
                        <button class="btn-outline" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Only initialize splash screen if it's being shown
        <?php if ($showSplash): ?>
        const splashProgress = document.getElementById('splash-progress');
        const splashProgressText = document.getElementById('splash-progress-text');
        const initialSplashScreen = document.getElementById('initial-splash-screen');
        const mainContent = document.getElementById('main-content');

        // List of all assets to preload
        const assetsToLoad = [
            // Meta Assets
            'assets/images/favicon.png',
            'manifest.json',

            // CSS files
            'assets/css/vendors/bootstrap.css',
            'assets/css/iconly.css',
            'assets/css/vendors/slick.css',
            'assets/css/vendors/slick-theme.css',
            'assets/css/style.css',
            'assets/css/login.css',

            // JavaScript files
            'assets/js/jquery-3.6.0.min.js',
            'assets/js/bootstrap.bundle.min.js',
            'assets/js/lord-icon-2.1.0.js',
            'assets/js/feather.min.js',
            'assets/js/slick.js',
            'assets/js/slick.min.js',
            'assets/js/slick-custom.js',
            'assets/js/theme-setting.js',
            'assets/js/script.js',

            // Images
            'assets/images/banner/bg-pattern2.png',
            'assets/images/logo/logo-w2.png',
            'assets/splash/logo.png',
            'assets/splash/splash.gif',
            'assets/loader/loader5.gif'
        ];

        const minimumLoadTime = 4000;
        const startTime = Date.now();
        let assetsLoaded = 0;
        const totalAssets = assetsToLoad.length;

        function updateProgressBar() {
            const elapsedTime = Date.now() - startTime;
            const timeProgress = (elapsedTime / minimumLoadTime) * 100;
            const assetProgress = (assetsLoaded / totalAssets) * 100;

            // Use the lower of the two progress values
            const currentProgress = Math.min(timeProgress, Math.min(98, assetProgress));

            splashProgress.style.width = `${currentProgress}%`;
            splashProgressText.textContent = `Loading assets... ${Math.round(currentProgress)}%`;

            if (assetsLoaded >= totalAssets && elapsedTime >= minimumLoadTime) {
                clearInterval(progressInterval);
                finishLoading();
            }
        }

        function finishLoading() {
            splashProgress.style.width = '100%';
            splashProgressText.textContent = 'Loading assets... 100%';

            setTimeout(() => {
                initialSplashScreen.style.opacity = '0';
                initialSplashScreen.style.transition = 'opacity 0.5s ease';
                setTimeout(() => {
                    initialSplashScreen.style.display = 'none';
                    mainContent.style.display = 'block';
                }, 500);
            }, 200);
        }

        function preloadAsset(url) {
            return new Promise((resolve, reject) => {
                if (url.endsWith('.css')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = url;
                    link.onload = () => {
                        assetsLoaded++;
                        resolve();
                    };
                    link.onerror = () => {
                        assetsLoaded++;
                        resolve();
                    };
                    document.head.appendChild(link);
                } else if (url.endsWith('.js')) {
                    const script = document.createElement('script');
                    script.src = url;
                    script.async = true;
                    script.onload = () => {
                        assetsLoaded++;
                        resolve();
                    };
                    script.onerror = () => {
                        assetsLoaded++;
                        resolve();
                    };
                    document.body.appendChild(script);
                } else if (url.match(/\.(jpg|jpeg|png|gif|svg|json)$/i)) {
                    if (url.endsWith('.json')) {
                        fetch(url)
                            .then(() => {
                                assetsLoaded++;
                                resolve();
                            })
                            .catch(() => {
                                assetsLoaded++;
                                resolve();
                            });
                    } else {
                        const img = new Image();
                        img.src = url;
                        img.onload = () => {
                            assetsLoaded++;
                            resolve();
                        };
                        img.onerror = () => {
                            assetsLoaded++;
                            resolve();
                        };
                    }
                }
            });
        }

        const progressInterval = setInterval(updateProgressBar, 16);

        // Preload all assets
        Promise.all(assetsToLoad.map(url => preloadAsset(url)))
            .catch(error => {
                console.error('Error loading assets:', error);
            });

        // Fallback timeout
        setTimeout(() => {
            if (assetsLoaded < totalAssets) {
                console.warn('Some assets failed to load, continuing anyway...');
                assetsLoaded = totalAssets;
            }
        }, Math.max(10000, minimumLoadTime));
        <?php endif; ?>

        // Login form handling
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();

            document.getElementById('login-loading-overlay').style.display = 'flex';

            const formData = new FormData(this);

            fetch('functions/login.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    setTimeout(() => {
                        document.getElementById('login-loading-overlay').style.display =
                            'none';

                        if (data.success) {
                            window.location.href = 'users/customer/homepage.php';
                        } else {
                            showErrorModal(data.message ||
                                'Login failed. Please try again.');
                        }
                    }, 2500);
                })
                .catch(error => {
                    setTimeout(() => {
                        document.getElementById('login-loading-overlay').style.display =
                            'none';
                        console.error('Error:', error);
                        showErrorModal('An error occurred during login. Please try again.');
                    }, 2500);
                });
        });

        // Error Modal Functions
        function showErrorModal(message) {
            const errorModal = document.getElementById('error-modal');
            const errorMessage = document.getElementById('error-message');
            errorMessage.textContent = message;
            errorModal.classList.add('show');
        }

        // Close error modal when clicking the X
        document.getElementById('error-close').addEventListener('click', function() {
            document.getElementById('error-modal').classList.remove('show');
        });

        // Close error modal when clicking outside
        window.addEventListener('click', function(event) {
            const errorModal = document.getElementById('error-modal');
            if (event.target === errorModal) {
                errorModal.classList.remove('show');
            }
        });

        // Show/Hide Password Toggle
        const showHidePassword = document.querySelector('.showHidePassword');
        const passwordInput = document.getElementById('password');

        if (showHidePassword && passwordInput) {
            showHidePassword.addEventListener('click', function() {
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.classList.remove('iconly-Hide');
                    this.classList.add('iconly-Show');
                } else {
                    passwordInput.type = 'password';
                    this.classList.remove('iconly-Show');
                    this.classList.add('iconly-Hide');
                }
            });
        }

        // Handle "Under Development" features
        document.querySelectorAll('.help-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const underDevModal = new bootstrap.Offcanvas(document.getElementById(
                    'underDev'));
                underDevModal.show();
            });
        });

        // Geolocation handling
        function requestGeolocationPermission() {
            if ("geolocation" in navigator) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        console.log("Latitude:", position.coords.latitude);
                        console.log("Longitude:", position.coords.longitude);
                        // You can store these coordinates or use them as needed
                    },
                    (error) => {
                        if (error.code === error.PERMISSION_DENIED) {
                            console.log("Location permission denied.");
                        } else {
                            console.log("Error getting location.");
                        }
                    }, {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            } else {
                console.log("Geolocation is not supported by your browser.");
            }
        }

        // Request geolocation permission
        requestGeolocationPermission();
    });
    </script>

    <?php include 'includes/script.php';?>
</body>

</html>