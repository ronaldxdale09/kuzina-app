<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/assessment.css">
<style>
    #errorModal .modal-content {
        position: relative;
        text-align: center;
    }

    #errorModal .close {
        position: absolute;
        top: 10px;
        right: 10px;
    }

    #errorModal button.btn-outline {
        margin: 15px auto 5px;
        display: block;
    }
</style>
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

        <form id="nutritional-assessment-form" method="POST" class="custom-form">

            <!-- Step 1: Basic Information -->
            <div id="step-1" class="form-step active">
                <h1 class="font-md title-color fw-600 text-center">Nutritional Assessment</h1>
                <p class="font-sm content-color">
                    Welcome! <?php echo  $_COOKIE['user_fname'] ?>
                </p>
                <!-- Age Input -->
                <div class="input-box">
                    <label for="age">1. What is your age?</label>
                    <input type="number" name="age" id="age" placeholder="Enter your age" required
                        class="form-control" />
                </div>

                <!-- Gender Select -->
                <div class="input-box">
                    <label for="gender">2. What is your gender?</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="" disabled selected>Select an option</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Height Input -->
                <div class="input-box">
                    <label for="height">3. What is your height?</label>
                    <div class="d-flex align-items-center">
                        <input type="number" name="height" id="height" placeholder="Enter height" required
                            class="form-control me-2" />
                        <select id="height-unit" name="height-unit" class="form-control w-auto">
                            <option value="cm">cm</option>
                            <option value="inches">inches</option>
                        </select>
                    </div>
                </div>

                <!-- Weight Input -->
                <div class="input-box">
                    <label for="weight">4. What is your current weight?</label>
                    <div class="d-flex align-items-center">
                        <input type="number" name="weight" id="weight" placeholder="Enter weight" required
                            class="form-control me-2" />
                        <select id="weight-unit" name="weight-unit" class="form-control w-auto">
                            <option value="kg">kg</option>
                            <option value="lbs">lbs</option>
                        </select>
                    </div>
                </div>

                <!-- Food Allergies Checkbox -->
                <div class="input-box">
                    <label>5. Do you have any food allergies or intolerances?</label>
                    <div class="form-group">
                        <div class="chip-group  me-2 ">
                            <div class="chip">
                                <input type="checkbox" id="dairy" name="allergens[]" value="Dairy" hidden />
                                <label for="dairy">Dairy</label>
                            </div>
                            <div class="chip">
                                <input type="checkbox" id="gluten" name="allergens[]" value="Gluten" hidden />
                                <label for="gluten">Gluten</label>
                            </div>
                            <div class="chip">
                                <input type="checkbox" id="nuts" name="allergens[]" value="Nuts" hidden />
                                <label for="nuts"> Nuts</label>
                            </div>
                            <div class="chip">
                                <input type="checkbox" id="none" name="allergens[]" value="None" hidden />
                                <label for="none"> None</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Button -->
                <button type="button" class="btn-solid btn-next">Next</button>
            </div>

            
            <!-- Step 2: Diet Type and Health Goals -->
            <div id="step-2" class="form-step">

                <!-- Diet Type Selection -->
                <label class="title-label">Select Your Diet Type:</label>
                <input type="hidden" id="diet_type_input" name="diet_type" />
                <div class="diet-type chip-group">
                    <div class="chip diet-chip" data-value="Vegetarian">
                        <img src="assets/images/assessment/vegetarian2.png" alt="Vegetarian" />
                        <span>Vegetarian</span>
                    </div>
                    <div class="chip diet-chip" data-value="Seafood-based">
                        <img src="assets/images/assessment/seafood.png" alt="Seafood-based" />
                        <span>Seafood-based</span>
                    </div>
                    <div class="chip diet-chip" data-value="Low-Carb">
                        <img src="assets/images/assessment/lowcarb.png" alt="Low-Carb" />
                        <span>Low-Carb</span>
                    </div>
                    <div class="chip diet-chip" data-value="Balanced Diet">
                        <img src="assets/images/assessment/balance.png" alt="Balanced Diet" />
                        <span>Balanced Diet</span>
                    </div>
                </div>

                <!-- Health Goals Selection -->
                <label class="title-label">Select Your Health Goals:</label>

                <input type="hidden" id="health_goal_input" name="health_goal" />
                <div class="health-goals chip-group">
                    <div class="chip goal-chip" data-value="Weight Loss">
                        <img src="assets/images/assessment/w_loss.png" alt="Weight Loss" />
                        <span>Weight Loss</span>
                    </div>
                    <div class="chip goal-chip" data-value="Muscle Gain">
                        <img src="assets/images/assessment/m_gain.png" alt="Muscle Gain" />
                        <span>Muscle Gain</span>
                    </div>
                    <div class="chip goal-chip" data-value="Improve Energy">
                        <img src="assets/images/assessment/energy.png" alt="Improve Energy" />
                        <span>Improve Energy</span>
                    </div>
                    <div class="chip goal-chip" data-value="Better Digestion">
                        <img src="assets/images/assessment/b_digestion.png" alt="Better Digestion" />
                        <span>Better Digestion</span>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-outline btn-prev">Previous</button>
                    <button type="submit" class="btn-solid" id="submit-assessment">Submit</button>
                </div>
            </div>

        </form>
    </main>
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
            <h2>You're All Set!</h2>
            <p>Your nutritional assessment has been recorded, and we're ready to serve you the healthiest meal options!</p>
            <button id="proceedBtn" class="btn-solid">Proceed to Assessment</button>
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
        // Show the success modal
        function showSuccessModal() {
            const successModal = document.getElementById('successModal');
            successModal.classList.add('show'); // Add the 'show' class to make it visible
        }

        // Proceed to the assessment page
        document.getElementById('proceedBtn').onclick = function() {
            window.location.href = 'users/customer/homepage.php';
        };

        // Close the modal when clicking outside the modal content
        window.onclick = function(event) {
            if (event.target === document.getElementById('successModal')) {
                document.getElementById('successModal').classList.remove('show'); // Hide modal if clicked outside
            }
        };

        // Handle form submission and show modal on success
        document.getElementById('nutritional-assessment-form').addEventListener('submit', function(e) {
            e.preventDefault();

            // Show the loader
            document.getElementById('loading-overlay').style.display = 'flex';

            // Perform AJAX request
            const formData = new FormData(document.getElementById('nutritional-assessment-form'));

            fetch('functions/assessment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading-overlay').style.display = 'none'; // Hide loader
                    if (data.success) {
                        showSuccessModal(); // Show success modal
                    } else {
                        showErrorModal(data.message); // Show error modal
                    }
                })
                .catch(error => {
                    document.getElementById('loading-overlay').style.display = 'none'; // Hide loader
                    console.error('Error:', error);
                    showErrorModal('An error occurred during submission. Please try again.');
                });
        });

        // Show error modal with custom message
        function showErrorModal(message) {
            const errorModal = document.getElementById('errorModal');
            document.getElementById('errorMessage').innerText = message;
            errorModal.classList.add('show'); // Show error modal
        }

        // Close the error modal
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }

        document.getElementById('closeErrorModal').onclick = function() {
            closeModal('errorModal');
        };

        // Chip selection logic and form navigation
        document.addEventListener('DOMContentLoaded', () => {
            const chips = document.querySelectorAll('.chip label');
            const noneChip = document.getElementById('none');
            const allergenChips = Array.from(document.querySelectorAll('input[name="allergens[]"]')).filter(chip => chip.id !== 'none');

            // Initialize with None checked if no allergies are selected
            if (!allergenChips.some(chip => chip.checked)) {
                noneChip.checked = true;
                noneChip.parentElement.classList.add('active');
            }

            chips.forEach(chip => {
                chip.addEventListener('click', function() {
                    const input = document.querySelector(`#${this.htmlFor}`);

                    // Handle the "None" chip case
                    if (input.id === 'none') {
                        // If clicking on None, uncheck all other options and select None
                        if (!input.checked) {
                            allergenChips.forEach(allergen => {
                                allergen.checked = false;
                                allergen.parentElement.classList.remove('active');
                            });
                            input.checked = true;
                            input.parentElement.classList.add('active');
                        }
                    } else {
                        // If clicking on any other option
                        if (!input.checked) {
                            // If selecting a non-None option, uncheck None
                            noneChip.checked = false;
                            noneChip.parentElement.classList.remove('active');

                            // Check the clicked option
                            input.checked = true;
                            input.parentElement.classList.add('active');
                        } else {
                            // If unchecking a non-None option
                            input.checked = false;
                            input.parentElement.classList.remove('active');

                            // If no allergens are selected, automatically select None
                            const anyAllergenSelected = allergenChips.some(allergen => allergen.checked);
                            if (!anyAllergenSelected) {
                                noneChip.checked = true;
                                noneChip.parentElement.classList.add('active');
                            }
                        }
                    }
                });
            });

            // Form navigation
            const nextBtn = document.querySelector('.btn-next');
            const prevBtn = document.querySelector('.btn-prev');
            const steps = document.querySelectorAll('.form-step');
            let currentStep = 0;

            function showStep(stepIndex) {
                steps.forEach((step, index) => {
                    if (index === stepIndex) {
                        step.style.display = 'block';
                        step.classList.add('active');
                    } else {
                        step.style.display = 'none';
                        step.classList.remove('active');
                    }
                });
            }

            showStep(currentStep);

            nextBtn.addEventListener('click', () => {
                // Validate fields in step 1 before proceeding
                if (currentStep === 0) {
                    const age = document.getElementById('age').value;
                    const gender = document.getElementById('gender').value;
                    const height = document.getElementById('height').value;
                    const weight = document.getElementById('weight').value;

                    if (!age || !gender || !height || !weight) {
                        showErrorModal('Please fill in all required fields before proceeding.');
                        return;
                    }
                }

                if (currentStep < steps.length - 1) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            if (prevBtn) {
                prevBtn.addEventListener('click', () => {
                    if (currentStep > 0) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });
            }

            // Diet type selection
            document.querySelectorAll('.diet-chip').forEach(chip => {
                chip.addEventListener('click', function() {
                    document.querySelectorAll('.diet-chip').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('diet_type_input').value = this.getAttribute('data-value');
                });
            });

            // Health goals selection
            document.querySelectorAll('.goal-chip').forEach(chip => {
                chip.addEventListener('click', function() {
                    document.querySelectorAll('.goal-chip').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    document.getElementById('health_goal_input').value = this.getAttribute('data-value');
                });
            });

            // Add validation before form submission
            document.getElementById('submit-assessment').addEventListener('click', function(e) {
                const dietType = document.getElementById('diet_type_input').value;
                const healthGoal = document.getElementById('health_goal_input').value;

                if (!dietType || !healthGoal) {
                    e.preventDefault();
                    showErrorModal('Please select both a diet type and a health goal before submitting.');
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