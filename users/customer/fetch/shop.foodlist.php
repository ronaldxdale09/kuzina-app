<script>
    document.addEventListener('DOMContentLoaded', () => {
        let currentFoods = []; // Store current foods for filtering
        let currentPage = 1;
        let totalItems = 0;
        let perPage = 10;
        let currentFilters = {
            category: "",
            search: "",
            mealTypes: [],
            dietTypes: [],
            minPrice: 0,
            maxPrice: 1000
        };
        let isLoading = false;

        // Function to fetch food listings with improved filtering
        function fetchFoodListings(page = 1, resetList = true) {
            if (isLoading) return;

            isLoading = true;
            showLoading(true);

            const tabContent = document.getElementById('pills-tabContent');
            if (resetList) {
                tabContent.classList.remove('fade-in');
            }

            const requestData = {
                category: currentFilters.category,
                search: currentFilters.search,
                mealTypes: JSON.stringify(currentFilters.mealTypes),
                dietTypes: JSON.stringify(currentFilters.dietTypes),
                minPrice: currentFilters.minPrice,
                maxPrice: currentFilters.maxPrice,
                page: page,
                perPage: perPage
            };

            fetch('fetch/shop.fetchFood.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams(requestData)
                })
                .then(response => response.json())
                .then(data => {
                    isLoading = false;
                    showLoading(false);

                    if (data.success) {
                        totalItems = data.total;
                        updateResultsCounter(data.total);

                        if (resetList) {
                            currentFoods = data.foods;
                            displayFoodListings(data.foods, true);
                        } else {
                            currentFoods = [...currentFoods, ...data.foods];
                            displayFoodListings(data.foods, false);
                        }

                        setTimeout(() => {
                            tabContent.classList.add('fade-in');
                        }, 100);
                    } else {
                        console.error('Error fetching food listings:', data.message);
                        showError(data.message);
                    }
                })
                .catch(error => {
                    isLoading = false;
                    showLoading(false);
                    console.error('Error:', error);
                    showError('Failed to connect to the server. Please try again later.');
                });
        }

        // Function to show/hide loading indicator
        function showLoading(show) {
            const loadingIndicator = document.getElementById('loadingIndicator');
            if (loadingIndicator) {
                loadingIndicator.style.display = show ? 'block' : 'none';
            }
        }

        // Function to update results counter
        function updateResultsCounter(count) {
            const counter = document.getElementById('resultsCounter');
            if (counter) {
                if (count === 0) {
                    counter.textContent = 'No items found';
                } else {
                    counter.textContent = `${count} item${count !== 1 ? 's' : ''} found`;
                }
            }
        }

        // Function to show error message
        function showError(message) {
            const tabContent = document.getElementById('pills-tabContent');
            tabContent.innerHTML = `
            <div class="alert alert-danger" role="alert">
                <i class='bx bx-error-circle'></i> ${message}
            </div>
        `;
        }

        // Function to display food listings with improved UI and animations
        function displayFoodListings(foods, resetList = true) {
            const tabContent = document.getElementById('pills-tabContent');

            if (resetList) {
                tabContent.innerHTML = '';
            }

            const existingTabPane = tabContent.querySelector('.tab-pane') || document.createElement('div');
            if (resetList) {
                existingTabPane.className = 'tab-pane fade show active';
                tabContent.appendChild(existingTabPane);
            }

            if (foods.length === 0 && resetList) {
                existingTabPane.innerHTML = `
                <div class="no-results">
                    <i class='bx bx-search-alt'></i>
                    <p>No food items found</p>
                    <button class="btn btn-outline-primary mt-3" id="clearSearch">
                        <i class='bx bx-refresh'></i> Clear search and filters
                    </button>
                </div>
            `;

                // Add event listener to clear search button
                setTimeout(() => {
                    const clearSearchBtn = document.getElementById('clearSearch');
                    if (clearSearchBtn) {
                        clearSearchBtn.addEventListener('click', () => {
                            resetAllFilters();
                        });
                    }
                }, 100);

                return;
            }

            foods.forEach(food => {
                const productList = document.createElement('div');
                productList.classList.add('product-list-item-container');
                productList.setAttribute('data-id', food.food_id);
                productList.setAttribute('data-price', food.price);
                productList.setAttribute('data-meal-type', food.meal_type);
                productList.setAttribute('data-diet-type', food.diet_type_suitable);

                // Format the diet types and health goals
                const dietTypes = food.diet_type_suitable ? food.diet_type_suitable.split(',')[0] : 'N/A';
                const healthGoals = food.health_goal_suitable ? food.health_goal_suitable.split(',')[0] : 'N/A';

                productList.innerHTML = `
                <div class="product-list-item">
                    <a href="product.php?prod=${food.food_id}" class="product-image-link">
                        <img src="../../uploads/${food.photo1 || 'placeholder.jpg'}" 
                            alt="${food.food_name}" 
                            class="product-image"
                            onerror="this.src='assets/images/placeholder-food.jpg'"/>
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
                            <span class="rating-text">${food.avg_rating} (${food.review_count} Review${food.review_count !== 1 ? 's' : ''})</span>
                        </div>
                        <div class="product-footer">
       <span class="price-tag">₱${parseFloat(food.price).toFixed(2)}</span>
                            <span class="category-tag">${food.category_name || food.category || 'Uncategorized'}</span>
                            <button class="cart-button" onclick="addToCart(${food.food_id})">
                                <i class="bx bx-cart-alt"></i>
                            </button>
                        </div>
                    </div>
                    <span class="meal-type-tag">${food.meal_type}</span>
                </div>
            `;

                // Add animation class for smooth entrance
                productList.style.opacity = '0';
                productList.style.transform = 'translateY(20px)';
                existingTabPane.appendChild(productList);

                // Trigger animation after a small delay
                setTimeout(() => {
                    productList.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    productList.style.opacity = '1';
                    productList.style.transform = 'translateY(0)';
                }, 50 * (existingTabPane.querySelectorAll('.product-list-item-container').length - 1));
            });

            // Add load more button if there are more items
            if (currentFoods.length < totalItems && resetList) {
                const loadMoreContainer = document.createElement('div');
                loadMoreContainer.className = 'text-center my-4';
                loadMoreContainer.innerHTML = `
                <button id="loadMoreBtn" class="btn btn-outline-primary">
                    <i class='bx bx-plus'></i> Load More Items
                </button>
            `;
                existingTabPane.appendChild(loadMoreContainer);

                // Add event listener to load more button
                setTimeout(() => {
                    const loadMoreBtn = document.getElementById('loadMoreBtn');
                    if (loadMoreBtn) {
                        loadMoreBtn.addEventListener('click', () => {
                            currentPage++;
                            fetchFoodListings(currentPage, false);
                            loadMoreBtn.parentNode.remove();
                        });
                    }
                }, 100);
            }
        }

        // Reset all filters and search
        function resetAllFilters() {
            // Reset filter checkboxes
            document.querySelectorAll('.filter-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Reset price range
            const priceRange = document.getElementById('priceRange');
            if (priceRange) {
                priceRange.value = 1000;
                updatePriceLabel(1000);
            }

            // Reset search input
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
            }

            // Reset category tabs
            document.querySelectorAll('.nav-link').forEach(btn => btn.classList.remove('active'));
            const allCategoryBtn = document.getElementById('category-all-tab');
            if (allCategoryBtn) {
                allCategoryBtn.classList.add('active');
            }

            // Reset filter variables
            currentFilters = {
                category: "",
                search: "",
                mealTypes: [],
                dietTypes: [],
                minPrice: 0,
                maxPrice: 1000
            };

            // Reset page number
            currentPage = 1;

            // Fetch foods with reset filters
            fetchFoodListings(1, true);
        }

        // Update price range label
        function updatePriceLabel(value) {
            const maxPriceLabel = document.getElementById('maxPrice');
            if (maxPriceLabel) {
                maxPriceLabel.textContent = `₱${value}`;
            }
        }

        // Search functionality with debounce for better performance
        const searchInput = document.getElementById('searchInput');
        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                currentFilters.search = e.target.value;
                currentPage = 1;
                fetchFoodListings(1, true);
            }, 500); // Increased debounce time for better performance
        });

        // Category tab listeners
        document.querySelectorAll('.nav-link').forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.nav-link').forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');

                const category = this.textContent.trim().replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                currentFilters.category = category === "All" ? "" : category;
                currentPage = 1;
                fetchFoodListings(1, true);
            });
        });

        // Price range slider
        const priceRange = document.getElementById('priceRange');
        if (priceRange) {
            priceRange.addEventListener('input', (e) => {
                updatePriceLabel(e.target.value);
            });
        }

        // Apply filters button
        const applyFiltersBtn = document.getElementById('applyFilters');
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', () => {
                // Get selected meal types
                const mealTypeCheckboxes = document.querySelectorAll('.filter-checkbox[data-filter="meal-type"]:checked');
                currentFilters.mealTypes = Array.from(mealTypeCheckboxes).map(cb => cb.value);

                // Get selected diet types
                const dietTypeCheckboxes = document.querySelectorAll('.filter-checkbox[data-filter="diet-type"]:checked');
                currentFilters.dietTypes = Array.from(dietTypeCheckboxes).map(cb => cb.value);

                // Get price range
                currentFilters.maxPrice = parseFloat(priceRange.value);

                // Reset page number
                currentPage = 1;

                // Fetch foods with applied filters
                fetchFoodListings(1, true);

                // Close the offcanvas
                const offcanvasElement = document.getElementById('filter');
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
                offcanvas.hide();
            });
        }

        // Reset filters button
        const resetFiltersBtn = document.getElementById('resetFilters');
        if (resetFiltersBtn) {
            resetFiltersBtn.addEventListener('click', resetAllFilters);
        }

        // Add a scroll event listener for lazy loading more items
        window.addEventListener('scroll', () => {
            const {
                scrollTop,
                scrollHeight,
                clientHeight
            } = document.documentElement;

            if (!isLoading &&
                scrollTop + clientHeight >= scrollHeight - 200 &&
                currentFoods.length < totalItems &&
                currentPage > 0) {
                currentPage++;
                fetchFoodListings(currentPage, false);
            }
        });

        // Initialize tooltips
        function initTooltips() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltipTriggerList.map(function(tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });
            }
        }

        // Function to add to cart
        window.addToCart = function(foodId) {
            const food = currentFoods.find(f => f.food_id == foodId);
            if (!food) return;

            // Animation for cart button
            const cartBtn = document.querySelector(`.product-list-item-container[data-id="${foodId}"] .cart-button`);
            if (cartBtn) {
                cartBtn.classList.add('adding');
                cartBtn.innerHTML = '<i class="bx bx-check"></i>';

                setTimeout(() => {
                    cartBtn.classList.remove('adding');
                    cartBtn.innerHTML = '<i class="bx bx-cart-alt"></i>';
                }, 1500);
            }

            // Send AJAX request to add item to cart
            fetch('functions/cart_functions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'add',
                        food_id: foodId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success toast
                        showToast(`${food.food_name} added to cart!`);

                        // Update cart count if available
                        if (typeof updateCartCount === 'function') {
                            updateCartCount(data.count);
                        }
                    } else {
                        showToast(data.message || 'Failed to add item to cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('Error adding item to cart', 'error');
                });
        };

        // Toast notification function
        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = `toast ${type === 'success' ? 'bg-success' : 'bg-danger'} text-white`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
            <div class="toast-body d-flex align-items-center">
                <i class='bx ${type === 'success' ? 'bx-check-circle' : 'bx-x-circle'} me-2'></i>
                ${message}
            </div>
        `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 3000
            });

            bsToast.show();

            // Remove the toast after it's hidden
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        // Create toast container if it doesn't exist
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            container.style.zIndex = '1050';
            document.body.appendChild(container);
            return container;
        }

        // Initialize the page
        initTooltips();
        fetchFoodListings();
    });
</script>