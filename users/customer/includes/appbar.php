<!-- 
        <li class="footer-item">
            <a href="search.php" class="footer-link">
                <i class="iconly-Search icli"></i>
                <span>Search</span>
            </a>
        </li> -->
        <!-- <li class="footer-item">
            <a href="#" data-bs-toggle="offcanvas" data-bs-target="#underDev" class="footer-link">
                <i class="iconly-Paper icli"></i>
                <span>Journal</span>
            </a>
        </li> -->



<footer class="footer-wrap" id="persistent-appbar">
    <ul class="footer">
        <li class="footer-item <?php echo ($current_page === 'homepage.php') ? 'active' : ''; ?>">
            <a href="homepage.php" class="footer-link">
                <i class="bx bxs-dashboard icli"></i>
                <span>Home</span>
            </a>
        </li>
        <li class="footer-item <?php echo ($current_page === 'shop.php') ? 'active' : ''; ?>">
            <a href="shop.php" class="footer-link">
            <i class="iconly-Category icli"></i>
                <span>Shop</span>
            </a>
        </li>

        <li class="footer-item <?php echo ($current_page === 'cart.php') ? 'active' : ''; ?>">
            <a href="cart.php" class="footer-link">
            <i class="bx bx-cart icli"></i>
            <span>Cart</span>
            </a>
        </li>
        <li class="footer-item  <?php echo ($current_page === 'order-history.php') ? 'active' : ''; ?>">
            <a href="order-history.php" class="footer-link">
                <i class="bx bx-book"></i>
                <span>Orders</span>
            </a>
        </li>
        <li class="footer-item <?php echo ($current_page === 'approvals.php') ? 'active' : ''; ?>">
            <a href="journal.php" class="footer-link">
            <i class="iconly-Paper icli"></i>
            <span>Journal</span>
            </a>
        </li>
        <li class="footer-item <?php echo ($current_page === 'education.php') ? 'active' : ''; ?>">
            <a href="messenger.chats.php" class="footer-link">
                <i class="bx bx-chat icli"></i>
                <span>Chat</span>
            </a>
        </li>
  
    </ul>
</footer>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<style>
/* Your existing styles remain the same */
.footer-wrap {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}

.main-wrap {
    padding-bottom: 70px;
}

.footer-item.active .footer-link,
.footer-item.active i {
    color: var(--theme-color, #d99f46);
}

.footer-link i,
.footer-link span {
    display: inline-block !important;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    transform: translateZ(0);
    -webkit-transform: translateZ(0);
}

.footer-wrap,
.footer-link,
.footer-link i,
.footer-link span,
.footer-item {
    transition: none !important;
}

body {
    margin-bottom: env(safe-area-inset-bottom, 70px);
}

#persistent-appbar {
    visibility: visible !important;
    opacity: 1 !important;
}

.footer-link {
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current page from URL
    const getCurrentPage = () => window.location.pathname.split('/').pop();
    
    // Update active state based on current page
    function updateActiveState() {
        const currentPage = getCurrentPage();
        const items = document.querySelectorAll('.footer-item');
        
        items.forEach(item => {
            const link = item.querySelector('.footer-link');
            if (link && !link.hasAttribute('data-bs-toggle')) {
                const href = link.getAttribute('href');
                item.classList.toggle('active', href === currentPage);
            }
        });
    }

    // Handle navigation clicks
    document.querySelectorAll('.footer-link').forEach(link => {
        if (!link.hasAttribute('data-bs-toggle')) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all items
                document.querySelectorAll('.footer-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Add active class to clicked item
                this.closest('.footer-item').classList.add('active');
                
                // Navigate to the new page
                const targetPage = this.getAttribute('href');
                window.location.href = targetPage;
            });
        }
    });

    // Initial active state
    updateActiveState();

    // Handle back/forward buttons
    window.addEventListener('popstate', function() {
        updateActiveState();
    });
});
</script>
