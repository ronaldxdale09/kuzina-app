<div class="order-list">
    <?php
                        $sql = "SELECT o.order_id, CONCAT(c.first_name, ' ', c.last_name) AS customer_name, 
                                    ua.street_address, ua.city, ua.zip_code, o.order_date, o.order_status, o.total_amount 
                                FROM orders o
                                JOIN customers c ON o.customer_id = c.customer_id
                                JOIN user_addresses ua ON o.address_id = ua.address_id
                                WHERE o.kitchen_id = ? AND o.order_status = 'Confirmed'
                                ORDER BY o.order_date ASC";
                        
                        $stmt = $conn->prepare($sql);
                        $kitchen_id = $_COOKIE['kitchen_id'];
                        $stmt->bind_param("i", $kitchen_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result && $result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $order_id = htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8');
                                $customer_name = htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8');
                                $address = htmlspecialchars($row['street_address'] . ', ' . $row['city'] . ', ' . $row['zip_code'], ENT_QUOTES, 'UTF-8');
                                $order_date = htmlspecialchars($row['order_date'], ENT_QUOTES, 'UTF-8');
                                $total_amount = htmlspecialchars($row['total_amount'], ENT_QUOTES, 'UTF-8');
                                ?>
    <div class="order-card">
        <div class="order-info">
            <h5>
                Order #<?= $order_id ?>
                <span class="status-badge status-confirmed">Confirmed</span>
            </h5>
            <p><strong>Customer:</strong> <?= $customer_name ?></p>
            <p><strong>Address:</strong> <?= $address ?></p>
            <p><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($order_date)) ?></p>
            <p><strong>Total:</strong> PHP <?= number_format($total_amount, 2) ?></p>
        </div>
        <div class="order-actions">
            <button class="btn-view" data-bs-toggle="offcanvas" data-bs-target="#orderDetailsModal"
                data-order-id="<?= $order_id ?>">
                View Details
            </button>

        </div>
    </div>
    <?php
                            }
                        } else {
                            ?>
    <div class="no-orders-container">
        <img src="assets/svg/no-order.svg" alt="No Orders" class="no-orders-icon">
        <p class="no-orders-message">No confirmed orders yet</p>
        <p class="no-orders-subtext">New orders will appear here once confirmed</p>
    </div>
    <?php
                        }
                        $stmt->close();
                        ?>
</div>