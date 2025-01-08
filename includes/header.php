<!DOCTYPE html>
<html lang="en">
<?php include 'connection/db.php'?>
<head>
    <!-- Essential Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Security Headers -->
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' cdn.jsdelivr.net;">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Permissions-Policy" content="interest-cohort=()">

    <!-- Primary Meta Tags -->
    <title>Kuzina - Food Delivery</title>
    <meta name="description" content="Order delicious food delivered to your doorstep with Kuzina Food Delivery service">
    <meta name="application-name" content="Kuzina">
    
    <!-- PWA Settings -->
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#8a0b10" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">
    <meta name="apple-mobile-web-app-title" content="Kuzina">
    <meta name="format-detection" content="telephone=no">

    <!-- Resource Hints -->
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link rel="dns-prefetch" href="https://cdn.jsdelivr.net">
    
    <!-- Critical Resources -->
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="assets/css/vendors/bootstrap.css" as="style">
    <link rel="modulepreload" href="assets/js/app.js">
    
    <!-- Favicons -->
    <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png">
    
    <!-- Critical CSS -->
    <style>
    /* Modern CSS Reset and Performance Optimizations */
    :root {
        --app-height: 100%;
        --primary-color: #8a0b10;
        color-scheme: light dark;
    }

    *, *::before, *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html {
        text-size-adjust: 100%;
        scroll-behavior: smooth;
        height: var(--app-height);
    }

    body {
        line-height: 1.5;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
        -webkit-tap-highlight-color: transparent;
        overscroll-behavior-y: contain;
        overflow-x: hidden;
        background: #fff;
    }

    @media (prefers-reduced-motion: reduce) {
        html {
            scroll-behavior: auto;
        }
        
        *, *::before, *::after {
            animation-duration: 0.01ms !important;
            animation-iteration-count: 1 !important;
            transition-duration: 0.01ms !important;
            scroll-behavior: auto !important;
        }
    }

    .no-js {
        display: none;
    }

    .app-loading {
        position: fixed;
        inset: 0;
        background: #fff;
        z-index: 9999;
        display: grid;
        place-items: center;
    }

    @media (prefers-color-scheme: dark) {
        body {
            background: #121212;
            color: #fff;
        }
        .app-loading {
            background: #121212;
        }
    }
    </style>

    <!-- Deferred Stylesheets -->
    <link rel="stylesheet" href="assets/css/vendors/bootstrap.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/iconly.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/vendors/slick.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- PWA Manifest -->
    <link rel="manifest" href="manifest.json" crossorigin="use-credentials">

    <!-- Fallback for No JavaScript -->
    <noscript>
        <link rel="stylesheet" href="assets/css/vendors/bootstrap.css">
        <link rel="stylesheet" href="assets/css/iconly.css">
        <link rel="stylesheet" href="assets/css/vendors/slick.css">
        <style>.no-js { display: block; }</style>
    </noscript>

    <!-- Performance Optimization Script -->
    <script type="module">
        // Modern dynamic viewport height calculation
        const setAppHeight = () => {
            document.documentElement.style.setProperty('--app-height', `${window.innerHeight}px`);
        };
        window.addEventListener('resize', setAppHeight);
        setAppHeight();

        // Implement resource hints
        const prefetchLinks = ['about.php', 'menu.php', 'contact.php'];
        if ('connection' in navigator && navigator.connection.effectiveType === '4g') {
            prefetchLinks.forEach(link => {
                const prefetch = document.createElement('link');
                prefetch.rel = 'prefetch';
                prefetch.href = link;
                document.head.appendChild(prefetch);
            });
        }

        // Register Service Worker
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js', {
                    scope: '/'
                }).catch(error => {
                    console.error('Service Worker registration failed:', error);
                });
            });
        }

        // Initialize performance monitoring
        if ('PerformanceObserver' in window) {
            const observer = new PerformanceObserver((list) => {
                const entries = list.getEntries();
                entries.forEach(entry => {
                    if (entry.entryType === 'largest-contentful-paint') {
                        console.log(`LCP: ${entry.startTime}`);
                    }
                });
            });
            
            observer.observe({
                entryTypes: ['largest-contentful-paint', 'first-input', 'layout-shift']
            });
        }

        // Remove loading indicator
        window.addEventListener('load', () => {
            requestAnimationFrame(() => {
                const loader = document.querySelector('.app-loading');
                if (loader) {
                    loader.style.opacity = '0';
                    loader.addEventListener('transitionend', () => loader.remove());
                }
            });
        });
    </script>
</head>