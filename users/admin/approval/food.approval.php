<?php
// Get pending food listings
$query = "SELECT f.*, 
          k.fname, k.lname, k.kitchen_id, k.email as kitchen_email
          FROM food_listings f
          JOIN kitchens k ON f.kitchen_id = k.kitchen_id
          WHERE f.isApproved = 0
          ORDER BY f.created_at DESC";
$result = $conn->query($query);

function limitWords($string, $limit = 30) {
    $words = explode(' ', $string);
    return count($words) > $limit ? 
           implode(' ', array_slice($words, 0, $limit)) . '...' : 
           $string;
}
?>

<div class="approval-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while($food = $result->fetch_assoc()): ?>
            <div class="food-card" data-food-id="<?= $food['food_id'] ?>">
                <div class="food-header">
                    <div class="food-title">
                        <h3><?= htmlspecialchars($food['food_name']) ?></h3>
                        <span class="kitchen-name">by <?= htmlspecialchars($food['fname'] . ' ' . $food['lname']) ?></span>
                    </div>
                    <span class="status-badge pending">Pending Review</span>
                </div>

                <div class="food-image">
                    <?php if ($food['photo1']): ?>
                        <img src="../../uploads/<?= htmlspecialchars($food['photo1']) ?>" 
                             alt="<?= htmlspecialchars($food['food_name']) ?>">
                    <?php endif; ?>
                </div>

                <div class="food-details">
                    <div class="detail-grid">
                        <div class="detail-item">
                            <i class='bx bx-purchase-tag'></i>
                            <span>₱<?= number_format($food['price'], 2) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class='bx bx-category'></i>
                            <span><?= htmlspecialchars($food['category']) ?></span>
                        </div>
                        <div class="detail-item">
                            <i class='bx bx-time'></i>
                            <span><?= htmlspecialchars($food['meal_type']) ?></span>
                        </div>
                    </div>

                    <div class="description-box">
                        <p><?= htmlspecialchars(limitWords($food['description'])) ?></p>
                    </div>

                    <div class="tags">
                        <?php if ($food['diet_type_suitable']): ?>
                            <span class="tag diet"><?= htmlspecialchars($food['diet_type_suitable']) ?></span>
                        <?php endif; ?>
                        <?php if ($food['allergens']): ?>
                            <span class="tag allergen">Contains: <?= htmlspecialchars($food['allergens']) ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="btn approve" onclick="approveFood(<?= $food['food_id'] ?>, <?= $food['kitchen_id'] ?>)">
                        <i class='bx bx-check'></i> Approve
                    </button>
                    <button class="btn reject" onclick="rejectFood(<?= $food['food_id'] ?>, <?= $food['kitchen_id'] ?>)">
                        <i class='bx bx-x'></i> Reject
                    </button>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="empty-state">
            <i class='bx bx-dish'></i>
            <h3>No Pending Items</h3>
            <p>No food items are currently awaiting approval.</p>
        </div>
    <?php endif; ?>
</div>


<div id="approveModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <i class='bx bx-check-circle success-icon'></i>
            <h4>Approve Food Item</h4>
            <button class="close-modal" onclick="closeModal('approveModal')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to approve this food item?</p>
            <div class="food-preview">
                <img src="" alt="Food preview" id="approvePreviewImage">
                <div class="preview-details">
                    <h5 id="approvePreviewName"></h5>
                    <span id="approvePreviewKitchen"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('approveModal')">
                <i class='bx bx-x'></i> Cancel
            </button>
            <button class="btn btn-success" id="confirmApprove">
                <i class='bx bx-check'></i> Approve
            </button>
        </div>
    </div>
</div>

<div id="rejectModal" class="custom-modal">
    <div class="modal-content">
        <div class="modal-header">
            <i class='bx bx-x-circle reject-icon'></i>
            <h4>Reject Food Item</h4>
            <button class="close-modal" onclick="closeModal('rejectModal')">
                <i class='bx bx-x'></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Please provide a reason for rejection:</p>
            <div class="food-preview">
                <img src="" alt="Food preview" id="rejectPreviewImage">
                <div class="preview-details">
                    <h5 id="rejectPreviewName"></h5>
                    <span id="rejectPreviewKitchen"></span>
                </div>
            </div>
            <textarea id="rejectionReason" 
                      class="form-control" 
                      placeholder="Enter rejection reason..."
                      rows="3"></textarea>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal('rejectModal')">
                <i class='bx bx-x'></i> Cancel
            </button>
            <button class="btn btn-danger" id="confirmReject">
                <i class='bx bx-x'></i> Reject
            </button>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #8a0b10;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --border-radius: 12px;
}

.approval-container {
    padding: 16px;
    max-width: 800px;
    margin: 0 auto;
}

