<?php include 'includes/header.php'; ?>
<!-- Header Start -->
<?php include 'navbar/main.navbar.php'; ?>
<style>
.content {
    padding: 1rem;
    background: #f8f9fa;
}

.header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.header-section h2 {
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #502121;
    margin: 0;
}

.filters {
    display: flex;
    gap: 0.75rem;
}

.search-input {
    position: relative;
}

.search-input input {
    padding: 0.5rem 1rem 0.5rem 2rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    width: 200px;
}

.search-input i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.status-select {
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 0.5rem;
    background: white;
}

.requests-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

.request-card {
    background: white;
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.request-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.request-id {
    font-weight: 600;
    color: #502121;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
}

.status-badge.pending {
    background: #fff3cd;
    color: #856404;
}

.status-badge.completed {
    background: #d4edda;
    color: #155724;
}

.status-badge.rejected {
    background: #f8d7da;
    color: #721c24;
}

.request-details {
    display: grid;
    gap: 0.5rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-item label {
    color: #666;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.amount {
    font-weight: 600;
    color: #502121;
}

.request-footer {
    margin-top: 0.75rem;
    padding-top: 0.75rem;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.date {
    font-size: 0.875rem;
    color: #666;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn-approve,
.btn-reject {
    padding: 0.25rem 0.75rem;
    border: none;
    border-radius: 0.25rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    cursor: pointer;
    transition: opacity 0.2s;
}

.btn-approve {
    background: #28a745;
    color: white;
}

.btn-reject {
    background: #dc3545;
    color: white;
}

.btn-approve:hover,
.btn-reject:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .header-section {
        flex-direction: column;
        align-items: stretch;
    }

    .filters {
        flex-direction: column;
    }

    .search-input input {
        width: 100%;
    }
}
</style>

<?php 

// Calculate statistics
$statsQuery = "SELECT 
    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
    SUM(CASE WHEN status = 'completed' THEN amount ELSE 0 END) as total_completed,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_count,
    COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
FROM kitchen_withdrawals";

$statsResult = $conn->query($statsQuery);
$stats = $statsResult->fetch_assoc();

$pendingCount = $stats['pending_count'];
$totalCompleted = $stats['total_completed'];
$completedCount = $stats['completed_count'];
$rejectedCount = $stats['rejected_count'];

// Get last 30 days total
$thirtyDaysQuery = "SELECT SUM(amount) as total_30_days 
                    FROM kitchen_withdrawals 
                    WHERE status = 'completed' 
                    AND completion_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$thirtyDaysResult = $conn->query($thirtyDaysQuery);
$thirtyDaysTotal = $thirtyDaysResult->fetch_assoc()['total_30_days'] ?? 0;

// Fetch withdrawals with kitchen information and pagination
$itemsPerPage = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $itemsPerPage;

// Get total count for pagination
$totalQuery = "SELECT COUNT(*) as total FROM kitchen_withdrawals";
$totalResult = $conn->query($totalQuery);
$totalItems = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalItems / $itemsPerPage);

// Main withdrawal query with joining kitchen details
$query = "SELECT w.*, 
          k.fname, k.lname, k.email, k.phone,
          COUNT(kw.withdrawal_id) as total_withdrawals,
          SUM(CASE WHEN kw.status = 'completed' THEN kw.amount ELSE 0 END) as total_withdrawn
          FROM kitchen_withdrawals w
          JOIN kitchens k ON w.kitchen_id = k.kitchen_id
          LEFT JOIN kitchen_withdrawals kw ON k.kitchen_id = kw.kitchen_id AND kw.status = 'completed'
          GROUP BY w.withdrawal_id
          ORDER BY w.request_date DESC
          LIMIT ?, ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $itemsPerPage);
$stmt->execute();
$result = $stmt->get_result();
?>
<!-- Navigation Start -->
<?php include 'includes/sidebar.php'; ?>
<div class="content pb-24">
    <!-- Added padding bottom -->
    <div class="container">
        <!-- Header Section -->
        <div class="header-section">
            <h2><i class='bx bx-arrow-back'></i> Withdrawal Requests</h2>
            <div class="filters">
                <div class="search-input">
                    <i class='bx bx-search'></i>
                    <input type="text" placeholder="Search requests...">
                </div>
                <select class="status-select">
                    <option>All Status</option>
                    <option>Pending</option>
                    <option>Completed</option>
                    <option>Rejected</option>
                </select>
            </div>
        </div>

        <!-- Requests Grid -->
        <div class="requests-grid">
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="request-card">
                <div class="request-header">
                    <span class="request-id">#<?php echo str_pad($row['withdrawal_id'], 6, '0', STR_PAD_LEFT); ?></span>
                    <span
                        class="status-badge <?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
                </div>
                <div class="request-details">
                    <div class="detail-item">
                        <label><i class='bx bx-user'></i> Kitchen</label>
                        <span><?php echo htmlspecialchars($row['fname'] . ' ' . $row['lname']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label><i class='bx bx-money'></i> Amount</label>
                        <span class="amount">₱<?php echo number_format($row['amount'], 2); ?></span>
                    </div>
                    <div class="detail-item">
                        <label><i class='bx bx-credit-card'></i> Payment</label>
                        <span><?php echo ucfirst($row['payment_method']); ?></span>
                    </div>
                    <div class="detail-item">
                        <label><i class='bx bx-phone'></i> Account</label>
                        <span><?php echo htmlspecialchars($row['payment_details']); ?></span>
                    </div>
                </div>
                <div class="request-footer">
                    <span class="date"><i class='bx bx-calendar'></i>
                        <?php echo date('M d, Y h:i A', strtotime($row['request_date'])); ?></span>
                    <?php if($row['status'] === 'pending'): ?>
                    <div class="action-buttons">
                        <button onclick="handleWithdrawal(<?php echo $row['withdrawal_id']; ?>, 'completed')"
                            class="btn-approve">
                            <i class='bx bx-check'></i> Approve
                        </button>
                        <button onclick="handleWithdrawal(<?php echo $row['withdrawal_id']; ?>, 'rejected')"
                            class="btn-reject">
                            <i class='bx bx-x'></i> Reject
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<div class="modal" id="confirmationModal">
    <div class="modal-content">
        <button type="button" class="close-modal" onclick="hideModal()">
            <i class='bx bx-x'></i>
        </button>
        <div class="modal-item-info">
            <h2>Confirm Action</h2>
            <div class="modal-icon">
                <i class='bx bx-question-circle'></i>
            </div>
            <p>
                Are you sure you want to <span id="actionText" style="font-weight: 600;"></span> this withdrawal
                request?
            </p>
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancel" onclick="hideModal()">
                <i class='bx bx-x'></i> Cancel
            </button>
            <button type="button" class="btn-confirm" id="confirmAction">
                <i class='bx bx-check'></i> Confirm
            </button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize status filter
    initializeStatusFilter();
    // Initialize search functionality
    initializeSearch();
    // Initialize modal listeners
    initializeModalListeners();
});

