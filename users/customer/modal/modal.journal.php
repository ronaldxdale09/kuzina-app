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

<script>document.addEventListener('DOMContentLoaded', function() {
    // Initialize constants and variables
    const foodSelectionModal = new bootstrap.Offcanvas(document.getElementById('foodSelectionModal'));
    let selectedFoodId = null;
    let currentMealType = '';

    // Modal control functions
    window.showErrorModal = function(message) {
        const modal = document.getElementById('errorModal');
        if (modal) {
            document.getElementById('errorMessage').textContent = message;
            modal.classList.add('show');
            setTimeout(() => closeErrorModal(), 3000);
        }
    };

    window.closeErrorModal = function() {
        const modal = document.getElementById('errorModal');
        if (modal) modal.classList.remove('show');
    };

    function showSuccessModal() {
        const modal = document.getElementById('successNotificationModal');
        if (modal) {
            modal.classList.add('show');
            setTimeout(() => closeSuccessModal(), 2000);
        }
    }

    function closeSuccessModal() {
        const modal = document.getElementById('successNotificationModal');
        if (modal) modal.classList.remove('show');
    }

    // Fetch and update daily summary
    function fetchDailySummary() {
        fetch('functions/journal/get_daily_summary.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                date: '<?= $selected_date ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMacroSummary(data.totals, data.goals);
            } else {
                console.error('Error fetching summary:', data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }

    // Food selection functions
    window.openMealSelector = function(mealType) {
        currentMealType = mealType;
        const mealTypeDisplay = document.getElementById('selectedMealType');
        if (mealTypeDisplay) {
            mealTypeDisplay.textContent = mealType.charAt(0).toUpperCase() + mealType.slice(1);
        }
        foodSelectionModal.show();
    };

    window.selectFood = function(element) {
        document.querySelectorAll('.food-select-item').forEach(item => {
            item.classList.remove('selected');
        });
        element.classList.add('selected');
        selectedFoodId = element.dataset.foodId;
    };

    window.filterFoodItems = function() {
        const searchText = document.getElementById('foodSearch').value.toLowerCase();
        document.querySelectorAll('.food-select-item').forEach(item => {
            const foodName = item.querySelector('h4').textContent.toLowerCase();
            item.style.display = foodName.includes(searchText) ? 'flex' : 'none';
        });
    };

    // Update macro summary display
    function updateMacroSummary(totals, goals) {
        // Update calories circle
        const caloriesCircle = document.querySelector('.circle-progress text.percentage');
        const caloriesPath = document.querySelector('.circle-progress path');
        if (caloriesCircle && caloriesPath) {
            caloriesCircle.textContent = Math.round(totals.total_calories);
            const caloriesPercentage = goals.daily_calories > 0 ? 
                Math.min((totals.total_calories / goals.daily_calories) * 100, 100) : 0;
            caloriesPath.setAttribute('stroke-dasharray', `${caloriesPercentage}, 100`);
        }

        // Update protein
        updateMacroElement(
            'protein',
            totals.total_protein,
            goals.daily_protein,
            '.macro-item .protein-bg + .macro-info .macro-value',
            '.progress.protein'
        );

        // Update carbs
        updateMacroElement(
            'carbs',
            totals.total_carbs,
            goals.daily_carbs,
            '.macro-item .carbs-bg + .macro-info .macro-value',
            '.progress.carbs'
        );

        // Update fat
        updateMacroElement(
            'fat',
            totals.total_fat,
            goals.daily_fat,
            '.macro-item .fat-bg + .macro-info .macro-value',
            '.progress.fat'
        );
    }

    function updateMacroElement(type, value, goal, valueSelector, progressSelector) {
        const valueElement = document.querySelector(valueSelector);
        const progressElement = document.querySelector(progressSelector);
        
        if (valueElement && progressElement) {
            valueElement.textContent = `${Math.round(value)}g`;
            const percentage = goal > 0 ? Math.min((value / goal) * 100, 100) : 0;
            progressElement.style.width = `${percentage}%`;
        }
    }

    // Journal entry management
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
            if (data.success) {
                updateMealSection(data.food);
                resetFoodSelection();
                fetchDailySummary();
                showSuccessModal();
            } else {
                showErrorModal(data.message || 'Error adding food to journal');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorModal('Error adding food to journal');
        });
    };

    window.deleteJournalEntry = function(journalId) {
        if (confirm('Are you sure you want to remove this entry?')) {
            fetch('functions/journal/delete_journal_entry.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ journal_id: journalId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const entry = document.querySelector(`[data-journal-id="${journalId}"]`);
                    if (entry) {
                        entry.remove();
                        checkEmptyMealSection(entry.closest('.meal-items'));
                    }
                    fetchDailySummary();
                    showSuccessModal();
                } else {
                    showErrorModal(data.message || 'Error deleting entry');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorModal('Error deleting entry');
            });
        }
    };

    // Helper functions
    function createFoodCardHTML(food) {
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
                             class="food-image"
                             onerror="this.src='assets/img/placeholder.jpg'">
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

    function updateMealSection(food) {
        const mealSection = document.querySelector(
            `.meal-section.${currentMealType.toLowerCase()} .meal-items`
        );
        
        if (mealSection) {
            const emptyState = mealSection.querySelector('.empty-meal');
            if (emptyState) emptyState.remove();
            
            mealSection.insertAdjacentHTML('afterbegin', createFoodCardHTML(food));
        }
    }

    function checkEmptyMealSection(mealItems) {
        if (mealItems && !mealItems.querySelector('.food-card')) {
            mealItems.innerHTML = `
                <div class="empty-meal">
                    <i class='bx bx-dish'></i>
                    <p>No meals logged</p>
                    <button class="add-meal-btn" onclick="openMealSelector('${currentMealType}')">
                        Add Food
                    </button>
                </div>
            `;
        }
    }

    function resetFoodSelection() {
        selectedFoodId = null;
        document.querySelectorAll('.food-select-item').forEach(item => {
            item.classList.remove('selected');
        });
        foodSelectionModal.hide();
    }

    // Initialize
    fetchDailySummary();
});
</script>