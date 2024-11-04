<?php 
include 'includes/header.php';
include 'fetch/fetch.menulist.php';
?>

<link rel="stylesheet" type="text/css" href="assets/css/menu_list.css" />

<body>

    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_homepage.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->
    <a href="javascript:void(0)" class="overlay-sidebar"></a>
    <aside class="header-sidebar">
        <div class="wrap">
            <div class="user-panel">
                <div class="media">
                    <a href="account.html"> <img src="../../uploads/avatar/avatar.jpg" alt="avatar" /></a>
                    <div class="media-body">
                        <a href="account.html" class="title-color font-sm">Andrea Joanne
                            <span class="content-color font-xs">andreajoanne@gmail.com</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Navigation Start -->
            <?php include 'includes/navbar.php'; ?>
            <!-- Navigation End -->
        </div>

        <div class="contact-us">
            <span class="title-color">Contact Support</span>
            <p class="content-color font-xs">If you have any problem,queries or questions feel free to reach out</p>
            <a href="javascript:void(0)" class="btn-solid"> Contact Us </a>
        </div>
    </aside>
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
        <h3 class="text-center pt-4"><span>Menu List </span><span class="line"></span></h3>

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

        <!-- Add New Product Button -->
        <div class="add-new pt-1">
            <a href="add_menu.php">+ Add New Product</a>
        </div>
    </main>

    <!-- Main End -->
    <div id="removeModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Confirm Removal</h2>
            <p>Are you sure you want to remove the following item?</p>
            <div class="modal-item-info">
                <p><strong>Food ID:</strong> <span id="modalFoodId"></span></p>
                <p><strong>Food Name:</strong> <span id="modalFoodName"></span></p>
                <p><strong>Price:</strong> PHP <span id="modalFoodPrice"></span></p>
            </div>
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-confirm" onclick="confirmRemoval()">Confirm</button>
        </div>
    </div>

    <!-- Footer Start -->

    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->


    <!-- Pwa Install App Popup Start -->

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>