// Create a new file: assets/js/icon-preloader.js
document.addEventListener('DOMContentLoaded', function() {
    // Preload Box Icons CSS if not already loaded
    if (!document.querySelector('link[href*="boxicons"]')) {
        const boxIconsCSS = document.createElement('link');
        boxIconsCSS.rel = 'stylesheet';
        boxIconsCSS.href = 'assets/boxicons/css/boxicons.min.css';
        document.head.appendChild(boxIconsCSS);
    }

    // Create hidden container for preloading icons
    const preloadContainer = document.createElement('div');
    preloadContainer.style.cssText = 'position: absolute; width: 0; height: 0; overflow: hidden; z-index: -1;';

    // List of icons to preload
    const iconsToPreload = [
        'bx bx-home',
        'bx bx-restaurant',
        'bx bx-book',
        'bx bx-bell',
        'bx bx-message'
    ];

    // Create elements for each icon
    iconsToPreload.forEach(iconClass => {
        const icon = document.createElement('i');
        icon.className = iconClass + ' icli';
        preloadContainer.appendChild(icon);
    });

    // Add container to body
    document.body.appendChild(preloadContainer);
});