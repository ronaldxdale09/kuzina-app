<?php
// Optimized function to fetch and render random products with reviews
function fetch_and_render_random_products($conn, $limit = 5) {
    // Fetch random products with reviews (average rating and review count)
    $sql = "SELECT fl.food_id, fl.food_name, fl.photo1, fl.price, fl.diet_type_suitable, fl.kitchen_id,
       COALESCE(AVG(r.rating), 0) AS avg_rating,
       COUNT(r.review_id) AS review_count
FROM food_listings fl
LEFT JOIN reviews r ON fl.food_id = r.food_id
WHERE fl.available = 1 
AND fl.listed = 1 
AND fl.isApproved = 1
GROUP BY fl.food_id
ORDER BY RAND()
LIMIT ?";  // Order by RAND() for random products

    // Prepare the statement for better performance
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);  // Limit the number of results
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    
    // Check if there are any items
    if ($result && $result->num_rows > 0) {
        // Loop through each item and render it
        while ($row = $result->fetch_assoc()) {
            $food_id = htmlspecialchars($row['food_id'], ENT_QUOTES, 'UTF-8');
            $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
            $photo1 = htmlspecialchars($row['photo1'], ENT_QUOTES, 'UTF-8');
            $price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
            $diet_types = explode(',', htmlspecialchars($row['diet_type_suitable'], ENT_QUOTES, 'UTF-8'));
            $diet_type = trim($diet_types[0]);  // Get the first diet type
            $avg_rating = floatval($row['avg_rating']);  // Average rating
            $review_count = intval($row['review_count']);  // Number of reviews

            // Render the product card
            ?>
<div class="product-card-wrap">
    <div class="product-card">
        <div class="img-wrap">
            <a href="product.php?prod=<?= $food_id ?>">
                <!-- Lazy loading for images -->
                <img src="../../uploads/<?= $photo1 ?>" class="img-fluid" alt="<?= $food_name ?>" loading="lazy" />
            </a>
        </div>
        <div class="content-wrap">
            <a href="product.php?prod=<?= $food_id ?>" class="font-sm title-color truncate-text">
                <?= $food_name ?>
            </a>
            <!-- Display star rating and review count -->
            <div class="rating">
                <span class="stars">
                    <?= str_repeat('★', round($avg_rating)) ?><?= str_repeat('☆', 5 - round($avg_rating)) ?>
                </span>
                <span class="review-count">(<?= $review_count ?>)</span>
            </div>
        </div>
        <!-- Display diet type inside a badge -->
        <span class="badge"><?= $diet_type ?></span>
        <div class="price-cart-wrap">
            <span class="title-color font-sm">₱<?= $price ?></span>
            <a href="product.php?prod=<?= $food_id ?>" class="cart-icon-link">
                <i class="iconly-Bag-2 cart-icon"></i>
            </a>
        </div>
    </div>
</div>
<?php
        }
    } else {
        // If no items are found, display a message
        echo "<p>No random products found.</p>";
    }
    $stmt->close();  // Close the statement
}
?>