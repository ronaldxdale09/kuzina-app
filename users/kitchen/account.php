<?php include 'includes/header.php'; ?>

<!-- Head End -->

<!-- Body Start -->

<body>
    <!-- Header Start -->
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();">
                <i class="iconly-Arrow-Left-Square icli"></i>
            </a>
            <h1 class="title-color font-md">Accounts</h1>
        </div>
        <div class="avatar-wrap">
            <a href="homepage.php">
                <i class="iconly-Home icli"></i>
            </a>
        </div>
    </header>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap account-page mb-xxl">
        <div class="account-wrap section-b-t">
            <div class="user-panel">
                <div class="media">
                    <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev" > <img src="assets/images/logo/logo49.png" alt="avatar" /></a>
                    <div class="media-body">
                        <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev"  class="title-color"><?php //echo  $_COOKIE['user_fname']?>
                            <span class="content-color font-sm"><?php //echo  $_COOKIE['user_email']?></span>
                        </a>
                    </div>
                </div>
            </div>


            <!-- Navigation Start -->
            <ul class="navigation">
                <li>
                    <a href="homepage.php" class="nav-link title-color font-sm">
                        <i class="iconly-Home icli"></i>
                        <span>Home</span>
                    </a>
                    <a href="homepage.php" class="arrow"><i data-feather="chevron-right"></i></a>
                </li>



                <li>
                    <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev"  class="nav-link title-color font-sm">
                        <i class="iconly-Document icli"></i>
                        <span>Orders</span>
                    </a>
                    <a href="#" class="arrow"><i data-feather="chevron-right"></i></a>
                </li>

                <li>
                    <a href="#"  data-bs-toggle="offcanvas" data-bs-target="#underDev" class="nav-link title-color font-sm">
                        <i class="iconly-Heart icli"></i>
                        <span>Your Wishlist</span>
                    </a>
                    <a href="#" class="arrow"><i data-feather="chevron-right"></i></a>
                </li>



                <li>
                    <a href="#"data-bs-toggle="offcanvas" data-bs-target="#underDev" class="nav-link title-color font-sm">
                        <i class="iconly-Notification icli"></i>
                        <span>Notification</span>
                    </a>
                    <a href="#" class="arrow"><i data-feather="chevron-right"></i></a>
                </li>

                <li>
                    <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev"  class="nav-link title-color font-sm">
                        <i class="iconly-Setting icli"></i>
                        <span>Settings</span>
                    </a>
                    <a href="#" class="arrow"><i data-feather="chevron-right"></i></a>
                </li>

            </ul>
            <!-- Navigation End -->
            <button class="log-out" data-bs-toggle="offcanvas" data-bs-target="#confirmation"
                aria-controls="confirmation"><i class="iconly-Logout icli"></i>Sign Out</button>
        </div>
    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <?php include 'includes/appbar.php'; ?>

    <!-- Footer End -->


    <!-- Action confirmation Start -->
    <div class="action action-confirmation offcanvas offcanvas-bottom" tabindex="-1" id="confirmation"
        aria-labelledby="confirmation">
        <div class="offcanvas-body small">
            <div class="confirmation-box">
                <h2>Are You Sure?</h2>
                <p class="font-sm content-color">The permission for the use/group, preview is inherited from the object,
                    Modifiying it for this object will create a new permission for this object</p>
                <div class="btn-box">
                    <button class="btn-outline" data-bs-dismiss="offcanvas" aria-label="Close">Cancel</button>
                    <a href="functions/logout.php" class="btn-solid d-block" data-bs-dismiss="offcanvas"
                        aria-label="Close">Sign Out</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Action Confirmation End -->

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>
<!-- Html End -->