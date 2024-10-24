<?php include 'includes/header.php';

// Fetch all menu items from the database
$query = "SELECT food_id, food_name, description, price, meal_type, photo1 FROM food_listings WHERE available = 1";
$result = mysqli_query($conn, $query);

$menuItems = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Group items by category
        $menuItems['all'][] = $row;
        $menuItems[strtolower($row['meal_type'])][] = $row;  // Group by category like breakfast, lunch, etc.
    }
}

?>

<link rel="stylesheet" type="text/css" href="assets/css/menu_list.css" />

<body>

    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_homepage.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'includes/top_header.php'; ?>

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
                <?php if (!empty($menuItems['all'])): ?>
                <?php foreach ($menuItems['all'] as $item): ?>
                <div class="menu-item">
                    <img src="../../uploads/<?= htmlspecialchars($item['photo1']) ?>"
                        alt="<?= htmlspecialchars($item['food_name']) ?>" />
                    <div class="menu-info">
                        <h5><?= htmlspecialchars($item['food_name']) ?></h5>
                        <span class="badge"><?= htmlspecialchars($item['meal_type']) ?></span>
                        <div class="price">PHP <?= number_format($item['price'], 2) ?></div>
                    </div>
                    <div class="action-icons">
                        <button><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No menu items found.</p>
                <?php endif; ?>
            </div>

            <!-- Breakfast Tab -->
            <div class="tab-pane fade" id="breakfast">
                <?php if (!empty($menuItems['breakfast'])): ?>
                <?php foreach ($menuItems['breakfast'] as $item): ?>
                <div class="menu-item">
                    <img src="../../uploads/<?= htmlspecialchars($item['photo1']) ?>"
                        alt="<?= htmlspecialchars($item['food_name']) ?>" />
                    <div class="menu-info">
                        <h5><?= htmlspecialchars($item['food_name']) ?></h5>
                        <span class="badge"><?= htmlspecialchars($item['meal_type']) ?></span>
                        <div class="price">PHP <?= number_format($item['price'], 2) ?></div>
                    </div>
                    <div class="action-icons">
                        <button><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No breakfast items found.</p>
                <?php endif; ?>
            </div>

            <!-- Lunch Tab -->
            <div class="tab-pane fade" id="lunch">
                <?php if (!empty($menuItems['lunch'])): ?>
                <?php foreach ($menuItems['lunch'] as $item): ?>
                <div class="menu-item">
                    <img src="../../uploads/<?= htmlspecialchars($item['photo1']) ?>"
                        alt="<?= htmlspecialchars($item['food_name']) ?>" />
                    <div class="menu-info">
                        <h5><?= htmlspecialchars($item['food_name']) ?></h5>
                        <span class="badge"><?= htmlspecialchars($item['meal_type']) ?></span>
                        <div class="price">PHP <?= number_format($item['price'], 2) ?></div>
                    </div>
                    <div class="action-icons">
                        <button><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No lunch items found.</p>
                <?php endif; ?>
            </div>

            <!-- Dinner Tab -->
            <div class="tab-pane fade" id="dinner">
                <?php if (!empty($menuItems['dinner'])): ?>
                <?php foreach ($menuItems['dinner'] as $item): ?>
                <div class="menu-item">
                    <img src="../../uploads/<?= htmlspecialchars($item['photo1']) ?>"
                        alt="<?= htmlspecialchars($item['food_name']) ?>" />
                    <div class="menu-info">
                        <h5><?= htmlspecialchars($item['food_name']) ?></h5>
                        <span class="badge"><?= htmlspecialchars($item['meal_type']) ?></span>
                        <div class="price">PHP <?= number_format($item['price'], 2) ?></div>
                    </div>
                    <div class="action-icons">
                        <button><i class="fas fa-ellipsis-h"></i></button>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php else: ?>
                <p>No dinner items found.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Add New Product Button -->
        <div class="add-new pt-1">
            <a href="add_menu.php">+ Add New Product</a>
        </div>
    </main>

    <!-- Main End -->

    <!-- Footer Start -->

    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->

    <!-- Action Language Start -->
    <div class="action action-language offcanvas offcanvas-bottom" tabindex="-1" id="language"
        aria-labelledby="language">
        <div class="offcanvas-body small">
            <h2 class="m-b-title1 font-md">Select Language</h2>

            <ul class="list">
                <li>
                    <a href="javascript:void(0)" data-bs-dismiss="offcanvas" aria-label="Close"> <img
                            src="assets/icons/flag/us.svg" alt="us" /> English </a>
                </li>

            </ul>
        </div>
    </div>
    <!-- Action Language End -->

    <!-- Pwa Install App Popup Start -->

    <?php include 'includes/scripts.php'; ?>

</body>
<!-- Body End -->

</html>