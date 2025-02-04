<!DOCTYPE html>
<html lang="en">
<?php 
// Database connection
include '../../connection/db.php';


// Check if the 'user_id' cookie is set and not empty
if (!isset($_COOKIE['user_id']) || empty($_COOKIE['user_id'])) {
    // Redirect to login.php
    header('Location: ../../index.php');
    exit(); // Stop further execution
}

// If the cookie is set, proceed with the rest of the script
$customer_id = $_COOKIE['user_id'];

// Cache headers
header('Cache-Control: public, max-age=31536000');
header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
header('Vary: Accept-Encoding');
?>

<head>
    <!-- Primary Meta Tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="description" content="Kuzina - Your Food Delivery Partner">
    <meta name="keywords" content="Kuzina, food delivery, restaurant">
    <meta name="author" content="Kuzina">
    
    <!-- Browser Caching -->
    <meta http-equiv="Cache-Control" content="max-age=31536000">
    
    <!-- Title -->
    <title>Kuzina</title>

    <!-- Favicon -->
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon">
    
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