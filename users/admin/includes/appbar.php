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

<footer class="footer-wrap" id="persistent-appbar">
    <ul class="footer">
        <li class="footer-item <?php echo ($current_page === 'homepage.php') ? 'active' : ''; ?>">
            <a href="homepage.php" class="footer-link">
                <i class="bx bxs-dashboard icli"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="footer-item <?php echo ($current_page === 'users.php') ? 'active' : ''; ?>">
            <a href="users.php" class="footer-link">
                <i class="bx bx-group icli"></i>
                <span>Users</span>
            </a>
        </li>
        <li class="footer-item <?php echo ($current_page === 'approvals.php') ? 'active' : ''; ?>">
            <a href="approvals.php" class="footer-link">
                <i class="bx bx-check-circle icli"></i>
                <span>Approvals</span>
            </a>
        </li>
        <li class="footer-item <?php echo ($current_page === 'reports.php') ? 'active' : ''; ?>">
            <a href="reports.php" class="footer-link">
                <i class="bx bx-line-chart icli"></i>
                <span>Reports</span>
            </a>
        </li>
        <li class="footer-item">
            <a href="withdrawal.php" class="footer-link">
                <i class="bx bx-money icli"></i>
                <span>Withdrawal</span>
            </a>
        </li>
        <li class="footer-item">
            <a href="settings.php" class="footer-link">
                <i class="bx bx-cog icli"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</footer>