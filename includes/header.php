<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Essential Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui">
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
    
    <!-- App Icons -->
    <link rel="icon" href="assets/images/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="assets/images/favicon.png">
    
    <!-- Performance Optimizations -->
    <link rel="preload" href="assets/css/vendors/bootstrap.css" as="style">
    <link rel="preload" href="assets/css/style.css" as="style">
    
    <!-- Essential Styles First -->
    <style>
        /* Critical CSS */
        body {
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior-y: contain;
        }
    </style>
    
    <!-- Main Stylesheets with defer -->
    <link rel="stylesheet" href="assets/css/vendors/bootstrap.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/iconly.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/vendors/slick.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/vendors/slick-theme.css" media="print" onload="this.media='all'">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Manifest -->
    <link rel="manifest" href="manifest.json">
    
    <!-- No Script Fallback -->
    <noscript>
        <link rel="stylesheet" href="assets/css/vendors/bootstrap.css">
        <link rel="stylesheet" href="assets/css/iconly.css">
        <link rel="stylesheet" href="assets/css/vendors/slick.css">
        <link rel="stylesheet" href="assets/css/vendors/slick-theme.css">
    </noscript>
    
    <!-- Mobile App Optimization -->
    <script>
        // Disable pull-to-refresh
        document.addEventListener('touchmove', function(e) {
            e.preventDefault();
        }, { passive: false });
        
        // Add to homescreen prompt
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('service-worker.js');
        }
        
        // Prevent text selection
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
        });
    </script>
</head>