
<!-- Order Details Modal -->
<div class="offcanvas order-details-modal offcanvas-bottom" tabindex="-1" id="orderDetailsModal"
    aria-labelledby="orderDetailsModalLabel">
    <div class="offcanvas-header">
        <h5>Order Details</h5>
    </div>
    
    <div class="offcanvas-body" id="orderDetailsContent">
        <div class="loading">Loading order details...</div>
    </div>
    
    <div class="offcanvas-footer">
        <button class="btn-outline" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
        <button id="processOrderBtn" class="btn-solid" data-order-id="">Process Order</button>
    </div>
</div>

<!-- Success Notification Modal -->
<div class="modal" id="successNotificationModal">
    <div class="modal-content">
        <span class="close-modal" id="closeSuccessModal">&times;</span>
        <div class="modal-item-info">
            <h2>Order Processed</h2>
            <p>The order has been successfully moved to Preparing status.</p>
        </div>
        <div class="modal-actions">
            <button class="btn-confirm" id="successModalConfirm">OK</button>
        </div>
    </div>
</div>
<script>
// JavaScript to handle loading order details in the modal
document.addEventListener('DOMContentLoaded', function() {
    const orderDetailsModal = new bootstrap.Offcanvas(document.getElementById('orderDetailsModal'));
    const orderDetailsContent = document.getElementById('orderDetailsContent');
    const processOrderBtn = document.getElementById('processOrderBtn');
    const successModal = document.getElementById('successNotificationModal');
    const closeSuccessModal = document.getElementById('closeSuccessModal');
    const successModalConfirm = document.getElementById('successModalConfirm');

    document.querySelectorAll('.btn-view').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            processOrderBtn.setAttribute('data-order-id', orderId);

            // Fetch order details using AJAX
            fetch('fetch/fetch_order_details.php?order_id=' + orderId)
                .then(response => response.text())
                .then(data => {
                    orderDetailsContent.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching order details:', error);
                    orderDetailsContent.innerHTML =
                        '<p>Error loading order details. Please try again.</p>';
                });
        });
    });

    // Event listener for the "Process Order" button
    processOrderBtn.addEventListener('click', function() {
        const orderId = this.getAttribute('data-order-id');

        // Call the function to move the order to "Preparing"
        processOrder(orderId);
    });

    // Function to open the success modal
    function showSuccessModal() {
        const successModal = document.getElementById('successNotificationModal');
        successModal.classList.add('show');
    }

    // Function to close the success modal
    function closeModal() {
        const successModal = document.getElementById('successNotificationModal');
        successModal.classList.remove('show');
        location.reload(); // Reload the page after closing the modal
    }
    // Add event listeners to close the modal
    closeSuccessModal.addEventListener('click', closeModal);
    successModalConfirm.addEventListener('click', closeModal);

    // Function to change the order status to "Preparing"
    function processOrder(orderId) {
        fetch('functions/process_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    order_id: orderId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Close the order details modal before showing the success modal
                    orderDetailsModal.hide();
                    setTimeout(showSuccessModal, 300); // Wait for the modal animation to finish
                } else {
                    orderDetailsContent.innerHTML +=
                        '<p class="error-message">Error processing the order: ' + data.message + '</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                orderDetailsContent.innerHTML +=
                    '<p class="error-message">An error occurred. Please try again.</p>';
            });
    }
});
</script>