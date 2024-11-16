<?php
include '../../../connection/db.php';

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    // Updated SQL to include photo1
    $sql = "SELECT f.food_name, f.photo1, oi.quantity, f.price, 
            (oi.quantity * f.price) AS total_price 
            FROM order_items oi
            JOIN food_listings f ON oi.food_id = f.food_id
            WHERE oi.order_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<div class='order-details'>";
        echo "<h5 class='section-title'>Order Items</h5>";
        echo "<ul class='product-list'>";

        $grandTotal = 0;

        while ($row = $result->fetch_assoc()) {
            $food_name = htmlspecialchars($row['food_name'], ENT_QUOTES, 'UTF-8');
            $photo1 = htmlspecialchars($row['photo1'], ENT_QUOTES, 'UTF-8');
            $quantity = htmlspecialchars($row['quantity'], ENT_QUOTES, 'UTF-8');
            $price = number_format($row['price'], 2);
            $total_price = number_format($row['total_price'], 2);
            $grandTotal += $row['total_price'];

            // Default image if none is provided
            $imageUrl = $photo1 ? "../../uploads/" . $photo1 : "../../../assets/img/default-food.jpg";

            echo "
            <li class='product-item'>
                <div class='product-image'>
                    <img src='{$imageUrl}' alt='{$food_name}' class='food-thumbnail'>
                </div>
                <div class='product-info'>
                    <h6 class='product-name'>{$food_name}</h6>
                    
                    <div class='product-meta'>
                        <div class='meta-item'>
                            <span class='meta-label'>Quantity:</span>
                            <span class='meta-value'>{$quantity}</span>
                        </div>
                        <div class='meta-item'>
                            <span class='meta-label'>Price:</span>
                            <span class='meta-value'>₱{$price}</span>
                        </div>
                    </div>

                    <div class='product-total'>
                        <span class='total-label'>Subtotal:</span>
                        <span class='total-value'>₱{$total_price}</span>
                    </div>
                </div>
            </li>";
        }

        echo "</ul>";
        echo "<div class='order-summary'>
                <h5>Total Amount: ₱" . number_format($grandTotal, 2) . "</h5>
              </div>";
        echo "</div>";
    } else {
        echo "<div class='empty-state'>
                <p>No items found for this order.</p>
              </div>";
    }

    $stmt->close();
}
$conn->close();
?>