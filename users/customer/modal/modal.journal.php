<!-- Food Selection Modal -->
<div class="offcanvas offcanvas-bottom food-selection-modal" tabindex="-1" id="foodSelectionModal"
    aria-labelledby="foodSelectionModalLabel">
    <div class="offcanvas-header">
        <h5>Add Food to <span id="selectedMealType">Meal</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body" id="foodSelectionContent">
        <div class="search-box mb-3">
            <input type="text" id="foodSearch" class="form-control" placeholder="Search your ordered food..."
                onkeyup="filterFoodItems()">
        </div>

        <div class="ordered-foods">
            <?php
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
                <div class="food-select-item" 
                     data-food-id="<?= $food['food_id'] ?>" 
                     data-food-name="<?= htmlspecialchars($food['food_name']) ?>"
                     onclick="selectFood(this)">
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

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteConfirmModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirm Delete</h2>
            <span class="close-modal" onclick="closeModal('deleteConfirmModal')">×</span>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to remove "<span id="deleteItemName"></span>" from your journal?</p>
            <p>This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button class="btn-cancel" onclick="closeModal('deleteConfirmModal')">Cancel</button>
            <button class="btn-delete" id="confirmDeleteBtn">Delete</button>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal" id="successNotificationModal">
    <div class="modal-content">
        <span class="close-modal" id="closeSuccessModal" onclick="closeModal('successNotificationModal')">×</span>
        <div class="modal-item-info">
            <h2>Success</h2>
            <p>Food has been added to your journal.</p>
        </div>
        <div class="modal-actions">
            <button class="btn-confirm" id="successModalConfirm" onclick="closeModal('successNotificationModal')">OK</button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal" id="errorModal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('errorModal')">×</span>
        <div class="modal-item-info">
            <h2>Error</h2>
            <p id="errorMessage" style="color: #dc3545;"></p>
        </div>
        <div class="modal-actions">
            <button class="btn-confirm" onclick="closeModal('errorModal')">OK</button>
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

.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1000;
}

.modal.show {
    display: block;
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border-radius: 8px;
    width: 90%;
    max-width: 400px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    border-top: 1px solid #eee;
    padding-top: 10px;
}

