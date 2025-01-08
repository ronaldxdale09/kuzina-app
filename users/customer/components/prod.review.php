<?php
// Fetch all reviews for this food item, including order information
$reviewSql = "SELECT r.review_id, r.rating, r.comment, r.created_at, 
              c.first_name, c.last_name, c.photo,
              o.order_id, oi.food_id
              FROM reviews r 
              JOIN customers c ON r.customer_id = c.customer_id
              JOIN orders o ON r.order_id = o.order_id
              JOIN order_items oi ON o.order_id = oi.order_id AND r.food_id = oi.food_id
              WHERE r.food_id = ? 
              ORDER BY r.created_at DESC
              LIMIT 2";

$reviewStmt = $conn->prepare($reviewSql);
$reviewStmt->bind_param("i", $productId); // Note: productId should be foodId for consistency
$reviewStmt->execute();
$reviewResult = $reviewStmt->get_result();
$reviews = $reviewResult->fetch_all(MYSQLI_ASSOC);
$reviewStmt->close();

// Get total review count
$countSql = "SELECT COUNT(*) as total 
             FROM reviews r
             JOIN orders o ON r.order_id = o.order_id
             JOIN order_items oi ON o.order_id = oi.order_id AND r.food_id = oi.food_id
             WHERE r.food_id = ?";
$countStmt = $conn->prepare($countSql);
$countStmt->bind_param("i", $productId);
$countStmt->execute();
$totalReviews = $countStmt->get_result()->fetch_assoc()['total'];
$countStmt->close();
?>
<!-- Product Review Section -->
<section class="product-review pb-0">
    <div class="top-content">
        <h3 class="title-color">Product Review (<?php echo $totalReviews; ?>)</h3>
        <?php if ($totalReviews > 2): ?>
        <a href="all-reviews.php?product=<?php echo $productId; ?>" class="font-xs">See all</a>
        <?php endif; ?>
    </div>
    <div class="review-wrap">
        <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $review): ?>
        <?php
                $customerName = htmlspecialchars($review['first_name'] . ' ' . $review['last_name']);
                $customerPhoto = !empty($review['photo']) 
                    ? "../../uploads/profile/" . htmlspecialchars($review['photo']) 
                    : "assets/images/avatar/avatar2.png";
                $rating = intval($review['rating']);
                $comment = htmlspecialchars($review['comment']);
                $createdAt = date("F j, Y", strtotime($review['created_at']));
                $orderId = $review['order_id'];
                ?>
        <div class="review-box">
            <div class="media">
                <img src="<?php echo $customerPhoto; ?>" alt="<?php echo $customerName; ?>" class="user-avatar" />
                <div class="media-body">
                    <div class="review-header">
                        <h4 class="font-sm title-color"><?php echo $customerName; ?></h4>
                        <span class="order-id font-xs">Order #<?php echo $orderId; ?></span>
                    </div>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="feather-star <?php echo $i <= $rating ? 'filled' : ''; ?>"></i>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            <div class="review-content">
                <p class="font-sm content-color"><?php echo $comment; ?></p>
                <div class="review-footer">
                    <span class="font-xs content-color"><?php echo $createdAt; ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="no-reviews">
            <p class="font-sm content-color">No reviews yet for this product.</p>
        </div>
        <?php endif; ?>
    </div>
</section>


<!-- POPUP -->
<div class="offcanvas all-review-offcanvas offcanvas-bottom" tabindex="-1" id="all-review" aria-labelledby="all-review">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">All Reviews</h5>
        <span data-bs-dismiss="offcanvas" aria-label="Close"><i data-feather="x"></i></span>
    </div>
    <div class="offcanvas-body small" id="all-review-content">
        <!-- Reviews will be dynamically loaded here -->
        <p class="font-sm content-color">Loading reviews...</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const allReviewOffcanvas = document.getElementById('all-review');
    const allReviewContent = document.getElementById('all-review-content');

    // Event listener for when the offcanvas is shown
    allReviewOffcanvas.addEventListener('show.bs.offcanvas', () => {
        const productId = <?php echo $productId; ?>; // Pass the product ID from PHP

        // Fetch reviews via AJAX
        fetch(`fetch/fetchReviews.php?product_id=${productId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear existing content
                    allReviewContent.innerHTML = '';

                    // Populate reviews
                    data.reviews.forEach(review => {
                        const reviewBox = document.createElement('div');
                        reviewBox.className = 'review-box';

                        reviewBox.innerHTML = `
                            <div class="media">
                                <img src="${review.customer_photo}" alt="avatar" />
                                <div class="media-body">
                                    <h4 class="font-sm title-color">${review.customer_name}</h4>
                                    <div class="rating">
                                        ${'<i data-feather="star" class="filled"></i>'.repeat(review.rating)}
                                        ${'<i data-feather="star" class="empty"></i>'.repeat(5 - review.rating)}
                                    </div>
                                </div>
                            </div>
                            <p class="font-sm content-color">${review.comment}</p>
                            <span class="font-xs content-color">${review.created_at}</span>
                        `;

                        allReviewContent.appendChild(reviewBox);
                    });

                    // Initialize Feather Icons (if needed)
                    if (feather) {
                        feather.replace();
                    }
                } else {
                    allReviewContent.innerHTML =
                        `<p class="font-sm content-color">${data.message}</p>`;
                }
            })
            .catch(error => {
                console.error('Error fetching reviews:', error);
                allReviewContent.innerHTML =
                    `<p class="font-sm content-color">Failed to load reviews. Please try again later.</p>`;
            });
    });

    // Event listener for when the offcanvas is hidden
    allReviewOffcanvas.addEventListener('hidden.bs.offcanvas', () => {
        // Clear the content when the offcanvas is closed
        allReviewContent.innerHTML = '<p class="font-sm content-color">Loading reviews...</p>';
    });
});
</script>