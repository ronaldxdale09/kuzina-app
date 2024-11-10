const PAYMONGO_PUBLIC_KEY = 'pk_test_1JxMWEHpEruyrNiD7MuvtVu9';

// Generate a token once for the entire payment session (used only for e-wallet payments)
let paymentToken = generateToken();

// Function to redirect after successful e-wallet payment
function handlePaymentSuccess(paymentMethod) {
    console.log("Redirecting to payment.php with status=success");
    window.location.href = `payment.php?status=success&payment_method=${encodeURIComponent(paymentMethod)}&payment_token=${encodeURIComponent(paymentToken)}`;
}

// Check if there's a status in the URL on page load
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const paymentMethod = urlParams.get('payment_method');
    const paymentTokenFromUrl = urlParams.get('payment_token');

    // Use the token from the URL if available (only for e-wallet)
    if (paymentTokenFromUrl) paymentToken = paymentTokenFromUrl;

    // Verify payment if redirected back with status=success (only for e-wallet)
    if (status === 'success' && paymentMethod && paymentToken) {
        console.log("Verifying e-wallet payment");
        verifyPayment(status, paymentMethod, paymentToken);
    }
};

// Confirm Payment Button Listener
document.getElementById('confirm-payment').addEventListener('click', function(event) {
    event.preventDefault();

    // Get the selected payment method
    const selectedPaymentMethodElement = document.querySelector('.filter-col.active[data-payment-method]');
    if (!selectedPaymentMethodElement) {
        // Show modal if no payment method is selected
        const paymentModal = new bootstrap.Offcanvas(document.getElementById('paymentMethodModal'));
        paymentModal.show();
        return;
    }

    const paymentType = selectedPaymentMethodElement.getAttribute('data-payment-method');
    console.log("Selected payment method:", paymentType);

    // Check if COD or online wallet
    if (paymentType === 'gcash' || paymentType === 'maya') {
        console.log("Initiating e-wallet payment with PayMongo");
        createPaymongoSource(paymentType); // Initiate online payment
    } else if (paymentType === 'cod') {
        console.log("Processing COD order directly without payment token");
        finalizeOrder(paymentType, null, true); // For COD, bypass token and set is_cod to true
    }
});

// Function to create a PayMongo source for GCash/Maya payments
function createPaymongoSource(type) {
    const amountToPayElement = document.getElementById('total-amount');
    const amountToPay = amountToPayElement ? amountToPayElement.value : null;

    if (!amountToPay) {
        showModal("Error", "Total amount is missing. Please try again.", true);
        return;
    }

    const requestBody = {
        data: {
            attributes: {
                type: type,
                amount: parseInt(amountToPay),
                currency: "PHP",
                redirect: {
                    success: `https://kuzina-app.shop/users/customer/payment.php?status=success&payment_method=${type}&payment_token=${paymentToken}`,
                    failed: `https://kuzina-app.shop/users/customer/payment.php?status=failed&payment_method=${type}&payment_token=${paymentToken}`
                }
            }
        }
    };

    fetch('https://api.paymongo.com/v1/sources', {
            method: 'POST',
            headers: {
                'Authorization': 'Basic ' + btoa(PAYMONGO_PUBLIC_KEY + ':'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        })
        .then(response => {
            if (!response.ok) throw new Error(`Network response was not ok: ${response.statusText}`);
            return response.json();
        })
        .then(data => {
            const checkoutUrl = data.data.attributes.redirect.checkout_url;

            if (checkoutUrl) {
                console.log("Redirecting to PayMongo checkout URL");
                createPendingPaymentRecord(type, amountToPay); // Record payment as pending before redirect
                window.location.href = checkoutUrl;
            } else {
                showModal("Error", "Failed to create payment source. Please try again.", true);
            }
        })
        .catch(error => {
            console.error('Error in createPaymongoSource:', error);
            showModal("Error", `An error occurred while finalizing your order: ${error.message}`, true);
        });
}

// Function to create an initial pending payment record in the database (for e-wallet)
function createPendingPaymentRecord(paymentType, amount) {
    fetch('functions/initial_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                payment_method: paymentType,
                amount: amount,
                payment_token: paymentToken
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                console.error("Failed to create pending payment record:", data.message);
                showModal("Error", "An error occurred while preparing your payment. Please try again.", true);
            }
        })
        .catch(error => {
            console.error('Error in createPendingPaymentRecord:', error);
        });
}

// Verify payment status on page load (only for e-wallet)
function verifyPayment(status, paymentMethod, paymentToken) {
    fetch(`functions/verify_payment.php?status=${encodeURIComponent(status)}&payment_method=${encodeURIComponent(paymentMethod)}&payment_token=${encodeURIComponent(paymentToken)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                finalizeOrder(paymentMethod, paymentToken, false);
            } else {
                showModal("Payment Failed", "Payment verification failed. Please try again.", true);
            }
        })
        .catch(error => {
            console.error('Error in verifyPayment:', error);
            showModal("Error", "An error occurred during payment verification.", true);
        });
}
// Function to finalize the order based on verified payment or COD
function finalizeOrder(paymentMethod, paymentToken = null, isCOD = false) {
    console.log("Finalizing order with method:", paymentMethod, "token:", paymentToken, "isCOD:", isCOD);

    fetch('functions/create_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                payment_method: paymentMethod,
                payment_token: isCOD ? null : paymentToken, // Send null for COD
                is_cod: isCOD // Indicate if it's a COD order
            })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log("Order creation response:", data);
            if (data.success) {
                // Show the order confirmation modal
                showModal("Order Successful", "Thank you! Your order has been confirmed.");

                // Set the onclick event of the modal confirmation button to redirect with order_id
                const modalConfirmButton = document.getElementById('modalConfirm');
                if (modalConfirmButton) {
                    modalConfirmButton.onclick = () => {
                        window.location.href = `order-success.php?order_id=${data.order_id}`;
                    };
                }
            } else {
                const errorMessage = data.message || "Failed to finalize the order. Please try again.";
                showModal("Error", errorMessage, true);
            }
        })
        .catch(error => {
            console.error('Error in finalizeOrder:', error);
            showModal("Error", "An error occurred while finalizing your order. Please try again.", true);
        });
}


// Helper function to generate a random token
function generateToken() {
    return Math.random().toString(36).substr(2, 10) + Date.now().toString(36);
}

// Helper function to display custom modal notifications
function showModal(title, message, isError = false) {
    const modal = document.getElementById('paymentNotificationModal');
    if (modal) {
        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalMessage').textContent = message;
        modal.style.display = 'flex';

        const modalCloseButton = document.getElementById('modalClose');
        const modalCancelButton = document.getElementById('modalCancel');
        const modalConfirmButton = document.getElementById('modalConfirm');

        if (modalCloseButton) {
            modalCloseButton.onclick = () => modal.style.display = 'none';
        }
        if (modalCancelButton) {
            modalCancelButton.onclick = () => modal.style.display = 'none';
        }
        if (modalConfirmButton && !isError) {
            modalConfirmButton.onclick = () => {
                window.location.href = "order-success.php";
            };
        } else if (modalConfirmButton) {
            modalConfirmButton.onclick = () => modal.style.display = 'none';
        }
    } else {
        console.error("Modal element not found in the DOM.");
    }
}

// Event listener for toggling payment method selection
document.querySelectorAll('.filter-col').forEach(option => {
    option.addEventListener('click', function() {
        document.querySelectorAll('.filter-col').forEach(opt => opt.classList.remove('active'));
        this.classList.add('active');
    });
});