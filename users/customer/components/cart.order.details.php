<script>
    document.addEventListener('DOMContentLoaded', () => {
        let baseFee = 50.00;
        let minOrderAmount = 100.00;
        let maxDeliveryRadius = 10;

        // Fetch system settings
        fetch('functions/getSystemSettings.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    baseFee = parseFloat(data.settings.rider_fee);
                    minOrderAmount = parseFloat(data.settings.min_order_amount);
                    maxDeliveryRadius = parseFloat(data.settings.max_delivery_radius);
                    calculateTotals();
                }
            })
            .catch(error => console.error('Error fetching settings:', error));

        function calculateDeliveryFee() {
            const totalItems = Array.from(document.querySelectorAll('.quantity-input'))
                .reduce((total, input) => total + (parseInt(input.value) || 0), 0);

            if (totalItems === 0) return 0;

            let deliveryFee = baseFee;
            if (totalItems > 1) {
                deliveryFee += (totalItems - 1) * 10;
            }
            return Math.min(deliveryFee, 150);
        }

        function calculateTotals() {
            let bagTotal = Array.from(document.querySelectorAll('.quantity-input')).reduce((total, input) => {
                const foodId = input.getAttribute('data-food-id');
                const quantity = parseInt(input.value) || 0;
                const priceDisplay = document.querySelector(`.price-display[data-food-id="${foodId}"]`);
                const unitPrice = parseFloat(priceDisplay.getAttribute('data-price')) || 0;
                const itemTotal = unitPrice * quantity;

                if (priceDisplay) {
                    priceDisplay.innerText = itemTotal.toFixed(2);
                }
                return total + itemTotal;
            }, 0);

            const deliveryFee = calculateDeliveryFee();
            const finalTotal = bagTotal > 0 ? bagTotal + deliveryFee : 0;

            // Update display elements
            document.getElementById('bag-total').innerText = bagTotal.toFixed(2);

            // Handle delivery fee section visibility
            document.getElementById('delivery-fee').innerText = deliveryFee.toFixed(2);
            document.getElementById('total-amount').innerText = finalTotal.toFixed(2);

            // Update hidden delivery fee input if it exists
            const deliveryFeeInput = document.querySelector('input[name="delivery_fee"]');
            if (deliveryFeeInput) {
                deliveryFeeInput.value = bagTotal > 0 ? deliveryFee : 0;
            }

            // Handle footer checkout and minimum order warning
            const footerCheckout = document.querySelector('.footer-wrap.footer-button');
            const isMinimumMet = bagTotal >= minOrderAmount;

            if (footerCheckout) {
                footerCheckout.style.display = bagTotal > 0 ? 'flex' : 'none';
                const checkoutLink = footerCheckout.querySelector('a');
                if (checkoutLink) {
                    if (!isMinimumMet) {
                        checkoutLink.style.pointerEvents = 'none';
                        checkoutLink.style.opacity = '0.5';
                    } else {
                        checkoutLink.style.pointerEvents = 'auto';
                        checkoutLink.style.opacity = '1';
                    }
                }
            }

            const minOrderWarning = document.getElementById('min-order-warning');
            if (minOrderWarning) {
                minOrderWarning.style.display = (!isMinimumMet && bagTotal > 0) ? 'block' : 'none';
                minOrderWarning.textContent = `Minimum order amount is ₱${minOrderAmount.toFixed(2)}`;
            }
        }

        function updateCartQuantity(foodId, newQuantity) {
            if (newQuantity <= 0) {
                removeCartItem(foodId);
                return;
            }

            fetch('functions/updateCart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        food_id: foodId,
                        quantity: newQuantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        calculateTotals();
                    } else {
                        showModal('errorModal', data.message || 'Failed to update quantity');
                    }
                })
                .catch(error => {
                    console.error('Error updating quantity:', error);
                    showModal('errorModal', 'An error occurred while updating the quantity');
                });
        }

        function removeCartItem(foodId) {
            if (!confirm('Are you sure you want to remove this item?')) {
                const quantityInput = document.querySelector(`.quantity-input[data-food-id="${foodId}"]`);
                if (quantityInput) {
                    quantityInput.value = 1;
                    updateCartQuantity(foodId, 1);
                }
                return;
            }

            fetch('functions/deleteCart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        food_id: foodId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const itemElement = document.querySelector(`.swipe-to-show[data-food-id="${foodId}"]`);
                        if (itemElement) {
                            itemElement.remove();
                        }

                        // Check if cart is empty
                        const remainingItems = document.querySelectorAll('.swipe-to-show');
                        if (remainingItems.length === 0) {
                            document.querySelector('.cart-items').innerHTML = `
                        <div class="empty-cart-container">
                            <div class="empty-cart-content">
                                <div class="empty-cart-icon">
                                    <i class="bx bx-cart-alt"></i>
                                </div>
                                <h3 class="empty-cart-title">Your Cart is Empty</h3>
                                <p class="empty-cart-message">Start adding some healthy and delicious meals!</p>
                                <div class="empty-cart-actions">
                                    <a href="menu.php" class="btn btn-primary">
                                        <i class="bx bx-restaurant"></i> Browse Menu
                                    </a>
                                </div>
                            </div>
                        </div>`;

                            // Hide footer checkout
                            const footerCheckout = document.querySelector('.footer-wrap.footer-button');
                            if (footerCheckout) footerCheckout.style.display = 'none';
                        }
                        calculateTotals();
                    } else {
                        showModal('errorModal', data.message || 'Failed to remove item');
                    }
                })
                .catch(error => {
                    console.error('Error removing item:', error);
                    showModal('errorModal', 'An error occurred while removing the item');
                });
        }

        // Event Listeners for quantity buttons
        document.querySelectorAll('.plus-minus i').forEach(button => {
            button.addEventListener('click', function() {
                const foodId = this.getAttribute('data-food-id');
                const quantityInput = this.closest('.plus-minus').querySelector('.quantity-input');
                let currentQuantity = parseInt(quantityInput.value) || 1;

                if (this.classList.contains('add') && currentQuantity < 10) {
                    currentQuantity++;
                    quantityInput.value = currentQuantity;
                    updateCartQuantity(foodId, currentQuantity);
                } else if (this.classList.contains('sub')) {
                    currentQuantity--;
                    if (currentQuantity <= 0) {
                        removeCartItem(foodId);
                    } else {
                        quantityInput.value = currentQuantity;
                        updateCartQuantity(foodId, currentQuantity);
                    }
                }
            });
        });

        // Event Listeners for delete buttons
        document.querySelectorAll('.delete-button').forEach(button => {
            button.addEventListener('click', function() {
                const foodId = this.closest('.swipe-to-show').querySelector('.plus-minus i')
                    .getAttribute('data-food-id');
                removeCartItem(foodId);
            });
        });

        // Initialize calculations
        calculateTotals();
    });
</script>