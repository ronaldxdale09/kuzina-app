<?php 
include 'includes/header.php';

if (!$customer_id) {
    header("Location: login.php");
    exit();
}

// Get order ID from URL
$order_id = isset($_GET['order']) ? (int)$_GET['order'] : 0;

// Fetch order details and items
$query = "SELECT 
    o.order_id,
    o.order_date,
    o.total_amount,
    k.kitchen_id,
    k.fname AS kitchen_name,
    k.lname AS kitchen_lname,
    oi.order_item_id,
    oi.food_id,
    oi.quantity,
    f.food_name,
    f.photo1,
    COALESCE(r.rating, 0) as rating,
    r.comment
FROM orders o
JOIN order_items oi ON o.order_id = oi.order_id
JOIN food_listings f ON oi.food_id = f.food_id
JOIN kitchens k ON o.kitchen_id = k.kitchen_id
LEFT JOIN reviews r ON r.order_id = o.order_id 
    AND r.food_id = f.food_id 
    AND r.customer_id = o.customer_id
WHERE o.order_id = ? AND o.customer_id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $order_id, $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$orderItems = $result->fetch_all(MYSQLI_ASSOC);

if (empty($orderItems)) {
    header("Location: order-history.php");
    exit();
}

// Get kitchen and order info from first item
$kitchenName = $orderItems[0]['kitchen_name'] . ' ' . $orderItems[0]['kitchen_lname'];
$orderDate = date('d M, Y', strtotime($orderItems[0]['order_date']));
?>

<link rel="stylesheet" href="assets/css/order.review.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<body>
    <!-- Header Start -->
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();">
                <i class="iconly-Arrow-Left-Square icli"></i>
            </a>
            <h1 class="title-color font-md">Rate Your Order</h1>
        </div>
    </header>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap mb-xxl">
        <div class="order-info">
            <h2 class="font-md">Order #<?php echo $order_id; ?></h2>
            <p class="font-sm content-color">
                from <?php echo htmlspecialchars($kitchenName); ?> on <?php echo $orderDate; ?>
            </p>
        </div>

        <div class="items-container">
            <?php foreach ($orderItems as $item): ?>
            <div class="rating-card" data-item-id="<?php echo $item['order_item_id']; ?>">
                <div class="food-info">
                    <img src="../../uploads/<?php echo htmlspecialchars($item['photo1']); ?>"
                        alt="<?php echo htmlspecialchars($item['food_name']); ?>" class="food-image">
                    <div class="food-details">
                        <h3 class="font-sm"><?php echo htmlspecialchars($item['food_name']); ?></h3>
                        <p class="font-xs content-color">Quantity: <?php echo $item['quantity']; ?></p>
                    </div>
                </div>

                <div class="rating-section">
                    <p class="font-sm">Your Rating</p>
                    <div class="stars-container">
                        <div class="stars" data-food-id="<?php echo $item['food_id']; ?>">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                            <button type="button" class="star-btn" data-value="<?php echo $i; ?>">
                                <i class="bx <?php echo $i <= $item['rating'] ? 'bxs-star filled' : 'bx-star'; ?>"
                                    data-rating="<?php echo $i; ?>"></i>
                            </button>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <textarea class="review-text" placeholder="Share your experience with this food..."
                        <?php echo $item['rating'] > 0 ? 'readonly' : ''; ?>><?php echo htmlspecialchars($item['comment'] ?? ''); ?></textarea>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="submit-section">
            <button id="submitReviews" class="btn-submit">
                Submit All Reviews
            </button>
        </div>
    </main>
    <!-- Main End -->

    <div id="alertModal" class="alert-modal">
        <div class="alert-content">
            <div class="alert-icon">
                <i class='bx bx-info-circle'></i>
            </div>
            <div class="alert-message"></div>
            <button class="alert-button">OK</button>
        </div>
    </div>
    <script>
    // Replace the star rating JavaScript with this improved version
    document.addEventListener('DOMContentLoaded', function() {
        const ratingCards = document.querySelectorAll('.rating-card');
        let reviewData = {};

        // Initialize review data for each card
        ratingCards.forEach(card => {
            const itemId = card.dataset.itemId;
            const currentRating = card.querySelector('.filled')?.parentElement?.dataset.value || 0;
            reviewData[itemId] = {
                rating: parseInt(currentRating),
                comment: card.querySelector('.review-text').value.trim()
            };
        });

        // Handle star rating
        ratingCards.forEach(card => {
            const itemId = card.dataset.itemId;
            const starsContainer = card.querySelector('.stars');
            const starButtons = card.querySelectorAll('.star-btn');
            const textarea = card.querySelector('.review-text');

            // Star button click handler
            starButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.value);
                    updateRating(card, rating);
                    reviewData[itemId].rating = rating;
                });

                // Hover effects
                btn.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.dataset.value);
                    showHoverState(card, rating);
                });
            });

            // Mouse leave handler for stars container
            starsContainer.addEventListener('mouseleave', function() {
                const currentRating = reviewData[itemId].rating;
                updateRating(card, currentRating);
            });

            // Textarea change handler
            textarea.addEventListener('input', function() {
                reviewData[itemId].comment = this.value.trim();
            });
        });

        // Function to update star rating display
        function updateRating(card, rating) {
            const stars = card.querySelectorAll('.star-btn i');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('bx-star');
                    star.classList.add('bxs-star', 'filled');
                } else {
                    star.classList.remove('bxs-star', 'filled');
                    star.classList.add('bx-star');
                }
            });
        }

        // Function to show hover state
        function showHoverState(card, rating) {
            const stars = card.querySelectorAll('.star-btn i');
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('bx-star');
                    star.classList.add('bxs-star', 'hover');
                } else {
                    star.classList.remove('bxs-star', 'hover');
                    star.classList.add('bx-star');
                }
            });
        }

        function showAlert(message, type = 'success', callback = null) {
            const modal = document.getElementById('alertModal');
            const messageEl = modal.querySelector('.alert-message');
            const icon = modal.querySelector('.alert-icon i');
            const button = modal.querySelector('.alert-button');

            // Reset classes
            modal.className = 'alert-modal';
            modal.classList.add(type);

            // Update icon based on type
            icon.className = 'bx';
            if (type === 'success') {
                icon.classList.add('bx-check-circle');
            } else if (type === 'error') {
                icon.classList.add('bx-x-circle');
            }

            // Set message
            messageEl.textContent = message;

            // Show modal
            modal.style.display = 'flex';

            // Handle button click
            button.onclick = () => {
                modal.style.display = 'none';
                if (callback) callback();
            };
        }

        // Update submit reviews handler
        document.getElementById('submitReviews').addEventListener('click', async function() {
            // Validate that at least one item has a rating
            let hasRating = Object.values(reviewData).some(review => review.rating > 0);

            if (!hasRating) {
                showAlert('Please rate at least one item', 'error');
                return;
            }

            try {
                const response = await fetch('functions/order.submit_rating.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: <?php echo $order_id; ?>,
                        reviews: reviewData
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showAlert('Thank you for your reviews!', 'success', () => {
                        window.location.href = 'order-history.php';
                    });
                } else {
                    throw new Error(data.message || 'Failed to submit reviews');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert(error.message || 'Error submitting reviews', 'error');
            }
        });

        // Close modal when clicking outside
        document.getElementById('alertModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    });
    </script>


</body>

</html>