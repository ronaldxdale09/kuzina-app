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

<div class="success-modal" id="successModal">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Success</h5>
            <span class="close-btn">&times;</span>
        </div>
        <div class="modal-body">
            <div class="success-icon">
                <i class='bx bx-check'></i>
            </div>
            <p id="successMessage">Operation completed successfully!</p>
        </div>
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
.success-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    width: 90%;
    max-width: 400px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from { transform: translateY(-100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.close-btn {
    cursor: pointer;
    font-size: 24px;
    color: #666;
}

.success-icon {
    text-align: center;
    margin: 10px 0;
}

.success-icon i {
    font-size: 48px;
    color: #4CAF50;
}

.modal-body {
    text-align: center;
}

.modal-body p {
    margin: 10px 0;
    color: #333;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const goalsModal = new bootstrap.Offcanvas(document.getElementById('goalsModal'));

    window.showSuccessModal = function(message = 'Operation completed successfully!') {
        const modal = document.getElementById('successModal');
        const messageElement = document.getElementById('successMessage');
        
        messageElement.textContent = message;
        modal.style.display = 'block';
        
        // Auto close after 2 seconds
        setTimeout(() => {
            closeSuccessModal();
            // Refresh the page or update the UI as needed
            location.reload();
        }, 2000);
    }

    window.closeSuccessModal = function() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('successModal');
        if (event.target === modal) {
            closeSuccessModal();
        }
    }

    // Close button handler
    document.querySelector('.success-modal .close-btn').onclick = closeSuccessModal;

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