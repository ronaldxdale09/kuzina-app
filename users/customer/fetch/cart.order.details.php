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
            priceDisplay.innerText = itemTotal.toFixed(2); // Update item total display

            return total + itemTotal;
        }, 0);

        document.getElementById('bag-total').innerText = bagTotal.toFixed(2);
        document.getElementById('coupon-discount').innerText = couponDiscount.toFixed(2);
        document.getElementById('delivery-fee').innerText = deliveryFee.toFixed(2);

        const totalAmount = (bagTotal + deliveryFee - couponDiscount).toFixed(2);
        document.getElementById('total-amount').innerText = totalAmount;
    }

    function updateCartQuantity(foodId, newQuantity) {
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

    document.querySelectorAll('.plus-minus i').forEach(button => {
        button.addEventListener('click', function() {
            const foodId = this.getAttribute('data-food-id');
            const quantityInput = this.closest('.plus-minus').querySelector('.quantity-input');
            let currentQuantity = parseInt(quantityInput.value);

            // Update quantity based on the button clicked
            if (this.classList.contains('add') && currentQuantity < 10) {
                currentQuantity++;
            } else if (this.classList.contains('sub') && currentQuantity > 1) {
                currentQuantity--;
            }

            // Set the new quantity and update the server
            quantityInput.value = currentQuantity;
            updateCartQuantity(foodId, currentQuantity);
        });
    });

    document.getElementById('apply-coupon-btn').addEventListener('click', () => {
        const couponCode = document.getElementById('coupon-code').value.trim();
        if (couponCode === 'DISCOUNT10') {
            couponDiscount = parseFloat(document.getElementById('bag-total').innerText) * 0.1;
        } else {
            couponDiscount = 0;
        }
        calculateTotals();
    });

    // Initial calculation on page load
    calculateTotals();
});
</script>