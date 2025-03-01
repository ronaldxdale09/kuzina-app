<?php
include 'includes/header.php';

// Fetch kitchen details
$kitchen_id = $_COOKIE['kitchen_id'];
$stmt = $conn->prepare("SELECT * FROM kitchens WHERE kitchen_id = ?");
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$kitchen = $stmt->get_result()->fetch_assoc();
?>

<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/kitchen_profile.css">
<?php include 'navbar/main.navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="profile-page">
    <main class="profile-content">
        <form id="profileForm" class="profile-form" enctype="multipart/form-data">
            <div class="form-header">
                <h2>Profile Settings</h2>
                <p>Update your kitchen profile information</p>
            </div>

            <div class="profile-photo-section">
                <div class="profile-photo">
                    <img src="<?php echo $kitchen['photo'] ? '../../uploads/kitchen_photos/' . $kitchen['photo'] : 'assets/images/default-kitchen.png'; ?>"
                        alt="Kitchen Profile" id="profilePreview">
                    <div class="photo-overlay">
                        <i class='bx bx-camera'></i>
                        <span>Change Photo</span>
                    </div>
                </div>
                <input type="file" id="photoInput" name="photo" accept="image/*" hidden>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class='bx bx-user'></i> Personal Information
                </div>

                <div class="form-group">
                    <label for="fname">First Name</label>
                    <div class="input-icon-wrapper">
                        <i class='bx bx-user input-icon'></i>
                        <input type="text" id="fname" name="fname" class="input-with-icon"
                            value="<?php echo htmlspecialchars($kitchen['fname']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <div class="input-icon-wrapper">
                        <i class='bx bx-user input-icon'></i>
                        <input type="text" id="lname" name="lname" class="input-with-icon"
                            value="<?php echo htmlspecialchars($kitchen['lname']); ?>" required>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class='bx bx-envelope'></i> Contact Information
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class='bx bx-envelope input-icon'></i>
                        <input type="email" id="email" name="email" class="input-with-icon"
                            value="<?php echo htmlspecialchars($kitchen['email']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <div class="input-icon-wrapper">
                        <i class='bx bx-phone input-icon'></i>
                        <input type="tel" id="phone" name="phone" class="input-with-icon"
                            value="<?php echo htmlspecialchars($kitchen['phone']); ?>" required>
                    </div>
                </div>
            </div>

            <!-- Location Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class='bx bx-map'></i> Location Information
                </div>

                <div class="form-group location-group">
                    <label for="address">Address</label>
                    <div class="address-container">
                        <textarea id="address" name="address" rows="3"
                            required><?php echo htmlspecialchars($kitchen['address']); ?></textarea>
                        <button type="button" id="getLocationBtn" class="location-btn">
                            <i class='bx bx-current-location'></i> Use My Location
                        </button>
                    </div>
                    <!-- Hidden fields for coordinates -->
                    <input type="hidden" id="latitude" name="latitude"
                        value="<?php echo htmlspecialchars($kitchen['latitude'] ?? ''); ?>">
                    <input type="hidden" id="longitude" name="longitude"
                        value="<?php echo htmlspecialchars($kitchen['longitude'] ?? ''); ?>">
                </div>
            </div>

            <!-- Kitchen Details Section -->
            <div class="form-section">
                <div class="section-title">
                    <i class='bx bx-restaurant'></i> Kitchen Details
                </div>

                <div class="form-group">
                    <label for="description">Kitchen Description</label>
                    <textarea id="description" name="description" rows="4"
                        placeholder="Tell customers about your kitchen, specialties, and what makes your food unique"><?php echo htmlspecialchars($kitchen['description']); ?></textarea>
                </div>
            </div>

            <button type="submit" id="saveBtn" class="save-btn">
                <span>Save Changes</span>
                <div class="spinner" style="display: none;">
                    <div class="spinner-border"></div>
                </div>
            </button>
        </form>
    </main>
</div>

<!-- Toast Container for Notifications -->
<div id="toast-container"></div>

<!-- Error Modal -->
<div id="errorModal" class="modal">
    <div class="modal-content">
        <span id="closeErrorModal" class="close">&times;</span>
        <h2>Error</h2>
        <p id="errorMessage"></p>
        <button id="closeErrorBtn" class="btn-outline">Close</button>
    </div>
