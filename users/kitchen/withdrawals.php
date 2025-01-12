<?php 
include 'includes/header.php';


$kitchen_id = $_COOKIE['kitchen_id'];
// Get kitchen balance
$balance_stmt = $conn->prepare("SELECT balance FROM kitchens WHERE kitchen_id = ?");
$balance_stmt->bind_param("i", $kitchen_id);
$balance_stmt->execute();
$balance_result = $balance_stmt->get_result();
$balance_data = $balance_result->fetch_assoc();
$current_balance = $balance_data['balance'] ?? 0;
?>
<!-- Header Start -->
<!-- Header Start -->
<?php include 'navbar/main.navbar.php'; ?>

<!-- Header End -->

<!-- Sidebar Start -->

<!-- Navigation Start -->
<?php include 'includes/sidebar.php'; ?>
<!-- Navigation End -->
<main class="main-wrap">
    <!-- Header -->
    <div class="mb-4">
        <div class="page-title">
            <h4>Withdraw Earnings</h4>
            <p class="text-muted">Withdraw your available balance to your preferred payment method</p>
        </div>
    </div>

    <!-- Balance Card -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Available Balance</h5>
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
                        <input type="number" class="form-control" id="amount" name="amount" min="100"
                            max="<?php echo $current_balance; ?>" required>
                    </div>
                    <small class="text-muted">Minimum withdrawal: ₱100</small>
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

                <button type="submit" class="btn btn-primary" <?php echo $current_balance < 100 ? 'disabled' : ''; ?>>
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
                <!-- <a href="withdrawal-logs.php" class="btn btn-link">View All</a> -->
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
                            "SELECT * FROM kitchen_withdrawals 
                            WHERE kitchen_id = ? 
                            ORDER BY request_date DESC LIMIT 5"
                        );
                        $withdrawals_stmt->bind_param("i", $kitchen_id);
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

<script>
document.getElementById('payment_method').addEventListener('change', function() {
    const accountDetails = document.getElementById('accountDetails');
    accountDetails.style.display = this.value ? 'block' : 'none';
});

document.getElementById('withdrawalForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData);

    // Validate amount
    if (data.amount > <?php echo $current_balance; ?>) {
        alert('Withdrawal amount cannot exceed available balance');
        return;
    }

    // Submit form
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
                alert('Withdrawal request submitted successfully');
                location.reload();
            } else {
                alert(data.message || 'Failed to submit withdrawal request');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request');
        });
});
</script>