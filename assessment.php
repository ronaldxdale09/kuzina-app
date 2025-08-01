<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/assessment.css">
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
            <!-- Progress Indicator -->
            <div class="form-progress">
                <div class="progress-step active" id="step-indicator-1">1</div>
                <div class="progress-step" id="step-indicator-2">2</div>
                <div class="progress-step" id="step-indicator-3">3</div>
            </div>

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
                        <div class="chip-group me-2">
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
                    <button type="button" class="btn-solid btn-next">Next</button>
                </div>
            </div>
            
            <!-- Step 3: Nutritional Targets -->
            <div id="step-3" class="form-step">
                <h2 class="font-md title-color fw-600 mb-4">Set Your Nutritional Targets</h2>
                
                <div class="nutrition-targets">                    
                    <div class="input-box">
                        <div class="nutrient-input">
                            <div class="nutrient-icon calorie-icon">🔥</div>
                            <label for="daily_calories">Daily Calories</label>
                            <div class="input-with-unit">
                                <input type="number" name="daily_calories" id="daily_calories" placeholder="2000" value="2000" class="form-control" />
                                <span class="unit">kcal</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-box">
                        <div class="nutrient-input">
                            <div class="nutrient-icon protein-icon">🍗</div>
                            <label for="daily_protein">Daily Protein</label>
                            <div class="input-with-unit">
                                <input type="number" name="daily_protein" id="daily_protein" placeholder="50" value="50" class="form-control" />
                                <span class="unit">g</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-box">
                        <div class="nutrient-input">
                            <div class="nutrient-icon carbs-icon">🍚</div>
                            <label for="daily_carbs">Daily Carbs</label>
                            <div class="input-with-unit">
                                <input type="number" name="daily_carbs" id="daily_carbs" placeholder="300" value="300" class="form-control" />
                                <span class="unit">g</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-box">
                        <div class="nutrient-input">
                            <div class="nutrient-icon fat-icon">🥑</div>
                            <label for="daily_fat">Daily Fat</label>
                            <div class="input-with-unit">
                                <input type="number" name="daily_fat" id="daily_fat" placeholder="65" value="65" class="form-control" />
                                <span class="unit">g</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-navigation">
                    <button type="button" class="btn-outline btn-prev">Previous</button>
                    <button type="submit" class="btn-solid" id="submit-assessment">Submit</button>
                </div>
            </div>
        </form>
    </main>

    <!-- Loading Overlay -->
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

            // Default values for different diet types and goals
            const nutritionProfiles = {
                'Vegetarian': {
                    'Weight Loss': { calories: 1800, protein: 60, carbs: 225, fat: 60 },
                    'Muscle Gain': { calories: 2200, protein: 100, carbs: 275, fat: 70 },
                    'Improve Energy': { calories: 2000, protein: 70, carbs: 300, fat: 55 },
                    'Better Digestion': { calories: 1900, protein: 65, carbs: 250, fat: 60 }
                },
                'Seafood-based': {
                    'Weight Loss': { calories: 1700, protein: 75, carbs: 170, fat: 65 },
                    'Muscle Gain': { calories: 2300, protein: 120, carbs: 225, fat: 75 },
                    'Improve Energy': { calories: 2100, protein: 90, carbs: 220, fat: 70 },
                    'Better Digestion': { calories: 1950, protein: 85, carbs: 200, fat: 65 }
                },
                'Low-Carb': {
                    'Weight Loss': { calories: 1600, protein: 80, carbs: 100, fat: 95 },
                    'Muscle Gain': { calories: 2200, protein: 130, carbs: 150, fat: 110 },
                    'Improve Energy': { calories: 2000, protein: 100, carbs: 130, fat: 105 },
                    'Better Digestion': { calories: 1800, protein: 90, carbs: 120, fat: 100 }
                },
                'Balanced Diet': {
                    'Weight Loss': { calories: 1800, protein: 70, carbs: 200, fat: 60 },
                    'Muscle Gain': { calories: 2400, protein: 110, carbs: 270, fat: 75 },
                    'Improve Energy': { calories: 2100, protein: 80, carbs: 250, fat: 65 },
                    'Better Digestion': { calories: 2000, protein: 75, carbs: 230, fat: 65 }
                }
            };

            // Form navigation
            const nextBtns = document.querySelectorAll('.btn-next');
            const prevBtns = document.querySelectorAll('.btn-prev');
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
                
                // Update progress indicators
                document.querySelectorAll('.progress-step').forEach((indicator, index) => {
                    if (index < stepIndex) {
                        indicator.classList.remove('active');
                        indicator.classList.add('completed');
                        indicator.innerHTML = '✓';
                    } else if (index === stepIndex) {
                        indicator.classList.add('active');
                        indicator.classList.remove('completed');
                        indicator.innerHTML = index + 1;
                    } else {
                        indicator.classList.remove('active', 'completed');
                        indicator.innerHTML = index + 1;
                    }
                });
                
                // Update nutrition values when navigating to step 3
                if (stepIndex === 2) {
                    updateNutritionValues();
                }
            }

            showStep(currentStep);

            nextBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    // Validate fields before proceeding
                    if (currentStep === 0) {
                        const age = document.getElementById('age').value;
                        const gender = document.getElementById('gender').value;
                        const height = document.getElementById('height').value;
                        const weight = document.getElementById('weight').value;

                        if (!age || !gender || !height || !weight) {
                            showErrorModal('Please fill in all required fields before proceeding.');
                            return;
                        }
                    } else if (currentStep === 1) {
                        const dietType = document.getElementById('diet_type_input').value;
                        const healthGoal = document.getElementById('health_goal_input').value;

                        if (!dietType || !healthGoal) {
                            showErrorModal('Please select both a diet type and a health goal before proceeding.');
                            return;
                        }
                    }

                    if (currentStep < steps.length - 1) {
                        currentStep++;
                        showStep(currentStep);
                    }
                });
            });

            prevBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    if (currentStep > 0) {
                        currentStep--;
                        showStep(currentStep);
                    }
                });
            });

            // Function to update nutritional values
            function updateNutritionValues() {
                const dietType = document.getElementById('diet_type_input').value;
                const healthGoal = document.getElementById('health_goal_input').value;
                
                if (dietType && healthGoal && nutritionProfiles[dietType] && nutritionProfiles[dietType][healthGoal]) {
                    const profile = nutritionProfiles[dietType][healthGoal];
                    
                    document.getElementById('daily_calories').value = profile.calories;
                    document.getElementById('daily_protein').value = profile.protein;
                    document.getElementById('daily_carbs').value = profile.carbs;
                    document.getElementById('daily_fat').value = profile.fat;
                }
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
                // Final validation check before submission
                const dietType = document.getElementById('diet_type_input').value;
                const healthGoal = document.getElementById('health_goal_input').value;
                const calories = document.getElementById('daily_calories').value;
                const protein = document.getElementById('daily_protein').value;
                const carbs = document.getElementById('daily_carbs').value;
                const fat = document.getElementById('daily_fat').value;

                if (!dietType || !healthGoal || !calories || !protein || !carbs || !fat) {
                    e.preventDefault();
                    showErrorModal('Please complete all fields before submitting.');
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