<?php
    $limit = 5;
    $sql = "SELECT food_id, food_name, photo1, price, diet_type_suitable 
            FROM food_listings 
            WHERE available = 1 
            ORDER BY price ASC 
            LIMIT ?";

    // Prepare the statement for better performance
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i",  $limit);
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

            // Limit diet type to the first one if there are multiple
            $diet_types = explode(',', htmlspecialchars($row['diet_type_suitable'], ENT_QUOTES, 'UTF-8'));
            $diet_type = trim($diet_types[0]); // Get the first diet type

            // Render the product card
            ?>
            <div>
                <div class="product-card">
                    <div class="img-wrap">
                        <a href="product.php?prod=<?= $food_id ?>">
                            <img src="../../uploads/<?= $photo1 ?>" class="img-fluid" alt="<?= $food_name ?>" loading="lazy" />
                        </a>
                    </div>
                    <div class="content-wrap">
                        <a href="product.php?prod=<?= $food_id ?>" class="font-sm title-color"><?= $food_name ?></a>
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
        echo "<p>No low-priced items found.</p>";
    }

?>
