<?php include 'includes/header.php';?>
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
                        <input type="email" name="email" placeholder="Email Address" required class="form-control"
                            autocomplete="off" />
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
                    <a href="kitchen_login.php" class="underline">Sign In</a>
                </span>
            </form>
            <!-- Registration Form End -->
        </section>
        <!-- Registration Section End -->
    </main>


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
    document.getElementById("goToDashboard").addEventListener("click", function() {
        window.location.href = "users/kitchen/homepage.php";
    });

    document.getElementById('getLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError);
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    });

    function showPosition(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;

        // Fill in hidden latitude and longitude fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lon;

        // Fetch human-readable address using Google Maps Geocoding API
        fetchAddressFromCoordinates(lat, lon);
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                alert("User denied the request for Geolocation.");
                break;
            case error.POSITION_UNAVAILABLE:
                alert("Location information is unavailable.");
                break;
            case error.TIMEOUT:
                alert("The request to get user location timed out.");
                break;
            case error.UNKNOWN_ERROR:
                alert("An unknown error occurred.");
                break;
        }
    }

    function fetchAddressFromCoordinates(lat, lon) {
        const apiKey = 'AIzaSyC9I3M93WS_XO1lElKa03kOGdzbMFwQGiM'; // Replace with your actual Google Maps API Key
        const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lon}&key=${apiKey}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status === "OK" && data.results[0]) {
                    const addressComponents = data.results[0].address_components;

                    let city = "";
                    let barangay = "";
                    let postalCode = "";

                    addressComponents.forEach(component => {
                        if (component.types.includes("locality")) {
                            city = component.long_name;
                        }
                        if (component.types.includes("sublocality") || component.types.includes(
                                "neighborhood") || component.types.includes(
                                "administrative_area_level_2")) {
                            barangay = component.long_name;
                        }
                        if (component.types.includes("postal_code")) {
                            postalCode = component.long_name;
                        }
                    });

                    // Display results in the combined location input
                    const locationField = document.getElementById('location');
                    if (city && barangay && postalCode) {
                        locationField.value = `${city}, ${barangay}, ${postalCode}`;
                    } else if (city && barangay) {
                        locationField.value = `${city}, ${barangay}`;
                    } else if (city) {
                        locationField.value = `${city}`;
                    } else {
                        alert("Could not retrieve complete address. Please try again.");
                    }
                } else {
                    alert("Could not retrieve address. Please try again.");
                }
            })
            .catch(error => console.error('Error fetching address:', error));
    }

    // Show the success modal
    function showSuccessModal() {
        const successModal = document.getElementById('successModal');
        successModal.classList.add('show'); // Add the 'show' class to make it visible
    }

    // Close the success modal
    document.getElementById('closeSuccessModal').onclick = function() {
        document.getElementById('successModal').classList.remove('show'); // Remove 'show' class to hide it
    };


    // Close the modal when clicking outside the modal content
    window.onclick = function(event) {
        if (event.target === document.getElementById('successModal')) {
            document.getElementById('successModal').classList.remove('show'); // Hide modal if clicked outside
        }
    };

    // Handle form submission and show modal on success
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('registration-form');

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
                    field.classList.remove(
                        'input-error'); // Remove highlight if field is filled
                }
            });

            if (!allFieldsFilled) {
                showErrorModal("Please fill in all required fields before submitting.");
                return; // Stop form submission if there are empty fields
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
    });


    // Show error modal with custom message
    function showErrorModal(message) {
        const errorModal = document.getElementById('errorModal');
        document.getElementById('errorMessage').innerText = message;
        errorModal.classList.add('show'); // Show error modal
    }

    // Close the error modal
    document.getElementById('closeErrorModal').onclick = function() {
        document.getElementById('errorModal').classList.remove('show');
    };
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