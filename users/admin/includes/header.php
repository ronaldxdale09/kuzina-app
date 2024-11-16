<!DOCTYPE html>
<html lang="en">
<!-- Head Start -->
<?php include '../../connection/db.php'?>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Kuzina" />
    <meta name="keywords" content="Kuzina" />
    <meta name="author" content="Kuzina" />
    <link rel="manifest" href="manifest.json" />
    <title>Kuzina</title>

    <!-- Favicon and Apple Touch Icons -->
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon" />
    <link rel="apple-touch-icon" href="assets/images/favicon.png" />
    
    <!-- Theme and Mobile Meta -->
    <meta name="theme-color" content="#d99f46" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="apple-mobile-web-app-title" content="Kuzina" />
    <meta name="msapplication-TileImage" content="assets/images/favicon.png" />
    <meta name="msapplication-TileColor" content="#FFFFFF" />

    <!-- Preload Critical Resources -->
    <link rel="preload" href="assets/css/vendors/bootstrap.css" as="style" />
    <link rel="preload" href="assets/css/style.css" as="style" />
    <link rel="preload" href="assets/boxicons/css/boxicons.min.css" as="style" />
    <link rel="preload" href="assets/boxicons/fonts/boxicons.woff2" as="font" type="font/woff2" crossorigin />

    <!-- Stylesheets -->
    <link rel="stylesheet" id="rtl-link" type="text/css" href="assets/css/vendors/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/iconly.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/slick.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/vendors/slick-theme.css" />
    <link rel="stylesheet" id="change-link" type="text/css" href="assets/css/style.css" />
    <link rel="stylesheet" id="change-link" type="text/css" href="assets/css/modal.css" />
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

    <!-- Prevent FOUC (Flash of Unstyled Content) -->
    <style>
        .footer-wrap {
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .footer-wrap.loaded {
            opacity: 1;
        }

        /* Ensure icons are visible immediately */
        .bx {
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Optimize icon rendering */
        .footer-link i {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            backface-visibility: hidden;
            transform: translateZ(0);
        }
    </style>

    <!-- Icon Loading Script -->
    <script>
        // Initialize icon state in SessionStorage
        if (!sessionStorage.getItem('iconsLoaded')) {
            sessionStorage.setItem('iconsLoaded', 'pending');
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check if icons are already loaded
            if (sessionStorage.getItem('iconsLoaded') === 'complete') {
                document.querySelector('.footer-wrap')?.classList.add('loaded');
                return;
            }

            // Load icons and cache them
            const iconCheck = setInterval(() => {
                const icons = document.querySelectorAll('.bx');
                if (icons.length > 0) {
                    icons.forEach(icon => {
                        icon.style.display = 'inline-block';
                    });
                    
                    // Mark icons as loaded
                    sessionStorage.setItem('iconsLoaded', 'complete');
                    document.querySelector('.footer-wrap')?.classList.add('loaded');
                    clearInterval(iconCheck);
                }
            }, 50);

            // Fallback if icons don't load
            setTimeout(() => {
                clearInterval(iconCheck);
                document.querySelector('.footer-wrap')?.classList.add('loaded');
            }, 2000);
        });

        // Store navigation state
        window.addEventListener('beforeunload', function() {
            sessionStorage.setItem('lastPage', window.location.pathname);
        });
    </script>
</head>