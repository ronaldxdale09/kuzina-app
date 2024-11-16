<?php
$query = "SELECT r.*, rd.* 
          FROM delivery_riders r
          LEFT JOIN rider_documents rd ON r.rider_id = rd.rider_id
          WHERE r.isApproved = 0
          ORDER BY r.created_at DESC";
$result = $conn->query($query);
?>

<div class="approval-container">
    <?php if ($result->num_rows > 0): ?>
    <?php while($rider = $result->fetch_assoc()): ?>
    <div class="approval-card">
        <div class="approval-header">
            <div class="applicant-info">
                <img src="../../uploads/profile/<?= htmlspecialchars($rider['profile_photo']) ?>" alt="Rider Photo"
                    class="applicant-photo">
                <div class="applicant-details">
                    <h3><?= htmlspecialchars($rider['first_name'] . ' ' . $rider['last_name']) ?></h3>
                    <span class="application-date">Applied:
                        <?= date('M d, Y', strtotime($rider['created_at'])) ?></span>
                </div>
            </div>
            <div class="status-badge pending">Pending Review</div>
        </div>

        <div class="approval-body">
            <div class="info-grid">
                <div class="info-item">
                    <i class='bx bx-envelope'></i>
                    <span><?= htmlspecialchars($rider['email']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-phone'></i>
                    <span><?= htmlspecialchars($rider['phone']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-car'></i>
                    <span><?= htmlspecialchars($rider['vehicle_type']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-id-card'></i>
                    <span><?= htmlspecialchars($rider['license_plate']) ?></span>
                </div>
            </div>

            <div class="documents-header" onclick="toggleDocuments(this)">
                <h4>Verification Documents</h4>
                <i class='bx bx-chevron-down'></i>
            </div>
            <div class="document-content collapsed">
                <div class="document-grid">
                    <div class="document-item">
                        <img src="../../uploads/riders/<?= htmlspecialchars($rider['id_front']) ?>" alt="ID Front">
                        <span>ID Front</span>
                    </div>
                    <div class="document-item">
                        <img src="../../uploads/riders/<?= htmlspecialchars($rider['id_back']) ?>" alt="ID Back">
                        <span>ID Back</span>
                    </div>
                </div>
            </div>

        </div>

        <div class="action-buttons">
            <button class="btn btn-details" onclick="viewRiderDetails(<?= $rider['rider_id'] ?>)">
                <i class='bx bx-show'></i>Rider Details
            </button>
           
        </div>

    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="no-applications">
        <i class='bx bx-cycling'></i>
        <h3>No Pending Applications</h3>
        <p>There are currently no rider applications waiting for review.</p>
    </div>
    <?php endif; ?>
</div>
<style>
.documents-section {
    margin-top: 20px;
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.documents-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
    padding: 5px 0;
}

.documents-header h4 {
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.documents-header i {
    font-size: 1.5rem;
    transition: transform 0.3s ease;
}

.documents-header.active i {
    transform: rotate(-180deg);
}

.document-content {
    max-height: 1000px;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
}

.document-content.collapsed {
    max-height: 0;
}

.document-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-top: 10px;
}

.document-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 5px;
}

.document-item img {
    width: 100%;
    max-height: 200px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.document-item img:hover {
    transform: scale(1.05);
}

.document-item span {
    font-size: 0.9rem;
    color: #666;
}

.action-buttons {
    display: flex !important;
    gap: 8px !important;
    padding: 15px !important;
    margin-top: 15px !important;
}

.btn {
    flex: 1 !important;
    padding: 10px !important;
    border: none !important;
    border-radius: 6px !important;
    font-size: 0.9rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
}

.btn i {
    font-size: 1.1rem !important;
}

.btn-details {
    background: #4a0404 !important;
    color: white !important;
}

.btn-approve {
    background: #82c91e !important;
    color: white !important;
}

.btn-reject {
    background: #dc3545 !important;
    color: white !important;
}

.btn:hover {
    opacity: 0.9 !important;
    transform: translateY(-1px) !important;
}

@media (max-width: 480px) {
    .action-buttons {
        padding: 10px !important;
    }

    .btn {
        padding: 8px !important;
        font-size: 0.8rem !important;
    }
}

@media (max-width: 768px) {
    .document-grid {
        grid-template-columns: 1fr;
    }
}
</style>


<script>
function toggleDocuments(header) {
    const content = header.nextElementSibling;
    header.classList.toggle('active');
    content.classList.toggle('collapsed');
}

// Rest of your existing JavaScript remains the same
document.querySelectorAll('.document-item img').forEach(img => {
    img.addEventListener('click', function() {
        window.open(this.src, '_blank');
    });
});

function viewRiderDetails(riderId) {
    window.location.href = `details.rider.php?rider_id=${riderId}`;
}


// Add image preview functionality
document.querySelectorAll('.document-item img').forEach(img => {
    img.addEventListener('click', function() {
        // Implement image preview modal
        window.open(this.src, '_blank');
    });
});
</script>