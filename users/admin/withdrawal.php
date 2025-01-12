<?php include 'includes/header.php'; ?>
<!-- Header Start -->
<?php include 'navbar/main.navbar.php'; ?>
<style>
/* Base Styles */
:root {
    --primary-color: #502121;
    --primary-hover: #632929;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --border-radius: 10px;
    --shadow: 0 2px 10px rgba(0,0,0,0.05);
}

/* Layout */
.content {
    padding: 20px;
    margin-bottom: 60px;
}

/* Card Styles */
.card {
    background: #fff;
    border: none;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
}

.card-header {
    background-color: var(--primary-color) !important;
    color: white !important;
    padding: 15px 20px;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    border: none;
}

.card-header h4 {
    font-size: 18px;
    margin: 0;
    font-weight: 600;
}

.card-body {
    padding: 20px;
}

/* Form Elements */
.form-select {
    border: 1px solid #e0e0e0;
    padding: 10px 15px;
    border-radius: var(--border-radius);
    color: var(--primary-color);
    font-size: 14px;
    background-color: #f8f9fa;
}

.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: none;
}

/* Table Styles */
.table-responsive {
    border-radius: var(--border-radius);
    background: #fff;
}

.table {
    margin-bottom: 0;
}

.table th, .table td {
    padding: 15px;
    vertical-align: middle;
}

.table thead th {
    background: #f8f9fa;
    color: var(--primary-color);
    font-weight: 600;
    font-size: 14px;
    border-top: none;
}

.table tbody td {
    color: #555;
    font-size: 14px;
    border-color: #f0f0f0;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

</style>

<!-- Navigation Start -->
<?php include 'includes/sidebar.php'; ?>
<div class="content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4 style="color:white">Withdrawal Requests</h4>
            </div>
            <div class="card-body">
                <!-- Filter Options -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <select class="form-select" id="statusFilter">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                </div>

                <!-- Withdrawals Table -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kitchen Name</th>
                                <th>Amount</th>
                                <th>Payment Method</th>
                                <th>Account Details</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch withdrawals with kitchen information
                            $query = "SELECT w.*, k.fname, k.lname 
                                    FROM kitchen_withdrawals w
                                    JOIN kitchens k ON w.kitchen_id = k.kitchen_id
                                    ORDER BY w.request_date DESC";
                            $result = $conn->query($query);

                            while($row = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td>#<?php echo str_pad($row['withdrawal_id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></td>
                                <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                                <td><?php echo ucfirst($row['payment_method']); ?></td>
                                <td><?php echo htmlspecialchars($row['payment_details']); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['request_date'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        echo $row['status'] === 'completed' ? 'success' : 
                                            ($row['status'] === 'pending' ? 'warning' : 'danger'); 
                                    ?>">
                                        <?php echo ucfirst($row['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($row['status'] === 'pending'): ?>
                                    <button class="btn btn-sm btn-success"
                                        onclick="handleWithdrawal(<?php echo $row['withdrawal_id']; ?>, 'completed')">
                                        <i class="fas fa-check"></i> Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger"
                                        onclick="handleWithdrawal(<?php echo $row['withdrawal_id']; ?>, 'rejected')">
                                        <i class="fas fa-times"></i> Reject
                                    </button>
                                    <?php else: ?>
                                    <span class="text-muted">No action needed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="confirmationModal">
    <div class="modal-content">
        <button type="button" class="close-modal" data-bs-dismiss="modal">
            &times;
        </button>
        <div class="modal-item-info">
            <h2>Confirm Action</h2>
            <div class="modal-icon">
                <i class="fas fa-question-circle" style="font-size: 48px; color: #e74c3c; margin-bottom: 15px;"></i>
            </div>
            <p>
                Are you sure you want to <span id="actionText" style="font-weight: 600;"></span> this withdrawal request?
            </p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" data-bs-dismiss="modal">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn-confirm" id="confirmAction">
                <i class="fas fa-check"></i> Confirm
            </button>
        </div>
    </div>
</div><script>
document.addEventListener('DOMContentLoaded', function() {
    // Status filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
        const status = this.value;
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(7)');
            const currentStatus = statusCell.textContent.trim().toLowerCase();
            
            if (status === 'all' || currentStatus === status) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Close modal when clicking outside
    const modal = document.getElementById('confirmationModal');
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            hideModal();
        }
    });

    // Close modal when clicking close button
    document.querySelector('.close-modal')?.addEventListener('click', hideModal);

    // Confirm action button listener
    document.getElementById('confirmAction')?.addEventListener('click', handleConfirmAction);
});

let currentWithdrawalId = null;
let currentAction = null;

function showModal() {
    document.getElementById('confirmationModal').classList.add('show');
}

function hideModal() {
    document.getElementById('confirmationModal').classList.remove('show');
}

function handleWithdrawal(withdrawalId, action) {
    currentWithdrawalId = withdrawalId;
    currentAction = action;
    
    const actionText = action === 'completed' ? 'approve' : 'reject';
    document.getElementById('actionText').textContent = actionText;
    
    showModal();
}

function handleConfirmAction() {
    if (!currentWithdrawalId || !currentAction) return;

    fetch('functions/update_withdrawal.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            withdrawal_id: currentWithdrawalId,
            action: currentAction
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideModal();
            showSuccessMessage(data.message || 'Action completed successfully');
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showErrorMessage(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('An error occurred while processing your request');
    });

    hideModal();
}

function showSuccessMessage(message) {
    // You can replace this with a custom success notification
    const notification = document.createElement('div');
    notification.className = 'notification success';
    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function showErrorMessage(message) {
    // You can replace this with a custom error notification
    const notification = document.createElement('div');
    notification.className = 'notification error';
    notification.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Add notification styles
const styles = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: white;
        animation: slideIn 0.3s ease-out;
        z-index: 9999;
    }

    .notification.success {
        background-color: #28a745;
    }

    .notification.error {
        background-color: #dc3545;
    }

    .notification i {
        font-size: 20px;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;

// Add styles to document
const styleSheet = document.createElement("style");
styleSheet.textContent = styles;
document.head.appendChild(styleSheet);
</script>

<?php include 'includes/appbar.php'; ?>
<!-- Footer End -->


<!-- Action Language End -->

<!-- Pwa Install App Popup Start -->

<?php include 'includes/scripts.php'; ?>