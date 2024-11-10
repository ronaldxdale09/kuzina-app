const PAYMONGO_PUBLIC_KEY = 'pk_test_1JxMWEHpEruyrNiD7MuvtVu9';

// Generate a token once and store it for the entire payment session
let paymentToken = generateToken();

// Function to redirect with parameters after successful payment
function handlePaymentSuccess(paymentMethod) {
    window.location.href = `payment.php?status=success&payment_method=${encodeURIComponent(paymentMethod)}&payment_token=${encodeURIComponent(paymentToken)}`;
}

// Check if there's a status in the URL on page load
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const paymentMethod = urlParams.get('payment_method');
    const paymentTokenFromUrl = urlParams.get('payment_token');

    // Use the token from the URL if available
    if (paymentTokenFromUrl) paymentToken = paymentTokenFromUrl;

    // Verify payment if redirected back with status=success
    if (status === 'success' && paymentMethod && paymentToken) {
        verifyPayment(status, paymentMethod, paymentToken);
    }
};

// Confirm Payment Button Listener
document.getElementById('confirm-payment').addEventListener('click', function(event) {
    event.preventDefault();
    const selectedPaymentMethod = document.querySelector('.filter-col.active[data-payment-method]');

    if (!selectedPaymentMethod) {
        const paymentModal = new bootstrap.Offcanvas(document.getElementById('paymentMethodModal'));
        paymentModal.show();
        return;
    }

    const paymentType = selectedPaymentMethod.getAttribute('data-payment-method');
    if (paymentType === 'gcash' || paymentType === 'maya') {
        createPaymongoSource(paymentType); // Initiate online payment
    } else if (paymentType === 'cod') {
        finalizeOrder('cod', 'confirmed'); // For COD, create order directly
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
                    success: `http://localhost/kuzina-app/users/customer/payment.php?status=success&payment_method=${type}&payment_token=${paymentToken}`,
                    failed: `http://localhost/kuzina-app/users/customer/payment.php?status=failed&payment_method=${type}&payment_token=${paymentToken}`
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

// Function to create an initial pending payment record in the database
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

// Verify payment status on page load
function verifyPayment(status, paymentMethod, paymentToken) {
    fetch(`functions/verify_payment.php?status=${encodeURIComponent(status)}&payment_method=${encodeURIComponent(paymentMethod)}&payment_token=${encodeURIComponent(paymentToken)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                finalizeOrder(paymentMethod, paymentToken);
            } else {
                showModal("Payment Failed", "Payment verification failed. Please try again.", true);
            }
        })
        .catch(error => {
            console.error('Error in verifyPayment:', error);
            showModal("Error", "An error occurred during payment verification.", true);
        });
}

// Function to finalize the order based on verified payment
function finalizeOrder(paymentMethod, paymentToken) {
    fetch('functions/create_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                payment_method: paymentMethod,
                payment_token: paymentToken
            })
        })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showModal("Payment Successful", "Thank you! Your order has been confirmed.");
                const modalConfirmButton = document.getElementById('modalConfirm');
                if (modalConfirmButton) {
                    modalConfirmButton.onclick = () => {
                        window.location.href = "order-success.php";
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