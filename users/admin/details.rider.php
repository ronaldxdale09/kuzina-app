<?php 
include 'includes/header.php';

// Initialize error array
$errors = [];

// Get rider ID from URL and validate
$rider_id = isset($_GET['rider_id']) ? intval($_GET['rider_id']) : 0;
if ($rider_id <= 0) {
    $errors[] = "Invalid rider ID";
}

// Initialize rider data as null
$rider = null;

if (empty($errors)) {
    // Query to fetch rider details
    $sql = "SELECT 
                dr.*,
                COALESCE(ua.street_address, '') as street_address,
                COALESCE(ua.apartment, '') as apartment,
                COALESCE(ua.city, '') as city,
                COALESCE(ua.state, '') as state,
                COALESCE(ua.zip_code, '') as zip_code,
                rd.id_front,
                rd.id_back,
                rd.is_verified,
                rd.verified_at,
                COALESCE(ra.is_available, 0) as is_available
            FROM delivery_riders dr
            LEFT JOIN user_addresses ua ON dr.rider_id = ua.user_id 
                AND ua.user_type = 'rider'
                AND ua.is_default = 1
            LEFT JOIN rider_documents rd ON dr.rider_id = rd.rider_id
            LEFT JOIN rider_availability ra ON dr.rider_id = ra.rider_id
            WHERE dr.rider_id = ?";

    try {
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $rider_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            $errors[] = "Rider not found";
        } else {
            $rider = $result->fetch_assoc();
        }
    } catch (Exception $e) {
        $errors[] = "Error: " . $e->getMessage();
    }
}
?>


