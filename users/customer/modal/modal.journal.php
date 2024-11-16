<!-- Food Selection Modal -->
<div class="offcanvas offcanvas-bottom food-selection-modal" tabindex="-1" id="foodSelectionModal"
    aria-labelledby="foodSelectionModalLabel">
    <div class="offcanvas-header">
        <h5>Add Food to <span id="selectedMealType">Meal</span></h5>
    </div>

    <div class="offcanvas-body" id="foodSelectionContent">
        <div class="search-box mb-3">
            <input type="text" id="foodSearch" class="form-control" placeholder="Search your ordered food..."
                onkeyup="filterFoodItems()">
        </div>

        <div class="ordered-foods">
            <?php
            // Fetch all delivered orders for this customer
            $food_query = "SELECT 
                f.food_id,
                f.food_name,
                f.calories,
                f.protein,
                f.carbs,
                f.fat,
                f.photo1,
                k.fname as kitchen_name,
                o.order_date
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            JOIN food_listings f ON oi.food_id = f.food_id
            JOIN kitchens k ON f.kitchen_id = k.kitchen_id
            WHERE o.customer_id = ? 
            AND o.order_status = 'Delivered'
            ORDER BY o.order_date DESC";

            $stmt = $conn->prepare($food_query);
            $stmt->bind_param("i", $customer_id);
            $stmt->execute();
            $foods = $stmt->get_result();
            ?>

            <div class="food-items-list">
                <?php while($food = $foods->fetch_assoc()): ?>
                <div class="food-select-item" data-food-id="<?= $food['food_id'] ?>" onclick="selectFood(this)">
                    <img src="../../uploads/<?= htmlspecialchars($food['photo1']) ?>"
                        alt="<?= htmlspecialchars($food['food_name']) ?>"
                        onerror="this.src='assets/img/placeholder.jpg'">
                    <div class="food-info">
                        <h6><?= htmlspecialchars($food['food_name']) ?></h6>
                        <p class="kitchen-name"><?= htmlspecialchars($food['kitchen_name']) ?></p>
                        <div class="macro-badges">
                            <span class="badge bg-primary"><?= $food['calories'] ?> kcal</span>
                            <span class="badge bg-success">P: <?= $food['protein'] ?>g</span>
                            <span class="badge bg-warning">C: <?= $food['carbs'] ?>g</span>
                            <span class="badge bg-info">F: <?= $food['fat'] ?>g</span>
                        </div>
                    </div>
                    <div class="select-check">
                        <i class='bx bx-check'></i>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <div class="offcanvas-footer">
        <button class="btn-outline" data-bs-dismiss="offcanvas">Cancel</button>
        <button class="btn-solid" onclick="addToJournal()">Add to Journal</button>
    </div>
</div>

<!-- Success Modal -->
<div class="modal" id="successNotificationModal">
    <div class="modal-content">
        <span class="close-modal" id="closeSuccessModal">&times;</span>
        <div class="modal-item-info">
            <h2>Success</h2>
            <p>Food has been added to your journal.</p>
        </div>
        <div class="modal-actions">
            <button class="btn-confirm" id="successModalConfirm">OK</button>
        </div>
    </div>
</div>

<div class="modal" id="errorModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeErrorModal()">&times;</span>
        <div class="modal-item-info">
            <h2>Error</h2>
            <p id="errorMessage" style="color: #dc3545;"></p>
        </div>
        <div class="modal-actions">
            <button class="btn-confirm" onclick="closeErrorModal()">OK</button>
        </div>
    </div>
</div>

<style>
.food-selection-modal {
    height: 80vh;
}

.food-items-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.food-select-item {
    display: flex;
    align-items: center;
    padding: 12px;
    border-radius: 12px;
    background: white;
    border: 1px solid #eee;
    cursor: pointer;
    transition: all 0.2s ease;
}

.food-select-item.selected {
    border-color: var(--accent-green);
    background: #f0f9e8;
}

.food-select-item img {
    width: 60px;
    height: 60px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 12px;
}

.food-info {
    flex: 1;
}

