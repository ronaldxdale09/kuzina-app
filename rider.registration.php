<?php include 'includes/header.php';?>
<link rel="stylesheet" href="assets/css/register.css">

<style>
/* Step Indicator Styles */
.step-indicator {
    display: flex;
    justify-content: space-between;
    margin: 24px 0;
    position: relative;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 2px;
    background: #e0e0e0;
    z-index: 1;
}

.step {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    position: relative;
    z-index: 2;
}

.step.active {
    background: var(--theme-color, #8a0b10);
    border-color: var(--theme-color, #8a0b10);
    color: white;
}

.step::after {
    content: attr(data-step);
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    font-size: 12px;
    color: #666;
    margin-top: 4px;
}
.doc-upload-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 24px;
    max-width: 800px;
    margin: 0 auto;
}

.doc-upload-box {
    position: relative;
    aspect-ratio: 3/2;
    width: 100%;
}

.doc-upload-label {
    width: 100%;
    height: 100%;
    border: 2px dashed #e0e0e0;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}

.doc-upload-label:hover {
    border-color: #8a0b10;
}

.doc-preview {
    text-align: center;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.doc-preview i {
    font-size: 24px;
    color: #666;
    margin-bottom: 8px;
}

.doc-preview span {
    font-size: 12px;
    color: #666;
    display: block;
}

.doc-preview img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 8px;
}

.doc-upload-box input[type="file"] {
    display: none;
}
/* Enhanced Form Styles */
.form-section-title {
    font-size: 16px;
    color: #333;
    font-weight: 600;
    margin: 24px 0 16px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}

.input-box {
    margin-bottom: 16px;
}

.input-box input,
.input-box select {
    width: 100%;
    padding: 12px 16px 12px 48px;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.input-box input:focus,
.input-box select:focus {
    border-color: var(--theme-color, #8a0b10);
    box-shadow: 0 0 0 2px rgba(138, 11, 16, 0.1);
}

/* Button Styles */
.button-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-top: 24px;
    margin-bottom: 24px;

}

.btn-solid,
.btn-outline {
    padding: 12px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-solid {
    background: var(--theme-color, #8a0b10);
    color: white;
    border: none;
}

.btn-outline {
    background: transparent;
    border: 1px solid var(--theme-color, #8a0b10);
    color: var(--theme-color, #8a0b10);
}

/* Image Preview Styles */
.profile-picture {
    width: 120px;
    height: 120px;
    margin: 0 auto 24px;
}

.profile-label {
    width: 100%;
    height: 100%;
    border-radius: 60px;
    overflow: hidden;
    display: block;
    position: relative;
    cursor: pointer;
}

.profile-label img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.edit-icon {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    padding: 4px;
    font-size: 12px;
    text-align: center;
}

.input-error {
    border-color: #dc3545 !important;
    background-color: #fff8f8 !important;
}

.form-error-message {
    color: #dc3545;
    background-color: #fff8f8;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    animation: shake 0.5s ease-in-out;
}

.profile-picture.input-error .profile-label {
    border: 2px solid #dc3545;
    background-color: #fff8f8;
}

.doc-upload-box.input-error .doc-upload-label {
    border: 2px solid #dc3545;
    background-color: #fff8f8;
}

.form-error-message {
    color: #dc3545;
    background-color: #fff8f8;
    padding: 12px;
    border-radius: 8px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

@keyframes shake {

    0%,
    100% {
        transform: translateX(0);
    }

    25% {
        transform: translateX(-5px);
    }

    75% {
        transform: translateX(5px);
    }
}
</style>
<!-- Head End -->
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<!-- Body Start -->

<body>
    <div class="bg-pattern-wrap ratio2_1">
        <!-- Background Image -->
        <div class="bg-patter">
            <img src="assets/images/banner/bg-pattern2.png" class="bg-img" alt="pattern" />
        </div>
    </div>



    <!-- Registration Section Start -->
    <main class="main-wrap login-page mb-xxl">
        <div class="center-content">
            <img class="logo" src="assets/images/logo/logo-w2.png" alt="logo" />
            <p class="font-sm content-color">
                Join our delivery network and become a valued delivery partner. Start earning by delivering happiness to
                customers.
            </p>
        </div>

        <section class="login-section p-0">
            <form id="registration-form" method="POST" class="custom-form" enctype="multipart/form-data">
                <h1 class="font-md title-color fw-600">Delivery Partner Registration</h1>
                <input type="text" name="type" value="rider" hidden />

                <!-- Progress Indicator -->
                <div class="step-indicator">
                    <div class="step active" data-step="1">
                        <i class='bx bx-user'></i>
                    </div>
                    <div class="step" data-step="2">
                        <i class='bx bx-file'></i>
                    </div>
                    <div class="step" data-step="3">
                        <i class='bx bx-cycling'></i>
                    </div>
                </div>
                <!-- Tab 1: Personal Information -->
                <div class="form-tab" id="tab-1">
                    <div class="form-section-title">Personal Information</div>

                    <div class="input-box">
                        <input type="text" name="first_name" placeholder="First Name" required class="form-control" />
                        <i class="iconly-Profile icli"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" name="last_name" placeholder="Last Name" required class="form-control" />
                        <i class="iconly-Profile icli"></i>
                    </div>
                    <div class="input-box">
                        <input type="email" name="email" placeholder="Email Address" required class="form-control" />
                        <i class="iconly-Message icli"></i>
                    </div>
                    <div class="input-box">
                        <input type="tel" name="phone" placeholder="Phone Number" required class="form-control" />
                        <i class="iconly-Call icli"></i>
                    </div>

                    <button type="button" class="btn-solid btn-next" onclick="goToTab(2)">Continue to Documents</button>
                </div>

                <!-- Tab 2: Documents & Identity -->
                <div class="form-tab" id="tab-2" style="display: none;">
                    <div class="form-section-title">Profile Photo</div>

                    <div class="profile-picture">
                        <label for="profilePhoto" class="profile-label">
                            <img id="profilePreview" src="assets/images/logo/default.png" alt="Profile Picture" />
                            <div class="edit-icon">
                                <i class="iconly-Edit icli"></i>
                            </div>
                        </label>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" class="profile-input"
                            onchange="previewImage(event, 'profilePreview')" required />
                    </div>

                    <div class="form-section-title">Drivers License</div>

                    <div class="doc-upload-container">
                        <!-- Front ID -->
                        <div class="doc-upload-box">
                            <label for="idFront" class="doc-upload-label">
                                <div class="doc-preview" id="idFrontPreview">
                                    <i class='bx bx-id-card'></i>
                                    <span>Front of ID</span>
                                </div>
                            </label>
                            <input type="file" id="idFront" name="id_front" accept="image/*"
                                onchange="previewImage(event, 'idFrontPreview')" required />
                        </div>

                        <!-- Back ID -->
                        <div class="doc-upload-box">
                            <label for="idBack" class="doc-upload-label">
                                <div class="doc-preview" id="idBackPreview">
                                    <i class='bx bx-id-card'></i>
                                    <span>Back of ID</span>
                                </div>
                            </label>
                            <input type="file" id="idBack" name="id_back" accept="image/*"
                                onchange="previewImage(event, 'idBackPreview')" required />
                        </div>
                    </div>

                    <div class="button-row">
                        <button type="button" class="btn-outline btn-prev" onclick="goToTab(1)">Back</button>
                        <button type="button" class="btn-solid btn-next" onclick="goToTab(3)">Continue</button>
                    </div>
                </div>

                <!-- Tab 3: Vehicle Information and Credentials -->
                <div class="form-tab" id="tab-3" style="display: none;">
                    <div class="form-section-title">Vehicle Information</div>

                    <div class="input-box">
                        <select name="vehicle_type" required class="form-control">
                            <option value="">Select Vehicle Type</option>
                            <option value="Motorcycle">Motorcycle</option>
                            <option value="Bicycle">Bicycle</option>
                            <option value="Car">Car</option>
                        </select>
                        <i class='bx bx-cycling'></i>
                    </div>

                    <div class="input-box">
                        <input type="text" name="license_plate" placeholder="License Plate Number" required
                            class="form-control" />
                        <i class='bx bx-id-card'></i>
                    </div>

                    <div class="form-section-title">Account Security</div>
                    <div class="input-box">
                        <input type="password" name="password" placeholder="Create Password" required
                            class="form-control" />
                        <i class="iconly-Hide icli showHidePassword"></i>
                    </div>
                    <div class="input-box">
                        <input type="password" name="confirm_password" placeholder="Confirm Password" required
                            class="form-control" />
                        <i class="iconly-Hide icli showHidePassword"></i>
                    </div>

                    <div class="button-row">
                        <button type="button" class="btn-outline btn-prev" onclick="goToTab(2)">Back</button>
                        <button id="submit-btn" class="btn-solid" type="submit">Complete Registration</button>
                    </div>
                </div>

                <span class="content-color font-sm d-block text-center fw-600">
                    Already registered? <a href="rider.php" class="underline">Sign In</a>
                </span>
            </form>
        </section>
    </main>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span id="closeSuccessModal" class="close">&times;</span>
            <div class="success-icon">
                <i class='bx bx-check-circle'></i>
            </div>
            <h2>Welcome Aboard!</h2>
            <p>Thank you for joining our delivery partner network. Your application has been received and is under
                review.
                We'll notify you shortly about the next steps.</p>
            <button id="goToDashboard" class="btn-solid">Proceed</button>
        </div>
    </div>


    <script>
    // JavaScript to handle tab navigation
    function nextTab() {
        document.getElementById("tab-1").style.display = "none";
        document.getElementById("tab-2").style.display = "block";
    }

    function prevTab() {
        document.getElementById("tab-2").style.display = "none";
        document.getElementById("tab-1").style.display = "block";
    }

    // JavaScript for image preview
    function previewImage(event) {
        const preview = document.getElementById('profilePreview');
        const file = event.target.files[0];

        if (file) {
            preview.src = URL.createObjectURL(file);
        } else {
            preview.src = 'assets/images/logo/default.png'; // Reset to default if no file selected
        }
    }
    </script>


    <!-- Main End -->
    <div id="loading-overlay" style="display: none;">
        <div id="loading-spinner">
            <img src="assets/loader/loader5.gif" alt="Loading...">
        </div>
    </div>



    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span id="closeErrorModal" class="close">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage"></p>
            <button class="btn-outline" class="close">Close</button>
        </div>
    </div>
    <script>
    // Tab Navigation Functions
    function goToTab(tabNumber) {
        const formTabs = document.querySelectorAll('.form-tab');
        const steps = document.querySelectorAll('.step');

        // Validate current tab before proceeding
        if (tabNumber > 1 && !validateTab(tabNumber - 1)) {
            return false;
        }

        // Update tabs
        formTabs.forEach(tab => tab.style.display = 'none');
        document.getElementById(`tab-${tabNumber}`).style.display = 'block';

        // Update steps
        steps.forEach(step => {
            step.classList.remove('active');
            if (parseInt(step.getAttribute('data-step')) <= tabNumber) {
                step.classList.add('active');
            }
        });
    }

    // Validation for each tab
    function validateTab(tabNumber) {
        const currentTab = document.getElementById(`tab-${tabNumber}`);
        const requiredFields = currentTab.querySelectorAll('[required]');
        let isValid = true;

        // Remove any existing error message
        const existingError = currentTab.querySelector('.form-error-message');
        if (existingError) {
            existingError.remove();
        }

        // Reset all fields' styling
        requiredFields.forEach(field => {
            field.classList.remove('input-error');
            if (field.closest('.doc-upload-box')) {
                field.closest('.doc-upload-box').classList.remove('input-error');
            }
        });

        // Special handling for file inputs on tab 2
        if (tabNumber === 2) {
            const profilePhoto = document.getElementById('profilePhoto');
            const idFront = document.getElementById('idFront');
            const idBack = document.getElementById('idBack');

            // Check each file input
            if (!profilePhoto.files || !profilePhoto.files[0]) {
                isValid = false;
                profilePhoto.closest('.profile-picture').classList.add('input-error');
            }
            if (!idFront.files || !idFront.files[0]) {
                isValid = false;
                idFront.closest('.doc-upload-box').classList.add('input-error');
            }
            if (!idBack.files || !idBack.files[0]) {
                isValid = false;
                idBack.closest('.doc-upload-box').classList.add('input-error');
            }
        } else {
            // For other tabs, check regular input fields
            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    isValid = false;
                    field.classList.add('input-error');
                }
            });
        }

        // If not valid, show error message
        if (!isValid) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-error-message';
            errorDiv.innerHTML = '<i class="bx bx-error-circle"></i> Please fill in all required fields';
            currentTab.insertBefore(errorDiv, currentTab.firstChild);
            return false;
        }

        return true;
    }

    // Image Preview Functions
    function previewImage(event, previewId) {
        const file = event.target.files[0];
        const preview = document.getElementById(previewId);

        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showErrorModal("File size should not exceed 5MB");
                event.target.value = '';
                return;
            }

            // Validate file type
            if (!file.type.match('image.*')) {
                showErrorModal("Please upload an image file");
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                if (previewId === 'profilePreview') {
                    preview.src = e.target.result;
                } else {
                    const previewContainer = document.getElementById(previewId);
                    previewContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Document Preview" style="width: 100%; height: 100%; object-fit: cover;">
                    <div class="preview-overlay">
                        <i class='bx bx-check'></i>
                    </div>`;
                }
            }
            reader.readAsDataURL(file);
        }
    }

    // Location Functions
    function initializeLocationServices() {
        document.getElementById('getLocationBtn')?.addEventListener('click', function() {
            if (navigator.geolocation) {
                document.getElementById('loading-spinner').style.visibility = 'visible';
                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                });
            } else {
                showErrorModal("Geolocation is not supported by this browser.");
            }
        });
    }

    function showPosition(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;

        fetchAddressFromCoordinates(lat, lon);
    }

    function showError(error) {
        document.getElementById('loading-spinner').style.visibility = 'hidden';
        let errorMessage = "Location error: ";
        switch (error.code) {
            case error.PERMISSION_DENIED:
                errorMessage += "Please enable location services to continue.";
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage += "Location information is unavailable.";
                break;
            case error.TIMEOUT:
                errorMessage += "Location request timed out.";
                break;
            default:
                errorMessage += "An unknown error occurred.";
        }
        showErrorModal(errorMessage);
    }

    async function fetchAddressFromCoordinates(lat, lon) {
        const apiKey = 'YOUR_API_KEY';
        try {
            const response = await fetch(
                `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lon}&key=${apiKey}`);
            const data = await response.json();

            if (data.status === "OK" && data.results[0]) {
                const addressComponents = data.results[0].address_components;
                let addressParts = {
                    city: "",
                    barangay: "",
                    postalCode: ""
                };

                addressComponents.forEach(component => {
                    if (component.types.includes("locality")) {
                        addressParts.city = component.long_name;
                    }
                    if (component.types.includes("sublocality") ||
                        component.types.includes("neighborhood") ||
                        component.types.includes("administrative_area_level_2")) {
                        addressParts.barangay = component.long_name;
                    }
                    if (component.types.includes("postal_code")) {
                        addressParts.postalCode = component.long_name;
                    }
                });

                updateLocationField(addressParts);
            } else {
                throw new Error("Could not retrieve address");
            }
        } catch (error) {
            showErrorModal("Error fetching address: " + error.message);
        } finally {
            document.getElementById('loading-spinner').style.visibility = 'hidden';
        }
    }

    function updateLocationField(addressParts) {
        const locationField = document.getElementById('location');
        if (addressParts.city && addressParts.barangay && addressParts.postalCode) {
            locationField.value = `${addressParts.city}, ${addressParts.barangay}, ${addressParts.postalCode}`;
        } else if (addressParts.city && addressParts.barangay) {
            locationField.value = `${addressParts.city}, ${addressParts.barangay}`;
        } else if (addressParts.city) {
            locationField.value = `${addressParts.city}`;
        } else {
            showErrorModal("Could not retrieve complete address. Please enter manually.");
        }
    }

    // Form Submission Handler
    document.addEventListener("DOMContentLoaded", function() {
        initializeLocationServices();

        const form = document.getElementById('registration-form');
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validateAllTabs()) {
                return;
            }

            document.getElementById('loading-spinner').style.visibility = 'visible';

            try {
                const formData = new FormData(form);
                const response = await fetch('functions/registration.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showSuccessModal();
                } else {
                    throw new Error(data.message || 'Registration failed');
                }
            } catch (error) {
                showErrorModal(error.message);
            } finally {
                document.getElementById('loading-spinner').style.visibility = 'hidden';
            }
        });
    });

    // Modal Functions
    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.classList.add('show');

        document.getElementById('goToDashboard').addEventListener('click', function() {
            window.location.href = "users/delivery/homepage.php";
        });
    }

    function showErrorModal(message) {
        const modal = document.getElementById('errorModal');
        document.getElementById('errorMessage').textContent = message;
        modal.classList.add('show');
    }

    // Close modal handlers
    document.querySelectorAll('.close').forEach(button => {
        button.addEventListener('click', function() {
            this.closest('.modal').classList.remove('show');
        });
    });

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.classList.remove('show');
        }
    });

    // Helper Functions
    function validateAllTabs() {
        for (let i = 1; i <= 3; i++) {
            if (!validateTab(i)) {
                goToTab(i);
                return false;
            }
        }
        return true;
    }

    function showErrorMessage(field, message) {
        const existingError = field.nextElementSibling?.classList.contains('error-message');
        if (!existingError) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.textContent = message;
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }
    }

    function removeErrorMessage(field) {
        const errorMessage = field.nextElementSibling;
        if (errorMessage?.classList.contains('error-message')) {
            errorMessage.remove();
        }
    }
    </script>



    <!-- jquery 3.6.0 -->
    <script src="assets/js/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Js -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>

    <!-- Feather Icon -->
    <script src="assets/js/feather.min.js"></script>

    <!-- Theme Setting js -->
    <script src="assets/js/theme-setting.js"></script>

    <!-- Script js -->
    <script src="assets/js/script.js"></script>
</body>
<!-- Body End -->

</html>
<!-- Html End -->