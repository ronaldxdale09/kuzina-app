<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" href="assets/css/kitchen.css"> <!-- Link to the new CSS file -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<body>
    <!-- Header Start -->
    <?php include 'navbar/secondary.navbar.php'; ?>
    <!-- Navigation Start -->
    <?php include 'includes/sidebar.php'; ?>

    <?php
    // Fetch kitchen details
    $kitchen_id = $_GET['id'] ?? null;
    $stmt = $conn->prepare("SELECT k.*, ua.latitude, ua.longitude 
                           FROM kitchens k 
                           LEFT JOIN user_addresses ua ON k.kitchen_id = ua.user_id AND ua.user_type = 'kitchen' 
                           WHERE k.kitchen_id = ?");
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kitchen = $result->fetch_assoc();

    // Fetch kitchen average rating
    $ratingStmt = $conn->prepare("
        SELECT AVG(rating) as avg_rating, COUNT(review_id) as review_count
        FROM reviews
        WHERE kitchen_id = ?
    ");
    $ratingStmt->bind_param("i", $kitchen_id);
    $ratingStmt->execute();
    $ratingResult = $ratingStmt->get_result();
    $ratingData = $ratingResult->fetch_assoc();
    $avgRating = number_format($ratingData['avg_rating'] ?? 0, 1);
    $reviewCount = $ratingData['review_count'] ?? 0;
    ?>

    <!-- Kitchen Hero Section -->
    <div class="kitchen-hero">
        <div class="hero-bg"></div>
        <div class="kitchen-profile">
            <div class="kitchen-header">
                <img src="../../uploads/kitchen_photos/<?php echo htmlspecialchars($kitchen['photo']); ?>" alt="<?php echo htmlspecialchars($kitchen['fname'] . ' ' . $kitchen['lname']); ?>" class="kitchen-logo">
                
                <div class="kitchen-info">
                    <h1><?php echo htmlspecialchars($kitchen['fname'] . ' ' . $kitchen['lname']); ?></h1>
                    
                    <div class="kitchen-stats">
                        <div class="rating">
                            <?php
                            // Display stars based on rating
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $avgRating) {
                                    echo '<i class="bx bxs-star"></i>';
                                } elseif ($i - 0.5 <= $avgRating) {
                                    echo '<i class="bx bxs-star-half"></i>';
                                } else {
                                    echo '<i class="bx bx-star"></i>';
                                }
                            }
                            ?>
                            <span><?php echo $avgRating; ?> (<?php echo $reviewCount; ?> reviews)</span>
                        </div>
                        
                        <div class="delivery-time">
                            <i class='bx bx-time'></i>
                            <span>30-45 min</span>
                        </div>
                    </div>
                    
                    <p class="kitchen-description"><?php echo htmlspecialchars($kitchen['description']); ?></p>
                    
                    <div class="kitchen-meta">
                        <div class="kitchen-meta-item">
                            <i class='bx bx-map'></i>
                            <span><?php echo htmlspecialchars($kitchen['address'] ?? 'Location not specified'); ?></span>
                        </div>
                        
                        <div class="kitchen-meta-item">
                            <i class='bx bx-phone'></i>
                            <span><?php echo htmlspecialchars($kitchen['phone']); ?></span>
                        </div>
                    </div>
                    
                    <div class="kitchen-actions">
                        <button class="action-btn primary" id="contactBtn">
                            <i class='bx bx-message-square-detail'></i>
                            <span>Contact</span>
                        </button>
                        
                        <button class="action-btn secondary" id="shareBtn">
                            <i class='bx bx-share-alt'></i>
                            <span>Share</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="search-container">
        <div class="search-box">
            <input type="text" id="searchInput" class="search-input" placeholder="Search menu items...">
            <button class="search-button" onclick="filterProducts()">
                <i class='bx bx-search'></i>
            </button>
        </div>
    </div>

    <!-- Category Scroll -->
    <div class="category-container">
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

            // Add "Popular" category
            echo "<div class='category-item' data-category='popular'>
                    <div class='category-emoji'>🔥</div>
                    <div class='category-name'>Popular</div>
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
                    'Delivery' => '🚚',
                    // Add more categories and emojis as needed
                ];
                return $emojiMap[$category] ?? '🍽️';
            }
            ?>
        </div>
    </div>

    <!-- Products Section -->
    <section class="products-section">
        <div class="section-header">
            <h2>Our Menu</h2>
            <div class="section-filter">
                <span>Sort by:</span>
                <select id="sortSelect" onchange="sortProducts()">
                    <option value="popular">Popular</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="rating">Rating</option>
                </select>
            </div>
        </div>

        <div class="product-grid">
            <?php
            // Get products with their average ratings
            $stmt = $conn->prepare("
                SELECT f.*, 
                    COALESCE(AVG(r.rating), 0) as avg_rating,
                    COUNT(r.review_id) as review_count
                FROM food_listings f
                LEFT JOIN reviews r ON f.food_id = r.food_id
                WHERE f.kitchen_id = ? AND f.available = 1 AND f.isApproved = 1
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
                $rating = round($product['avg_rating'], 1);
                $isPopular = $product['review_count'] > 2 && $rating >= 4.0; // Example criteria for "popular"
                ?>
                <div class="product-card" 
                     data-category="<?php echo htmlspecialchars($product['category']); ?>"
                     data-name="<?php echo htmlspecialchars($product['food_name']); ?>"
                     data-price="<?php echo $product['price']; ?>"
                     data-rating="<?php echo $rating; ?>"
                     data-popular="<?php echo $isPopular ? '1' : '0'; ?>">
                    <a href="product.php?prod=<?php echo $product['food_id']; ?>" class="product-link">
                        <div class="product-image-container">
                            <img src="../../uploads/<?php echo htmlspecialchars($product['photo1']); ?>"
                                alt="<?php echo htmlspecialchars($product['food_name']); ?>" class="product-image">
                            <?php if ($isPopular): ?>
                                <div class="product-badge">Popular</div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['food_name']); ?></h3>
                            <span class="product-type"><?php echo htmlspecialchars($product['category']); ?></span>
                            <p class="product-description"><?php echo htmlspecialchars($description); ?></p>
                            
                            <div class="product-meta">
                                <div class="price-rating-container">
                                    <div class="product-price">₱<?php echo number_format($product['price'], 2); ?></div>
                                    <div class="product-rating">
                                        <?php
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
                                                echo $product['review_count'];
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <button class="view-product-btn" data-id="<?php echo $product['food_id']; ?>">
                                    <i class='bx bx-show'></i> View Item
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            <?php
            }
            
            if ($result->num_rows === 0) {
                echo '<div class="no-results"><i class="bx bx-dish"></i><p>No menu items available</p></div>';
            }
            ?>
        </div>
    </section>

    <!-- Cart Popup -->
    <div id="cart-popup" class="cart-popup">
        <i class='bx bx-check-circle'></i>
        <span>Added to cart!</span>
    </div>

    <!-- Contact Modal (to be implemented) -->
    <div id="contactModal" class="modal" style="display: none;">
        <!-- Modal content -->
    </div>

    <!-- Share Modal (to be implemented) -->
    <div id="shareModal" class="modal" style="display: none;">
        <!-- Modal content -->
    </div>

    <?php include 'includes/scripts.php'; ?>
    <script>
  // Enhanced filtering and search functionality with fuzzy matching
