<div class="order-list">
    <?php
    $sql = "SELECT o.order_id, CONCAT(c.first_name, ' ', c.last_name) AS customer_name, 
                   ua.street_address, ua.city, ua.zip_code, o.order_date, o.order_status, o.total_amount 
            FROM orders o
            JOIN customers c ON o.customer_id = c.customer_id
            JOIN user_addresses ua ON o.address_id = ua.address_id
            WHERE o.kitchen_id = ? AND o.order_status = 'Preparing'
            ORDER BY o.order_id ASC";
    
    $stmt = $conn->prepare($sql);
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
            <div class="order-header">
                <h5 class="order-number">Order #<?= $order_id ?></h5>
                <div class="status-badge status-preparing">Preparing</div>
            </div>
            <p><strong>Customer:</strong> <?= $customer_name ?></p>
            <p><strong>Address:</strong> <?= $address ?></p>
            <p><strong>Date:</strong> <?= date('M d, Y h:i A', strtotime($order_date)) ?></p>
            <p><strong>Total:</strong> ₱<?= number_format($total_amount, 2) ?></p>
        </div>
        <div class="order-actions">
            <button class="btn-details" onclick="viewOrderDetails(<?= $order_id ?>)">
                <i class="bi bi-eye"></i> View Order Details
            </button>
        </div>
    </div>
    <?php
        }
    } else {
        ?>
    <div class="no-orders-container">
        <img src="assets/svg/no-order.svg" alt="No Orders" class="no-orders-icon">
        <p class="no-orders-message">No orders in preparation</p>
        <p class="no-orders-subtext">Check back later for new orders</p>
    </div>
    <?php
    }
    $stmt->close();
    ?>
</div>

<!-- Order Details Modal -->
<div class="offcanvas order-details-modal offcanvas-bottom" tabindex="-1" id="preparingOrderModal">
    <div class="offcanvas-header">
        <h5 class="modal-title">Order Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body" id="orderDetailsPrepContent">
        <div class="loading-spinner">Loading...</div>
    </div>
    <div class="modal-footer-buttons">
        <button class="btn-details btn-print" onclick="printOrder()">
            <i class="bi bi-printer"></i> Print Order
        </button>
        <button class="btn-details btn-ready" onclick="markOrderReady()">
            <i class="bi bi-check2-circle"></i> Mark as Ready
        </button>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-check-circle text-success fs-1"></i>
                <h5 class="mt-3">Order Ready</h5>
                <p class="mb-0">Order has been marked as ready for pickup</p>
            </div>
            <div class="modal-footer justify-content-center border-top-0">
                <button type="button" class="btn-confirm" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<script>
// Function to view order details
// Function to view order details
function viewOrderDetails(orderId) {
    const modal = new bootstrap.Offcanvas(document.getElementById('preparingOrderModal'));
    const contentDiv = document.getElementById('orderDetailsPrepContent');
    
    // Store orderId in a data attribute for later use
    contentDiv.setAttribute('data-order-id', orderId);

    // Show loading state
    contentDiv.innerHTML = `
        <div class="loading-state">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading order details...</p>
        </div>
    `;

    // Show modal
    modal.show();

    // Fetch order details
    fetch(`fetch/fetch_order_details.php?order_id=${orderId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(data => {
            if (data.trim().length === 0) {
                throw new Error('Empty response received');
            }
            contentDiv.innerHTML = data;
        })
        .catch(error => {
            console.error('Error:', error);
            contentDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Error loading order details. Please try again.
                    <br>
                    <small class="mt-2 d-block">Details: ${error.message}</small>
                </div>
            `;
        });
}


// Function to print order
function printOrder() {
    const content = document.getElementById('preparingOrderModal').innerHTML;
    const printWindow = window.open('', '_blank');

    printWindow.document.write(`
        <html>
            <head>
                <title>Order Details - Print</title>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        padding: 20px;
                        line-height: 1.6;
                    }
                   /* Product List Styling */
.product-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

/* Product Item Layout */
.product-item {
    display: flex;
    gap: 16px;
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 12px;
    transition: transform 0.2s ease;
}

.product-item:hover {
    transform: translateY(-2px);
}

/* Product Image Container */
.product-image {
    flex-shrink: 0;
    width: 80px;
    height: 80px;
    border-radius: 8px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Product Image */
.food-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

/* Product Information */
.product-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 8px 0;
}

/* Product Meta Information */
.product-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 8px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.meta-label {
    color: #64748b;
    font-size: 0.9rem;
}

.meta-value {
    color: #0f172a;
    font-weight: 500;
    font-size: 0.9rem;
}

/* Product Total */
.product-total {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    margin-top: auto;
    padding-top: 8px;
    border-top: 1px dashed #e2e8f0;
}

.total-label {
    color: #64748b;
    font-size: 0.9rem;
}

.total-value {
    color: #0f172a;
    font-weight: 600;
    font-size: 1rem;
}

/* Order Summary */
.order-summary {
    background: #f1f5f9;
    border-radius: 12px;
    padding: 16px;
    margin-top: 20px;
}

.order-summary h5 {
    color: #0f172a;
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    text-align: right;
}


/* Print Styles */
@media print {
    .product-item {
        break-inside: avoid;
        border: 1px solid #e2e8f0;
        margin-bottom: 10px;
    }

    .product-image {
        width: 60px;
        height: 60px;
    }
}
                    @media print {
                        .no-print { 
                            display: none !important; 
                        }
                    }
                </style>
            </head>
            <body>
                <h2>Order Details</h2>
                ${content}
            </body>
        </html>
    `);

    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}
// Function to mark order as ready
function markOrderReady() {
    // Get the order ID from the data attribute
    const contentDiv = document.getElementById('orderDetailsPrepContent');
    const orderId = contentDiv.getAttribute('data-order-id');

    if (!orderId) {
        console.error('No order ID found');
        return;
    }

    // Show loading state on button
    const readyBtn = document.querySelector('.btn-ready');
    const originalText = readyBtn.innerHTML;
    readyBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
    readyBtn.disabled = true;

    fetch('functions/mark_order_ready.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            order_id: orderId
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close order details modal
            const orderModal = document.getElementById('preparingOrderModal');
            if (orderModal) {
                bootstrap.Offcanvas.getInstance(orderModal).hide();
            }

            // Show success modal
            const successModal = document.getElementById('successModal');
            const successTitle = successModal.querySelector('h5');
            const successMessage = successModal.querySelector('p');

            successTitle.textContent = 'Order Ready';
            successMessage.textContent = `Order #${orderId} has been marked as ready for delivery.`;

            const bsSuccessModal = new bootstrap.Modal(successModal);
            bsSuccessModal.show();

            // Refresh page after delay
            // setTimeout(() => {
            //     location.reload();
            // }, 1500);
        } else {
            throw new Error(data.message || 'Failed to update order status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message in modal
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-3';
        errorDiv.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>
            ${error.message || 'An error occurred. Please try again.'}
        `;
        contentDiv.appendChild(errorDiv);
    })
    .finally(() => {
        // Restore button state
        readyBtn.innerHTML = originalText;
        readyBtn.disabled = false;
    });
}

// Event listener for success modal close
document.getElementById('successModal').addEventListener('hidden.bs.modal', () => {
    location.reload();
});

// Add debugging
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script loaded');
    const viewButtons = document.querySelectorAll('.btn-details');
    console.log('Found view buttons:', viewButtons.length);
});
</script>