.food-info h6 {
    margin: 0 0 4px 0;
    font-weight: 600;
}

.kitchen-name {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 8px;
}

.macro-badges {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.select-check {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 12px;
}

.food-select-item.selected .select-check {
    background: var(--accent-green);
    border-color: var(--accent-green);
    color: white;
}

.offcanvas-footer {
    padding: 1rem;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const foodSelectionModal = new bootstrap.Offcanvas(document.getElementById('foodSelectionModal'));
    let selectedFoodId = null;
    let currentMealType = '';

    // Function to show error modal
    window.showErrorModal = function(message) {
        document.getElementById('errorMessage').textContent = message;
        document.getElementById('errorModal').classList.add('show');
    }

    // Function to close error modal
    window.closeErrorModal = function() {
        document.getElementById('errorModal').classList.remove('show');
    }

    // Function to open food selection modal
    window.openMealSelector = function(mealType) {
        currentMealType = mealType;
        document.getElementById('selectedMealType').textContent =
            mealType.charAt(0).toUpperCase() + mealType.slice(1);
        foodSelectionModal.show();
    }

    // Function to select food item
    window.selectFood = function(element) {
        document.querySelectorAll('.food-select-item').forEach(item => {
            item.classList.remove('selected');
        });
        element.classList.add('selected');
        selectedFoodId = element.dataset.foodId;
    }

    // Function to filter food items
    window.filterFoodItems = function() {
        const searchText = document.getElementById('foodSearch').value.toLowerCase();
        document.querySelectorAll('.food-select-item').forEach(item => {
            const foodName = item.querySelector('h6').textContent.toLowerCase();
            item.style.display = foodName.includes(searchText) ? 'flex' : 'none';
        });
    }

    // Function to create food card HTML
    function createFoodCardHTML(food) {
        return `
            <div class="food-card" data-journal-id="${food.journal_id}">
                <div class="food-card-inner">
                    <div class="food-image-wrapper">
                        <img src="../../uploads/${food.photo1 || ''}" 
                             alt="${food.food_name}" 
                             class="food-image"
                             onerror="this.src='assets/img/placeholder.jpg'">
                        <button class="delete-entry" onclick="deleteJournalEntry(${food.journal_id})">
                            <i class='bx bx-trash'></i>
                        </button>
                    </div>
                    <div class="food-content">
                        <div class="food-header">
                            <div class="food-title">
                                <h4>${food.food_name}</h4>
                                <p class="kitchen-name">by ${food.kitchen_name || 'Unknown Kitchen'}</p>
                            </div>
                            <span class="portion-badge">×${food.portion}</span>
                        </div>
                        <div class="macro-tags">
                            <span class="tag-item calories">
                                <i class='bx bx-flame'></i>
                                ${food.calories} kcal
                            </span>
                            <span class="tag-item protein">
                                <i class='bx bx-bowl-hot'></i>
                                ${food.protein}g
                            </span>
                            <span class="tag-item carbs">
                                <i class='bx bx-baguette'></i>
                                ${food.carbs}g
                            </span>
                            <span class="tag-item fat">
                                <i class='bx bx-droplet'></i>
                                ${food.fat}g
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Function to update macro summary
    function updateMacroSummary(totals) {
        document.querySelector('.circle-progress text.percentage').textContent =
            Math.round(totals.total_calories);

        document.querySelector('.circle-progress path').setAttribute(
            'stroke-dasharray',
            `${Math.min((totals.total_calories / 2000) * 100, 100)}, 100`
        );

        // Update protein
        document.querySelector('.macro-item .protein-bg + .macro-info .macro-value').textContent =
            `${Math.round(totals.total_protein)}g`;
        document.querySelector('.progress.protein').style.width =
            `${Math.min((totals.total_protein / 50) * 100, 100)}%`;

        // Update carbs
        document.querySelector('.macro-item .carbs-bg + .macro-info .macro-value').textContent =
            `${Math.round(totals.total_carbs)}g`;
        document.querySelector('.progress.carbs').style.width =
            `${Math.min((totals.total_carbs / 300) * 100, 100)}%`;

        // Update fat
        document.querySelector('.macro-item .fat-bg + .macro-info .macro-value').textContent =
            `${Math.round(totals.total_fat)}g`;
        document.querySelector('.progress.fat').style.width =
            `${Math.min((totals.total_fat / 65) * 100, 100)}%`;
    }

    // Function to add to journal
    window.addToJournal = function() {
        if (!selectedFoodId) {
            showErrorModal('Please select a food item');
            return;
        }

        fetch('functions/journal/add_to_journal.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    food_id: selectedFoodId,
                    meal_type: currentMealType,
                    date: '<?= $selected_date ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data); // Debug log
                if (data.success) {
                    // Get the meal section
                    const mealSection = document.querySelector(
                        `.meal-section.${currentMealType.toLowerCase()} .meal-items`
                    );

                    // Check if mealSection exists
                    if (!mealSection) {
                        console.error('Meal section not found:', currentMealType);
                        return;
                    }

                    // Remove empty state if it exists
                    const emptyState = mealSection.querySelector('.empty-meal');
                    if (emptyState) {
                        emptyState.remove();
                    }

                    // Add the new food card
                    if (data.food) {
                        const newFoodCard = createFoodCardHTML(data.food);
                        mealSection.insertAdjacentHTML('afterbegin', newFoodCard);
                    }

                    // Update macro summary if totals are provided
                    if (data.totals) {
                        updateMacroSummary(data.totals);
                    }

                    // Close modals
                    foodSelectionModal.hide();
                    // Reset selection
                    selectedFoodId = null;
                    document.querySelectorAll('.food-select-item').forEach(item => {
                        item.classList.remove('selected');
                    });

                    // Show success message
                    showSuccessModal();
                } else {
                    showErrorModal(data.message || 'Error adding food to journal');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('Error adding food to journal. Please try again.');
            });
    }

    // Function to create food card HTML
    function createFoodCardHTML(food) {
        // Add null checks and default values
        const safeFood = {
            journal_id: food.journal_id || 0,
            food_name: food.food_name || 'Unknown Food',
            photo1: food.photo1 || '',
            kitchen_name: food.kitchen_name || 'Unknown Kitchen',
            portion: food.portion || 1,
            calories: food.calories || 0,
            protein: food.protein || 0,
            carbs: food.carbs || 0,
            fat: food.fat || 0
        };

        return `
        <div class="food-card" data-journal-id="${safeFood.journal_id}">
            <div class="food-card-inner">
                <div class="food-image-wrapper">
                    <img src="../../uploads/${safeFood.photo1}" 
                         alt="${safeFood.food_name}" 
                         class="food-image">
                    <button class="delete-entry" onclick="deleteJournalEntry(${safeFood.journal_id})">
                        <i class='bx bx-trash'></i>
                    </button>
                </div>
                <div class="food-content">
                    <div class="food-header">
                        <div class="food-title">
                            <h4>${safeFood.food_name}</h4>
                            <p class="kitchen-name">by ${safeFood.kitchen_name}</p>
                        </div>
                        <span class="portion-badge">×${safeFood.portion}</span>
                    </div>
                    <div class="macro-tags">
                        <span class="tag-item calories">
                            <i class='bx bx-flame'></i>
                            ${safeFood.calories} kcal
                        </span>
                        <span class="tag-item protein">
                            <i class='bx bx-bowl-hot'></i>
                            ${safeFood.protein}g
                        </span>
                        <span class="tag-item carbs">
                            <i class='bx bx-baguette'></i>
                            ${safeFood.carbs}g
                        </span>
                        <span class="tag-item fat">
                            <i class='bx bx-droplet'></i>
                            ${safeFood.fat}g
                        </span>
                    </div>
                </div>
            </div>
        </div>
    `;
    }

    // Success modal functions
    function showSuccessModal() {
        const modal = document.getElementById('successNotificationModal');
        if (modal) {
            modal.classList.add('show');
            // Auto close after 2 seconds
            setTimeout(() => {
                closeSuccessModal();
            }, 2000);
        }
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successNotificationModal');
        if (modal) {
            modal.classList.remove('show');
        }
    }
});
</script>