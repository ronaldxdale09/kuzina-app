<?php
include 'includes/header.php';
include 'fetch/fetch.menulist.php';
?>

<link rel="stylesheet" type="text/css" href="assets/css/menu_list.css" />

<body>

    <!-- Skeleton loader Start -->
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_menulist.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->
    <a href="javascript:void(0)" class="overlay-sidebar"></a>

    <!-- Sidebar End -->

    <!-- Main Start -->
    <!-- Main Start -->
    <main class="main-wrap index-page mb-xxl">
        <ul class="nav nav-tabs pt-2" id="menuTabs">
            <li class="nav-item">
                <a class="nav-link active" href="#all" data-bs-toggle="tab">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#breakfast" data-bs-toggle="tab">Breakfast</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#lunch" data-bs-toggle="tab">Lunch</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#dinner" data-bs-toggle="tab">Dinner</a>
            </li>
        </ul>
        <div class="header-content">
            <h3>Menu List</h3>
    
            <a href="add_menu.php" class="add-new-btn">
                <i class='bx bx-plus-circle'></i>
                Add New Product
            </a>
        </div>
                <!-- Add search box -->
                <div class="search-box">
                <input type="text" id="searchFood" placeholder="Search food items...">
                <i class='bx bx-search'></i>
            </div>
        <!-- Tab Content -->
        <div class="tab-content container menu-list">
            <!-- All Menu Items -->
            <div class="tab-pane fade show active" id="all">
                <?php renderMenuItems($menuItems['all']); ?>
            </div>

            <!-- Breakfast Tab -->
            <div class="tab-pane fade" id="breakfast">
                <?php renderMenuItems($menuItems['breakfast']); ?>
            </div>

            <!-- Lunch Tab -->
            <div class="tab-pane fade" id="lunch">
                <?php renderMenuItems($menuItems['lunch']); ?>
            </div>

            <!-- Dinner Tab -->
            <div class="tab-pane fade" id="dinner">
                <?php renderMenuItems($menuItems['dinner']); ?>
            </div>
        </div>


    </main>

   
    <!-- Footer Start -->

    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->


    <!-- Pwa Install App Popup Start -->

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>