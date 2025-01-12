<?php
function fetch_and_render_kitchen_products($conn, $kitchen_id) {
    // Limit the number of results to improve performance
    $limit = 5;
    $sql = "SELECT food_id, food_name, photo1, price, diet_type_suitable 
FROM food_listings 
WHERE available = 1 
AND kitchen_id = ? 
AND isApproved = 0
AND listed = 1
LIMIT ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $kitchen_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $food_id = htmlspecialchars($row['food_id'], ENT_QUOTES, 'UTF-8');
            $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
            $photo1 = htmlspecialchars($row['photo1'], ENT_QUOTES, 'UTF-8');
            $price = htmlspecialchars($row['price'], ENT_QUOTES, 'UTF-8');
            $diet_types = explode(',', htmlspecialchars($row['diet_type_suitable'], ENT_QUOTES, 'UTF-8'));
            $diet_type = trim($diet_types[0]);
            ?>
            <div class="product-card-wrap">
                <div class="product-card">
                    <div class="img-wrap">
                        <a href="product.php?prod=<?= $food_id ?>">
                            <img src="../../uploads/<?= $photo1 ?>" class="img-fluid" alt="<?= $food_name ?>" loading="lazy" />
                        </a>
                    </div>
                    <div class="content-wrap">
                        <a href="product.php?prod=<?= $food_id ?>" class="font-sm title-color" style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= $food_name ?>
                        </a>
                    </div>
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
        echo "<p>No products found for this kitchen.</p>";
    }
    $stmt->close();
}

// Example usage
// fetch_and_render_kitchen_products($conn, $kitchen_id);
?>