<!DOCTYPE html>
<!-- Html Start -->
<html lang="en">
  <!-- Head Start -->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Fastkart" />
    <meta name="keywords" content="Fastkart" />
    <meta name="author" content="Fastkart" />
    <link rel="manifest" href="./manifest.json" />
    <title>Fastkart PWA App</title>
    <link rel="icon" href="assets/images/favicon.png" type="image/x-icon" />
    <link rel="apple-touch-icon" href="assets/images/favicon.png" />
    <meta name="theme-color" content="#0baf9a" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="apple-mobile-web-app-title" content="Fastkart" />
    <meta name="msapplication-TileImage" content="assets/images/favicon.png" />
    <meta name="msapplication-TileColor" content="#FFFFFF" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" id="rtl-link" type="text/css" href="assets/css/vendors/bootstrap.css" />

    <!-- Iconly Icon css -->
    <link rel="stylesheet" type="text/css" href="assets/css/iconly.css" />

    <!-- Style css -->
    <link rel="stylesheet" id="change-link" type="text/css" href="assets/css/style.css" />
  </head>
  <!-- Head End -->

  <!-- Body Start -->
  <body>
    <!-- Skeleton loader Start -->
    <div class="skeleton-loader">
      <!-- Header Start -->
      <header class="header">
        <div class="logo-wrap">
          <i class="iconly-Category icli nav-bar"></i>
          <a href="homepage.php"> <img class="logo logo-w" src="assets/images/logo/logo-w.png" alt="logo" /></a
          ><a href="homepage.php"> <img class="logo" src="assets/images/logo/logo.png" alt="logo" /></a>
        </div>
        <div class="avatar-wrap">
          <span class="font-sm"><i class="iconly-Location icli font-xl"></i> Los Angeles</span>
          <a href="account.html"> <img class="avatar" src="assets/images/avatar/avatar.jpg" alt="avatar" /></a>
        </div>
      </header>
      <!-- Header End -->

      <!-- Main Start -->
      <div class="main-wrap error-404 mb-xxl">
        <!-- Banner Start -->
        <div class="banner-box">
          <img src="assets/images/banner/404.png" class="img-fluid" alt="404" />
        </div>
        <!-- Banner End -->

        <!-- Error Section Start -->
        <div class="error mb-large section-p-tb">
          <h2 class="font-lg">PAGE NOT FOUND</h2>
          <p class="content-color font-md sk-1"></p>
          <p class="content-color font-md sk-2"></p>
          <p class="content-color font-md sk-3"></p>
          <p class="content-color font-md sk-4"></p>
          <a href="javascript:void(0)" class="btn-solid"><span></span></a>
        </div>
        <!-- Error Section End -->
      </div>
      <!-- Main End -->
    </div>
    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <header class="header">
      <div class="logo-wrap">
        <i class="iconly-Category icli nav-bar"></i>
        <a href="homepage.php"> <img class="logo logo-w" src="assets/images/logo/logo-w.png" alt="logo" /></a><a href="homepage.php"> <img class="logo" src="assets/images/logo/logo.png" alt="logo" /></a>
      </div>
      <div class="avatar-wrap">
        <span class="font-sm"><i class="iconly-Location icli font-xl"></i> Los Angeles</span>
        <a href="account.html"> <img class="avatar" src="assets/images/avatar/avatar.jpg" alt="avatar" /></a>
      </div>
    </header>
    <!-- Header End -->

    <!-- Sidebar Start -->
    <a href="javascript:void(0)" class="overlay-sidebar"></a>
    <aside class="header-sidebar">
      <div class="wrap">
        <div class="user-panel">
          <div class="media">
            <a href="account.html"> <img src="assets/images/avatar/avatar.jpg" alt="avatar" /></a>
            <div class="media-body">
              <a href="account.html" class="title-color font-sm"
                >Andrea Joanne
                <span class="content-color font-xs">andreajoanne@gmail.com</span>
              </a>
            </div>
          </div>
        </div>

        <!-- Navigation Start -->
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
              <a href="pages-list.html" class="nav-link title-color font-sm">
                <i class="iconly-Paper icli"></i>
                <span>Fastkart Pages list</span>
              </a>
              <a class="arrow" href="pages-list.html"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="category-wide.html" class="nav-link title-color font-sm">
                <i class="iconly-Category icli"></i>
                <span>Shop by Category</span>
              </a>
              <a class="arrow" href="category-wide.html"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="order-history.html" class="nav-link title-color font-sm">
                <i class="iconly-Document icli"></i>
                <span>Orders</span>
              </a>
              <a class="arrow" href="order-history.html"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="wishlist.html" class="nav-link title-color font-sm">
                <i class="iconly-Heart icli"></i>
                <span>Your Wishlist</span>
              </a>
              <a class="arrow" href="wishlist.html"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="javascript:void(0)" data-bs-toggle="offcanvas" data-bs-target="#language" aria-controls="language" class="nav-link title-color font-sm">
                <img src="assets/icons/png/flags.png" alt="flag" />
                <span>Langauge</span>
              </a>
              <a class="arrow" href="javascript:void(0)"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="account.html" class="nav-link title-color font-sm">
                <i class="iconly-Add-User icli"></i>
                <span>Your Account</span>
              </a>
              <a class="arrow" href="account.html"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="notification.html" class="nav-link title-color font-sm">
                <i class="iconly-Notification icli"></i>
                <span>Notification</span>
              </a>
              <a class="arrow" href="notification.html"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="setting.html" class="nav-link title-color font-sm">
                <i class="iconly-Setting icli"></i>
                <span>Settings</span>
              </a>
              <a class="arrow" href="setting.html"><i data-feather="chevron-right"></i></a>
            </li>

            <li>
              <a href="javascript:void(0)" class="nav-link title-color font-sm">
                <i class="iconly-Graph icli"></i>
                <span>Dark</span>
              </a>

              <div class="dark-switch">
                <input id="darkButton" type="checkbox" />
                <span></span>
              </div>
            </li>

            <li>
              <a href="javascript:void(0)" class="nav-link title-color font-sm">
                <i class="iconly-Filter icli"></i>
                <span>RTL</span>
              </a>

              <div class="dark-switch">
                <input id="rtlButton" type="checkbox" />
                <span class="before-none"></span>
              </div>
            </li>
          </ul>
        </nav>
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
    <main class="main-wrap error-404 mb-xxl">
      <!-- Banner Start -->
      <div class="banner-box">
        <img src="assets/images/banner/404.png" class="img-fluid" alt="404" />
      </div>
      <!-- Banner End -->

      <!-- Error Section Start -->
      <section class="error mb-large">
        <h2 class="font-lg">PAGE NOT FOUND</h2>
        <p class="content-color font-md">We are sorry but the page you are looking for doesn't exist or has been removed. Please check back later or search again.</p>
        <a href="homepage.php" class="btn-solid">Back to Home</a>
      </section>
      <!-- Error Section End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <footer class="footer-wrap">
      <ul class="footer">
        <li class="footer-item">
          <a href="homepage.php" class="footer-link">
            <i class="iconly-Home icli"></i>
            <span>Home</span>
          </a>
        </li>
        <li class="footer-item">
          <a href="category-wide.html" class="footer-link">
            <i class="iconly-Category icli"></i>
            <span>Category</span>
          </a>
        </li>
        <li class="footer-item">
          <a href="search.html" class="footer-link">
            <i class="iconly-Search icli"></i>
            <span>Search</span>
          </a>
        </li>
        <li class="footer-item">
          <a href="offer.html" class="footer-link">
            <lord-icon class="icon" src="assets/icons/gift.json" trigger="loop" stroke="70" colors="primary:#ffffff,secondary:#ffffff"></lord-icon>
            <span class="offer">Offers</span>
          </a>
        </li>
        <li class="footer-item">
          <a href="cart.html" class="footer-link">
            <i class="iconly-Bag-2 icli"></i>
            <span>Cart</span>
          </a>
        </li>
      </ul>
    </footer>
    <!-- Footer End -->

    <!-- Action Language Start -->
    <div class="action action-language offcanvas offcanvas-bottom" tabindex="-1" id="language" aria-labelledby="language">
      <div class="offcanvas-body small">
        <h2 class="m-b-title1 font-md">Select Language</h2>
        <ul class="list">
          <li>
            <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img src="assets/icons/flag/us.svg" alt="us" /> English </a>
          </li>
          <li>
            <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img src="assets/icons/flag/in.svg" alt="us" />Indian </a>
          </li>
          <li>
            <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img src="assets/icons/flag/it.svg" alt="us" />Italian</a>
          </li>
          <li>
            <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img src="assets/icons/flag/tf.svg" alt="us" /> French</a>
          </li>
          <li>
            <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img src="assets/icons/flag/cn.svg" alt="us" /> Chines</a>
          </li>
        </ul>
      </div>
    </div>
    <!-- Action Language End -->

    <!-- jquery 3.6.0 -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap js -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Lord Icon -->
    <script src="assets/js/lord-icon-2.1.0.js"></script>

    <!-- Feather Icon -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Theme Setting js -->
    <script src="assets/js/theme-setting.js"></script>

    <!-- Script js -->
    <script src="assets/js/script.js"></script>
  </body>
  <!-- Body End -->
</html>
<!-- Html End -->
