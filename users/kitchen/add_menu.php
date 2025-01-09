<?php
include 'includes/header.php';

// Check if we're in edit mode
$edit_mode = false;
$food_item = null;

if (isset($_GET['food_id'])) {
    $food_id = $_GET['food_id'];
    $edit_mode = true;
    
    // Fetch food item details
    $query = "SELECT * FROM food_listings WHERE food_id = ? AND kitchen_id = ?";
    $stmt = $conn->prepare($query);
    $kitchen_id = $_COOKIE['kitchen_id'];
    $stmt->bind_param("ii", $food_id, $kitchen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $food_item = $result->fetch_assoc();

    // If no item found or doesn't belong to current kitchen, redirect
    if (!$food_item) {
        header('Location: menu_list.php');
        exit();
    }
}

// Add this at the top of your file after fetching the food item
if ($edit_mode) {
    echo "<script>console.log('Diet Types:', " . json_encode($food_item['diet_type_suitable']) . ");</script>";
    echo "<script>console.log('Health Goals:', " . json_encode($food_item['health_goal_suitable']) . ");</script>";
    echo "<script>console.log('Allergens:', " . json_encode($food_item['allergens']) . ");</script>";
}
?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/add_menu.css" />

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_add_menu.php'; ?>
    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/shop.navbar.php'; ?>
    <!-- Header End -->

    <!-- Main Start -->
    <main class="main-wrap product-page mb-xxl">
        <!-- Page Header -->
        <div class="page-header pt-4">
            <div class="header-content" style="display: flex; align-items: center; gap: 20px;">
                <div class="icon-wrapper"
                    style="background-color: #4C0710; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="bx <?php echo $edit_mode ? 'bx-edit' : 'bx-plus'; ?>"
                        style="color: #ffffff; font-size: 20px;"></i>
                </div>
                <h1 style="font-size: 28px; color: #4e0707; font-weight: bold;">
                    <?php echo $edit_mode ? 'Edit Item' : 'Add New Items'; ?>
                </h1>
            </div>
        </div>

        <!-- Add/Edit Item Form -->
        <form id="addItemForm"
            action="functions/<?php echo $edit_mode ? 'process_edit_menu.php' : 'process_add_menu.php'; ?>"
            enctype="multipart/form-data" method="POST">
            <?php if ($edit_mode): ?>
            <input type="hidden" name="food_id" value="<?php echo $food_item['food_id']; ?>">
            <?php endif; ?>

            <!-- Item Name -->
            <div class="form-group">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" name="itemName" placeholder="Enter item name"
                    value="<?php echo $edit_mode ? htmlspecialchars($food_item['food_name']) : ''; ?>" required />
            </div>

            <!-- Meal Type -->
            <!-- Meal Type -->
            <div class="form-group">
                <label>Meal Type</label>
                <div class="chip-group">
                    <?php
        $meal_types = array(
            'Breakfast' => 'bx-coffee',
            'Lunch' => 'bx-food-menu',
            'Snacks' => 'bx-cookie',
            'Dinner' => 'bx-wine',
            'All' => 'bx-calendar'
        );

        foreach ($meal_types as $type => $icon):
            $is_checked = $edit_mode && isset($food_item['meal_type']) && trim($food_item['meal_type']) === $type;
        ?>
                    <div class="chip <?php echo $is_checked ? 'active' : ''; ?>">
                        <input type="radio" id="<?php echo strtolower($type); ?>" name="mealType"
                            value="<?php echo $type; ?>" <?php echo $is_checked ? 'checked' : ''; ?> hidden />
                        <label for="<?php echo strtolower($type); ?>">
                            <i class="bx <?php echo $icon; ?>"></i> <?php echo $type; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>



            <!-- Upload Photo -->
            <div class="form-group">
                <label for="uploadPhoto">Upload Photo</label>
                <div class="upload-photo pt-1">
                    <?php for($i = 1; $i <= 3; $i++): ?>
                    <div class="photo-placeholder" data-placeholder-index="<?php echo $i; ?>"
                        id="photo-placeholder-<?php echo $i; ?>">
                        <input type="file" id="photo<?php echo $i; ?>" name="photo<?php echo $i; ?>" accept="image/*"
                            hidden />
                        <label for="photo<?php echo $i; ?>" class="photo-label">
                            <?php if ($edit_mode && !empty($food_item["photo$i"])): ?>
                            <img src="../../uploads/<?php echo htmlspecialchars($food_item["photo$i"]); ?>"
                                alt="Photo <?php echo $i; ?>" style="width: 100%; height: 100%; object-fit: cover;" />
                            <?php else: ?>
                            <i class="bx bx-camera"></i>
                            <?php endif; ?>
                        </label>
                        <div class="image-preview" id="preview<?php echo $i; ?>"></div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- Price -->
            <div class="form-group price-input">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" placeholder="PRICE"
                    value="<?php echo $edit_mode ? $food_item['price'] : ''; ?>" required />
                <div class="pickup-delivery">
                    <div class="option <?php echo $edit_mode && $food_item['category'] == 'Pick Up' ? 'active' : ''; ?>"
                        onclick="toggleOption(this)">
                        <input type="radio" id="pickup" name="pickupDelivery" value="Pick Up"
                            <?php echo $edit_mode && $food_item['category'] == 'Pick Up' ? 'checked' : ''; ?> hidden />
                        <label for="pickup"><i class="bx bx-shopping-bag"></i> Pick Up</label>
                    </div>
                    <div class="option <?php echo $edit_mode && $food_item['category'] == 'Delivery' ? 'active' : ''; ?>"
                        onclick="toggleOption(this)">
                        <input type="radio" id="delivery" name="pickupDelivery" value="Delivery"
                            <?php echo $edit_mode && $food_item['category'] == 'Delivery' ? 'checked' : ''; ?> hidden />
                        <label for="delivery"><i class="bx bx-truck"></i> Delivery</label>
                    </div>
                </div>
            </div>


            <!-- Type of Diet -->
            <div class="form-group">
                <label>Type of Diet</label>
                <div class="chip-group">
                    <?php
        $diet_types = array(
            'Vegetarian' => 'bx-leaf',
            'Vegan' => 'bx-leaf',
            'Keto' => 'bx-dumbbell',
            'Balanced Diet' => 'bx-heart',
            'All' => 'bx-list-ul'
        );

        $current_diets = [];
        if ($edit_mode && isset($food_item['diet_type_suitable']) && !empty($food_item['diet_type_suitable'])) {
            $current_diets = array_map('trim', explode(',', $food_item['diet_type_suitable']));
        }

        foreach ($diet_types as $type => $icon):
            $is_checked = in_array($type, $current_diets);
        ?>
                    <div class="chip <?php echo $is_checked ? 'active' : ''; ?>">
                        <input type="checkbox" id="diet_<?php echo strtolower(str_replace(' ', '', $type)); ?>"
                            name="dietType[]" value="<?php echo $type; ?>" <?php echo $is_checked ? 'checked' : ''; ?>
                            hidden />
                        <label for="diet_<?php echo strtolower(str_replace(' ', '', $type)); ?>">
                            <i class="bx <?php echo $icon; ?>"></i> <?php echo $type; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>


            <div class="form-group">
                <label>Health Goal Suitable</label>
                <div class="chip-group">
                    <?php
        $health_goals = array(
            'Weight Loss' => 'bx-trending-down',
            'Muscle Gain' => 'bx-trending-up',
            'Improve Energy Levels' => 'bx-battery',
            'Better Digestion' => 'bx-happy',
            'All' => 'bx-list-ul'
        );

        $current_goals = [];
        if ($edit_mode && isset($food_item['health_goal_suitable']) && !empty($food_item['health_goal_suitable'])) {
            $current_goals = array_map('trim', explode(',', $food_item['health_goal_suitable']));
        }

        foreach ($health_goals as $goal => $icon):
            $is_checked = in_array($goal, $current_goals);
        ?>
                    <div class="chip <?php echo $is_checked ? 'active' : ''; ?>">
                        <input type="checkbox" id="goal_<?php echo strtolower(str_replace(' ', '', $goal)); ?>"
                            name="healthGoal[]" value="<?php echo $goal; ?>" <?php echo $is_checked ? 'checked' : ''; ?>
                            hidden />
                        <label for="goal_<?php echo strtolower(str_replace(' ', '', $goal)); ?>">
                            <i class="bx <?php echo $icon; ?>"></i> <?php echo $goal; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Allergens -->
            <div class="form-group">
                <label>Allergens</label>
                <div class="chip-group">
                    <?php
        $allergens = array(
            'Dairy' => 'bx-error',
            'Gluten' => 'bx-error',
            'Nuts' => 'bx-error',
            'None' => 'bx-check-circle'
        );

        $current_allergens = [];
        if ($edit_mode && isset($food_item['allergens']) && !empty($food_item['allergens'])) {
            $current_allergens = array_map('trim', explode(',', $food_item['allergens']));
        }

        foreach ($allergens as $allergen => $icon):
            $is_checked = in_array($allergen, $current_allergens);
        ?>
                    <div class="chip <?php echo $is_checked ? 'active' : ''; ?>">
                        <input type="checkbox" id="allergen_<?php echo strtolower($allergen); ?>" name="allergens[]"
                            value="<?php echo $allergen; ?>" <?php echo $is_checked ? 'checked' : ''; ?> hidden />
                        <label for="allergen_<?php echo strtolower($allergen); ?>">
                            <i class="bx <?php echo $icon; ?>"></i> <?php echo $allergen; ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <!-- Details -->
            <div class="details-section">
                <label for="description">Details</label>
                <textarea id="description" name="description" placeholder="About the meal" rows="4" required><?php 
                    echo $edit_mode ? htmlspecialchars($food_item['description']) : ''; 
                ?></textarea>
            </div>

            <!-- Nutritional Info -->
            <div class="nutrition-section">
                <label class="nutrition-label">Nutritional Info</label>
                <div class="nutrition-inputs">
                    <div class="nutrition-item">
                        <label for="protein">Protein (g)</label>
                        <input type="number" id="protein" name="protein" placeholder="Protein"
                            value="<?php echo $edit_mode ? $food_item['protein'] : ''; ?>" required />
                    </div>
                    <div class="nutrition-item">
                        <label for="carbs">Carbs (g)</label>
                        <input type="number" id="carbs" name="carbs" placeholder="Carbs"
                            value="<?php echo $edit_mode ? $food_item['carbs'] : ''; ?>" required />
                    </div>
                    <div class="nutrition-item">
                        <label for="fat">Fat (g)</label>
                        <input type="number" id="fat" name="fat" placeholder="Fat"
                            value="<?php echo $edit_mode ? $food_item['fat'] : ''; ?>" required />
                    </div>
                </div>
                <div class="nutrition-item">
                    <label for="totalCalories">Total Calories</label>
                    <input type="number" id="totalCalories" name="totalCalories" placeholder="Total Calories"
                        value="<?php echo $edit_mode ? $food_item['calories'] : ''; ?>" required />
                </div>
            </div>

            <!-- Save Button -->
            <div class="form-group">
                <button type="submit" class="save-btn" id="save-btn">
                    <span id="spinner" class="spinner-border spinner-border-sm" role="status"
                        style="display: none;"></span>
                    <?php echo $edit_mode ? 'UPDATE ITEM' : 'SAVE CHANGES'; ?>
                </button>
            </div>
        </form>
    </main>

    <!-- Success Popup -->
    <div id="custom-popup" class="popup">
        <div class="popup-content">
            <h2>Success!</h2>
            <p>Your item has been <?php echo $edit_mode ? 'updated' : 'added'; ?> successfully.</p>
            <a href="menu_list.php" id="confirm-btn" class="btn">OK</a>
        </div>
    </div>

    <!-- Footer Start -->
    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->

    <?php include 'includes/scripts.php'; ?>
    <script src="custom_scripts/add_item.js"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preview images before upload
        function previewImage(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.style.backgroundImage = `url(${e.target.result})`;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        }

        // Add event listeners for file inputs
        for (let i = 1; i <= 3; i++) {
            const input = document.getElementById(`photo${i}`);
            input.addEventListener('change', function() {
                const label = this.nextElementSibling;
                const preview = document.getElementById(`preview${i}`);

                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        // Clear existing content
                        label.innerHTML = '';
                        // Create and add image
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        label.appendChild(img);
                    };
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }

        // Toggle option for pickup/delivery
        window.toggleOption = function(element) {
            const options = document.querySelectorAll('.option');
            options.forEach(opt => opt.classList.remove('active'));
            element.classList.add('active');

            const radio = element.querySelector('input[type="radio"]');
            radio.checked = true;
        };

        // Form submission handling
        const form = document.getElementById('addItemForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading spinner
            document.getElementById('spinner').style.display = 'inline-block';
            document.getElementById('save-btn').disabled = true;

            // Submit form
            this.submit();
        });

        // Chip selection handling
        document.querySelectorAll('.chip').forEach(chip => {
            if (!chip) return;

            chip.addEventListener('click', function() {
                // Find input element (could be checkbox or radio)
                const input = this.querySelector('input');
                if (!input) return; // Skip if no input found

                // Handle checkbox case
                if (input.type === 'checkbox') {
                    input.checked = !input.checked;
                    this.classList.toggle('active', input.checked);
                }
                // Handle radio case
                else if (input.type === 'radio') {
                    // Remove active class from all chips in the same group
                    const name = input.name;
                    if (name) {
                        document.querySelectorAll(`input[name="${name}"]`).forEach(radio => {
                            const parentChip = radio.closest('.chip');
                            if (parentChip) {
                                parentChip.classList.remove('active');
                            }
                        });
                    }
                    // Set this chip as active
                    input.checked = true;
                    this.classList.add('active');
                }

                // Prevent any default button behavior
                return false;
            });

            // Initialize state based on input
            const input = chip.querySelector('input');
            if (input && input.checked) {
                chip.classList.add('active');
            }
        });

        // Initialize selected chips in edit mode
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            checkbox.closest('.chip').classList.add('selected');
        });
    });
    </script>
</body>

</html>