.btn-delete {
    background-color: #ff4444;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.btn-delete:hover {
    background-color: #cc0000;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configuration object for better maintainability
    const CONFIG = {
        SELECTORS: {
            foodSelectionModal: '#foodSelectionModal',
            errorMessage: '#errorMessage',
            errorModal: '#errorModal',
            successNotificationModal: '#successNotificationModal',
            selectedMealType: '#selectedMealType',
            foodSearch: '#foodSearch',
            deleteItemName: '#deleteItemName',
            deleteConfirmModal: '#deleteConfirmModal'
        },
        CLASSES: {
            foodSelectItem: '.food-select-item',
            selected: 'selected',
            foodCard: '.food-card',
            mealItems: '.meal-items'
        },
        ENDPOINTS: {
            ADD_TO_JOURNAL: 'functions/journal/add_to_journal.php',
            DELETE_JOURNAL_ENTRY: 'functions/delete_journal_entry.php',
            FETCH_DAILY_SUMMARY: 'fetch/get_daily_summary.php'
        }
    };

    const foodSelectionModal = new bootstrap.Offcanvas(document.querySelector(CONFIG.SELECTORS.foodSelectionModal));
    let selectedFoodId = null;
    let currentMealType = '';
    let currentJournalId = null;

    // Improved error handling with centralized modal management
    const ModalManager = {
        showError: function(message) {
            const errorModal = document.querySelector(CONFIG.SELECTORS.errorModal);
            const messageElement = errorModal.querySelector(CONFIG.SELECTORS.errorMessage);
            messageElement.textContent = message;
            errorModal.style.display = 'block';
            console.error('Error:', message);
        },
        
        close: function(modalId) {
            const modal = document.querySelector(modalId);
            if (modal) modal.style.display = 'none';
            if (modalId === CONFIG.SELECTORS.deleteConfirmModal) currentJournalId = null;
        },
        
        showSuccess: function(message = 'Operation successful!') {
            const modal = document.querySelector(CONFIG.SELECTORS.successNotificationModal);
            const messageElement = modal.querySelector('p');
            messageElement.textContent = message;
            modal.style.display = 'block';
            setTimeout(() => this.close(CONFIG.SELECTORS.successNotificationModal), 2000);
        }
    };

    // Fetch wrapper for better error handling
    async function safeFetch(url, options) {
        try {
            const response = await fetch(url, options);
            const data = await response.json();
            
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Request failed');
            }
            
            return data;
        } catch (error) {
            console.error(`Fetch error at ${url}:`, error);
            ModalManager.showError(error.message);
            return null;
        }
    }

    // Core functions for journal management
    function openMealSelector(mealType) {
        currentMealType = mealType;
        document.querySelector(CONFIG.SELECTORS.selectedMealType).textContent = 
            mealType.charAt(0).toUpperCase() + mealType.slice(1);
        foodSelectionModal.show();
    }

    function selectFood(element) {
        document.querySelectorAll(CONFIG.CLASSES.foodSelectItem).forEach(item => {
            item.classList.remove(CONFIG.CLASSES.selected);
        });
        element.classList.add(CONFIG.CLASSES.selected);
        selectedFoodId = element.dataset.foodId;
    }

    function filterFoodItems() {
        const searchText = document.querySelector(CONFIG.SELECTORS.foodSearch).value.toLowerCase();
        document.querySelectorAll(CONFIG.CLASSES.foodSelectItem).forEach(item => {
            const foodName = item.querySelector('h6').textContent.toLowerCase();
            item.style.display = foodName.includes(searchText) ? 'flex' : 'none';
        });
    }

    async function addToJournal() {
        if (!selectedFoodId) {
            return ModalManager.showError('Please select a food item');
        }

        const data = await safeFetch(CONFIG.ENDPOINTS.ADD_TO_JOURNAL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                food_id: selectedFoodId,
                meal_type: currentMealType,
                date: '<?= $selected_date ?>'
            })
        });

        if (data) {
            updateMealSection(data.food);
            resetFoodSelection();
            fetchDailySummary();
            ModalManager.showSuccess('Food added to journal');
        }
    }

    function showDeleteConfirm(journalId, foodName) {
        currentJournalId = journalId;
        document.querySelector(CONFIG.SELECTORS.deleteItemName).textContent = foodName;
        document.querySelector(CONFIG.SELECTORS.deleteConfirmModal).style.display = 'block';
    }

    async function deleteJournalEntry() {
        if (!currentJournalId) return;

        const data = await safeFetch(CONFIG.ENDPOINTS.DELETE_JOURNAL_ENTRY, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ journal_id: currentJournalId })
        });

        if (data) {
            ModalManager.close(CONFIG.SELECTORS.deleteConfirmModal);
            
            const entryCard = document.querySelector(`${CONFIG.CLASSES.foodCard}:has([data-journal-id="${currentJournalId}"])`);
            if (entryCard) {
                const mealItems = entryCard.closest(CONFIG.CLASSES.mealItems);
                entryCard.remove();
                checkEmptyMealSection(mealItems);
            }

            fetchDailySummary();
            ModalManager.showSuccess('Entry deleted successfully');
        }
    }

    async function fetchDailySummary() {
        const data = await safeFetch(CONFIG.ENDPOINTS.FETCH_DAILY_SUMMARY, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                customer_id: '<?= $customer_id ?>',
                date: '<?= $selected_date ?>'
            })
        });

        if (data) {
            updateSummaryDisplay(data.totals);
        }
    }

    function updateSummaryDisplay(totals) {
        const goals = {
            calories: <?= $current_goals['daily_calories'] ?>,
            protein: <?= $current_goals['daily_protein'] ?>,
            carbs: <?= $current_goals['daily_carbs'] ?>,
            fat: <?= $current_goals['daily_fat'] ?>
        };

        updateMacroCircle('calories', totals.total_calories, goals.calories);
        updateMacroProgress('protein', totals.total_protein, goals.protein);
        updateMacroProgress('carbs', totals.total_carbs, goals.carbs);
        updateMacroProgress('fat', totals.total_fat, goals.fat);

        updateAchievementMessage(totals, goals);
    }

    function updateMacroCircle(type, value, goal) {
        const circleProgress = document.querySelector(`.circle-progress.${type}`);
        if (!circleProgress) return;

        const percentage = goal > 0 ? Math.min((value / goal) * 100, 100) : 0;
        const text = circleProgress.querySelector('text.percentage');
        const path = circleProgress.querySelector('path');
        const label = circleProgress.querySelector('.macro-label');

        if (text) text.textContent = Math.round(value);
        if (path) path.setAttribute('stroke-dasharray', `${percentage}, 100`);
        if (label) {
            label.textContent = `${type.charAt(0).toUpperCase() + type.slice(1)} (${Math.round(percentage)}%)`;
        }
        circleProgress.classList.toggle('full', percentage >= 100);
    }

    function updateMacroProgress(type, value, goal) {
        const progressContainer = document.querySelector(`.macro-item .${type}-bg`);
        if (!progressContainer) return;

        const valueElement = progressContainer.nextElementSibling.querySelector('.macro-value');
        const nameElement = progressContainer.nextElementSibling.querySelector('.macro-name');
        const progressElement = document.querySelector(`.progress.${type}`);

        if (valueElement) valueElement.textContent = `${Math.round(value)}g`;
        
        const percentage = goal > 0 ? Math.min((value / goal) * 100, 100) : 0;
        if (progressElement) progressElement.style.width = `${percentage}%`;
        
        if (nameElement) {
            nameElement.textContent = `${type.charAt(0).toUpperCase() + type.slice(1)} (${Math.round(percentage)}%)`;
        }
    }

    function updateAchievementMessage(totals, goals) {
        const calculatePercentage = (value, goal) => goal > 0 ? (value / goal) * 100 : 0;
        
        const percentages = {
            calories: calculatePercentage(totals.total_calories, goals.calories),
            protein: calculatePercentage(totals.total_protein, goals.protein),
            carbs: calculatePercentage(totals.total_carbs, goals.carbs),
            fat: calculatePercentage(totals.total_fat, goals.fat)
        };

        const summaryCard = document.querySelector('.summary-card');
        // Remove existing messages
        const existingMessage = summaryCard.querySelector('.achievement-message, .warning-message');
        if (existingMessage) existingMessage.remove();

        const allGoalsAchieved = Object.values(percentages).every(p => p >= 90 && p <= 110);
        const caloriesExceeded = percentages.calories >= 100;

        if (allGoalsAchieved) {
            summaryCard.insertAdjacentHTML('beforeend', `
                <div class="achievement-message">
                    <i class='bx bx-check-circle'></i>
                    <span>Congratulations! You've achieved your daily nutritional goals!</span>
                </div>
            `);
        } else if (caloriesExceeded) {
            summaryCard.insertAdjacentHTML('beforeend', `
                <div class="warning-message">
                    <i class='bx bx-info-circle'></i>
                    <span>Warning: You've exceeded your daily calorie goal!</span>
                </div>
            `);
        }
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
        if (mealItems && mealItems.children.length === 0) {
            const mealSection = mealItems.closest('.meal-section');
            const mealType = mealSection.classList[1];
            const iconClass = mealSection.querySelector('.meal-icon i').className;
            mealItems.innerHTML = `
                <div class="empty-meal">
                    <i class='${iconClass}'></i>
                    <p>No ${mealType} logged today</p>
                    <button class="add-meal-btn" onclick="openMealSelector('${mealType}')">
                        Add Food
                    </button>
                </div>
            `;
        }
    }

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
                        <button class="delete-entry" 
                                onclick="showDeleteConfirm(${safeFood.journal_id}, '${safeFood.food_name}')">
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
                            <span class="tag-item calories"><i class='bx bx-flame'></i>${safeFood.calories} kcal</span>
                            <span class="tag-item protein"><i class='bx bx-bowl-hot'></i>${safeFood.protein}g</span>
                            <span class="tag-item carbs"><i class='bx bx-baguette'></i>${safeFood.carbs}g</span>
                            <span class="tag-item fat"><i class='bx bx-droplet'></i>${safeFood.fat}g</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function resetFoodSelection() {
        selectedFoodId = null;
        document.querySelectorAll(CONFIG.CLASSES.foodSelectItem).forEach(item => {
            item.classList.remove(CONFIG.CLASSES.selected);
        });
        foodSelectionModal.hide();
    }

    // Attach global methods for external access
    window.openMealSelector = openMealSelector;
    window.selectFood = selectFood;
    window.filterFoodItems = filterFoodItems;
    window.addToJournal = addToJournal;
    window.showDeleteConfirm = showDeleteConfirm;
    window.deleteJournalEntry = deleteJournalEntry;
    window.showErrorModal = ModalManager.showError.bind(ModalManager);
    window.closeModal = ModalManager.close.bind(ModalManager);
    window.showSuccessModal = ModalManager.showSuccess.bind(ModalManager);

    // Initial load
    fetchDailySummary();
});
</script>