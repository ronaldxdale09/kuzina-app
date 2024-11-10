<div class="action action-confirmation offcanvas offcanvas-bottom" tabindex="-1" id="paymentMethodModal"
    aria-labelledby="paymentMethodModalLabel">
    <div class="offcanvas-body small">
        <div class="confirmation-box">
            <h2 class="title-color">Select Payment Method</h2>
            <p class="font-sm content-color">Please select a payment method to proceed with your order.</p>
            <div class="btn-box">
                <button class="btn-outline font-md" data-bs-dismiss="offcanvas" aria-label="Close">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Custom Modal -->
<div class="modal" id="paymentNotificationModal">
    <div class="modal-content">
        <span class="close-modal" id="closeModal">&times;</span>
        <div class="modal-item-info">
            <h2 id="modalTitle">Payment Status</h2>
            <p id="modalMessage">Your payment was processed successfully.</p>
        </div>
        <div class="modal-actions">
            <button class="btn-cancel" id="modalCancel">Cancel</button>
            <button class="btn-confirm" id="modalConfirm">OK</button>
        </div>
    </div>
</div>
