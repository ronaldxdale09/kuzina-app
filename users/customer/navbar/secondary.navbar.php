<header class="header">
    <div class="logo-wrap">
    <a href="javascript:void(0);" onclick="window.history.back();">
            <i class="iconly-Arrow-Left-Square icli"></i>
        </a> 
        <a href="homepage.php"> <img class="logo logo-w" src="assets/images/logo/logo-w2.png" alt="logo" /></a><a
            href="homepage.php"> <img class="logo" src="assets/images/logo/logo-w2.png" alt="logo" /></a>
    </div>
      
    <div class="header-actions">
    
        <div class="cart-icon-wrap">
            <a href="cart.php">
                <i class='bx bx-cart'></i>
                <span class="cart-count" id="cartCountBadge">
                    <?php
                    // Get customer ID from cookie
                    $customer_id = isset($_COOKIE['user_id']) ? intval($_COOKIE['user_id']) : 0;
                    
                    // Fast query to get cart count - using COUNT() for efficiency
                    $cart_count = 0;
                    if ($customer_id > 0) {
                        $cart_query = "SELECT COUNT(*) as count FROM cart_items WHERE customer_id = ?";
                        $stmt = $conn->prepare($cart_query);
                        $stmt->bind_param("i", $customer_id);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($row = $result->fetch_assoc()) {
                            $cart_count = $row['count'];
                        }
                        $stmt->close();
                    }
                    
                    echo $cart_count;
                    ?>
                </span>
            </a>
        </div>
    </div>
</header>


<style>
/* Logo area */
.logo-wrap {
    display: flex;
    align-items: center;
}

.logo-wrap .nav-bar {
    color: white;
    font-size: 24px;
    margin-right: 15px;
    cursor: pointer;
}

.logo {
    height: 32px;
    width: auto;
}

/* Cart icon styling */
.cart-icon-wrap {
    position: relative;
}

.cart-icon-wrap a {
    display: block;
    text-decoration: none;
}

.cart-icon-wrap i {
    font-size: 24px;
    transition: transform 0.2s ease;
}

.cart-icon-wrap:hover i {
    transform: translateY(-2px);
}

.cart-count {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #FF6B35;
    color: white;
    font-size: 11px;
    font-weight: 600;
    height: 18px;
    width: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    transition: transform 0.2s ease;
}

.cart-icon-wrap:hover .cart-count {
    transform: scale(1.1);
}


/* Responsive adjustments */
@media (max-width: 576px) {
    .header {
        padding: 12px 16px;
    }
    
    .user-welcome {
        display: none; /* Hide welcome text on very small screens */
    }
    
    .header-actions {
        gap: 12px;
    }
}

@media (min-width: 577px) and (max-width: 768px) {
    .user-welcome .font-sm {
        font-size: 12px;
    }
}</style>