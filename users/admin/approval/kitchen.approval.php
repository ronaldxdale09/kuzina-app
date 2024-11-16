<?php
// Get pending kitchen applications
$query = "SELECT k.*, COUNT(r.review_id) as review_count, 
          AVG(CASE WHEN r.rating IS NOT NULL THEN r.rating ELSE 0 END) as avg_rating
          FROM kitchens k
          LEFT JOIN reviews r ON k.kitchen_id = r.kitchen_id
          WHERE k.isApproved = 0
          GROUP BY k.kitchen_id
          ORDER BY k.created_at DESC";
$result = $conn->query($query);
?>

<div class="approval-container">
    <?php if ($result->num_rows > 0): ?>
    <?php while($kitchen = $result->fetch_assoc()): ?>
    <div class="approval-card">
        <div class="approval-header">
            <div class="applicant-info">
                <img src="../../uploads/profile/<?= htmlspecialchars($kitchen['photo']) ?>" alt="Kitchen Photo"
                    class="applicant-photo">
                <div class="applicant-details">
                    <h3><?= htmlspecialchars($kitchen['fname'] . ' ' . $kitchen['lname']) ?></h3>
                    <span class="application-date">Applied:
                        <?= date('M d, Y', strtotime($kitchen['created_at'])) ?></span>
                </div>
            </div>
            <div class="status-badge pending">Pending Review</div>
        </div>

        <div class="approval-body">
            <div class="info-grid">
                <div class="info-item">
                    <i class='bx bx-envelope'></i>
                    <span><?= htmlspecialchars($kitchen['email']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-phone'></i>
                    <span><?= htmlspecialchars($kitchen['phone']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-map'></i>
                    <span><?= htmlspecialchars($kitchen['address']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-buildings'></i>
                    <span><?= htmlspecialchars($kitchen['city']) ?>, <?= htmlspecialchars($kitchen['country']) ?></span>
                </div>
            </div>

            <div class="description">
                <h4>Kitchen Description</h4>
                <p><?= htmlspecialchars($kitchen['description']) ?></p>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-details" onclick="viewKitchenDetails(<?= $kitchen['kitchen_id'] ?>)">
                <i class='bx bx-show'></i> Details
            </button>
        </div>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="no-applications">
        <i class='bx bx-package'></i>
        <h3>No Pending Applications</h3>
        <p>There are currently no kitchen applications waiting for review.</p>
    </div>
    <?php endif; ?>
</div>


<script>
let actionKitchenId = null;
let currentAction = null;

function viewKitchenDetails(kitchenId) {
    window.location.href = `details.kitchen.php?kitchen_id=${kitchenId}`;
}

</script>
<style>
.approval-container {
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
}

.approval-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
    overflow: hidden;
}

.approval-header {
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
}

.applicant-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.applicant-photo {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.applicant-details h3 {
    margin: 0;
    font-size: 1.1rem;
}

.application-date {
    color: #666;
    font-size: 0.9rem;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.9rem;
}

.status-badge.pending {
    background: #fff3e0;
    color: #f57c00;
}

.approval-body {
    padding: 15px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-item i {
    color: #666;
    font-size: 1.2rem;
}

.description {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.description h4 {
    margin: 0 0 10px 0;
    font-size: 1rem;
}

.approval-actions {
    padding: 15px;
    display: flex;
    gap: 10px;
    border-top: 1px solid #eee;
}

.approval-actions button {
    padding: 8px 16px;
    border-radius: 6px;
    border: none;
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.no-applications {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 48px 20px;
    text-align: center;
    background: #ABCF63;
    color: white !important;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    margin: 20px;
    min-height: 300px;
}

.no-applications i {
    font-size: 48px;
    color: white !important;
    margin-bottom: 16px;
    animation: float 3s ease-in-out infinite;
}

.no-applications h3 {
    color: white !important;
    font-size: 20px;
    font-weight: 600;
    margin: 0 0 8px 0;
}

.no-applications p {
    color: white !important;
    font-size: 14px;
    line-height: 1.5;
    margin: 0;
    max-width: 280px;
}

/* Floating animation */
@keyframes float {
    0% {
        transform: translateY(0px);
    }

    50% {
        transform: translateY(-10px);
    }

    100% {
        transform: translateY(0px);
    }
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    .no-applications {
        padding: 32px 16px;
        min-height: 250px;
        margin: 16px;
    }

    .no-applications i {
        font-size: 40px;
    }

    .no-applications h3 {
        font-size: 18px;
    }

    .no-applications p {
        font-size: 13px;
        max-width: 240px;
    }
}


@media (max-width: 768px) {
    .info-grid {
        grid-template-columns: 1fr;
    }

    .approval-actions {
        flex-direction: column;
    }

    .approval-actions button {
        width: 100%;
        justify-content: center;
    }
}
</style>