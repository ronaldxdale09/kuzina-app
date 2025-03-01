<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/register.css">

<!-- Head End -->

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
                Please create an account to start browsing our selection of delicious meals from your kitchen
                mothers.
            </p>
        </div>

        <!-- Registration Section Start -->
        <section class="login-section p-0">
            <!-- Registration Form Start -->
            <form id="registration-form" method="POST" class="custom-form" enctype="multipart/form-data">
                <h1 class="font-md title-color fw-600">Register Account</h1>
                <input type="text" name="type" value="kitchen" hidden />
                <!-- Tab 1: Personal Information -->
                <div class="form-tab" id="tab-1">
                    <div class="profile-picture">
                        <label for="profilePhoto" class="profile-label">
                            <img id="profilePreview" src="assets/images/logo/default.png" alt="Profile Picture" />
                            <div class="edit-icon">
                                <i class="iconly-Edit icli"></i>
                            </div>
                        </label>
                        <input type="file" id="profilePhoto" name="profilePhoto" accept="image/*" class="profile-input"
                            onchange="previewImage(event)" />
                    </div> <br>

                    <!-- Full Name Inputs Start -->
                    <div class="input-box">
                        <input type="text" name="fname" placeholder="First Name" required class="form-control"
                            autocomplete="off" />
                        <i class="iconly-Profile icli"></i>
                    </div>
                    <div class="input-box">
                        <input type="text" name="lname" placeholder="Last Name" required class="form-control"
                            autocomplete="off" />
                        <i class="iconly-Profile icli"></i>
                    </div>

                    <!-- Email Input Start -->
                    <div class="input-box">
                        <input type="email" name="email" placeholder="Email Address" class="form-control" required />
                        <i data-feather="at-sign"></i>
                    </div>

                    <!-- Next Button -->
                    <button type="button" class="btn-solid btn-next" onclick="nextTab()">Next</button>
                </div>


                <!-- Tab 2: Location, Password, and Profile Picture -->
                <div class="form-tab" id="tab-2" style="display: none;">
                    <!-- Location Input Start -->
                    <div class="input-box">
                        <input type="text" id="location" name="location" placeholder="City, Barangay, Postal Code"
                            required class="form-control" />
                        <button type="button" class="btn btn-location" id="getLocationBtn"> Use My Location </button>
                    </div>

                    <!-- Hidden Latitude and Longitude Inputs -->
                    <input type="hidden" id="latitude" name="latitude" />
                    <input type="hidden" id="longitude" name="longitude" />

                    <div class="input-box">
                        <input type="text" name="phone" placeholder="Phone Number" required class="form-control"
                            autocomplete="off" />
                        <i class="iconly-Call icli"></i>
                    </div>

                    <!-- Password Input Start -->
                    <div class="input-box">
                        <input type="password" name="password" placeholder="Password" required class="form-control" />
                        <i class="iconly-Hide icli showHidePassword"></i>
                    </div>

                    <!-- Kitchen Description and Goals Textarea Start -->
                    <div class="input-box">
                        <textarea name="description" placeholder="Give your Kitchen Description and Goals" required
                            class="form-control" rows="4"></textarea>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="button-row">
                        <button type="button" class="btn-outline btn-prev" onclick="prevTab()">Previous</button>
                        <button id="submit-btn" class="btn-solid" type="submit">Register</button>
                    </div>
                </div>

                <span class="content-color font-sm d-block text-center fw-600">Already have an Account?
                    <a href="kitchen.php" class="underline">Sign In</a>
                </span>
            </form>
            <!-- Registration Form End -->
        </section>
        <!-- Registration Section End -->
    </main>


    <!-- Main End -->
    <div id="loading-overlay" style="display: none;">
        <div id="loading-spinner">
            <img src="assets/loader/loader5.gif" alt="Loading...">
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span id="closeSuccessModal" class="close">&times;</span>
            <h2>Welcome to the Family!</h2>
            <p>Thank you for joining our community of passionate kitchen mothers! Your application is now under review,
                and we can’t wait to see you bring delicious, home-cooked meals to life. You’ll hear from us soon with
                the next steps!</p>
            <button id="goToDashboard" class="btn-solid">Proceed</button>
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

        // Show the success modal
        function showSuccessModal() {
            const successModal = document.getElementById('successModal');
            successModal.classList.add('show'); // Add the 'show' class to make it visible
        }

        // Show error modal with custom message
        function showErrorModal(message) {
            const errorModal = document.getElementById('errorModal');
            document.getElementById('errorMessage').innerText = message;
            errorModal.classList.add('show'); // Show error modal
        }

        // Show success toast notification
        function showSuccessToast(message) {
            // Check if toast container exists, if not create it
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                document.body.appendChild(toastContainer);

                // Add styles to the toast container
                toastContainer.style.position = 'fixed';
                toastContainer.style.bottom = '20px';
                toastContainer.style.right = '20px';
                toastContainer.style.zIndex = '9999';
            }

            // Create toast element
            const toast = document.createElement('div');
            toast.className = 'success-toast';
            toast.innerHTML = `
        <div class="toast-icon">✓</div>
        <div class="toast-message">${message}</div>
    `;

            // Style the toast
            toast.style.backgroundColor = '#4CAF50';
            toast.style.color = 'white';
            toast.style.padding = '12px 20px';
            toast.style.borderRadius = '4px';
            toast.style.marginTop = '10px';
            toast.style.display = 'flex';
            toast.style.alignItems = 'center';
            toast.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
            toast.style.minWidth = '250px';
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.5s ease';

            // Style the icon
            const iconDiv = toast.querySelector('.toast-icon');
            iconDiv.style.marginRight = '10px';
            iconDiv.style.fontSize = '18px';
            iconDiv.style.fontWeight = 'bold';

            // Add to container
            toastContainer.appendChild(toast);

            // Trigger animation
            setTimeout(() => {
                toast.style.opacity = '1';
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    toastContainer.removeChild(toast);
                }, 500);
            }, 3000);
        }

        function showPosition(position) {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;

            // Fill in hidden latitude and longitude fields
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lon;

            // Fetch human-readable address using OpenStreetMap Nominatim
            fetchAddressFromCoordinates(lat, lon);
        }

        function showError(error) {
            // Reset the location button if there was an error
            if (document.getElementById('getLocationBtn')) {
                const locationBtn = document.getElementById('getLocationBtn');
                locationBtn.innerHTML = 'Use My Location';
                locationBtn.disabled = false;
            }

            let errorMessage = "";
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    errorMessage = "Location access was denied. Please enable location services in your browser settings.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    errorMessage = "Location information is unavailable. Please try again.";
                    break;
                case error.TIMEOUT:
                    errorMessage = "The request to get your location timed out. Please try again.";
                    break;
                case error.UNKNOWN_ERROR:
                    errorMessage = "An unknown error occurred while retrieving your location.";
                    break;
            }
            showErrorModal(errorMessage);
        }

        function fetchAddressFromCoordinates(lat, lon) {
            // Using our proxy script to fetch from Nominatim
            const url = `loc_api/nominatim.php?lat=${lat}&lon=${lon}`;

            // Show temporary text in the location field while fetching
            const locationField = document.getElementById('location');
            locationField.value = "Getting address...";

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
                    // Reset the location button
                    if (document.getElementById('getLocationBtn')) {
                        const locationBtn = document.getElementById('getLocationBtn');
                        locationBtn.innerHTML = 'Use My Location';
                        locationBtn.disabled = false;
                    }

                    if (data && data.address) {
                        const address = data.address;

                        // Extract relevant address components
                        const city = address.city || address.town || address.village || address.county || '';
                        const suburb = address.suburb || address.neighbourhood || address.borough || '';
                        const postcode = address.postcode || '';

                        // Display results in the combined location input
                        if (city && suburb && postcode) {
                            locationField.value = `${city}, ${suburb}, ${postcode}`;
                        } else if (city && suburb) {
                            locationField.value = `${city}, ${suburb}`;
                        } else if (city) {
                            locationField.value = city;
                        } else {
                            // If we can't get specific components, use the full display name
                            locationField.value = data.display_name || "Address found but details unavailable";
                        }

                        // Show a success message
                        showSuccessToast("Location detected successfully!");
                    } else if (data && data.display_name) {
                        // If we have display_name but no address structure
                        locationField.value = data.display_name;
                        showSuccessToast("Location detected!");
                    } else {
                        // Try fallback geocoding
                        useFallbackGeocoding(lat, lon);
                    }
                })
                .catch(error => {
                    console.error('Error fetching address:', error);

                    // Reset the location button if there was an error
                    if (document.getElementById('getLocationBtn')) {
                        const locationBtn = document.getElementById('getLocationBtn');
                        locationBtn.innerHTML = 'Use My Location';
                        locationBtn.disabled = false;
                    }

                    // Try fallback geocoding
                    useFallbackGeocoding(lat, lon);
                });
        }

        // Fallback geocoding using BigDataCloud
        function useFallbackGeocoding(lat, lon) {
            const locationField = document.getElementById('location');

            // Use BigDataCloud's client-side API (no key required)
            const url = `https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=en`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        // Extract relevant address components
                        const city = data.city || data.locality || '';
                        const district = data.principalSubdivision || '';
                        const postcode = data.postcode || '';

                        if (city && district && postcode) {
                            locationField.value = `${city}, ${district}, ${postcode}`;
                        } else if (city && district) {
                            locationField.value = `${city}, ${district}`;
                        } else if (city) {
                            locationField.value = city;
                        } else {
                            // If we still couldn't get a proper address, use coordinates
                            locationField.value = `Location at coordinates: ${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                        }

                        showSuccessToast("Location detected!");
                    } else {
                        // Last resort - just use the coordinates
                        locationField.value = `Location at coordinates: ${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                        showErrorModal("Could not retrieve detailed address information. Please enter your address manually.");
                    }
                })
                .catch(error => {
                    console.error('Error with fallback geocoding:', error);

                    // At this point both geocoding attempts failed
                    locationField.value = `Location at coordinates: ${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                    showErrorModal("There was a problem retrieving your address. Please try again or enter your address manually.");
                });
        }

        // Add password show/hide functionality
        function setupPasswordToggle() {
            const toggleButtons = document.querySelectorAll('.showHidePassword');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const passwordInput = this.previousElementSibling;

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.classList.remove('iconly-Hide');
                        this.classList.add('iconly-Show');
                    } else {
                        passwordInput.type = 'password';
                        this.classList.remove('iconly-Show');
                        this.classList.add('iconly-Hide');
                    }
                });
            });
        }

        // Load event handlers when document is ready
        document.addEventListener("DOMContentLoaded", function() {
            // Set up password toggle
            setupPasswordToggle();

            // Get Location button
            const locationBtn = document.getElementById('getLocationBtn');
            if (locationBtn) {
                locationBtn.addEventListener('click', function() {
                    if (navigator.geolocation) {
                        // Show a loading indicator while fetching location
                        const originalText = this.innerHTML;
                        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
                        this.disabled = true;

                        navigator.geolocation.getCurrentPosition(showPosition, showError, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        });
                    } else {
                        showErrorModal("Geolocation is not supported by this browser.");
                    }
                });
            }

            // Dashboard button in success modal
            const dashboardBtn = document.getElementById('goToDashboard');
            if (dashboardBtn) {
                dashboardBtn.addEventListener("click", function() {
                    window.location.href = "users/kitchen/homepage.php";
                });
            }

            // Close success modal
            const closeSuccessBtn = document.getElementById('closeSuccessModal');
            if (closeSuccessBtn) {
                closeSuccessBtn.addEventListener('click', function() {
                    document.getElementById('successModal').classList.remove('show');
                });
            }

            // Close error modal
            const closeErrorBtn = document.getElementById('closeErrorModal');
            if (closeErrorBtn) {
                closeErrorBtn.addEventListener('click', function() {
                    document.getElementById('errorModal').classList.remove('show');
                });
            }

            // Close modals when clicking outside
            window.addEventListener('click', function(event) {
                const successModal = document.getElementById('successModal');
                const errorModal = document.getElementById('errorModal');

                if (event.target === successModal) {
                    successModal.classList.remove('show');
                }

                if (event.target === errorModal) {
                    errorModal.classList.remove('show');
                }
            });

            // Form submission
            const form = document.getElementById('registration-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault(); // Prevent the default form submission

                    // Check if all required fields are filled
                    const requiredFields = form.querySelectorAll('[required]');
                    let allFieldsFilled = true;

                    requiredFields.forEach(field => {
                        if (field.value.trim() === '') {
                            allFieldsFilled = false;
                            field.classList.add('input-error'); // Highlight empty fields
                        } else {
                            field.classList.remove('input-error'); // Remove highlight if field is filled
                        }
                    });

                    if (!allFieldsFilled) {
                        showErrorModal("Please fill in all required fields before submitting.");
                        return; // Stop form submission if there are empty fields
                    }

                    // Show the loader
                    const loadingOverlay = document.getElementById('loading-overlay');
                    if (loadingOverlay) {
                        loadingOverlay.style.display = 'flex';
                    }

                    // Perform AJAX request
                    const formData = new FormData(form);

                    fetch('functions/registration.php', {
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
                            // Hide the loader
                            if (loadingOverlay) {
                                loadingOverlay.style.display = 'none';
                            }

                            if (data.success) {
                                showSuccessModal();
                            } else {
                                showErrorModal(data.message || 'Registration failed. Please try again.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);

                            // Hide the loader
                            if (loadingOverlay) {
                                loadingOverlay.style.display = 'none';
                            }

                            showErrorModal('An error occurred during registration. Please try again.');
                        });
                });
            }

            // Add event listeners to remove error styling when user starts typing
            const formInputs = document.querySelectorAll('input, textarea, select');
            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('input-error');
                });
            });
        });
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