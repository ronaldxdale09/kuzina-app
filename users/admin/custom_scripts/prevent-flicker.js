// // assets/js/navigation.js
// document.addEventListener('DOMContentLoaded', function() {
//     const appBar = document.getElementById('fixed-appbar');

//     // If there's no app bar in the DOM, inject it from localStorage
//     if (!appBar && localStorage.getItem('appBarHtml')) {
//         const appBarContainer = document.createElement('div');
//         appBarContainer.innerHTML = localStorage.getItem('appBarHtml');
//         document.body.appendChild(appBarContainer.firstElementChild);
//     }

//     // Store the app bar HTML for other pages
//     if (appBar) {
//         localStorage.setItem('appBarHtml', appBar.outerHTML);
//     }

//     // Add click handlers to all navigation links
//     document.querySelectorAll('.footer-link').forEach(link => {
//         if (!link.hasAttribute('data-bs-toggle')) {
//             link.addEventListener('click', function(e) {
//                 const currentBar = document.getElementById('fixed-appbar');
//                 if (currentBar) {
//                     // Ensure the bar stays visible during transition
//                     currentBar.style.transition = 'none';
//                     currentBar.style.opacity = '1';
//                 }
//             });
//         }
//     });

//     // Update active state
//     updateActiveState(window.location.pathname);
// });

// function updateActiveState(currentPath) {
//     const currentPage = currentPath.split('/').pop();
//     const footerItems = document.querySelectorAll('.footer-item');

//     footerItems.forEach(item => {
//         const link = item.querySelector('.footer-link');
//         if (link) {
//             const href = link.getAttribute('href');
//             item.classList.toggle('active', href === currentPage);
//         }
//     });
// }

// // Handle page visibility changes
// document.addEventListener('visibilitychange', function() {
//     if (!document.hidden) {
//         updateActiveState(window.location.pathname);
//     }
// });