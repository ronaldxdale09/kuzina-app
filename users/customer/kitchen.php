<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/kitchen.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    <!-- Header Start -->
    <?php include 'navbar/secondary.navbar.php'; ?>
    <!-- Navigation Start -->
    <?php include 'includes/sidebar.php'; ?>
<body>
    <!-- Header with back navigation -->
    <header class="kitchen-profile">
        <div class="kitchen-header">
            <?php
            // Fetch kitchen details
            $kitchen_id = $_GET['id'] ?? null;
            $stmt = $conn->prepare("SELECT * FROM kitchens WHERE kitchen_id = ?");
            $stmt->bind_param("i", $kitchen_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $kitchen = $result->fetch_assoc();
            ?>
            <img src="../../uploads/profile/<?php echo htmlspecialchars($kitchen['photo']); ?>" alt="Kitchen Logo"
                class="kitchen-logo">
            <div class="kitchen-info">
                <h1><?php echo htmlspecialchars($kitchen['fname'] . ' ' . $kitchen['lname']); ?></h1>
                <div class="rating">
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star-half'></i>
                    <span>4.8 (1200+ ratings)</span>
                </div>
                <p><?php echo htmlspecialchars($kitchen['description']); ?></p>
            </div>
        </div>
    </header>

    <!-- Search Box -->
    <div class="search-box">
        <input type="text" id="searchInput" class="search-input" placeholder="Search menu items...">
        <button class="search-button" onclick="filterProducts()">
            <i class='bx bx-search'></i>
        </button>
    </div>

    <!-- Category Grid -->
    <div class="category-grid">
        <?php
        // Fetch unique categories from the database
        $categoryQuery = "SELECT DISTINCT category FROM food_listings WHERE kitchen_id = ? AND available = 1";
        $stmt = $conn->prepare($categoryQuery);
        $stmt->bind_param("i", $kitchen_id);
        $stmt->execute();
        $categoryResult = $stmt->get_result();
        
        // Add "All" category
        echo "<div class='category-item active' data-category='all'>
                <div class='category-emoji'>🍽️</div>
                <div class='category-name'>All</div>
              </div>";

        while ($category = $categoryResult->fetch_assoc()) {
            if (!empty($category['category'])) {
                $categoryName = htmlspecialchars($category['category']);
                $emoji = getCategoryEmoji($categoryName); // Helper function to get emoji
                echo "<div class='category-item' data-category='" . htmlspecialchars($categoryName) . "'>
                        <div class='category-emoji'>$emoji</div>
                        <div class='category-name'>$categoryName</div>
                      </div>";
            }
        }

        function getCategoryEmoji($category) {
            $emojiMap = [
                'Grilled & Roasted' => '🍖',
                'Vegetable-Based Stews' => '🥘',
                'Healthy Stir-Fried' => '🥗',
                'Soup-Based Meal' => '🍜',
                'Pick Up' => '🛵',
                // Add more categories and emojis as needed
            ];
            return $emojiMap[$category] ?? '🍽️';
        }
        ?>
    </div>

    <!-- Products Section -->
    <section class="products-section">
        <div class="section-header">
            <h2>Our Menu</h2>
        </div>

        <div class="product-grid">
            <?php
            // Get products with their average ratings
            $stmt = $conn->prepare("
                SELECT f.*, 
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(r.review_id) as review_count
                FROM food_listings f
                LEFT JOIN reviews r ON f.kitchen_id = r.kitchen_id
                WHERE f.kitchen_id = ? AND f.available = 1
                GROUP BY f.food_id
            ");
            $stmt->bind_param("i", $kitchen_id);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($product = $result->fetch_assoc()) {
                $description = substr($product['description'], 0, 100); 
                if (strlen($product['description']) > 100) {
                    $description .= '...';
                }
                ?>
            <div class="product-card" data-category="<?php echo htmlspecialchars($product['category']); ?>"
                data-name="<?php echo htmlspecialchars($product['food_name']); ?>">
                <a href="product.php?prod=<?php echo $product['food_id']; ?>" class="product-link">
                    <img src="../../uploads/<?php echo htmlspecialchars($product['photo1']); ?>"
                        alt="<?php echo htmlspecialchars($product['food_name']); ?>" class="product-image">
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($product['food_name']); ?></h3>
                        <span class="product-type"><?php echo htmlspecialchars($product['category']); ?></span>
                        <p class="product-description"><?php echo htmlspecialchars($description); ?></p>
                        <div class="price-rating-container">
                            <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                            <div class="product-rating">
                                <?php
                                    $rating = round($product['avg_rating'], 1);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $rating) {
                                            echo '<i class="bx bxs-star"></i>';
                                        } elseif ($i - 0.5 <= $rating) {
                                            echo '<i class="bx bxs-star-half"></i>';
                                        } else {
                                            echo '<i class="bx bx-star"></i>';
                                        }
                                    }
                                    ?>
                                <span class="rating-count">
                                    <?php 
                                        if ($product['review_count'] > 0) {
                                            echo number_format($rating, 1) . " (" . $product['review_count'] . ")";
                                        } else {
                                            echo "No reviews";
                                        }
                                        ?>
                                </span>
                            </div>
                        </div>
                        <button class="add-to-cart" data-id="<?php echo $product['food_id']; ?>">
                            <i class='bx bx-cart'></i> View
                        </button>
                    </div>
                </a>
            </div>
            <?php
            }
            ?>
        </div>
    </section>


    <!-- Cart Popup -->
    <div id="cart-popup" class="cart-popup">
        <i class='bx bx-check-circle'></i>
        <span>Added to cart!</span>
    </div>


    <?php include 'includes/scripts.php'; ?>
    <script>
    // Category filtering
    const categoryItems = document.querySelectorAll('.category-item');
    const productCards = document.querySelectorAll('.product-card');
    let currentCategory = 'all';
    let currentSearch = '';

    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all categories
            categoryItems.forEach(cat => cat.classList.remove('active'));
            // Add active class to clicked category
            this.classList.add('active');

            currentCategory = this.dataset.category;
            filterProducts();
        });
    });

    // Search and filter functionality
    function filterProducts() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        currentSearch = searchTerm;

        let hasVisibleProducts = false;

        productCards.forEach(card => {
            const category = card.dataset.category;
            const name = card.dataset.name.toLowerCase();

            const matchesCategory = currentCategory === 'all' || category === currentCategory;
            const matchesSearch = name.includes(currentSearch);

            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
                hasVisibleProducts = true;
            } else {
                card.style.display = 'none';
            }
        });

        // Show no results message if needed
        const existingNoResults = document.querySelector('.no-results');
        if (existingNoResults) {
            existingNoResults.remove();
        }

        if (!hasVisibleProducts) {
            const noResults = document.createElement('div');
            noResults.className = 'no-results';
            noResults.innerHTML = '<i class="bx bx-search"></i><p>No items found</p>';
            document.querySelector('.product-grid').appendChild(noResults);
        }
    }

    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;

            // Send add to cart request
            fetch('functions/addCart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=add&food_id=${productId}&quantity=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showPopup();
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    });

    function showPopup() {
        const popup = document.getElementById('cart-popup');
        popup.classList.add('show');
        setTimeout(() => {
            popup.classList.remove('show');
        }, 2000);
    }
    </script>
</body>

</html>