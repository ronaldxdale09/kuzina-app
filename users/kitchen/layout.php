<?php include 'includes/header.php'; ?>

<style>
/* Add this to your homepage.css or create a new CSS file */
.footer-wrap {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #fff;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
}

.main-wrap {
    padding-bottom: 70px;
    /* Adjust this value based on your footer height */
}

/* Add smooth transitions */
.footer {
    transition: all 0.3s ease;
}

/* Active state styling */
.footer-item.active .footer-link {
    color: var(--theme-color, #007bff);
}

/* Hide default browser loading indicator */
body {
    scroll-behavior: smooth;
}
</style>
<?php
$content = 'pages/homepage_content.php';
include 'layout.php';
?>


<body>
    <?php
    // Check if kitchen user is logged in and retrieve kitchen ID
    $kitchen_id = $_COOKIE['kitchen_id'] ?? null;
    if (!$kitchen_id) {
        header("Location: ../../kitchen.php");
        exit();
    }

    // Query to check if the kitchen is approved
    $stmt = $conn->prepare("SELECT isApproved FROM kitchens WHERE kitchen_id = ? AND isApproved = 1");
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $stmt->store_result();

    // Redirect if the kitchen is not approved
    if ($stmt->num_rows === 0) {
        header("Location: isApproved.php");
        exit();
    }
    $stmt->close();
    ?>

    <!-- Skeleton loader -->
    <?php include 'skeleton/sk_homepage.php'; ?>

    <!-- Navigation -->
    <?php include 'navbar/main.navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-wrap">
        <?php include $content; // This will be set by each page ?>
    </main>

    <!-- Fixed App Bar -->
    <?php include 'includes/appbar.php'; ?>

    <!-- Scripts -->
    <?php include 'includes/scripts.php'; ?>
</body>

</html>