<?php 
include 'includes/header.php';

// Initialize error array
$errors = [];

// Get kitchen ID from URL and validate
$kitchen_id = isset($_GET['kitchen_id']) ? intval($_GET['kitchen_id']) : 0;
if ($kitchen_id <= 0) {
    $errors[] = "Invalid kitchen ID";
}

// Initialize kitchen and food_result as null
$kitchen = null;
$food_result = null;

if (empty($errors)) {
    // Modified query to handle NULL values and proper joins
    $sql = "SELECT 
                k.*,
                COALESCE(ua.street_address, '') as street_address,
                COALESCE(ua.apartment, '') as apartment,
                COALESCE(ua.city, k.city) as city,
                COALESCE(ua.state, '') as state,
                COALESCE(ua.zip_code, k.postal_code) as zip_code,
                COALESCE(k.latitude, '') as latitude,
                COALESCE(k.longitude, '') as longitude,
                COALESCE(k.photo, '') as photo,
                COALESCE(k.description, '') as description
            FROM kitchens k
            LEFT JOIN user_addresses ua ON k.kitchen_id = ua.user_id 
                AND ua.user_type = 'kitchen'
                AND ua.is_default = 1
            WHERE k.kitchen_id = ?";

    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $kitchen_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $errors[] = "Kitchen not found";
        } else {
            $kitchen = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        $errors[] = "Error: " . $e->getMessage();
    }

    // Fetch food listings with error handling
    if (empty($errors)) {
        try {
            $food_sql = "SELECT 
                            food_id,
                            food_name,
                            COALESCE(photo1, '') as photo1,
                            price,
                            COALESCE(description, '') as description
                        FROM food_listings 
                        WHERE kitchen_id = ? 
                        AND available = 1 
                        LIMIT 5";
                        
            $food_stmt = $conn->prepare($food_sql);
            if (!$food_stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            
            $food_stmt->bind_param("i", $kitchen_id);
            if (!$food_stmt->execute()) {
                throw new Exception("Execute failed: " . $food_stmt->error);
            }
            
            $food_result = $food_stmt->get_result();
        } catch (Exception $e) {
            error_log("Error fetching food listings: " . $e->getMessage());
        }
    }
}
?>

<link rel="stylesheet" type="text/css" href="assets/css/kitchen.details.css" />
<link rel="stylesheet" type="text/css" href="assets/css/modal.css" />

