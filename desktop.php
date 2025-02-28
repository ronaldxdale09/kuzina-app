<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuzina - Download Our App</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.2s ease-out;
        }

        .modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.95);
            background: white;
            padding: 1.5rem;
            border-radius: 20px;
            width: 90%;
            max-width: 360px;
            z-index: 1001;
            opacity: 0;
            transition: all 0.3s ease-out;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .modal.active,
        .modal-overlay.active {
            opacity: 1;
        }

        .modal.active {
            transform: translate(-50%, -50%) scale(1);
        }

        .modal-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 1rem;
            display: block;
            color: var(--primary);
        }

        .modal-title {
            font-size: 1.4rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .modal-message {
            text-align: center;
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .modal-actions {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
        }

        .modal-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            flex: 1;
            max-width: 150px;
        }

        .modal-btn-primary {
            background: var(--primary);
            color: white;
        }

        .modal-btn-primary:hover {
            background: var(--primary-dark);
        }

        .modal-btn-secondary {
            background: #f3f4f6;
            color: var(--text-primary);
        }

        .modal-btn-secondary:hover {
            background: #e5e7eb;
        }

        :root {
            --primary: #502121;
            --primary-dark: #3d1919;
            --text-primary: #2d3748;
            --text-secondary: #4a5568;
            --background: #f7fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: var(--background);
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -20%;
            width: 140%;
            height: 100%;
            background: linear-gradient(135deg, rgba(80,33,33,0.05) 0%, rgba(80,33,33,0.1) 100%);
            transform: rotate(-12deg);
            z-index: 0;
        }

        .container {
            position: relative;
            text-align: center;
            padding: 3rem;
            max-width: 90%;
            width: 600px;
            background: white;
            border-radius: 24px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            z-index: 1;
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo {
            width: 180px;
            margin-bottom: 2.5rem;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        h1 {
            color: var(--primary);
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            line-height: 1.7;
            margin-bottom: 2.5rem;
            padding: 0 1rem;
        }

        .download-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .download-btn {
            background: var(--primary);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(80,33,33,0.2);
        }

        .download-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(80,33,33,0.25);
        }

        .download-icon {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(0,0,0,0.1);
        }

        .feature {
            text-align: center;
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .feature h3 {
            font-size: 1.1rem;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .feature p {
            font-size: 0.95rem;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .container {
                padding: 2rem;
                margin: 1rem;
            }

            h1 {
                font-size: 1.8rem;
            }

            p {
                font-size: 1rem;
                padding: 0;
            }

            .features {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="assets/images/logo/logo-w2.png" alt="Kuzina Logo" class="logo">
        <h1>Experience Kuzina on Mobile</h1>
        <p>Unlock the full potential of Kuzina's healthy food delivery service with our mobile app. Enjoy seamless ordering, real-time delivery tracking, and exclusive mobile-only offers.</p>
        
        <div class="download-section">
            <a href="app/KUZINA_9_3.apk" 
               download="KUZINA_9_3.apk" 
               class="download-btn"
               onclick="trackDownload()"
                <svg class="download-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="7 10 12 15 17 10"></polyline>
                    <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
                Download Now
            </a>
        </div>

        <div class="features">
            <div class="feature">
                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                <h3>Real-Time Tracking</h3>
                <p>Track your order from kitchen to doorstep in real-time.</p>
            </div>
            <div class="feature">
                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                </svg>
                <h3>Exclusive Offers</h3>
                <p>Access special deals and promotions.</p>
            </div>
            <div class="feature">
                <svg class="feature-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <h3>Easy Ordering</h3>
                <p>Simplified checkout process.</p>
            </div>
        </div>
    </div>

    <!-- Download Modal -->
    <div class="modal-overlay" id="downloadModal">
        <div class="modal">
            <svg class="modal-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            <h2 class="modal-title">Download Starting</h2>
            <p class="modal-message">Thank you for choosing Kuzina! Your download will begin automatically. Once completed, open the file to install the app.</p>
            <div class="modal-actions">
                <button class="modal-btn modal-btn-primary" onclick="closeModal()">Got it</button>
            </div>
        </div>
    </div>

    <script>
        const modal = document.querySelector('.modal');
        const modalOverlay = document.querySelector('.modal-overlay');

        function showModal() {
            modalOverlay.style.display = 'block';
            // Trigger reflow
            modalOverlay.offsetHeight;
            modalOverlay.classList.add('active');
            modal.classList.add('active');
        }

        function closeModal() {
            modal.classList.remove('active');
            modalOverlay.classList.remove('active');
            setTimeout(() => {
                modalOverlay.style.display = 'none';
            }, 300);
        }

        function trackDownload() {
            // You can add analytics tracking here if needed
            console.log('App download initiated');
            
            // Show the download modal
            showModal();
        }

        // Close modal when clicking outside
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) {
                closeModal();
            }
        });

        // Close modal on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>