// Advanced search and filter functionality with performance optimizations
document.addEventListener('DOMContentLoaded', function() {
    // Get DOM elements for better performance (avoid repetitive queries)
    const categoryItems = document.querySelectorAll('.category-item');
    const productCards = document.querySelectorAll('.product-card');
    const productGrid = document.querySelector('.product-grid');
    const searchInput = document.getElementById('searchInput');
    const sortSelect = document.getElementById('sortSelect');
    
    // State variables
    let currentCategory = 'all';
    let currentSearch = '';
    let currentSort = 'popular';
    let productCache = Array.from(productCards); // Cache products for faster operations
    
    // Add event listeners
    categoryItems.forEach(item => {
        item.addEventListener('click', function() {
            // Toggle active class efficiently
            document.querySelector('.category-item.active').classList.remove('active');
            this.classList.add('active');
            
            currentCategory = this.dataset.category;
            filterAndDisplayProducts();
        });
    });
    
    // Debounced search for better performance
    searchInput.addEventListener('input', debounce(function() {
        currentSearch = this.value.toLowerCase().trim();
        filterAndDisplayProducts();
    }, 300));
    
    sortSelect.addEventListener('change', function() {
        currentSort = this.value;
        sortAndDisplayProducts();
    });
    
    // Initialize the view
    filterAndDisplayProducts();
    
    /**
     * Main function to filter products by search term and category
     * Uses fuzzy matching for better search results
     */
    function filterAndDisplayProducts() {
        let visibleProducts = [];
        
        // Filter products based on category and search criteria
        productCache.forEach(card => {
            const category = card.dataset.category;
            const isPopular = card.dataset.popular === '1';
            const name = card.dataset.name.toLowerCase();
            const description = card.querySelector('.product-description')?.textContent.toLowerCase() || '';
            
            // Category filtering
            let matchesCategory = currentCategory === 'all' || 
                                  category === currentCategory || 
                                  (currentCategory === 'popular' && isPopular);
            
            // Enhanced search with fuzzy matching
            let matchesSearch = true;
            if (currentSearch) {
                // Split search into words for better matching
                const searchTerms = currentSearch.split(/\s+/);
                matchesSearch = searchTerms.every(term => 
                    name.includes(term) || 
                    description.includes(term) ||
                    // Fuzzy match - check if term is at least 60% similar to any word in name
                    name.split(/\s+/).some(word => calculateSimilarity(word, term) > 0.6)
                );
            }
            
            // Show or hide based on filters
            if (matchesCategory && matchesSearch) {
                card.style.display = 'block';
                visibleProducts.push(card);
            } else {
                card.style.display = 'none';
            }
        });
        
        // Show/hide "no results" message
        updateNoResultsMessage(visibleProducts.length === 0);
        
        // Sort the visible products
        if (visibleProducts.length > 0) {
            sortProducts(visibleProducts);
        }
    }
    
    /**
     * Sort products based on selected criteria and update the DOM
     */
    function sortProducts(products) {
        products.sort((a, b) => {
            switch (currentSort) {
                case 'price-low':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-high':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'rating':
                    return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                case 'popular':
                default:
                    // Sort by popular first, then rating for tie-breakers
                    if (b.dataset.popular !== a.dataset.popular) {
                        return parseInt(b.dataset.popular) - parseInt(a.dataset.popular);
                    }
                    const ratingDiff = parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
                    // If ratings are close, sort by price as a second tie-breaker
                    return Math.abs(ratingDiff) < 0.3 ? 
                        parseFloat(a.dataset.price) - parseFloat(b.dataset.price) : 
                        ratingDiff;
            }
        });
        
        // Efficient DOM update: document fragment for better performance
        const fragment = document.createDocumentFragment();
        products.forEach(product => fragment.appendChild(product));
        
        // Clear and repopulate the grid
        const noResults = productGrid.querySelector('.no-results');
        if (noResults) productGrid.removeChild(noResults);
        
        productGrid.innerHTML = '';
        productGrid.appendChild(fragment);
    }
    
    /**
     * Efficiently update the no results message
     */
    function updateNoResultsMessage(shouldShow) {
        let noResults = productGrid.querySelector('.no-results');
        
        if (shouldShow) {
            if (!noResults) {
                noResults = document.createElement('div');
                noResults.className = 'no-results';
                noResults.innerHTML = `
                    <i class="bx bx-search"></i>
                    <p>No items found matching "${currentSearch}" in ${
                        currentCategory === 'all' ? 'all categories' : 
                        currentCategory === 'popular' ? 'popular items' : 
                        'category "' + currentCategory + '"'
                    }</p>
                    <button class="reset-search">Clear filters</button>
                `;
                noResults.querySelector('.reset-search').addEventListener('click', resetFilters);
                productGrid.appendChild(noResults);
            }
        } else if (noResults) {
            productGrid.removeChild(noResults);
        }
    }
    
    /**
     * Reset all filters to default state
     */
    function resetFilters() {
        // Reset category
        document.querySelector('.category-item.active').classList.remove('active');
        document.querySelector('[data-category="all"]').classList.add('active');
        currentCategory = 'all';
        
        // Reset search
        searchInput.value = '';
        currentSearch = '';
        
        // Reset sort
        sortSelect.value = 'popular';
        currentSort = 'popular';
        
        // Apply filters
        filterAndDisplayProducts();
    }
    
    /**
     * Calculate similarity between two strings (simple Levenshtein distance ratio)
     * Returns a value between 0 (completely different) and 1 (identical)
     */
    function calculateSimilarity(str1, str2) {
        if (!str1 || !str2) return 0;
        if (str1 === str2) return 1;
        
        // Basic implementation - can be replaced with a more sophisticated algorithm
        const longer = str1.length >= str2.length ? str1 : str2;
        const shorter = str1.length >= str2.length ? str2 : str1;
        
        // Simple character overlap check
        let matched = 0;
        for (let i = 0; i < shorter.length; i++) {
            if (longer.includes(shorter[i])) {
                matched++;
            }
        }
        
        return matched / longer.length;
    }
    
    /**
     * Debounce function to prevent excessive function calls
     */
    function debounce(func, delay) {
        let timeout;
        return function() {
            const context = this;
            const args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(context, args), delay);
        };
    }
    
    /**
     * Sort and display products without refiltering
     */
    function sortAndDisplayProducts() {
        const visibleProducts = Array.from(document.querySelectorAll('.product-card:not([style*="display: none"])'));
        sortProducts(visibleProducts);
    }
});

