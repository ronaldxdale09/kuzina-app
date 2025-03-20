<?php
// session_start(); 
// // Change this to your connection info.
// $DATABASE_HOST = 'localhost';
// $DATABASE_USER = 'root';
// $DATABASE_PASS = '';
// $DATABASE_NAME = 'kuzina_db';
// // Try and connect using the info above.
// $conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
// if ( mysqli_connect_errno() ) {
// 	// If there is an error with the connection, stop the script and display the error.
// 	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
// }




session_start(); 
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'u607598273_kroot';
$DATABASE_PASS = 'Aetherio@2023';
$DATABASE_NAME = 'u607598273_kuzina';
// Try and connect using the info above.
$conn = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


function isWebView() {
    if (isset($_GET['app'])) {
        return true;  // If URL has ?app parameter
    }
    
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    
    // Check for WebView indicators
    $isWebView = (
        strpos($userAgent, 'wv') !== false ||         // Android WebView
        strpos($userAgent, 'WebView') !== false ||    // Generic WebView
        isset($_SERVER['HTTP_X_REQUESTED_WITH'])      // App requests
    );
    
    return $isWebView;
}

// Determine if we're in localhost environment
$isLocalhost = ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1');

// If NOT a WebView (not from app), redirect appropriately based on environment
if (!isWebView()) {
    if ($isLocalhost) {
        header("Location: /kuzina-app/desktop.php");  // Local development path
    } else {
        header("Location: /desktop.php");  // Production path
    }
    exit;
}
?>