<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loading...</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    #splash-screen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 99999;
    }

    .logo-container {
        margin-top: 40px;
        margin-bottom: 20px;
    }

    .logo-container img {
        max-width: 200px;
        height: auto;
    }

    .animation-container {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    .animation-container img {
        max-width: 300px;
        height: auto;
    }

    .progress-container {
        position: absolute;
        bottom: 20%;
        width: 80%;
        max-width: 300px;
    }

    .progress-bar {
        width: 100%;
        height: 4px;
        background: rgba(0, 0, 0, 0.1);
        border-radius: 2px;
        overflow: hidden;
    }

    .progress {
        width: 0%;
        height: 100%;
        background: #007bff;
        transition: width 0.3s ease;
    }

    .progress-text {
        text-align: center;
        margin-top: 10px;
        font-family: Arial, sans-serif;
        font-size: 14px;
        color: #666;
    }

    #main-content {
        display: none;
    }

    @media (max-height: 600px) {
        .logo-container {
            margin-top: 20px;
        }

        .progress-container {
            bottom: 15%;
        }
    }
    </style>
</head>

<body>
    <!-- Splash Screen -->
    <div id="splash-screen">
        <div class="logo-container">
            <img src="assets/splash/logo.png" alt="Logo" id="splash-logo">
        </div>
        <div class="animation-container">
            <img src="assets/splash/splash.gif" alt="Loading Animation" id="splash-animation">
        </div>
        <div class="progress-container">
            <div class="progress-bar">
                <div class="progress" id="progress"></div>
            </div>
            <div class="progress-text" id="progress-text">Loading assets... 0%</div>
        </div>
    </div>

    <!-- Main Content (hidden initially) -->
    <div id="main-content">
        <!-- Your website content will go here -->
    </div>
    <script>
 document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progress');
    const progressText = document.getElementById('progress-text');
    const splashScreen = document.getElementById('splash-screen');
    const mainContent = document.getElementById('main-content');

    // List of all assets to preload
    const assetsToLoad = [
        'assets/css/vendors/bootstrap.css',
        'assets/css/iconly.css',
        'assets/css/vendors/slick.css',
        'assets/css/vendors/slick-theme.css',
        'assets/css/style.css',
        'assets/js/jquery-3.6.0.min.js',
        'assets/js/bootstrap.bundle.min.js',
        'assets/js/lord-icon-2.1.0.js',
        'assets/js/feather.min.js',
        'assets/js/slick.js',
        'assets/js/slick.min.js',
        'assets/js/slick-custom.js',
        'assets/js/theme-setting.js',
        'assets/js/script.js'
    ];

    const minimumLoadTime = 4000; // 4 seconds
    const startTime = Date.now();
    let assetsLoaded = false;
    let currentProgress = 0;

    // Function to update the visual progress
    function updateProgressBar() {
        const elapsedTime = Date.now() - startTime;
        const timeProgress = (elapsedTime / minimumLoadTime) * 100;
        
        // Ensure progress moves smoothly but never exceeds 98% until assets are loaded
        currentProgress = assetsLoaded ? 
            Math.min(100, timeProgress) : 
            Math.min(98, timeProgress);

        progressBar.style.width = `${currentProgress}%`;
        progressText.textContent = `Loading assets... ${Math.round(currentProgress)}%`;

        // Check if we should finish loading
        if (assetsLoaded && elapsedTime >= minimumLoadTime) {
            clearInterval(progressInterval);
            finishLoading();
        }
    }

    function finishLoading() {
        // Ensure we reach 100% before fading
        progressBar.style.width = '100%';
        progressText.textContent = 'Loading assets... 100%';
        
        setTimeout(() => {
            splashScreen.style.opacity = '0';
            splashScreen.style.transition = 'opacity 0.5s ease';
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 500);
        }, 200); // Short delay to show 100%
    }

    function preloadAsset(url) {
        return new Promise((resolve, reject) => {
            if (url.endsWith('.css')) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = url;
                link.onload = resolve;
                link.onerror = resolve; // Still continue if asset fails
                document.head.appendChild(link);
            } else if (url.endsWith('.js')) {
                const script = document.createElement('script');
                script.src = url;
                script.async = true;
                script.onload = resolve;
                script.onerror = resolve; // Still continue if asset fails
                document.body.appendChild(script);
            }
        });
    }

    // Start progress update interval - update every 16ms for smooth 60fps animation
    const progressInterval = setInterval(updateProgressBar, 16);

    // Preload all assets
    Promise.all(assetsToLoad.map(url => preloadAsset(url)))
        .then(() => {
            assetsLoaded = true;
        })
        .catch(error => {
            console.error('Error loading assets:', error);
            assetsLoaded = true;
        });

    // Fallback to ensure splash screen doesn't hang
    setTimeout(() => {
        if (!assetsLoaded) {
            console.warn('Some assets failed to load, continuing anyway...');
            assetsLoaded = true;
        }
    }, Math.max(10000, minimumLoadTime)); // Use longer of 10 seconds or minimum load time
});
    </script>
</body>

</html>