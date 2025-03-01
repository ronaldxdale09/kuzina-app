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

    <!-- Main Start -->
    <main class="main-wrap login-page mb-xxl">
        <div class="center-content">
            <img class="logo" src="assets/images/logo/logo-w2.png" alt="logo" />
            <img class="logo logo-w" src="assets/images/logo/logo-w2.png" alt="logo" />
            <p class="font-sm content-color">
                Please create an account or sign in to your existing account to start browsing our selection of
                delicious meals from your kitchen mothers.
            </p>
        </div>

        <!-- Login Section Start -->
        <section class="login-section p-0">
            <!-- Registration Form Start -->
            <form id="registration-form" method="POST" class="custom-form">
                <h1 class="font-md title-color fw-600">Register Account</h1>
                <input type="text" name="type" value="customer" hidden />

                <!-- Full Name Input Start -->
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
                    <input type="email" name="email" placeholder="Email Address" required class="form-control"
                        autocomplete="off" />
                    <i data-feather="at-sign"></i>
                </div>

                <!-- Phone Number Input Start -->
                <div class="input-box">
                    <input type="number" name="phone" placeholder="Phone Number" required class="form-control"
                        autocomplete="off" />
                    <i class="iconly-Call icli"></i>
                </div>

                <!-- Location Input Start -->
                <div class="input-box">
                    <input type="text" id="location" name="location" placeholder="City, Barangay, Postal Code" required
                        class="form-control" />
                    <button type="button" class="btn btn-location" id="getLocationBtn"> Use My Location
                    </button>

                </div>
                <!-- Location Input End -->

                <!-- Hidden Latitude and Longitude Inputs -->
                <input type="hidden" id="latitude" name="latitude" />
                <input type="hidden" id="longitude" name="longitude" />

                <!-- Password Input Start -->
                <div class="input-box">
                    <input type="password" name="password" placeholder="Password" required class="form-control" />
                    <i class="iconly-Hide icli showHidePassword"></i>
                </div>

                <button id="submit-btn" class="btn-solid" type="submit">Register</button>
                <span class="content-color font-sm d-block text-center fw-600">Already have an Account?
                    <a href="index.php" class="underline">Sign In</a>
                </span>
            </form>
            <!-- Registration Form End -->
        </section>
        <!-- Login Section End -->
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
            <h2>Registration Successful!</h2>
            <p>Let's proceed with a quick nutritional assessment for better food recommendations.</p>
            <button id="goToAssessment" class="btn-solid">Proceed to Assessment</button>
        </div>
    </div>

    <!-- Error Modal -->
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span id="closeErrorModal" class="close">&times;</span>
            <h2>Error</h2>
            <p id="errorMessage"></p>
            <button class="btn-outline  close-modal">Close</button>
        </div>
    </div>
    <script>
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                // Show a loading indicator while fetching location
                const locationBtn = document.getElementById('getLocationBtn');
                const originalText = locationBtn.innerHTML;
                locationBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Getting location...';
                locationBtn.disabled = true;

                navigator.geolocation.getCurrentPosition(showPosition, showError, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                });

                function resetButton() {
                    locationBtn.innerHTML = originalText;
                    locationBtn.disabled = false;
                }
            } else {
                showErrorModal("Geolocation is not supported by this browser.");
            }
        });

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
                locationBtn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Get My Location';
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
                        locationBtn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Get My Location';
                        locationBtn.disabled = false;
                    }

                    if (data && data.address) {
                        const address = data.address;

                        // Extract relevant address components
                        const city = address.city || address.town || address.village || address.county || '';
                        const suburb = address.suburb || address.neighbourhood || address.borough || '';
                        const postcode = address.postcode || '';

                        // Display results in the combined location input
                        const locationField = document.getElementById('location');
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
                    } else {
                        // If the address data is missing, show a manual input form
                        showErrorModal("Could not retrieve detailed address information. Please enter your address manually.");
                        // Set coordinates-only so we at least have that data
                        const locationField = document.getElementById('location');
                        locationField.value = `Location at coordinates: ${lat.toFixed(6)}, ${lon.toFixed(6)}`;
                    }
                })
                .catch(error => {
                    console.error('Error fetching address:', error);

                    // Reset the location button if there was an error
                    if (document.getElementById('getLocationBtn')) {
                        const locationBtn = document.getElementById('getLocationBtn');
                        locationBtn.innerHTML = '<i class="fas fa-map-marker-alt"></i> Get My Location';
                        locationBtn.disabled = false;
                    }

                    showErrorModal("There was a problem retrieving your address. Please try again or enter your address manually.");
                });
        }

        // Show the success modal
        function showSuccessModal() {
            const successModal = document.getElementById('successModal');
            successModal.classList.add('show');
        }

        // Proceed to the assessment page
        document.getElementById('goToAssessment').onclick = function() {
            window.location.href = 'assessment.php';
        };

        // Close the modal when clicking outside the modal content
        window.onclick = function(event) {
            if (event.target === document.getElementById('successModal')) {
                document.getElementById('successModal').classList.remove('show');
            }
            if (event.target === document.getElementById('errorModal')) {
                document.getElementById('errorModal').classList.remove('show');
            }
        };

        document.getElementById('submit-btn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default form submission

            // Check if all required fields are filled
            const form = document.getElementById('registration-form');
            const requiredFields = form.querySelectorAll('[required]');
            let allFieldsFilled = true;

            requiredFields.forEach(field => {
                if (field.value.trim() === '') {
                    allFieldsFilled = false;
                    field.classList.add('input-error');
                } else {
                    field.classList.remove('input-error');
                }
            });

            if (!allFieldsFilled) {
                showErrorModal("Please fill in all required fields before submitting.");
                return;
            }

            // Show the loader and handle AJAX if all fields are filled
            document.getElementById('loading-spinner').style.visibility = 'visible';

            // Perform AJAX request
            const formData = new FormData(form);

            fetch('functions/registration.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading-spinner').style.visibility = 'hidden';
                    if (data.success) {
                        showSuccessModal();
                    } else {
                        showErrorModal(data.message || 'Registration failed. Please try again.');
                    }
                })
                .catch(error => {
                    document.getElementById('loading-spinner').style.visibility = 'hidden';
                    console.error('Error:', error);
                    showErrorModal('An error occurred during registration. Please try again.');
                });
        });

        // Show error modal with custom message
        function showErrorModal(message) {
            const errorModal = document.getElementById('errorModal');
            document.getElementById('errorMessage').innerText = message;
            errorModal.classList.add('show');
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

        // Close the error modal when clicking the close icon or button
        document.getElementById('closeErrorModal').onclick = function() {
            document.getElementById('errorModal').classList.remove('show');
        };

        document.querySelectorAll('.close-modal').forEach(button => {
            button.onclick = function() {
                this.closest('.custom-modal').classList.remove('show');
            };
        });

        // Add event listeners to remove error styling when user starts typing
        document.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('input-error');
                }
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