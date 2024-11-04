<?php
// Start the session
session_start();

// Destroy the session and unset session variables
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session itself

// Clear cookies (if any user-related cookies are set)
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, "/"); // Clear the user_id cookie
}

if (isset($_COOKIE['user_fname'])) {
    setcookie('user_fname', '', time() - 3600, "/"); // Clear the user_fname cookie
}

// Redirect to login page or homepage
header("Location: ../../../index.php"); // Replace with the actual path to your login or homepage
exit();
?>
