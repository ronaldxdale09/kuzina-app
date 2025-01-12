<?php
// Get kitchen_id from cookie
$kitchen_id = $_COOKIE['kitchen_id'] ?? null;

// Initialize variables
$average_rating = 0;
$total_reviews = 0;

if ($kitchen_id) {
    // Query to get average rating and total reviews
    $query = "SELECT 
                COUNT(*) as total_reviews,
                ROUND(AVG(rating), 1) as average_rating
              FROM reviews 
              WHERE kitchen_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $average_rating = $row['average_rating'] ?? 0;
        $total_reviews = $row['total_reviews'] ?? 0;
    }
    $stmt->close();
}
?>

<!-- Reviews Summary Section -->
<section class="reviews-section">
    <div class="reviews-summary">
        <div class="review-score">
            <span class="star">★</span>
            <h4><?php echo number_format($average_rating, 1); ?></h4>
        </div>
        <p class="total-reviews">Total <?php echo number_format($total_reviews); ?> Reviews</p>
        <a href="javascript:void(0)" class="review-link" data-bs-toggle="offcanvas" data-bs-target="#reviewsModal">
            See All Reviews
        </a>
    </div>
</section>

<!-- Reviews Modal -->
<div class="offcanvas offcanvas-bottom reviews-modal" tabindex="-1" id="reviewsModal" aria-labelledby="reviewsModalLabel">
    <div class="offcanvas-header">
        <h5 class="modal-title">Customer Reviews</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="reviews-container">
            <!-- Reviews will be loaded here via AJAX -->
            <div class="text-center p-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Reviews Section Styles */
.reviews-section {
    background-color: #ffffff;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-align: center;
}

.review-score {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 10px;
}

.review-score .star {
    font-size: 2em;
    color: #FFD700;
    margin-right: 10px;
}

.review-score h4 {
    font-size: 2em;
    color: #4CAF50;
    margin: 0;
}

.total-reviews {
    color: #666;
    margin-bottom: 15px;
}

.review-link {
    color: #502121;
    text-decoration: none;
    font-weight: bold;
    padding: 5px 15px;
    border-radius: 15px;
    background: rgba(80, 33, 33, 0.1);
    transition: all 0.3s ease;
}

.review-link:hover {
    background: rgba(80, 33, 33, 0.2);
}

/* Modal Styles */
.reviews-modal {
    height: 80vh;
}

.reviews-modal .offcanvas-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #dee2e6;
}

.reviews-modal .modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #502121;
}

.reviews-modal .offcanvas-body {
    padding: 1.5rem;
}

.review-item {
    border-bottom: 1px solid #eee;
    padding: 1rem 0;
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.reviewer-name {
    font-weight: 600;
    color: #333;
}

.review-rating {
    color: #FFD700;
}

.review-date {
    font-size: 0.85rem;
    color: #666;
}

.review-content {
    color: #444;
    line-height: 1.5;
}

.no-reviews {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.review-item {
    border-bottom: 1px solid #eee;
    padding: 1rem 0;
}

.review-item:last-child {
    border-bottom: none;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.reviewer-info {
    flex-grow: 1;
}

.reviewer-name {
    font-weight: 600;
    color: #333;
    margin-bottom: 2px;
}

.review-rating {
    color: #FFD700;
    white-space: nowrap;
}

.review-date {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 8px;
}

.food-details {
    display: flex;
    align-items: center;
    background: #f8f9fa;
    padding: 8px;
    border-radius: 8px;
    margin-bottom: 10px;
}

.food-image {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 12px;
}

.food-info {
    flex-grow: 1;
}

.food-name {
    font-weight: 500;
    color: #502121;
    margin-bottom: 2px;
}

.order-id {
    font-size: 0.85rem;
    color: #666;
}

.review-content {
    color: #444;
    line-height: 1.5;
    margin-top: 8px;
}

.no-reviews {
    text-align: center;
    padding: 2rem;
    color: #666;
}

.no-reviews i {
    display: block;
    margin-bottom: 1rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reviewsModal = document.getElementById('reviewsModal');
    
    reviewsModal.addEventListener('show.bs.offcanvas', function() {
        loadReviews();
    });
    
    function loadReviews() {
        const container = document.querySelector('.reviews-container');
        
        // Show loading spinner
        container.innerHTML = `
            <div class="text-center p-3">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
        // Fetch reviews
        fetch('fetch/get_reviews.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.reviews.length > 0) {
                        container.innerHTML = data.reviews.map(review => `
                            <div class="review-item">
                                <div class="review-header">
                                    <div class="reviewer-info">
                                        <div class="reviewer-name">${review.customer_name}</div>
                                        <div class="review-rating">
                                            ${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}
                                        </div>
                                        <div class="review-date">
                                            ${new Date(review.created_at).toLocaleDateString()}
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="food-details">
                                    <img src="../../uploads/${review.food_photo}" 
                                         alt="${review.food_name}" 
                                         class="food-image"
                                         onerror="this.src='assets/images/default-food.jpg'">
                                    <div class="food-info">
                                        <div class="food-name">${review.food_name}</div>
                                        <div class="order-id">Order #${review.order_id}</div>
                                    </div>
                                </div>
                                
                                <div class="review-content">
                                    ${review.comment}
                                </div>
                            </div>
                        `).join('');
                    } else {
                        container.innerHTML = `
                            <div class="no-reviews">
                                <i class='bx bx-message-square-detail' style='font-size: 48px; color: #ddd;'></i>
                                <p>No reviews yet</p>
                            </div>
                        `;
                    }
                } else {
                    container.innerHTML = `
                        <div class="alert alert-danger">
                            Failed to load reviews. Please try again.
                        </div>
                    `;
                }
            })
            .catch(error => {
                container.innerHTML = `
                    <div class="alert alert-danger">
                        An error occurred while loading reviews.
                    </div>
                `;
                console.error('Error:', error);
            });
    }
});
</script>