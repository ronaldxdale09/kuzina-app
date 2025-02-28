<?php
    $limit = 5;
    $sql = "SELECT 
                k.kitchen_id, 
                k.fname, 
                k.lname, 
                k.photo, 
                k.description,
                COALESCE(AVG(r.rating), 0) AS avg_rating,
                COUNT(r.review_id) AS review_count,
                SUBSTRING_INDEX(GROUP_CONCAT(DISTINCT f.category SEPARATOR ', '), ',', 1) AS main_food_category
            FROM kitchens k
            LEFT JOIN reviews r ON k.kitchen_id = r.kitchen_id
            LEFT JOIN food_listings f ON k.kitchen_id = f.kitchen_id AND f.listed = 1
            WHERE EXISTS (
                SELECT 1 
                FROM food_listings fl 
                WHERE fl.kitchen_id = k.kitchen_id 
                AND fl.listed = 1
            )
            GROUP BY k.kitchen_id
            ORDER BY avg_rating DESC
            LIMIT ?";

    // Prepare the statement for better performance
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();
    
    // Check if there are any items
    if ($result && $result->num_rows > 0) {
        // Start slider container
        echo '<div class="kitchen-slider">';
        
        // Loop through each kitchen and render it
        while ($row = $result->fetch_assoc()) {
            $kitchen_id = htmlspecialchars($row['kitchen_id'], ENT_QUOTES, 'UTF-8');
            $kitchen_name = htmlspecialchars($row['fname'] . ' ' . $row['lname'], ENT_QUOTES, 'UTF-8');
            $kitchen_photo = htmlspecialchars($row['photo'], ENT_QUOTES, 'UTF-8');
            $kitchen_description = htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8');
            $avg_rating = floatval($row['avg_rating']); // Ensure it's a float
            $review_count = intval($row['review_count']); // Ensure it's an integer
            $main_food_category = htmlspecialchars($row['main_food_category'], ENT_QUOTES, 'UTF-8');

            // Limit description to 100 characters
            $short_description = strlen($kitchen_description) > 100 ? substr($kitchen_description, 0, 100) . '...' : $kitchen_description;

            // Render the kitchen profile
            ?>
<a href="kitchen.php?id=<?= $kitchen_id ?>" class="kitchen-profile">
    <div class="kitchen-header">
        <img src="../../uploads/kitchen_photos/<?= $kitchen_photo ?>" class="kitchen-img" alt="<?= $kitchen_name ?>"
            loading="lazy" />
        <div class="kitchen-info">
            <h3><?= $kitchen_name ?></h3>
            <p><?= $short_description ?></p>
            <div class="rating">
                <span class="stars"><?= str_repeat('★', round($avg_rating)) ?></span>
                <span class="rating-value"><?= number_format($avg_rating, 1) ?> (<?= $review_count ?> reviews)</span>
            </div>
            <?php if (!empty($main_food_category)): ?>
            <div class="food-badge">
                <span><?= $main_food_category ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</a>
<?php
        }
        
        // End slider container
        echo '</div>';
    } else {
        // If no items are found, display a message
        echo "<p>No kitchens found.</p>";
    }
?>