.food-card {
    background: white;
    border-radius: var(--border-radius);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.food-header {
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid #eee;
}

.food-title h3 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.kitchen-name {
    color: #666;
    font-size: 14px;
}

.status-badge {
    background: var(--warning-color);
    color: #333;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
}

.food-image {
    width: 100%;
    height: 200px;
    overflow: hidden;
}

.food-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.food-details {
    padding: 16px;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 16px;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.detail-item i {
    font-size: 18px;
    color: var(--primary-color);
}

.description-box {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 16px;
}

.description-box p {
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
    color: #444;
}

.tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: 16px;
}

.tag {
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
}

.tag.diet {
    background: #e3f2fd;
    color: #1976d2;
}

.tag.allergen {
    background: #fff3e0;
    color: #f57c00;
}

.action-buttons {
    display: flex;
    gap: 12px;
    padding: 16px;
    border-top: 1px solid #eee;
}

.btn {
    flex: 1;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn.approve {
    background: var(--success-color);
    color: white;
}

.btn.reject {
    background: var(--danger-color);
    color: white;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #666;
}

.empty-state i {
    font-size: 48px;
    color: #ccc;
    margin-bottom: 16px;
}

@media (max-width: 576px) {
    .approval-container {
        padding: 12px;
    }

    .detail-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .food-image {
        height: 160px;
    }

    .action-buttons {
        flex-direction: column;
    }
}
/* Modal Styles */
.custom-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
    animation: fadeIn 0.3s ease-out;
}

.modal-content {
    position: relative;
    background-color: #fff;
    margin: 20px auto;
    max-width: 500px;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s ease-out;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 12px;
}

.modal-header h4 {
    margin: 0;
    flex: 1;
}

.success-icon, .reject-icon {
    font-size: 24px;
}

.success-icon {
    color: #28a745;
}

.reject-icon {
    color: #dc3545;
}

.close-modal {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.modal-body {
    padding: 20px;
}

.food-preview {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 15px 0;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.food-preview img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}

.preview-details h5 {
    margin: 0 0 5px 0;
    font-size: 16px;
}

.preview-details span {
    color: #666;
    font-size: 14px;
}

#rejectionReason {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    resize: vertical;
    margin-top: 10px;
}

.modal-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.btn {
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@media (max-width: 576px) {
    .modal-content {
        margin: 10px;
        max-width: calc(100% - 20px);
    }

    .food-preview {
        flex-direction: column;
        text-align: center;
    }

    .food-preview img {
        width: 100%;
        height: 160px;
    }
}
</style>

<script>
let currentFoodId = null;
let currentKitchenId = null;

function approveFood(foodId, kitchenId) {
    currentFoodId = foodId;
    currentKitchenId = kitchenId;
    
    // Get food details from the card
    const card = document.querySelector(`[data-food-id="${foodId}"]`);
    const foodName = card.querySelector('.food-title h3').textContent;
    const kitchenName = card.querySelector('.kitchen-name').textContent;
    const foodImage = card.querySelector('.food-image img').src;

    // Update modal content
    document.getElementById('approvePreviewImage').src = foodImage;
    document.getElementById('approvePreviewName').textContent = foodName;
    document.getElementById('approvePreviewKitchen').textContent = kitchenName;

    // Show modal
    document.getElementById('approveModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function rejectFood(foodId, kitchenId) {
    currentFoodId = foodId;
    currentKitchenId = kitchenId;
    
    // Get food details from the card
    const card = document.querySelector(`[data-food-id="${foodId}"]`);
    const foodName = card.querySelector('.food-title h3').textContent;
    const kitchenName = card.querySelector('.kitchen-name').textContent;
    const foodImage = card.querySelector('.food-image img').src;

    // Update modal content
    document.getElementById('rejectPreviewImage').src = foodImage;
    document.getElementById('rejectPreviewName').textContent = foodName;
    document.getElementById('rejectPreviewKitchen').textContent = kitchenName;

    // Show modal
    document.getElementById('rejectModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    document.body.style.overflow = 'auto';
    if (modalId === 'rejectModal') {
        document.getElementById('rejectionReason').value = '';
    }
}

// Update your existing updateFoodStatus function
function updateFoodStatus(foodId, kitchenId, status, reason = '') {
    fetch('functions/update_food_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            food_id: foodId,
            kitchen_id: kitchenId,
            status: status,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.querySelector(`[data-food-id="${foodId}"]`);
            card.style.opacity = '0';
            setTimeout(() => {
                card.remove();
                if (document.querySelectorAll('.food-card').length === 0) {
                    location.reload();
                }
            }, 300);
            
            // Close modal
            closeModal(status ? 'approveModal' : 'rejectModal');
            
            // Show success message
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'An error occurred', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating the status', 'error');
    });
}

// Add event listeners when document loads
document.addEventListener('DOMContentLoaded', function() {
    // Confirm approve button
    document.getElementById('confirmApprove').addEventListener('click', function() {
        updateFoodStatus(currentFoodId, currentKitchenId, 1);
    });

    // Confirm reject button
    document.getElementById('confirmReject').addEventListener('click', function() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            showNotification('Please provide a reason for rejection', 'error');
            return;
        }
        updateFoodStatus(currentFoodId, currentKitchenId, 0, reason);
    });

    // Close modals when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('custom-modal')) {
            closeModal(event.target.id);
        }
    }
});

// Add this notification function
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
    }, 100);
}
</script>

<!-- Add this style for notifications -->
<style>
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
    transform: translateX(120%);
    transition: transform 0.3s ease-out;
    z-index: 1100;
}

.notification.show {
    transform: translateX(0);
}

.notification.success {
    background: #28a745;
}

.notification.error {
    background: #dc3545;
}

.notification i {
    font-size: 20px;
}
</style>