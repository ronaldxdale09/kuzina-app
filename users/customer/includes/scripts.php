<!-- Critical scripts loaded with defer -->
<script defer src="assets/js/jquery-3.6.0.min.js"></script>
<script defer src="assets/js/bootstrap.bundle.min.js"></script>

<!-- Non-critical scripts loaded efficiently -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Array of scripts to load
    const scripts = [
        'assets/js/lord-icon-2.1.0.js',
        'assets/js/feather.min.js',
        'assets/js/slick.js',
        'assets/js/slick-custom.js',
        'assets/js/theme-setting.js',
        'assets/js/script.js'
    ];

    // Load scripts sequentially
    scripts.forEach(src => {
        const script = document.createElement('script');
        script.src = src;
        script.defer = true;
        document.body.appendChild(script);
    });
});
</script>

<?php include 'modal/modal.dev.php'?>