// Status filter functionality
function initializeStatusFilter() {
    const statusSelect = document.querySelector('.status-select');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            const status = this.value.toLowerCase();
            const cards = document.querySelectorAll('.request-card');

            cards.forEach(card => {
                const statusBadge = card.querySelector('.status-badge');
                const cardStatus = statusBadge.textContent.trim().toLowerCase();

                if (status === 'all' || cardStatus === status) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

// Search functionality
function initializeSearch() {
    const searchInput = document.querySelector('.search-input input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const cards = document.querySelectorAll('.request-card');

            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

// Modal functionality
function initializeModalListeners() {
    const modal = document.getElementById('confirmationModal');

    // Close on outside click
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            hideModal();
        }
    });

    // Close on button click
    document.querySelector('.close-modal')?.addEventListener('click', hideModal);
    document.querySelector('.btn-cancel')?.addEventListener('click', hideModal);

    // Confirm action
    document.getElementById('confirmAction')?.addEventListener('click', handleConfirmAction);
}

let currentWithdrawalId = null;
let currentAction = null;

function showModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'flex'; // Changed to flex
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
    document.body.style.overflow = 'hidden';
}

function hideModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 300);
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
            hideModal();

            if (data.success) {
                showNotification(data.message || 'Action completed successfully', 'success');
                // Reload page after 1.5 seconds to allow notification to be seen
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'An error occurred', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while processing your request', 'error');
            hideModal();
        });
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class='bx bx-${type === 'success' ? 'check' : 'x'}-circle'></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }, 10);
}

function checkEmptyState() {
    const cards = document.querySelectorAll('.request-card');
    const container = document.querySelector('.requests-grid');

    if (cards.length === 0 && container) {
        container.innerHTML = `
            <div class="empty-state">
                <i class='bx bx-folder-open'></i>
                <h3>No Withdrawal Requests</h3>
                <p>There are currently no withdrawal requests to display.</p>
            </div>
        `;
    }
}

// Add styles dynamically
const notificationStyles = `
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
        background: white;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transform: translateX(120%);
        transition: transform 0.3s ease;
        z-index: 9999;
    }

    .notification.show {
        transform: translateX(0);
    }

    .notification.success {
        border-left: 4px solid #28a745;
    }

    .notification.error {
        border-left: 4px solid #dc3545;
    }

    .notification i {
        font-size: 1.25rem;
    }

    .notification.success i {
        color: #28a745;
    }

    .notification.error i {
        color: #dc3545;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #666;
    }

    .empty-state i {
        font-size: 3rem;
        color: #ccc;
        margin-bottom: 1rem;
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = notificationStyles;
document.head.appendChild(styleSheet);
</script>

<?php include 'includes/appbar.php'; ?>
<!-- Footer End -->


<!-- Action Language End -->

<!-- Pwa Install App Popup Start -->

<?php include 'includes/scripts.php'; ?>