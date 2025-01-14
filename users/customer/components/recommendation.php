<div class="offer-section section-p-t">
    <div class="offer">
        <div class="top-content">
            <div>
                <h4 class="title-color">Recommendation</h4>
                <p class="content-color">Based on your nutritional assessment</p>
            </div>
            <a href="javascript:void(0)" class="font-xs font-theme">See all</a>
        </div>

        <div class="offer-wrap">
            <?php
            // Function to fetch recommended products with kitchen reviews
            function fetchRecommendations($conn, $customer_id, $limit = 3) {
                // First get the user's latest assessment
                $assessment_sql = "SELECT diet_type, health_goal 
                                  FROM nutritional_assessments 
                                  WHERE customer_id = ? 
                                  ORDER BY created_at DESC 
                                  LIMIT 1";
                                  
                $assessment_stmt = $conn->prepare($assessment_sql);
                $assessment_stmt->bind_param("i", $customer_id);
                $assessment_stmt->execute();
                $assessment_result = $assessment_stmt->get_result();
                
                // If user has an assessment, use it for recommendations
                if ($assessment = $assessment_result->fetch_assoc()) {
                    $sql = "SELECT fl.food_id, fl.food_name, fl.price, fl.photo1, 
                                   fl.health_goal_suitable, fl.meal_type, fl.kitchen_id,
                                   COALESCE(AVG(r.rating), 0) AS avg_rating,
                                   COUNT(r.review_id) AS review_count
                            FROM food_listings fl
                            LEFT JOIN reviews r ON fl.food_id = r.food_id
                            WHERE fl.available = 1 
                            AND fl.listed = 1
                            AND fl.isApproved = 1
                            AND (
                                FIND_IN_SET(?, fl.diet_type_suitable) > 0
                                OR FIND_IN_SET(?, fl.health_goal_suitable) > 0
                            )
                            GROUP BY fl.food_id
                            LIMIT ?";
                            
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssi", $assessment['diet_type'], 
                                           $assessment['health_goal'], 
                                           $limit);
                } else {
                    // Fallback query if no assessment found
                    $sql = "SELECT fl.food_id, fl.food_name, fl.price, fl.photo1, 
                                   fl.health_goal_suitable, fl.meal_type, fl.kitchen_id,
                                   COALESCE(AVG(r.rating), 0) AS avg_rating,
                                   COUNT(r.review_id) AS review_count
                            FROM food_listings fl
                            LEFT JOIN reviews r ON fl.food_id = r.food_id
                            WHERE fl.available = 1 
                            AND fl.listed = 1
                            AND fl.isApproved = 1
                            GROUP BY fl.food_id
                            LIMIT ?";
                            
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $limit);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                $products = $result->fetch_all(MYSQLI_ASSOC);
                
                $stmt->close();
                $assessment_stmt->close();
                
                return $products;
            }
            
            // Usage:
            $customer_id = $_COOKIE['user_id'] ?? null;
            $recommendations = $customer_id ? fetchRecommendations($conn, $customer_id) : [];
            ;

            // Check if products are available
            if (!empty($recommendations)) {
                foreach ($recommendations as $product) {
                    $food_id = htmlspecialchars($product['food_id'], ENT_QUOTES, 'UTF-8');
                    $food_name = htmlspecialchars($product['food_name'], ENT_QUOTES, 'UTF-8');
                    $photo1 = htmlspecialchars($product['photo1'], ENT_QUOTES, 'UTF-8');
                    $price = number_format($product['price'], 2);
                    $health_goal = htmlspecialchars($product['health_goal_suitable'], ENT_QUOTES, 'UTF-8');
                    $meal_type = htmlspecialchars($product['meal_type'], ENT_QUOTES, 'UTF-8');
                    $avg_rating = floatval($product['avg_rating']); // Average rating
                    $review_count = intval($product['review_count']); // Number of reviews
                    ?>
                    <div class="product-list media">
                        <!-- Link the product card to product.php?prod=food_id -->
                        <a href="product.php?prod=<?= $food_id ?>">
                            <img src="../../uploads/<?= $photo1 ?>" 
                                 alt="<?= $food_name ?>" 
                                loading="lazy" 
                                 class="img-fluid" />
                        </a>
                        <div class="media-body">
                            <!-- Link the product name to product.php?prod=food_id -->
                            <a href="product.php?prod=<?= $food_id ?>" 
                               class="font-sm truncate-text">
                                <?= $food_name ?>
                            </a>
                            <!-- Display star rating and review count -->
                            <div class="rating">
                                <span class="stars">
                                    <?= str_repeat('★', round($avg_rating)) ?><?= str_repeat('☆', 5 - round($avg_rating)) ?>
                                </span>
                                <span class="review-count">(<?= $review_count ?> reviews)</span>
                            </div>
                            <span class="content-color font-xs"><?= $health_goal ?></span>
                            <span class="title-color font-sm">₱ <?= $price ?>
                                <span class="badges-round bg-theme-theme font-xs"><?= $meal_type ?></span>
                            </span>
                            <div class="plus-minus d-xs-none">
                                <button class="cart-btn" onclick="addToCart(<?= $food_id ?>)">
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