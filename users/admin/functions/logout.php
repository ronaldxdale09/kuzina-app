<?php
// Start the session if not already started
session_start();

// Destroy the session and unset session variables
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session itself

// Clear admin-specific cookies
if (isset($_COOKIE['admin_id'])) {
    setcookie('admin_id', '', time() - 3600, "/");
}

if (isset($_COOKIE['admin_username'])) {
    setcookie('admin_username', '', time() - 3600, "/");
}

// Clear any other potential admin cookies
$admin_cookies = ['admin_role', 'admin_email', 'admin_last_login'];
foreach ($admin_cookies as $cookie) {
    if (isset($_COOKIE[$cookie])) {
        setcookie($cookie, '', time() - 3600, "/");
    }
}



// Clear all cookies by expiring them
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 3600, "/");
    }
}

// Ensure all output buffers are cleaned and closed
while (ob_get_level()) {
    ob_end_clean();
}

// Redirect to admin login page
header("Location: ../../../admin.php");
exit();
?>