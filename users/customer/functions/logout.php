<script>
function signOut() {
    // Clear session storage (or localStorage if used)
    sessionStorage.clear(); // You can also use localStorage.clear(); if needed.

    // Optionally, you can clear specific tokens or user data
    // sessionStorage.removeItem('token');
    // sessionStorage.removeItem('user');

    // Redirect the user to the login or home page
    window.location.href = '../../../index.php'; // Replace 'login.html' with the actual login or landing page
}

signOut();
</script>
<?php
// Destroy all session data
session_destroy();

exit();
?>