<link rel="stylesheet" type="text/css" href="assets/css/rider.details.css" />
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
            <button class="btn-return mt-3" onclick="window.location.href='rider_approvals.php'">
                <i class='bx bx-arrow-back'></i>
                Return to List
            </button>
        </div>
        <?php else: ?>
        <div class="delivery-page">
            <!-- Status Card -->
            <div class="status-card">
                <div class="order-id-status">
                    <div class="order-number">Rider ID #<?= htmlspecialchars($rider_id) ?></div>
                    <div class="status-badge <?= $rider['isApproved'] ? 'approved' : 'pending' ?>">
                        <?= $rider['isApproved'] ? 'Approved' : 'Pending Approval' ?>
                    </div>
                </div>
                <div class="order-time">
                    <i class='bx bx-time-five'></i>
                    Registered on <?= date('F d, Y h:i A', strtotime($rider['created_at'])) ?>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="info-section">
                <!-- Rider Personal Info -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-user'></i>
                        </div>
                        <h2>Rider Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Profile Photo</span>
                            <div class="value">
                                <div class="rider-photo-wrapper">
                                    <?php if (!empty($rider['profile_photo'])): ?>
                                    <img class="rider-photo"
                                        src="../../uploads/riders/<?= htmlspecialchars($rider['profile_photo']) ?>"
                                        alt="Rider Photo"
                                        onerror="this.onerror=null; this.src='assets/img/placeholder.jpg';">
                                    <?php else: ?>
                                    <div class="rider-photo-placeholder">
                                        <i class='bx bx-image'></i>
                                        <p>No photo</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>


                        <div class="info-item">
                            <span class="label">Name</span>
                            <span class="value">
                                <?= htmlspecialchars($rider['first_name'] ?? '') ?>
                                <?= htmlspecialchars($rider['last_name'] ?? '') ?>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="label">Email</span>
                            <span class="value"><?= htmlspecialchars($rider['email'] ?? '') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">Phone</span>
                            <span class="value">
                                <?php if (!empty($rider['phone'])): ?>
                                <a href="tel:<?= htmlspecialchars($rider['phone']) ?>">
                                    <?= htmlspecialchars($rider['phone']) ?>
                                </a>
                                <?php else: ?>
                                Not provided
                                <?php endif; ?>
                            </span>
                        </div>

                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-car'></i>
                        </div>
                        <h2>Vehicle Information</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">Vehicle Type</span>
                            <span class="value"><?= htmlspecialchars($rider['vehicle_type'] ?? 'Not provided') ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label">License Plate</span>
                            <span
                                class="value"><?= htmlspecialchars($rider['license_plate'] ?? 'Not provided') ?></span>
                        </div>
                    </div>
                </div>

                <!-- Document Verification -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="header-icon">
                            <i class='bx bx-file'></i>
                        </div>
                        <h2>Document Verification</h2>
                    </div>
                    <div class="card-content">
                        <div class="info-item">
                            <span class="label">ID Front</span>
                            <div class="value">
                                <div class="document-photo-wrapper">
                                    <?php if (!empty($rider['id_front'])): ?>
                                    <img class="document-photo"
                                        src="../../uploads/riders/<?= htmlspecialchars($rider['id_front']) ?>"
                                        alt="ID Front"
                                        onclick="openImageModal('../../uploads/riders/<?= htmlspecialchars($rider['id_front']) ?>')"
                                        onerror="this.onerror=null; this.src='assets/img/placeholder.jpg';">
                                    <?php else: ?>
                                    <div class="document-photo-placeholder">
                                        <i class='bx bx-image'></i>
                                        <p>No document uploaded</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="label">ID Back</span>
                            <div class="value">
                                <div class="document-photo-wrapper">
                                    <?php if (!empty($rider['id_back'])): ?>
                                    <img class="document-photo"
                                        src="../../uploads/riders/<?= htmlspecialchars($rider['id_back']) ?>"
                                        alt="ID Back"
                                        onclick="openImageModal('../../uploads/riders/<?= htmlspecialchars($rider['id_back']) ?>')"
                                        onerror="this.onerror=null; this.src='assets/img/placeholder.jpg';">
                                    <?php else: ?>
                                    <div class="document-photo-placeholder">
                                        <i class='bx bx-image'></i>
                                        <p>No document uploaded</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="info-item">
                            <span class="label">Verification Status</span>
                            <span class="verification-badge <?= $rider['is_verified'] ? 'verified' : 'unverified' ?>">
                                <?= $rider['is_verified'] ? 'Verified' : 'Unverified' ?>
                            </span>
                        </div>
                        <?php if ($rider['is_verified'] && $rider['verified_at']): ?>
                        <div class="info-item">
                            <span class="label">Verified On</span>
                            <span class="value">
                                <?= date('F d, Y h:i A', strtotime($rider['verified_at'])) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <button class="btn-return" onclick="window.location.href='approvals.php?tab=1'">
                    <i class='bx bx-arrow-back'></i>
                    Return to List
                </button>
                <?php if (!$rider['isApproved']): ?>
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
                <h2>Approve Rider</h2>
                <p>Are you sure you want to approve this rider? This will allow them to start accepting delivery
                    requests.</p>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal('approveModal')">Cancel</button>
                <button class="btn-confirm" onclick="processRider('approve', <?= $rider_id ?>)">Approve</button>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div class="modal" id="rejectModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('rejectModal')">&times;</span>
            <div class="modal-item-info">
                <h2>Reject Rider</h2>
                <p>Please provide a reason for rejecting this rider application.</p>
                <div class="form-group">
                    <textarea id="rejectReason" rows="3" class="modal-textarea"
                        placeholder="Enter reason for rejection"></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal('rejectModal')">Cancel</button>
                <button class="btn-reject" onclick="processRider('reject', <?= $rider_id ?>)">Reject</button>
            </div>
        </div>
    </div>

    <!-- Suspend Confirmation Modal -->
    <div class="modal" id="suspendModal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('suspendModal')">&times;</span>
            <div class="modal-item-info">
                <h2>Suspend Rider</h2>
                <p>Please provide a reason for suspending this rider.</p>
                <div class="form-group">
                    <textarea id="suspendReason" rows="3" class="modal-textarea"
                        placeholder="Enter reason for suspension"></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button class="btn-cancel" onclick="closeModal('suspendModal')">Cancel</button>
                <button class="btn-reject" onclick="processRider('suspend', <?= $rider_id ?>)">Suspend</button>
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

    <!-- Error Modal -->
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
    // Modal utility functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.add('show');
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        modal.classList.remove('show');
    }

    function closeModalAndReload(modalId) {
        closeModal(modalId);
        location.reload();
    }

    function showSuccess(message) {
        document.getElementById('successMessage').textContent = message;
        openModal('successModal');
    }

    function showError(message) {
        document.getElementById('errorMessage').textContent = message;
        openModal('errorModal');
    }

    // Function to process rider actions
    function processRider(action, riderId) {
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
        formData.append('rider_id', riderId);
        formData.append('action', action);
        formData.append('reason', reason);

        // Send AJAX request
        fetch('functions/rider_approval.php', {
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
                            message = 'Rider has been successfully approved to start deliveries.';
                            break;
                        case 'reject':
                            message = 'Rider application has been rejected.';
                            break;
                        case 'suspend':
                            message = 'Rider account has been suspended.';
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

    // Close modals with Escape key
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