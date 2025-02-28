<!DOCTYPE html>
<html lang="en">
<?php 
// Quick auth check
if (!isset($_COOKIE['user_id']) || empty($_COOKIE['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

$customer_id = $_COOKIE['user_id'];

// Better caching
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');

// Include required files
include '../../connection/db.php';
include '../../includes/webview.php';
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kuzina</title>
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
    
    <!-- PWA Support -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#502121">
    <meta name="apple-mobile-web-app-capable" content="yes">
    
    <!-- Fast loading: Critical CSS inline -->
    <style>
    /* Only essential styles for first render */
    body {margin: 0; -webkit-tap-highlight-color: transparent;}
    .header {display: flex; align-items: center; justify-content: space-between; padding: 16px;  position: sticky; top: 0; z-index: 999; box-shadow: 0 2px 8px rgba(0,0,0,0.1);}
    .logo-wrap {display: flex; align-items: center;}
    .logo {height: 28px;}
    .cart-icon-wrap {position: relative;}
    .cart-count {position: absolute; top: -8px; right: -8px; background-color: #FF6B35; color: white; font-size: 11px; font-weight: 600; height: 18px; min-width: 18px; border-radius: 9px; display: flex; align-items: center; justify-content: center;}
    </style>

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/images/favicon.png">
    <meta name="theme-color" content="#d99f46">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="Kuzina">
    <meta name="msapplication-TileImage" content="assets/images/favicon.png">
    <meta name="msapplication-TileColor" content="#FFFFFF">

    <!-- Resource Hints - Preload Critical Assets -->
    <link rel="preload" href="assets/css/vendors/bootstrap.css" as="style">
    <link rel="preload" href="assets/css/style.css" as="style">
    <link rel="preload" href="assets/css/iconly.css" as="style">
    
    <!-- Preconnect to required origins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Combined and Minified CSS -->
    <link rel="stylesheet" href="assets/css/vendors/bootstrap.css?v=1.0.0">
    <link rel="stylesheet" href="assets/css/iconly.css?v=1.0.0">
    <link rel="stylesheet" href="assets/css/vendors/slick.css?v=1.0.0">
    <link rel="stylesheet" href="assets/css/vendors/slick-theme.css?v=1.0.0">
    <link rel="stylesheet" href="assets/css/style.css?v=1.0.0">

    <!-- Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>

    <!-- Disable iOS telephone number detection -->
    <meta name="format-detection" content="telephone=no">
</head>