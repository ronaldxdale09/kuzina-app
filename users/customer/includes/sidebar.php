<a href="javascript:void(0)" class="overlay-sidebar"></a>
<aside class="header-sidebar">
    <div class="wrap">
        <div class="user-panel">
            <div class="media">
                <a href="homepage.php"> <img src="assets/images/logo/logo49.png" alt="avatar" /></a>
                <div class="media-body">
                    <a href="homepage.php" class="title-color font-sm"><?php echo  $_COOKIE['user_fname'] ?>
                        <span class="content-color font-xs"><?php echo  $_COOKIE['user_email'] ?></span>
                    </a>
                </div>
            </div>
        </div>

        <nav class="navigation">
            <ul>
                <li>
                    <a href="homepage.php" class="nav-link title-color font-sm">
                        <i class="iconly-Home icli"></i>
                        <span>Home</span>
                    </a>
                    <a class="arrow" href="homepage.php"><i data-feather="chevron-right"></i></a>
                </li>
                <li>
                    <a href="education.php" class="nav-link title-color font-sm">
                        <i class="bx bxs-food-menu"></i> <span>Food Education</span>
                    </a>
                    <a class="arrow" href="education.php"><i data-feather="chevron-right"></i></a>
                </li>

                <li>
                    <a href="bug.report.php" class="nav-link title-color font-sm">
                        <i class="bx bx-bug"></i> <span>Report Bugs</span>
                    </a>
                    <a class="arrow" href="bug.report.php">
                        <i data-feather="chevron-right"></i>
                    </a>
                </li>


                <li>
                    <a href="shop.php" class="nav-link title-color font-sm">
                        <i class="iconly-Category icli"></i>
                        <span>Shop by Category</span>
                    </a>
                    <a class="arrow" href="shop.php"><i data-feather="chevron-right"></i></a>
                </li>

                <li>
                    <a href="order-history.php" data-bs-toggle="offcanvas" data-bs-target="#underDev" class="nav-link title-color font-sm">
                        <i class="iconly-Document icli"></i>
                        <span>Orders</span>
                    </a>
                    <a class="arrow" href="order-history.php"><i data-feather="chevron-right"></i></a>
                </li>





                <li>
                    <a href="user.profile.php" class="nav-link title-color font-sm">
                        <i class="iconly-Setting icli"></i>
                        <span>Settings</span>
                    </a>
                    <a class="arrow" href="user.profile.php"><i data-feather="chevron-right"></i></a>
                </li>

            </ul>
            <button class="log-out" data-bs-toggle="offcanvas" data-bs-target="#confirmation"
                aria-controls="confirmation"><i class="iconly-Logout icli"></i>Sign Out</button>
        </nav>

    </div>

</aside>
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