// Enhanced contact functionality
document.getElementById('contactBtn').addEventListener('click', function() {
    const kitchenId = new URLSearchParams(window.location.search).get('id');
    window.location.href = `messenger.php?kitchen_id=${kitchenId}`;
});

// Enhanced sharing functionality with fallback
document.getElementById('shareBtn').addEventListener('click', function() {
    const title = document.querySelector('.kitchen-info h1').textContent.trim();
    const description = document.querySelector('.kitchen-description').textContent.trim();
    
    if (navigator.share) {
        navigator.share({
            title: title,
            text: description,
            url: window.location.href,
        }).catch(error => console.error('Error sharing:', error));
    } else {
        // Fallback for browsers that don't support Web Share API
        const shareModal = document.createElement('div');
        shareModal.className = 'share-modal modal';
        shareModal.innerHTML = `
            <div class="modal-content">
                <span class="close-modal">&times;</span>
                <h3>Share this kitchen</h3>
                <div class="share-options">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}" target="_blank" class="share-option facebook">
                        <i class='bx bxl-facebook'></i>
                        <span>Facebook</span>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent(title)}&url=${encodeURIComponent(window.location.href)}" target="_blank" class="share-option twitter">
                        <i class='bx bxl-twitter'></i>
                        <span>Twitter</span>
                    </a>
                    <a href="https://wa.me/?text=${encodeURIComponent(title + ' - ' + window.location.href)}" target="_blank" class="share-option whatsapp">
                        <i class='bx bxl-whatsapp'></i>
                        <span>WhatsApp</span>
                    </a>
                    <button class="share-option copy" onclick="copyToClipboard('${window.location.href}')">
                        <i class='bx bx-link'></i>
                        <span>Copy Link</span>
                    </button>
                </div>
            </div>
        `;
        
        document.body.appendChild(shareModal);
        shareModal.style.display = 'block';
        
        // Close modal functionality
        shareModal.querySelector('.close-modal').addEventListener('click', function() {
            shareModal.style.display = 'none';
            setTimeout(() => shareModal.remove(), 300);
        });
        
        // Clicking outside closes modal
        window.addEventListener('click', function(event) {
            if (event.target === shareModal) {
                shareModal.style.display = 'none';
                setTimeout(() => shareModal.remove(), 300);
            }
        });
    }
});

// Helper function for copy to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Link copied to clipboard');
        }).catch(err => {
            console.error('Could not copy text: ', err);
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        
        try {
            document.execCommand('copy');
            showToast('Link copied to clipboard');
        } catch (err) {
            console.error('Could not copy text: ', err);
        }
        
        document.body.removeChild(textArea);
    }
}

// Helper function to show toast messages
function showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => toast.classList.add('show'), 10);
    
    // Automatically remove after 3 seconds
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}
    </script>
</body>

</html>