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
                    <input type="text" name="phone" placeholder="Phone Number" required class="form-control"
                        autocomplete="off" />
                    <i class="iconly-Call icli"></i>
                </div>

                <!-- Location Input Start -->
                <div class="input-box">
                    <input type="text" id="location" name="location" placeholder="City, Barangay, Postal Code" required
                        class="form-control" readonly />
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
                    <a href="login.html" class="underline">Sign In</a>
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
            <button class="btn-outline" onclick="closeModal('errorModal')">Close</button>
        </div>
    </div>
    <script>
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

    // Proceed to the assessment page
    document.getElementById('goToAssessment').onclick = function() {
        window.location.href = 'assessment.php';
    };

    // Close the modal when clicking outside the modal content
    window.onclick = function(event) {
        if (event.target === document.getElementById('successModal')) {
            document.getElementById('successModal').classList.remove('show'); // Hide modal if clicked outside
        }
    };

    // Handle form submission and show modal on success
    document.getElementById('submit-btn').addEventListener('click', function(e) {
        e.preventDefault();

        // Show the loader and handle AJAX
        document.getElementById('loading-spinner').style.visibility = 'visible';

        // Perform AJAX request
        const formData = new FormData(document.getElementById('registration-form'));

        fetch('functions/registration.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-spinner').style.visibility = 'hidden'; // Hide loader
                if (data.success) {
                    showSuccessModal(); // Show success modal
                } else {
                    showErrorModal(data.message); // Show error modal
                }
            })
            .catch(error => {
                document.getElementById('loading-spinner').style.visibility = 'hidden'; // Hide loader
                console.error('Error:', error);
                showErrorModal('An error occurred during registration. Please try again.');
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