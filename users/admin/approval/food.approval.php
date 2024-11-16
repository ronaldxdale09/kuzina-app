<?php
// Get pending kitchen applications
$query = "SELECT f.*, 
          k.fname, k.lname, k.photo as kitchen_photo, k.email as kitchen_email
          FROM food_listings f
          JOIN kitchens k ON f.kitchen_id = k.kitchen_id
          WHERE f.isApproved = 0
          ORDER BY f.created_at DESC";
$result = $conn->query($query);

function limitWords($string, $word_limit = 50) {
    $words = explode(' ', $string);
    $trimmed_string = implode(' ', array_slice($words, 0, $word_limit));
    
    if (count($words) > $word_limit) {
        $trimmed_string .= '...';
    }
    
    return $trimmed_string;
}
?>
<div class="approval-container">
    <?php if ($result->num_rows > 0): ?>
    <?php while($food = $result->fetch_assoc()): ?>
    <div class="approval-card">
        <div class="approval-header">
            <div class="applicant-info">
                <img src="../../uploads/profile/<?= htmlspecialchars($food['kitchen_photo']) ?>" alt="Kitchen Photo"
                    class="applicant-photo">
                <div class="applicant-details">
                    <h3><?= htmlspecialchars($food['food_name']) ?></h3>
                    <span class="kitchen-name">By: <?= htmlspecialchars($food['fname'] . ' ' . $food['lname']) ?></span>
                    <span class="application-date">Posted: <?= date('M d, Y', strtotime($food['created_at'])) ?></span>
                </div>
            </div>
            <div class="status-badge pending">Pending Review</div>
        </div>

        <div class="approval-body">
            <div class="food-images">
                <?php if ($food['photo1']): ?>
                <img src="../../uploads/<?= htmlspecialchars($food['photo1']) ?>"
                    alt="<?= htmlspecialchars($food['food_name']) ?>" class="food-photo">
                <?php endif; ?>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <i class='bx bx-category'></i>
                    <span>Category: <?= htmlspecialchars($food['category']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-time-five'></i>
                    <span>Meal Type: <?= htmlspecialchars($food['meal_type']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-money'></i>
                    <span>Price: ₱<?= number_format($food['price'], 2) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-restaurant'></i>
                    <span>Diet: <?= htmlspecialchars($food['diet_type_suitable']) ?></span>
                </div>
                <div class="info-item">
                    <i class='bx bx-restaurant'></i>
                    <span>Allergens: <?= htmlspecialchars($food['allergens']) ?></span>
                </div>
            </div>

            <div class="description">
                <div class="description">
                    <h4>Food Description</h4>
                    <p class="limited-text"><?= htmlspecialchars(limitWords($food['description'])) ?></p>
                </div>

                <?php if ($food['allergens']): ?>

                <?php endif; ?>
            </div>
      
        </div>

        <div class="action-buttons">
            <button class="btn btn-details" onclick="viewFoodDetails(<?= $food['food_id'] ?>)">
                <i class='bx bx-show'></i> View Details
            </button>
        </div>
    </div>
    <?php endwhile; ?>
    <?php else: ?>
    <div class="no-applications">
        <i class='bx bx-dish'></i>
        <h3>No Pending Food Items</h3>
        <p>There are currently no food items waiting for review.</p>
    </div>
    <?php endif; ?>
</div>

<style>/* Base container */
.approval-container {
    padding: 10px;
    max-width: 100%;
}

/* Card styling */
.approval-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 16px;
    overflow: hidden;
}

/* Header section */
.approval-header {
    padding: 12px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.applicant-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.applicant-photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.applicant-details {
    flex: 1;
}

.applicant-details h3 {
    font-size: 16px;
    margin: 0;
}

.kitchen-name, .application-date {
    font-size: 12px;
    color: #666;
    display: block;
}

.status-badge {
    align-self: flex-start;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    background: #ffd700;
}

/* Food images */
.food-images {
    padding: 8px;
}

.food-photo {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
}

/* Info grid */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
    padding: 12px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 13px;
}

.info-item i {
    font-size: 16px;
    color: #666;
}

/* Description */
.description {
    padding: 12px;
}

.description h4 {
    font-size: 14px;
    margin-bottom: 8px;
}

.limited-text {
    font-size: 13px;
    line-height: 1.4;
    max-height: 56px;
}

/* Action buttons */
.action-buttons {
    padding: 12px;
    display: flex;
    justify-content: center;
}

.btn-details {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    background: #8a0b10;
    color: white;
    border: none;
    font-size: 14px;
}

/* No applications state */
.no-applications {
    text-align: center;
    padding: 24px;
}

.no-applications i {
    font-size: 48px;
    color: #ccc;
}

/* Loading states */
.food-photo.loading {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200% 100%;
    animation: loading 1.5s infinite;
}

@media (max-width: 360px) {
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        font-size: 12px;
    }
    
    .food-photo {
        height: 150px;
    }
}


</style>