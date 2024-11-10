<style>

</style>
<div class="order-list">
    <?php
    $sql = "SELECT o.order_id, o.order_status, o.order_date, o.total_amount, o.final_total_amount,
                   CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
                   c.phone AS customer_phone,
                   ua.street_address, ua.city, ua.zip_code,
                   p.payment_method, p.payment_status,
                   r.first_name AS rider_first_name, r.last_name AS rider_last_name,
                   r.phone AS rider_phone
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN user_addresses ua ON o.address_id = ua.address_id
            LEFT JOIN payments p ON o.payment_id = p.payment_id
            LEFT JOIN delivery_riders r ON o.rider_id = r.rider_id
            WHERE o.kitchen_id = ? AND o.order_status = 'For Pickup'
            ORDER BY o.order_id ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $order_id = htmlspecialchars($row['order_id'], ENT_QUOTES, 'UTF-8');
            $customer_name = htmlspecialchars($row['customer_name'], ENT_QUOTES, 'UTF-8');
            $customer_phone = htmlspecialchars($row['customer_phone'], ENT_QUOTES, 'UTF-8');
            $address = htmlspecialchars($row['street_address'] . ', ' . $row['city'] . ', ' . $row['zip_code'], ENT_QUOTES, 'UTF-8');
            $order_date = htmlspecialchars($row['order_date'], ENT_QUOTES, 'UTF-8');
            $total_amount = htmlspecialchars($row['final_total_amount'], ENT_QUOTES, 'UTF-8');
            $payment_method = htmlspecialchars($row['payment_method'], ENT_QUOTES, 'UTF-8');
            $payment_status = htmlspecialchars($row['payment_status'], ENT_QUOTES, 'UTF-8');
            
            $order_status = htmlspecialchars($row['order_status'], ENT_QUOTES, 'UTF-8');

            
            // Rider info (if assigned)
            $rider_name = $row['rider_first_name'] ? 
                         htmlspecialchars($row['rider_first_name'] . ' ' . $row['rider_last_name'], ENT_QUOTES, 'UTF-8') : 
                         'Not Assigned';
            $rider_phone = $row['rider_phone'] ? 
                          htmlspecialchars($row['rider_phone'], ENT_QUOTES, 'UTF-8') : 
                          'N/A';
            ?>
    <div class="order-card">
        <div class="order-info">
            <div class="order-header">
                <h5 class="order-number">Order #<?= $order_id ?></h5>
                <div class="status-badge status-pickup"><?= $order_status ?></div>
            </div>

            <!-- Customer Information -->
            <div class="customer-info">
                <p><strong>Customer:</strong> <?= $customer_name ?></p>
                <p><strong>Phone:</strong> <?= $customer_phone ?></p>
                <p><strong>Address:</strong> <?= $address ?></p>
                <p><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($order_date)) ?></p>
            </div>

            <!-- Payment Information -->
            <div class="payment-info">
                <p>
                    <strong>Payment:</strong>
                    <?= ucfirst($payment_method) ?>
                    <span class="payment-status <?= strtolower($payment_status) ?>">
                        (<?= $payment_status ?>)
                    </span>
                </p>
                <p><strong>Total:</strong> ₱<?= number_format($total_amount, 2) ?></p>
            </div>

            <!-- Rider Information -->
            <div class="rider-info">
                <p><strong>Rider:</strong> <?= $rider_name ?></p>
                <?php if ($rider_phone !== 'N/A'): ?>
                <p><strong>Contact:</strong> <?= $rider_phone ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="order-actions">
            <a href="delivery.php?order_id=<?= $order_id ?>" class="btn-details">
                <i class="bi bi-eye"></i> View Details
            </a>
        </div>
    </div>
    <?php
        }
    } else {
        ?>
    <div class="no-orders-container">
        <img src="assets/svg/no-order.svg" alt="No Deliveries" class="no-orders-icon">
        <p class="no-orders-message">No orders out for delivery</p>
        <p class="no-orders-subtext">Check back later for new deliveries</p>
    </div>
    <?php
    }
    $stmt->close();
    ?>
</div>