<body>
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->

    <!-- Navigation Start -->
    <?php include 'includes/sidebar.php'; ?>
    <main class="main-wrap dashboard-page mb-xxl">
        <?php if (!empty($errors)): ?>
        <div class="error-container">
            <?php foreach ($errors as $error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
            <button class="btn-return mt-3" onclick="window.location.href='approvals.php'">
                <i class='bx bx-arrow-back'></i>
                Return to List
            </button>
        </div>
        <?php else: ?>
        <div class="delivery-page">
            <!-- Status Card -->
            <div class="status-card">
                <div class="order-id-status">
                    <div class="order-number">Kitchen ID #<?= htmlspecialchars($kitchen_id) ?></div>
                    <div class="status-badge <?= $kitchen['isApproved'] ? 'approved' : 'pending' ?>">
                        <?= $kitchen['isApproved'] ? 'Approved' : 'Pending Approval' ?>
                    </div>
                </div>
                <div class="order-time">
                    <i class='bx bx-time-five'></i>
                    Registered on <?= date('F d, Y h:i A', strtotime($kitchen['created_at'])) ?>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="info-section">
                <!-- Kitchen Owner Info -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-user'></i>
                        </div>
                        <h2>Kitchen Owner Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Name</span>
                            <span class="value">
                                <?= htmlspecialchars($kitchen['fname'] ?? '') ?>
                                <?= htmlspecialchars($kitchen['lname'] ?? '') ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Email</span>
                            <span class="value"><?= htmlspecialchars($kitchen['email'] ?? '') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Phone</span>
                            <span class="value">
                                <?php if (!empty($kitchen['phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($kitchen['phone']) ?>">
                                    <?= htmlspecialchars($kitchen['phone']) ?>
                                </a>
                                <?php else: ?>
                                Not provided
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kitchen Location -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-map'></i>
                        </div>
                        <h2>Kitchen Location</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Address</span>
                            <span class="value"><?= htmlspecialchars($kitchen['address'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">City</span>
                            <span class="value"><?= htmlspecialchars($kitchen['city'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Postal Code</span>
                            <span
                                class="value"><?= htmlspecialchars($kitchen['postal_code'] ?? 'Not provided') ?></span>
                        </div>
                        <?php if (!empty($kitchen['latitude']) && !empty($kitchen['longitude'])): ?>
                        <div class="info-item">
                            <span class="label">Coordinates</span>
                            <span class="value">
                                Lat: <?= htmlspecialchars($kitchen['latitude']) ?><br>
                                Long: <?= htmlspecialchars($kitchen['longitude']) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Kitchen Details -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-store'></i>
                        </div>
                        <h2>Kitchen Details</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Description</span>
                            <span class="value">
                                <?= !empty($kitchen['description']) ? 
                                        htmlspecialchars($kitchen['description']) : 
                                        'No description available' ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Kitchen Photo</span>
                            <div class="value">
                                <div class="kitchen-photo-wrapper">
                                    <?php if (!empty($kitchen['photo'])): ?>
                                    <img class="kitchen-photo"
                                        src="../../uploads/profile/<?= htmlspecialchars($kitchen['photo']) ?>"
                                        alt="Kitchen Photo"
                                        onerror="this.onerror=null; this.src='assets/img/placeholder.jpg';">
                                    <?php else: ?>
                                    <div class="kitchen-photo-placeholder">
                                        <i class='bx bx-image'></i>
                                        <p>No photo</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Menu Items -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-food-menu'></i>
                        </div>
                        <h2>Sample Menu Items</h2>
                    </div>
                    <div class="card-content">
                        <div class="food-items">
                            <?php if ($food_result && $food_result->num_rows > 0): ?>
                            <?php while($food = $food_result->fetch_assoc()): ?>
                            <div class="food-item">
                                <div class="item-image">
                                    <?php if (!empty($food['photo1'])): ?>
                                    <img src="../../uploads/<?= htmlspecialchars($food['photo1']) ?>"
                                        alt="<?= htmlspecialchars($food['food_name']) ?>"
                                        onerror="this.onerror=null; this.src='assets/img/placeholder.jpg';">
                                    <?php else: ?>
                                    <div class="placeholder-image">
                                        <i class='bx bx-image'></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="item-details">
                                    <div class="item-name"><?= htmlspecialchars($food['food_name']) ?></div>
                                    <div class="item-price">₱<?= number_format($food['price'] ?? 0, 2) ?></div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                            <?php else: ?>
                            <div class="no-items">
                                <i class='bx bx-info-circle'></i>
                                <p>No menu items available</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn-return" onclick="window.location.href='approvals.php'">
                    <i class='bx bx-arrow-back'></i>
                    Return to List
                </button>
                <?php if (!$kitchen['isApproved']): ?>
                <button class="btn-reject" onclick="openModal('rejectModal')">
                    <i class='bx bx-x-circle'></i>
                    Reject
                </button>
                <button class="btn-primary" onclick="openModal('approveModal')">
                    <i class='bx bx-check-circle'></i>
                    Approve
                </button>
                <?php else: ?>
                <button class="btn-secondary" onclick="openModal('suspendModal')">
                    <i class='bx bx-pause-circle'></i>
                    Suspend
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <!-- Approve Confirmation Modal -->
    <div class="modal" id="approveModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('approveModal')">&times;</span>
            <div class="modal-item-info">
                <h2>Approve Kitchen</h2>
                <p>Are you sure you want to approve this kitchen? This will allow them to start accepting orders.</p>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal('approveModal')">Cancel</button>
                <button class="btn-confirm" onclick="processKitchen('approve', <?= $kitchen_id ?>)">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div class="modal" id="rejectModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('rejectModal')">&times;</span>
            <div class="modal-item-info">
                <h2>Reject Kitchen</h2>
                <p>Please provide a reason for rejecting this kitchen application.</p>
                <div class="form-group">
                    <textarea id="rejectReason" rows="3" class="modal-textarea"
                        placeholder="Enter reason for rejection"></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="btn-reject" onclick="processKitchen('reject', <?= $kitchen_id ?>)">Reject</button>
            </div>
        </div>
    </div>

    <!-- Suspend Confirmation Modal -->
    <div class="modal" id="suspendModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('suspendModal')">&times;</span>
            <div class="modal-item-info">
                <h2>Suspend Kitchen</h2>
                <p>Please provide a reason for suspending this kitchen.</p>
                <div class="form-group">
                    <textarea id="suspendReason" rows="3" class="modal-textarea"
                        placeholder="Enter reason for suspension"></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal('suspendModal')">Cancel</button>
                <button class="btn-reject" onclick="processKitchen('suspend', <?= $kitchen_id ?>)">Suspend</button>
            </div>
        </div>
    </div>

    <!-- Success Notification Modal -->
    <div class="modal" id="successModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('successModal')">&times;</span>
            <div class="modal-item-info">
                <h2>Success</h2>
                <p id="successMessage"></p>
            </div>
            <div class="modal-actions">
                <button class="btn-confirm" onclick="closeModalAndReload('successModal')">OK</button>
            </div>
        </div>
    </div>

    <div class="modal" id="errorModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('errorModal')">&times;</span>
            <div class="modal-item-info">
                <h2>Error</h2>
                <p id="errorMessage" style="color: #dc3545;"></p>
            </div>
            <div class="modal-actions">
                <button class="btn-confirm" onclick="closeModal('errorModal')">OK</button>
            </div>
        </div>
    </div>

    <script>
    // Function to open modal
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('show');
    }

    // Function to close modal
    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
    }

    // Function to close modal and reload page
    function closeModalAndReload(modalId) {
        closeModal(modalId);
        location.reload();
    }

    // Function to show success message
    function showSuccess(message) {
        document.getElementById('successMessage').textContent = message;
        openModal('successModal');
    }

    // Function to show error message
    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        openModal('errorModal');
    }

    // Function to process kitchen actions
    function processKitchen(action, kitchenId) {
        let reason = '';
        if (action === 'reject') {
            reason = document.getElementById('rejectReason').value.trim();
            if (!reason) {
                showError('Please provide a reason for rejection');
                return;
            }
        } else if (action === 'suspend') {
            reason = document.getElementById('suspendReason').value.trim();
            if (!reason) {
                showError('Please provide a reason for suspension');
                return;
            }
        }

        // Create form data
        const formData = new FormData();
        formData.append('kitchen_id', kitchenId);
        formData.append('action', action);
        formData.append('reason', reason);

        // Send AJAX request
        fetch('functions/kitchen_approval.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                closeModal(action + 'Modal');
                if (data.success) {
                    let message = '';
                    switch (action) {
                        case 'approve':
                            message = 'Kitchen has been successfully approved.';
                            break;
                        case 'reject':
                            message = 'Kitchen application has been rejected.';
                            break;
                        case 'suspend':
                            message = 'Kitchen has been suspended.';
                            break;
                    }
                    showSuccess(message);
                } else {
                    showError(data.message || 'An error occurred while processing your request');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('An error occurred while processing your request. Please try again later.');
            });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            closeModal(event.target.id);
        }
    }

    // Optional: Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const openModal = document.querySelector('.modal.show');
            if (openModal) {
                closeModal(openModal.id);
            }
        }
    });
    </script>

    <!-- Optional: Add these styles for the error message -->
    <style>
    #errorMessage {
        color: #dc3545;
        background-color: #f8d7da;
        border: 1px solid #f5c6cb;
        border-radius: 4px;
        padding: 10px;
        margin: 10px 0;
    }

    .modal-item-info h2 {
        margin-bottom: 15px;
    }

    #errorModal .modal-content {
        border-top: 4px solid #dc3545;
    }

    #successModal .modal-content {
        border-top: 4px solid #28a745;
    }
    </style>

    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->
    <?php include 'includes/scripts.php'; ?>

</body>

</html>