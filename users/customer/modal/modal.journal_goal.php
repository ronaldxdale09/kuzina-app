<!-- Goals Modal -->
<div class="offcanvas offcanvas-bottom goals-modal" tabindex="-1" id="goalsModal" aria-labelledby="goalsModalLabel">
    <div class="offcanvas-header">
        <h5>Nutritional Goals</h5>
    </div>

    <div class="offcanvas-body">
        <form id="goalsForm">
            <div class="goal-input-group">
                <label>
                    <i class='bx bx-flame'></i>
                    Daily Calories
                </label>
                <div class="input-with-unit">
                    <input type="number" id="goalCalories" name="daily_calories"
                        value="<?= $current_goals['daily_calories'] ?>" min="0" step="50">
                    <span class="unit">kcal</span>
                </div>
            </div>

            <div class="goal-input-group">
                <label>
                    <i class='bx bx-bowl-hot'></i>
                    Daily Protein
                </label>
                <div class="input-with-unit">
                    <input type="number" id="goalProtein" name="daily_protein"
                        value="<?= $current_goals['daily_protein'] ?>" min="0" step="5">
                    <span class="unit">g</span>
                </div>
            </div>

            <div class="goal-input-group">
                <label>
                    <i class='bx bx-baguette'></i>
                    Daily Carbs
                </label>
                <div class="input-with-unit">
                    <input type="number" id="goalCarbs" name="daily_carbs" value="<?= $current_goals['daily_carbs'] ?>"
                        min="0" step="5">
                    <span class="unit">g</span>
                </div>
            </div>

            <div class="goal-input-group">
                <label>
                    <i class='bx bx-droplet'></i>
                    Daily Fat
                </label>
                <div class="input-with-unit">
                    <input type="number" id="goalFat" name="daily_fat" value="<?= $current_goals['daily_fat'] ?>"
                        min="0" step="5">
                    <span class="unit">g</span>
                </div>
            </div>
        </form>
    </div>

    <div class="offcanvas-footer">
        <button class="btn-outline" data-bs-dismiss="offcanvas">Cancel</button>
        <button class="btn-solid" onclick="saveGoals()">Save Goals</button>
    </div>
</div>

<style>
.goals-modal {
    height: 80vh;
}

.summary-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.edit-goals-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border: none;
    background: var(--accent-green);
    color: white;
    border-radius: 20px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.edit-goals-btn:hover {
    background: var(--primary-orange);
}

.goal-input-group {
    margin-bottom: 24px;
}

.goal-input-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    color: var(--text-dark);
    font-weight: 500;
}

.goal-input-group label i {
    color: var(--accent-green);
    font-size: 20px;
}

.input-with-unit {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8f9fa;
    padding: 4px;
    border-radius: 12px;
}

.input-with-unit input {
    flex: 1;
    padding: 12px;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    font-size: 16px;
    width: 100%;
}

.input-with-unit .unit {
    padding: 0 12px;
    color: var(--text-light);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const goalsModal = new bootstrap.Offcanvas(document.getElementById('goalsModal'));

    window.openGoalsModal = function() {
        goalsModal.show();
    }

    window.saveGoals = function() {
        const formData = new FormData(document.getElementById('goalsForm'));

        fetch('functions/journal/update_goals.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    goalsModal.hide();
                    showSuccessModal('Goals updated successfully');
                } else {
                    alert(data.message || 'Error updating goals');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating goals');
            });
    }
});
</script>