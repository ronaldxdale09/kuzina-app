<?php include 'includes/header.php'; ?>
<!-- Body Start -->
<link rel="stylesheet" href="assets/css/shop.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<style>
    .section-header {
        text-align: center;
        margin-bottom: 20px;
        padding-top: 15px;
    }

    .section-title {
        color: #502121;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .section-subtitle {
        color: #666;
        font-size: 15px;
    }

    .results-counter {
        margin: 10px 0;
        color: #666;
        font-size: 14px;
    }

    .filter-group {
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .filter-group h6 {
        color: #502121;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .price-range {
        padding: 0 10px;
    }

    .price-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 5px;
        font-size: 14px;
        color: #666;
    }
</style>

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_shop.php'; ?>
    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/shop.navbar.php'; ?>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap shop-page mb-xxl">
        <!-- Catagories Tabs Start -->
        <ul class="nav nav-tab nav-pills custom-scroll-hidden" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="category-all-tab" data-bs-toggle="pill" type="button" role="tab">
                    All
                </button>
            </li>
            <?php
            // Fetch categories from database
            $category_sql = "SELECT category_id, name, icon FROM food_categories WHERE is_active = 1 ORDER BY name";
            $category_result = $conn->query($category_sql);

            if ($category_result && $category_result->num_rows > 0) {
                while ($category = $category_result->fetch_assoc()) {
                    echo '<li class="nav-item" role="presentation">';
                    echo '<button class="nav-link" id="category-' . $category['category_id'] . '-tab" data-bs-toggle="pill" type="button" role="tab">';
                    echo '<i class="' . htmlspecialchars($category['icon']) . '"></i> ';
                    echo htmlspecialchars($category['name']);
                    echo '</button>';
                    echo '</li>';
                }
            }
            ?>
        </ul>
        <!-- Catagories Tabs End -->

        <!-- Search Box Start -->
        <div class="search-box">
            <div>
                <i class="bx bx-search icli search"></i>
                <input class="form-control" type="search" id="searchInput" placeholder="Search foods, categories, or diets..." />
            </div>
            <button class="filter font-md" type="button" data-bs-toggle="offcanvas" data-bs-target="#filter">
                <i class='bx bx-filter-alt'></i>
            </button>
        </div>
        <!-- Food count indicator -->
        <div class="results-counter" id="resultsCounter"></div>

        <div id="loadingIndicator" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">
            <!-- Food listings will be loaded here -->
        </div>
        <!-- Tab Content End -->
    </main>
    <!-- Main End -->

    <!-- Footer Start -->
    <?php include 'components/shop.viewCart.php'; ?>

    <!-- Footer End -->

    <!-- Filter Offcanvas Start -->

    <!-- Filter Offcanvas Start -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="filter">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Options</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="filter-group">
                <h6>Meal Type</h6>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" value="Breakfast" id="breakfast-check" data-filter="meal-type">
                    <label class="form-check-label" for="breakfast-check">Breakfast</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" value="Lunch" id="lunch-check" data-filter="meal-type">
                    <label class="form-check-label" for="lunch-check">Lunch</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" value="Dinner" id="dinner-check" data-filter="meal-type">
                    <label class="form-check-label" for="dinner-check">Dinner</label>
                </div>
            </div>

            <div class="filter-group">
                <h6>Diet Type</h6>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" value="Vegetarian" id="vegetarian-check" data-filter="diet-type">
                    <label class="form-check-label" for="vegetarian-check">Vegetarian</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" value="Vegan" id="vegan-check" data-filter="diet-type">
                    <label class="form-check-label" for="vegan-check">Vegan</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" value="Keto" id="keto-check" data-filter="diet-type">
                    <label class="form-check-label" for="keto-check">Keto</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input filter-checkbox" type="checkbox" value="Balanced Diet" id="balanced-check" data-filter="diet-type">
                    <label class="form-check-label" for="balanced-check">Balanced Diet</label>
                </div>
            </div>

            <div class="filter-group">
                <h6>Price Range</h6>
                <div class="price-range">
                    <input type="range" class="form-range" id="priceRange" min="0" max="1000" step="50">
                    <div class="price-labels">
                        <span id="minPrice">₱0</span>
                        <span id="maxPrice">₱1000</span>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btnFilter w-100" id="applyFilters">Apply Filters</button>
                <button class="btn btn-outline-secondary w-100 mt-2" id="resetFilters">Reset Filters</button>
            </div>
        </div>
    </div>



    <!-- Filter Offcanvas End -->
    <?php include 'fetch/shop.foodlist.php'; ?>

    <?php include 'includes/scripts.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get search parameter from URL
            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('search');

            // If search parameter exists, populate the search input
            if (searchQuery) {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = searchQuery;

                    // Trigger search immediately - manual approach
                    // This approach works by directly setting currentFilters.search
                    if (window.currentFilters) {
                        window.currentFilters.search = searchQuery;
                        // Reset page to 1 before fetching
                        window.currentPage = 1;
                        // Call fetchFoodListings directly
                        window.fetchFoodListings(1, true);
                    } else {
                        // Alternative approach - trigger input event
                        const inputEvent = new Event('input', {
                            bubbles: true,
                            cancelable: true
                        });
                        searchInput.dispatchEvent(inputEvent);
                    }
                }
            }
        });
    </script>
</body>
<!-- Body End -->

</html>
<!-- Html End -->