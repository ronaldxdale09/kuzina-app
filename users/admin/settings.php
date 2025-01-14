<?php include 'includes/header.php'; ?>

<style>
/* Keeping your existing base styles */
/* Adding specific styles for settings page */
.setting-group {
    border-bottom: 1px solid #eee;
    padding-bottom: 20px;
    margin-bottom: 20px;
}

.setting-group:last-child {
    border-bottom: none;
}

.input-group-text {
    background-color: #502121;
    color: white;
    border: none;
}

.form-control:focus {
    border-color: #502121;
    box-shadow: none;
}

.btn-save {
    background-color: #502121;
    color: white;
    padding: 10px 25px;
    border: none;
    border-radius: 5px;
}

.btn-save:hover {
    background-color: #632929;
    color: white;
}

.btn-secondary {
    background-color: #6c757d;
    color: white;
    padding: 10px 25px;
    border: none;
    border-radius: 5px;
}

.btn-secondary:hover {
    background-color: #5a6268;
    color: white;
}

.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: white;
    animation: slideIn 0.3s ease-out;
    z-index: 9999;
}

.notification.success {
    background-color: #28a745;
}

.notification.error {
    background-color: #dc3545;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }

    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
<!-- Header Start -->
<?php include 'navbar/main.navbar.php'; ?>
<!-- Navigation Start -->
<?php include 'includes/sidebar.php'; ?>
<div class="content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h4>System Settings</h4>
            </div>
            <div class="card-body">
                <form id="settingsForm">
                    <!-- Platform Settings -->
                    <div class="setting-group">
                        <h5 class="mb-4">Platform Settings</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Platform Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-store'></i></span>
                                    <input type="text" class="form-control" name="platform_name"
                                        value="<?php echo getSettingValue('platform_name'); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Support Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                    <input type="email" class="form-control" name="platform_contact_email"
                                        value="<?php echo getSettingValue('platform_contact_email'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Settings -->
                    <div class="setting-group">
                        <h5 class="mb-4">Commission Settings</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Kitchen Commission Rate (%)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-dollar-circle'></i></span>
                                    <input type="number" class="form-control" name="kitchen_commission_rate"
                                        value="<?php echo getSettingValue('kitchen_commission_rate'); ?>" min="0"
                                        max="100">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Base Delivery Fee</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-cycling'></i></span>
                                    <input type="number" class="form-control" name="rider_fee"
                                        value="<?php echo getSettingValue('rider_fee'); ?>" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Settings -->
                    <div class="setting-group">
                        <h5 class="mb-4">Order Settings</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Minimum Order Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-purchase-tag'></i></span>
                                    <input type="number" class="form-control" name="min_order_amount"
                                        value="<?php echo getSettingValue('min_order_amount'); ?>" min="0">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Maximum Delivery Radius (km)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-map'></i></span>
                                    <input type="number" class="form-control" name="max_delivery_radius"
                                        value="<?php echo getSettingValue('max_delivery_radius'); ?>" min="1">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Operating Hours -->
                    <div class="setting-group">
                        <h5 class="mb-4">Operating Hours</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Opening Time</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-time'></i></span>
                                    <input type="time" class="form-control" name="operating_hours_start"
                                        value="<?php echo json_decode(getSettingValue('operating_hours'))->start ?? '08:00'; ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Closing Time</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-time'></i></span>
                                    <input type="time" class="form-control" name="operating_hours_end"
                                        value="<?php echo json_decode(getSettingValue('operating_hours'))->end ?? '22:00'; ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-secondary me-2" onclick="resetForm()">
                            <i class='bx bx-reset me-2'></i> Reset
                        </button>
                        <button type="submit" class="btn btn-save">
                            <i class='bx bx-save me-2'></i> Save Settings
                        </button>
                    </div>

                    <br><br><br><br>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Store original values for reset functionality
let originalValues = {};

document.addEventListener('DOMContentLoaded', function() {
    // Store original form values
    const form = document.getElementById('settingsForm');
    const formData = new FormData(form);
    formData.forEach((value, key) => {
        originalValues[key] = value;
    });

    // Add form submit handler
    form.addEventListener('submit', handleSubmit);
});

function resetForm() {
    const form = document.getElementById('settingsForm');
    for (const [key, value] of Object.entries(originalValues)) {
        const input = form.elements[key];
        if (input) {
            input.value = value;
        }
    }
    showSuccessMessage('Form reset to original values');
}

async function handleSubmit(e) {
    e.preventDefault();

    try {
        // Show loading state
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Saving...';
        submitBtn.disabled = true;

        const formData = new FormData(this);
        const settings = {};
        formData.forEach((value, key) => {
            settings[key] = value;
        });

        // Handle operating hours
        settings.operating_hours = JSON.stringify({
            start: settings.operating_hours_start,
            end: settings.operating_hours_end
        });
        delete settings.operating_hours_start;
        delete settings.operating_hours_end;

        // Validate inputs
        for (const [key, value] of Object.entries(settings)) {
            if (!value && key !== 'maintenance_mode') {
                throw new Error(`${key.replace('_', ' ')} cannot be empty`);
            }
        }

        const response = await fetch('functions/update_settings.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(settings)
        });

        const data = await response.json();

        if (data.success) {
            showSuccessMessage('Settings updated successfully');
            // Update original values
            originalValues = {
                ...settings
            };
        } else {
            throw new Error(data.message || 'Failed to update settings');
        }

    } catch (error) {
        showErrorMessage(error.message);
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Add validation to number inputs
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', function() {
        const min = parseFloat(this.min);
        const max = parseFloat(this.max);
        let value = parseFloat(this.value);

        if (min !== undefined && value < min) {
            this.value = min;
        }
        if (max !== undefined && value > max) {
            this.value = max;
        }
    });
});

// Add these helper functions if not already present
function showSuccessMessage(message) {
    const notification = document.createElement('div');
    notification.className = 'notification success';
    notification.innerHTML = `
        <i class="fas fa-check-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

function showErrorMessage(message) {
    const notification = document.createElement('div');
    notification.className = 'notification error';
    notification.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
</script>

<?php
// Helper function to get setting value
function getSettingValue($key) {
    global $conn;
    $sql = "SELECT setting_value FROM system_settings WHERE setting_key = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $key);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    return '';
}
?>

<?php include 'includes/appbar.php'; ?>
<?php include 'includes/scripts.php'; ?>