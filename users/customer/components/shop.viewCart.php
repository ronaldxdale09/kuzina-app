<footer class="footer-wrap shop">
    <ul class="footer">
        <li class="footer-item"><span class="font-xs">2 Items</span> <span class="font-sm">PHP 250.00</span></li>
        <li class="footer-item">
            <a href="cart.php" class="font-md">View Cart <i data-feather="chevron-right"></i></a>
        </li>
    </ul>
</footer>


<script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('functions/footer.cart.fetch.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update footer with cart data
                document.querySelector('.footer-item .font-xs').textContent =
                    `${data.item_count} Item${data.item_count !== 1 ? 's' : ''}`;
                document.querySelector('.footer-item .font-sm').textContent = `PHP ${data.total_price}`;
            } else {
                console.error('Failed to fetch cart data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching cart data:', error);
        });
});
</script>