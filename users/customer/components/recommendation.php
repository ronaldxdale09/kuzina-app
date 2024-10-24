<div class="offer-section section-p-t">
    <div class="offer">
        <div class="top-content">
            <div>
                <h4 class="title-color">Recommendation</h4>
                <p class="content-color">Based on your nutritional assessment</p>
            </div>
            <a href="javascript(0)" class="font-xs font-theme">See all</a>
        </div>

        <div class="offer-wrap">
            <?php
            // Function to fetch recommended products
            function fetchRecommendations($conn, $limit = 3) {
                $stmt = $conn->prepare("SELECT food_id, food_name, price, photo1, health_goal_suitable, meal_type FROM food_listings WHERE available = 1 LIMIT ?");
                $stmt->bind_param("i", $limit);
                $stmt->execute();
                $result = $stmt->get_result();
                $products = $result->fetch_all(MYSQLI_ASSOC);  // Fetch all results at once
                $stmt->close();
                return $products;
            }

            // Fetch recommended products
            $recommendations = fetchRecommendations($conn);

            // Check if products are available
            if (!empty($recommendations)) {
                foreach ($recommendations as $product) {
                    // Use heredoc to output HTML with embedded PHP
                    $food_id = htmlspecialchars($product['food_id']); // Get the food_id for the product link
                    ?>
            <div class="product-list media">
                <!-- Link the product card to product.php?prod=food_id -->
                <a href="product.php?prod=<?php echo $food_id; ?>">
                    <img src="../../uploads/<?php echo htmlspecialchars($product['photo1']); ?>"
                        alt="<?php echo htmlspecialchars($product['food_name']); ?>" />
                </a>
                <div class="media-body">
                    <!-- Link the product name to product.php?prod=food_id -->
                    <a href="product.php?prod=<?php echo $food_id; ?>"
                        class="font-sm"><?php echo htmlspecialchars($product['food_name']); ?></a>
                    <span
                        class="content-color font-xs"><?php echo htmlspecialchars($product['health_goal_suitable']); ?></span>
                    <span class="title-color font-sm">₱ <?php echo number_format($product['price'], 2); ?>
                        <span
                            class="badges-round bg-theme-theme font-xs"><?php echo htmlspecialchars($product['meal_type']); ?>
                        </span>
                    </span>
                    <div class="plus-minus d-xs-none">
                        <button class="cart-btn" onclick="addToCart()">
                            <i class="iconly-Bag-2 icli"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php
                }
            } else {
                // Display a fallback message if no products are available
                echo "<p>No recommendations available at the moment.</p>";
            }

            ?>
        </div>
    </div>
</div>