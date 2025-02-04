<script>
document.addEventListener('DOMContentLoaded', () => {
    let currentFoods = []; // Store current foods for filtering

    // Function to fetch food listings
    function fetchFoodListings(category = "", searchTerm = "") {
        const tabContent = document.getElementById('pills-tabContent');
        tabContent.classList.remove('fade-in');

        fetch('fetch/shop.fetchFood.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                category: category,
                search: searchTerm
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                currentFoods = data.foods;
                setTimeout(() => {
                    displayFoodListings(currentFoods);
                    tabContent.classList.add('fade-in');
                }, 300);
            } else {
                console.error('Error fetching food listings:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Function to display food listings
    function displayFoodListings(foods) {
        const tabContent = document.getElementById('pills-tabContent');
        tabContent.innerHTML = '';

        if (foods.length === 0) {
            tabContent.innerHTML = `
                <div class="no-results">
                    <i class='bx bx-search-alt' style="font-size: 48px; color: #666;"></i>
                    <p>No food items found</p>
                </div>
            `;
            return;
        }

        const tabPane = document.createElement('div');
        tabPane.classList.add('tab-pane', 'fade', 'show', 'active');

        foods.forEach(food => {
            const productList = document.createElement('div');
            productList.classList.add('product-list-item1', 'media');

            // Format the diet types and health goals
            const dietTypes = food.diet_type_suitable ? food.diet_type_suitable.split(',')[0] : 'N/A';
            const healthGoals = food.health_goal_suitable ? food.health_goal_suitable.split(',')[0] : 'N/A';

            productList.innerHTML = `
                <div class="product-list-item">
                    <a href="product.php?prod=${food.food_id}" class="product-image-link">
                        <img src="../../uploads/${food.photo1}" alt="${food.food_name}" class="product-image"/>
                    </a>
                    <div class="product-details">
                        <div class="product-header">
                            <a href="product.php?prod=${food.food_id}" class="product-name">${food.food_name}</a>
                        </div>
                        <div class="product-info">
                            <span class="info-text">Diet: ${dietTypes}</span>
                            <span class="info-text">Health Goal: ${healthGoals}</span>
                        </div>
                        <div class="rating-container">
                            <i class='bx bxs-star'></i>
                            <span class="rating-text">4.9 (10 Review)</span>
                        </div>
                        <div class="product-footer">
                            <span class="price-tag">₱${parseFloat(food.price).toFixed(2)}</span>
                            <span class="category-tag">${food.category_name || food.category}</span>
                            <span class="meal-type-tag">${food.meal_type}</span>
                            <button class="cart-button" onclick="addToCart(${food.food_id})">
                                <i class="bx bx-cart-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            tabPane.appendChild(productList);
        });

        tabContent.appendChild(tabPane);
    }

    // Search functionality
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;

    searchInput.addEventListener('input', (e) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            const activeCategory = document.querySelector('.nav-link.active').textContent.trim();
            fetchFoodListings(
                activeCategory === "All" ? "" : activeCategory,
                e.target.value
            );
        }, 300);
    });

    // Category tab listeners
    document.querySelectorAll('.nav-link').forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            document.querySelectorAll('.nav-link').forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const category = this.textContent.trim();
            fetchFoodListings(
                category === "All" ? "" : category,
                searchInput.value
            );
        });
    });

    // Initial fetch
    fetchFoodListings();
});
</script>

<style>
.no-results {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.no-results i {
    margin-bottom: 15px;
}

.no-results p {
    font-size: 16px;
}

.product-list-item {
    margin-bottom: 20px;
    padding: 15px;
    border-radius: 10px;
    background: white;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.price-tag {
    font-weight: bold;
    color: #502121;
}

.category-tag, .meal-type-tag {
    font-size: 0.9em;
    padding: 3px 8px;
    border-radius: 4px;
    background: #f0f0f0;
    color: #666;
}
</style>