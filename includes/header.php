<!DOCTYPE html>
<html lang="en">
<?php include 'connection/db.php'?>
<head>
    <!-- Essential Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- App Info -->
    <title>Kuzina</title>
    <meta name="description" content="Kuzina Food Delivery">
    <meta name="application-name" content="Kuzina">

    <!-- PWA & Mobile App Settings -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#8a0b10">
    <meta name="apple-mobile-web-app-title" content="Kuzina">
    <meta name="format-detection" content="telephone=no">

    <!-- Resource Hints -->
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="preconnect" href="//cdn.jsdelivr.net" crossorigin>

    <!-- Critical Resources -->
    <link rel="preload" href="assets/css/vendors/bootstrap.css" as="style" fetchpriority="high">
    <link rel="preload" href="assets/css/style.css" as="style" fetchpriority="high">
    <link rel="preload" href="assets/fonts/your-main-font.woff2" as="font" type="font/woff2" crossorigin>

    <!-- App Icons with Different Sizes -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">

    <!-- Critical CSS -->
    <style>
    /* Performance Optimizations */
    :root {
        --app-height: 100%;
    }

    html {
        box-sizing: border-box;
        text-size-adjust: 100%;
        -webkit-text-size-adjust: 100%;
    }

    body {
        margin: 0;
        padding: 0;
        -webkit-tap-highlight-color: transparent;
        overscroll-behavior-y: contain;
        overflow-x: hidden;
        min-height: var(--app-height);
        background: #fff;
    }

    /* Prevent FOUC */
    .no-fouc {
        visibility: hidden;
    }

    /* Loading Indicator */
    .app-loading {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: #fff;
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    </style>

    <!-- Deferred Stylesheets -->
    <link rel="stylesheet" href="assets/css/vendors/bootstrap.css" media="print"
        onload="this.media='all'; this.onload=null;">
    <link rel="stylesheet" href="assets/css/iconly.css" media="print" onload="this.media='all'; this.onload=null;">
    <link rel="stylesheet" href="assets/css/vendors/slick.css" media="print"
        onload="this.media='all'; this.onload=null;">
    <link rel="stylesheet" href="assets/css/vendors/slick-theme.css" media="print"
        onload="this.media='all'; this.onload=null;">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json" crossorigin="use-credentials">

    <!-- No Script Fallback -->
    <noscript>
        <link rel="stylesheet" href="assets/css/vendors/bootstrap.css">
        <link rel="stylesheet" href="assets/css/iconly.css">
        <link rel="stylesheet" href="assets/css/vendors/slick.css">
        <link rel="stylesheet" href="assets/css/vendors/slick-theme.css">
    </noscript>

    <!-- Performance Optimization Script -->
    <script>
    // Cache control
    if ('caches' in window) {
        caches.open('kuzina-static-v1').then(cache => {
            const resourcesToCache = [
                'assets/css/vendors/bootstrap.css',
                'assets/css/style.css',
                'assets/css/iconly.css',
                'assets/css/vendors/slick.css',
                'assets/css/vendors/slick-theme.css',
                'assets/images/favicon.png'
            ];
            cache.addAll(resourcesToCache);
        });
    }

    // Calculate actual viewport height for mobile
    function setAppHeight() {
        document.documentElement.style.setProperty('--app-height', `${window.innerHeight}px`);
    }
    window.addEventListener('resize', setAppHeight);
    setAppHeight();

    // Optimize mobile interactions
    document.addEventListener('touchmove', function(e) {
        if (!e.target.closest('.scrollable')) {
            e.preventDefault();
        }
    }, {
        passive: false
    });


    // Prevent FOUC
    document.documentElement.classList.add('no-fouc');
    window.addEventListener('load', function() {
        document.documentElement.classList.remove('no-fouc');
        document.querySelector('.app-loading')?.remove();
    });

    // Performance monitoring
    if ('performance' in window) {
        const observer = new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (entry.entryType === 'largest-contentful-paint') {
                    console.log('LCP:', entry.startTime);
                }
            }
        });
        observer.observe({
            entryTypes: ['largest-contentful-paint']
        });
    }
    </script>
</head>