</div>
<?php include 'includes/appbar.php'; ?>
<?php include 'includes/scripts.php'; ?>

<!-- Add this CSS to your page -->
<style>
.save-btn {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 44px;
}

.save-btn .spinner {
    position: absolute;
    display: flex;
    align-items: center;
    justify-content: center;
}

.spinner-border {
    display: inline-block;
    width: 1.5rem;
    height: 1.5rem;
    border: 0.2em solid currentColor;
    border-right-color: transparent;
    border-radius: 50%;
    animation: spinner-border .75s linear infinite;
}

@keyframes spinner-border {
    to {
        transform: rotate(360deg);
    }
}

/* Toast styles */
#toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}

.toast {
    min-width: 300px;
    margin-top: 10px;
    padding: 15px 20px;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    font-size: 14px;
    transition: transform 0.3s ease, opacity 0.3s ease;
    transform: translateY(25px);
    opacity: 0;
}

.toast.show {
    transform: translateY(0);
    opacity: 1;
}

.toast-success {
    background-color: #4CAF50;
    color: white;
}

.toast-error {
    background-color: #f44336;
    color: white;
}

.toast-icon {
    margin-right: 12px;
    font-size: 20px;
}

.toast-message {
    flex: 1;
}

.toast-close {
    cursor: pointer;
    font-size: 16px;
    margin-left: 10px;
    opacity: 0.7;
}

.toast-close:hover {
    opacity: 1;
}
</style>

