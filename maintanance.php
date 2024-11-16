<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Under Maintenance - Kuzina</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <style>
    :root {
        --theme-color: #8a0b10;
        --text-color: #333333;
        --light-text: #666666;
        --background: #f5f5f5;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    }

    body {
        background: var(--background);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .maintenance-container {
        max-width: 480px;
        width: 100%;
        text-align: center;
        padding: 24px;
        margin-top: 40px;
        position: relative;
        z-index: 2;
    }

    .logo {
        width: 120px;
        height: auto;
        margin-bottom: 24px;
    }

    .maintenance-icon {
        width: 80px;
        height: 80px;
        background: var(--theme-color);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 24px;
        animation: pulse 2s infinite;
    }

    .maintenance-icon i {
        font-size: 40px;
        color: white;
    }

    .maintenance-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 24px;
    }

    h1 {
        color: var(--text-color);
        font-size: 24px;
        margin-bottom: 12px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(138, 11, 16, 0.1);
        color: var(--theme-color);
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .status-badge i {
        font-size: 18px;
    }

    p {
        color: var(--light-text);
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 24px;
    }

    .estimated-time {
        font-size: 14px;
        color: var(--light-text);
        margin-bottom: 24px;
    }

    .refresh-button {
        background: var(--theme-color);
        color: white;
        border: none;
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin: 0 auto;
        cursor: pointer;
        transition: transform 0.2s;
    }

    .refresh-button:active {
        transform: scale(0.98);
    }

    .background-pattern {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(138, 11, 16, 0.05) 0%, rgba(138, 11, 16, 0.02) 100%);
        z-index: 1;
    }

    .social-links {
        margin-top: 24px;
    }

    .social-links a {
        color: var(--light-text);
        text-decoration: none;
        font-size: 24px;
        margin: 0 8px;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(138, 11, 16, 0.4);
        }

        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(138, 11, 16, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(138, 11, 16, 0);
        }
    }

    @media (max-width: 480px) {
        .maintenance-container {
            padding: 16px;
            margin-top: 20px;
        }

        h1 {
            font-size: 20px;
        }

        p {
            font-size: 14px;
        }
    }

    @supports (padding: max(0px)) {
        body {
            padding-top: max(20px, env(safe-area-inset-top));
            padding-bottom: max(20px, env(safe-area-inset-bottom));
        }
    }
    </style>

</head>

<body>
    <div class="background-pattern"></div>

    <div class="maintenance-container">
        <img src="assets/images/logo/logo-w2.png" alt="Kuzina" class="logo">

        <div class="maintenance-card">
            <div class="maintenance-icon">
                <i class='bx bx-wrench'></i>
            </div>

            <div class="status-badge">
                <i class='bx bx-time-five'></i>
                Maintenance in Progress
            </div>

            <h1>We're Making Things Better</h1>

            <p>We're currently performing some maintenance to improve your experience. We'll be back shortly with new
                features and improvements.</p>

            <div class="estimated-time">
                Estimated completion time: TBA
            </div>

            <button class="refresh-button" onclick="window.location.reload()">
                <i class='bx bx-refresh'></i>
                Refresh Page
            </button>
        </div>

        <div class="social-links">
            <a href="#"><i class='bx bxl-facebook-circle'></i></a>
            <a href="#"><i class='bx bxl-instagram'></i></a>
            <a href="#"><i class='bx bxl-twitter'></i></a>
        </div>
    </div>

    <script>
    // Auto refresh every 5 minutes
    setTimeout(() => {
        window.location.reload();
    }, 300000);

    // Add iOS bounce effect prevention
    document.body.addEventListener('touchmove', function(e) {
        e.preventDefault();
    }, {
        passive: false
    });
    </script>
</body>

</html>