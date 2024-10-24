<script>
document.addEventListener('DOMContentLoaded', () => {
    // Function to fetch food listings from the server based on category
    function fetchFoodListings(category = "") {
        const tabContent = document.getElementById('pills-tabContent');
        tabContent.classList.remove('fade-in'); // Remove existing fade-in animation

        // AJAX request to fetch food listings
        fetch('fetch/shop.fetchFood.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    category: category // Send category parameter
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    setTimeout(() => {
                        displayFoodListings(data
                            .foods); // Call function to display the listings
                        tabContent.classList.add('fade-in'); // Apply fade-in animation
                    }, 300); // Adjust delay for fade-in effect
                } else {
                    console.error('Error fetching food listings:', data.message);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Function to display food listings dynamically in the layout format
    function displayFoodListings(foods) {
        const tabContent = document.getElementById('pills-tabContent');
        tabContent.innerHTML = ''; // Clear previous content

        const tabPane = document.createElement('div');
        tabPane.classList.add('tab-pane', 'fade', 'show', 'active');

        foods.forEach(food => {
            const productList = document.createElement('div');
            productList.classList.add('product-list-item1', 'media');

            // Template for each food item, updated to match the new design
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
                            <span class="info-text">Diet: ${food.diet_type_suitable}</span>
                            <span class="info-text">Health Goal: ${food.health_goal_suitable}</span>
                        </div>
                        <div class="rating-container">
                            <i class='bx bxs-star'></i>
                            <span class="rating-text">4.9 (10 Review)</span>
                        </div>
                        <div class="product-footer">
                            <span class="price-tag">PHP ${food.price} </span>
                             <span class="category-tag">${food.category}</span>

                            <span class="meal-type-tag">${food.meal_type}</span>
                            <button class="cart-button" onclick="addToCart()">
                                <i class="bx bx-cart-alt"></i>
                            </button>
                        </div>
                    </div>
                </div> </a>

            `;
            tabPane.appendChild(productList);
        });

        tabContent.appendChild(tabPane);
    }

    // Add event listeners to category tabs
    document.querySelectorAll('.nav-link').forEach(button => {
        button.addEventListener('click', function() {
            const category = this.innerText.trim(); // Get the category text
            // If the category is "All", fetch all products
            fetchFoodListings(category === "All" ? "" : category);
        });
    });

    // Call function to fetch all food listings when the page loads
    fetchFoodListings(); // Fetch all products by default
});
</script>