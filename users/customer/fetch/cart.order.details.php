<script>
document.addEventListener('DOMContentLoaded', () => {
    let couponDiscount = 0.00;

    function calculateDeliveryFee() {
        const totalItems = Array.from(document.querySelectorAll('.quantity-input'))
            .reduce((total, input) => {
                return total + (parseInt(input.value) || 0);
            }, 0);

        let deliveryFee = 50.00; // Base fee

        if (totalItems > 1) {
            // Add ₱10 for each additional item
            deliveryFee += (totalItems - 1) * 10;
        }

        // Cap at ₱150
        return Math.min(deliveryFee, 150);
    }

    function calculateTotals() {
        let bagTotal = Array.from(document.querySelectorAll('.quantity-input')).reduce((total, input) => {
            const foodId = input.getAttribute('data-food-id');
            const quantity = parseInt(input.value) || 1;
            const priceDisplay = document.querySelector(`.price-display[data-food-id="${foodId}"]`);
            const unitPrice = parseFloat(priceDisplay.getAttribute('data-price')) || 0;
            const itemTotal = unitPrice * quantity;

            priceDisplay.innerText = itemTotal.toFixed(2);
            return total + itemTotal;
        }, 0);

        const deliveryFee = calculateDeliveryFee();

        // Update the display elements
        document.getElementById('bag-total').innerText = bagTotal.toFixed(2);
        document.getElementById('coupon-discount').innerText = couponDiscount.toFixed(2);
        document.getElementById('delivery-fee').innerText = deliveryFee.toFixed(2);
        document.getElementById('total-amount').innerText = (bagTotal + deliveryFee - couponDiscount).toFixed(
        2);

        // Update hidden input if it exists
        const deliveryFeeInput = document.querySelector('input[name="delivery_fee"]');
        if (deliveryFeeInput) {
            deliveryFeeInput.value = deliveryFee;
        }

        // Update checkout button state if it exists
        const checkoutButton = document.querySelector('.checkout-button');
        if (checkoutButton) {
            checkoutButton.disabled = bagTotal <= 0;
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
                    calculateTotals();

                    // Check if cart is empty
                    const remainingItems = document.querySelectorAll('.swipe-to-show');
                    if (remainingItems.length === 0) {
                        document.querySelector('.cart-items').innerHTML = `
                        <div class="text-center p-4">
                            <p>Your cart is empty.</p>
                            <a href="menu.php" class="btn btn-primary">Browse Menu</a>
                        </div>
                    `;
                    }
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error removing item:', error);
                alert('An error occurred while removing the item');
            });
    }

    // Event Listeners
    document.querySelectorAll('.plus-minus i').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.getAttribute('data-food-id');
            const quantityInput = this.closest('.plus-minus').querySelector('.quantity-input');
            let currentQuantity = parseInt(quantityInput.value) || 1;

            if (this.classList.contains('add') && currentQuantity < 10) {
                currentQuantity++;
            } else if (this.classList.contains('sub') && currentQuantity > 1) {
                currentQuantity--;
            }

            quantityInput.value = currentQuantity;
            updateCartQuantity(foodId, currentQuantity);
        });
    });

    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.closest('.swipe-to-show').querySelector('.plus-minus i')
                .getAttribute('data-food-id');
            if (confirm('Are you sure you want to remove this item?')) {
                removeCartItem(foodId);
            }
        });
    });

    const couponButton = document.getElementById('apply-coupon-btn');
    if (couponButton) {
        couponButton.addEventListener('click', () => {
            const couponCode = document.getElementById('coupon-code').value.trim().toUpperCase();

            if (!couponCode) {
                alert('Please enter a coupon code');
                return;
            }

            if (couponCode === 'DISCOUNT10') {
                const bagTotal = parseFloat(document.getElementById('bag-total').innerText);
                couponDiscount = bagTotal * 0.1;
                alert('Coupon applied successfully!');
            } else {
                couponDiscount = 0;
                alert('Invalid coupon code');
            }

            calculateTotals();
            document.getElementById('coupon-code').value = '';
        });
    }

    // Initialize calculations
    calculateTotals();
});
</script>