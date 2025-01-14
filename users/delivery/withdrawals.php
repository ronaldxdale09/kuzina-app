<?php 
include 'includes/header.php';

$rider_id = $_COOKIE['rider_id'];
// Get rider balance
$balance_stmt = $conn->prepare("SELECT balance FROM delivery_riders WHERE rider_id = ?");
$balance_stmt->bind_param("i", $rider_id);
$balance_stmt->execute();
$balance_result = $balance_stmt->get_result();
$balance_data = $balance_result->fetch_assoc();
$current_balance = $balance_data['balance'] ?? 0;
?>

<?php include 'navbar/main.navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main class="main-wrap">
    <div class="mb-4">
        <div class="page-title">
            <h4>Withdraw Earnings</h4>
            <p class="text-muted">Withdraw your delivery earnings to your preferred payment method</p>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Available Balane</h5>
            <h2 class="balance-amount">₱<?php echo number_format($current_balance, 2); ?></h2>
        </div>
    </div>

    <!-- Withdrawal Form -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">Request Withdrawal</h5>
            <form id="withdrawalForm">
                <div class="mb-3">
                    <label for="amount" class="form-label">Amount to Withdraw</label>
                    <div class="input-group">
                        <span class="input-group-text">₱</span>
                        <input type="number" class="form-control" id="amount" name="amount" min="50"
                            max="<?php echo $current_balance; ?>" required>
                    </div>
                    <small class="text-muted">Minimum withdrawal: ₱50</small>
                </div>

                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <select class="form-select" id="payment_method" name="payment_method" required>
                        <option value="">Select payment method</option>
                        <option value="gcash">GCash</option>
                        <option value="bank">Bank Transfer</option>
                    </select>
                </div>

                <div id="accountDetails" class="mb-3" style="display: none;">
                    <label for="account_number" class="form-label">Account Number</label>
                    <input type="text" class="form-control" id="account_number" name="account_number">
                </div>

                <button type="submit" class="btn btn-primary" <?php echo $current_balance < 50 ? 'disabled' : ''; ?>>
                    Request Withdrawal
                </button>
            </form>
        </div>
    </div>

    <!-- Recent Withdrawals -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Recent Withdrawals</h5>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $withdrawals_stmt = $conn->prepare(
                            "SELECT * FROM rider_withdrawals 
                            WHERE rider_id = ? 
                            ORDER BY request_date DESC LIMIT 5"
                        );
                        $withdrawals_stmt->bind_param("i", $rider_id);
                        $withdrawals_stmt->execute();
                        $withdrawals = $withdrawals_stmt->get_result();

                        while ($withdrawal = $withdrawals->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($withdrawal['request_date'])); ?></td>
                            <td>₱<?php echo number_format($withdrawal['amount'], 2); ?></td>
                            <td><?php echo ucfirst($withdrawal['payment_method']); ?></td>
                            <td>
                                <span class="badge bg-<?php 
                                    echo $withdrawal['status'] === 'completed' ? 'success' : 
                                        ($withdrawal['status'] === 'pending' ? 'warning' : 'danger'); 
                                ?>">
                                    <?php echo ucfirst($withdrawal['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>


<?php include 'includes/appbar.php'; ?>
  

  <?php include 'includes/scripts.php'; ?>

<style>
.page-title {
    padding: 20px 0;
}

.balance-amount {
    font-size: 32px;
    color: #502121;
    font-weight: 600;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.btn-primary {
    background: #502121;
    border-color: #502121;
}

.btn-primary:hover {
    background: #632929;
    border-color: #632929;
}

.btn-primary:disabled {
    background: #ccc;
    border-color: #ccc;
}

.badge {
    padding: 8px 12px;
    border-radius: 6px;
}
</style>
<!-- Add these modal structures to your HTML -->
<div class="custom-modal" id="errorModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Error</h5>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p id="errorMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeModal('errorModal')">OK</button>
        </div>
    </div>
</div>

<div class="custom-modal" id="successModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Success</h5>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <p id="successMessage"></p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="handleSuccessClose()">OK</button>
        </div>
    </div>
</div>

<style>
.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 0;
    width: 90%;
    max-width: 500px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    animation: modalSlide 0.3s ease;
}

@keyframes modalSlide {
    from {transform: translateY(-100px); opacity: 0;}
    to {transform: translateY(0); opacity: 1;}
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    text-align: right;
}

.close-modal {
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.close-modal:hover {
    color: #333;
}
</style>

<script>
function showModal(modalId, message) {
    document.getElementById(modalId).style.display = 'block';
    if (modalId === 'errorModal') {
        document.getElementById('errorMessage').textContent = message;
    } else {
        document.getElementById('successMessage').textContent = message;
    }
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function handleSuccessClose() {
    closeModal('successModal');
    location.reload();
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = document.getElementsByClassName('custom-modal');
    for (let modal of modals) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
}

// Add click events to all close buttons
document.querySelectorAll('.close-modal').forEach(button => {
    button.onclick = function() {
        const modal = this.closest('.custom-modal');
        modal.style.display = 'none';
    }
});

document.getElementById('payment_method').addEventListener('change', function() {
    const accountDetails = document.getElementById('accountDetails');
    accountDetails.style.display = this.value ? 'block' : 'none';
});

document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    if (data.amount > <?php echo $current_balance; ?>) {
        showModal('errorModal', 'Withdrawal amount cannot exceed available balance');
        return;
    }

    fetch('functions/process_withdrawal.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showModal('successModal', 'Withdrawal request submitted successfully');
        } else {
            showModal('errorModal', data.message || 'Failed to submit withdrawal request');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showModal('errorModal', 'An error occurred while processing your request');
    });
});
</script>