<!-- Update your JavaScript code -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profileForm');
    const photoInput = document.getElementById('photoInput');
    const profilePreview = document.getElementById('profilePreview');
    const photoOverlay = document.querySelector('.photo-overlay');
    const getLocationBtn = document.getElementById('getLocationBtn');
    const saveBtn = document.getElementById('saveBtn');

    // Toast container
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    // Handle photo selection
    photoOverlay.addEventListener('click', () => photoInput.click());

    photoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePreview.src = e.target.result;
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // Get location functionality 
    if (getLocationBtn) {
        getLocationBtn.addEventListener('click', function() {
            if (navigator.geolocation) {
                // Show loading state
                const originalText = this.textContent;
                this.textContent = 'Getting location...';
                this.disabled = true;

                navigator.geolocation.getCurrentPosition(
                    // Success callback
                    function(position) {
                        const lat = position.coords.latitude;
                        const lon = position.coords.longitude;

                        // Fill coordinates in hidden fields
                        document.getElementById('latitude').value = lat;
                        document.getElementById('longitude').value = lon;

                        // Fetch address using coordinates
                        fetchAddressFromCoordinates(lat, lon);
                    },
                    // Error callback
                    function(error) {
                        // Reset button
                        getLocationBtn.textContent = originalText;
                        getLocationBtn.disabled = false;

                        let errorMessage = "";
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMessage =
                                    "Location access was denied. Please enable location services in your browser settings.";
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMessage =
                                    "Location information is unavailable. Please try again.";
                                break;
                            case error.TIMEOUT:
                                errorMessage =
                                    "The request to get your location timed out. Please try again.";
                                break;
                            case error.UNKNOWN_ERROR:
                                errorMessage =
                                    "An unknown error occurred while retrieving your location.";
                                break;
                        }
                        showToast(errorMessage, 'error');
                    },
                    // Options
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            } else {
                showToast("Geolocation is not supported by this browser.", 'error');
            }
        });
    }

    // Function to fetch address from coordinates
    function fetchAddressFromCoordinates(lat, lon) {
        // Show fetching state in address field
        const addressField = document.getElementById('address');
        addressField.value = "Getting address...";

        // Using proxy script to avoid CORS issues
        const url = `../../loc_api/nominatim.php?lat=${lat}&lon=${lon}`;

        fetch(url, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reset location button
                getLocationBtn.textContent = 'Use My Location';
                getLocationBtn.disabled = false;

                if (data && data.address) {
                    const address = data.address;

                    // Extract relevant address components
                    const city = address.city || address.town || address.village || address.county || '';
                    const suburb = address.suburb || address.neighbourhood || address.borough || '';
                    const postcode = address.postcode || '';
                    const street = address.road || address.street || '';
                    const houseNumber = address.house_number || '';
                    const state = address.state || '';
                    const country = address.country || '';

                    // Format address
                    let formattedAddress = '';
                    if (street && houseNumber) {
                        formattedAddress += `${houseNumber} ${street}, `;
                    } else if (street) {
                        formattedAddress += `${street}, `;
                    }

                    if (suburb) formattedAddress += `${suburb}, `;
                    if (city) formattedAddress += `${city}, `;
                    if (state) formattedAddress += `${state}, `;
                    if (postcode) formattedAddress += `${postcode}, `;
                    if (country) formattedAddress += country;

                    // Remove trailing comma and space if present
                    formattedAddress = formattedAddress.replace(/,\s*$/, '');

                    // Update address field
                    addressField.value = formattedAddress || data.display_name ||
                        "Address found but details unavailable";

                    // Show success message
                    showToast("Location detected successfully!", 'success');
                } else if (data && data.display_name) {
                    // If we only have display_name
                    addressField.value = data.display_name;
                    showToast("Location detected!", 'success');
                } else {
                    // Fallback to coordinates
                    useFallbackGeocoding(lat, lon);
                }
            })
            .catch(error => {
                console.error('Error fetching address:', error);

                // Reset button
                getLocationBtn.textContent = 'Use My Location';
                getLocationBtn.disabled = false;

                // Try fallback
                useFallbackGeocoding(lat, lon);
            });
    }

    // Fallback geocoding function
    function useFallbackGeocoding(lat, lon) {
        const addressField = document.getElementById('address');

        // Use BigDataCloud client-side API
        const url =
            `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=en`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data) {
                    // Format address from BigDataCloud response
                    let formattedAddress = '';

                    if (data.locality) formattedAddress += `${data.locality}, `;
                    if (data.city) formattedAddress += `${data.city}, `;
                    if (data.principalSubdivision) formattedAddress += `${data.principalSubdivision}, `;
                    if (data.countryName) formattedAddress += data.countryName;

                    // Remove trailing comma and space if present
                    formattedAddress = formattedAddress.replace(/,\s*$/, '');

                    addressField.value = formattedAddress ||
                        `Location at: ${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                    showToast("Location detected!", 'success');
                } else {
                    // Last resort - use coordinates
                    addressField.value = `Location at: ${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                    showToast("Could not retrieve detailed address. Coordinates have been used instead.",
                        'error');
                }
            })
            .catch(error => {
                console.error('Error with fallback geocoding:', error);

                // If all else fails, just use coordinates
                addressField.value = `Location at: ${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                showToast(
                    "There was a problem retrieving your address. Please enter your address manually.",
                    'error');
            });
    }

    // Show toast notification
    function showToast(message, type = 'success') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;

        // Icon based on type
        let icon = '✓';
        if (type === 'error') {
            icon = '✕';
        }

        toast.innerHTML = `
            <div class="toast-icon">${icon}</div>
            <div class="toast-message">${message}</div>
            <div class="toast-close">×</div>
        `;

        // Add to container and show
        toastContainer.appendChild(toast);

        // Close button functionality
        const closeBtn = toast.querySelector('.toast-close');
        closeBtn.addEventListener('click', function() {
            toast.style.opacity = '0';
            setTimeout(() => {
                toastContainer.removeChild(toast);
            }, 300);
        });

        // Show toast with animation
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Auto-remove after delay
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                if (toast.parentNode === toastContainer) {
                    toastContainer.removeChild(toast);
                }
            }, 300);
        }, 4000);
    }

    // Handle form submission
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loading state
        const saveButtonText = saveBtn.querySelector('span');
        const saveButtonSpinner = saveBtn.querySelector('.spinner');

        saveButtonText.style.visibility = 'hidden';
        saveButtonSpinner.style.display = 'flex';
        saveBtn.disabled = true;

        const formData = new FormData(this);

        fetch('functions/update_kitchen_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reset button state
                saveButtonText.style.visibility = 'visible';
                saveButtonSpinner.style.display = 'none';
                saveBtn.disabled = false;

                if (data.success) {
                    showToast('Profile updated successfully!', 'success');
                } else {
                    showToast(data.message || 'Failed to update profile', 'error');
                }
            })
            .catch(error => {
                // Reset button state
                saveButtonText.style.visibility = 'visible';
                saveButtonSpinner.style.display = 'none';
                saveBtn.disabled = false;

                console.error('Error:', error);
                showToast('An error occurred while updating the profile', 'error');
            });
    });
});
</script>