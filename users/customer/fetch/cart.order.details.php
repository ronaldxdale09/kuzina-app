<script>
document.addEventListener('DOMContentLoaded', () => {
    const deliveryFee = 50.00;
    let couponDiscount = 0.00;

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

        document.getElementById('bag-total').innerText = bagTotal.toFixed(2);
        document.getElementById('coupon-discount').innerText = couponDiscount.toFixed(2);
        document.getElementById('delivery-fee').innerText = deliveryFee.toFixed(2);

        const totalAmount = (bagTotal + deliveryFee - couponDiscount).toFixed(2);
        document.getElementById('total-amount').innerText = totalAmount;
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
                    // Remove the item's HTML element
                    const itemElement = document.querySelector(`.swipe-to-show[data-food-id="${foodId}"]`);
                    if (itemElement) {
                        itemElement.remove();
                    }
                    calculateTotals();

                    // Check if cart is empty
                    const remainingItems = document.querySelectorAll('.swipe-to-show');
                    if (remainingItems.length === 0) {
                        document.querySelector('.cart-items').innerHTML = "<p>Your cart is empty.</p>";
                    }
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error removing item:', error));
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
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error updating quantity:', error));
    }

    // Plus-minus button handlers
    document.querySelectorAll('.plus-minus i').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.getAttribute('data-food-id');
            const quantityInput = this.closest('.plus-minus').querySelector('.quantity-input');
            let currentQuantity = parseInt(quantityInput.value);

            if (this.classList.contains('add') && currentQuantity < 10) {
                currentQuantity++;
            } else if (this.classList.contains('sub') && currentQuantity > 0) {
                currentQuantity--;
            }

            quantityInput.value = currentQuantity;
            updateCartQuantity(foodId, currentQuantity);
        });
    });

    // Delete button handlers
    document.querySelectorAll('.delete-button').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.closest('.swipe-to-show').querySelector('.plus-minus i')
                .getAttribute('data-food-id');
            if (confirm('Are you sure you want to remove this item?')) {
                removeCartItem(foodId);
            }
        });
    });

    // Coupon handler
    document.getElementById('apply-coupon-btn')?.addEventListener('click', () => {
        const couponCode = document.getElementById('coupon-code').value.trim();
        if (couponCode === 'DISCOUNT10') {
            couponDiscount = parseFloat(document.getElementById('bag-total').innerText) * 0.1;
        } else {
            couponDiscount = 0;
        }
        calculateTotals();
    });

    // Initial calculation
    calculateTotals();
});
</script>