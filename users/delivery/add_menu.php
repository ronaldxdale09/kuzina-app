<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<link rel="stylesheet" type="text/css" href="assets/css/add_menu.css" />

<body>

    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_add_menu.php'; ?>
    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'navbar/shop.navbar.php'; ?>
    <!-- Header End -->

    <!-- Sidebar Start -->
   
    <!-- Sidebar End -->

    <!-- Main Start -->
    <main class="main-wrap product-page mb-xxl">
        <!-- Page Header -->
        <div class="page-header pt-4">
            <div class="header-content" style="display: flex; align-items: center; gap: 20px;">
                <div class="icon-wrapper"
                    style="background-color: #4C0710; width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    <i class="bx bx-plus" style="color: #ffffff; font-size: 20px;"></i>
                </div>
                <h1 style="font-size: 28px; color: #4e0707; font-weight: bold;">Add New Items</h1>
            </div>
        </div>
        <!-- Add New Item Form -->
        <form id="addItemForm" enctype="multipart/form-data" method="POST">
            <!-- Item Name -->
            <div class="form-group">
                <label for="itemName">Item Name</label>
                <input type="text" id="itemName" name="itemName" placeholder="Enter item name" required />
            </div>

            <div class="form-group">
                <label>Meal Type</label>
                <div class="chip-group">
                    <div class="chip">
                        <input type="checkbox" id="breakfast" name="mealType[]" value="Breakfast" hidden />
                        <label for="breakfast"><i class="bx bx-coffee"></i> Breakfast</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="lunch" name="mealType[]" value="Lunch" hidden />
                        <label for="lunch"><i class="bx bx-food-menu"></i> Lunch</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="snacks" name="mealType[]" value="Snacks" hidden />
                        <label for="snacks"><i class="bx bx-cookie"></i> Snacks</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="dinner" name="mealType[]" value="Dinner" hidden />
                        <label for="dinner"><i class="bx bx-wine"></i> Dinner</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="all" name="mealType[]" value="All" hidden />
                        <label for="all"><i class="bx bx-calendar"></i> All Day</label>
                    </div>
                </div>
            </div>


            <!-- Upload Photo -->
            <div class="form-group">
                <label for="uploadPhoto">Upload Photo</label>
                <div class="upload-photo pt-1">
                    <!-- First Photo -->
                    <div class="photo-placeholder" data-placeholder-index="1" id="photo-placeholder-1">
                        <input type="file" id="photo1" name="photo1" accept="image/*" hidden />
                        <label for="photo1" class="photo-label"><i class="bx bx-camera"></i></label>
                        <div class="image-preview" id="preview1"></div>
                    </div>
                    <!-- Second Photo -->
                    <div class="photo-placeholder" data-placeholder-index="2" id="photo-placeholder-2">
                        <input type="file" id="photo2" name="photo2" accept="image/*" hidden />
                        <label for="photo2" class="photo-label"><i class="bx bx-camera"></i></label>
                        <div class="image-preview" id="preview2"></div>
                    </div>
                    <!-- Third Photo -->
                    <div class="photo-placeholder" data-placeholder-index="3" id="photo-placeholder-3">
                        <input type="file" id="photo3" name="photo3" accept="image/*" hidden />
                        <label for="photo3" class="photo-label"><i class="bx bx-camera"></i></label>
                        <div class="image-preview" id="preview3"></div>
                    </div>
                </div>
            </div>

            <!-- Price and Pickup/Delivery -->
            <div class="form-group price-input">
                <label for="price">Price</label>
                <input type="number" id="price" name="price" placeholder="PRICE" required />
                <div class="pickup-delivery">
                    <div class="option active" onclick="toggleOption(this)">
                        <input type="radio" id="pickup" name="pickupDelivery" value="Pick Up" checked hidden />
                        <label for="pickup"><i class="bx bx-shopping-bag"></i> Pick Up</label>
                    </div>
                    <div class="option" onclick="toggleOption(this)">
                        <input type="radio" id="delivery" name="pickupDelivery" value="Delivery" hidden />
                        <label for="delivery"><i class="bx bx-truck"></i> Delivery</label>
                    </div>
                </div>
            </div>

            <!-- Type of Diet -->
            <div class="form-group">
                <label>Type of Diet</label>
                <div class="chip-group">
                    <div class="chip">
                        <input type="checkbox" id="vegetarian" name="dietType[]" value="Vegetarian" hidden />
                        <label for="vegetarian"><i class="bx bx-leaf"></i> Vegetarian</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="vegan" name="dietType[]" value="Vegan" hidden />
                        <label for="vegan"><i class="bx bx-leaf"></i> Vegan</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="keto" name="dietType[]" value="Keto" hidden />
                        <label for="keto"><i class="bx bx-dumbbell"></i> Keto</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="balanced" name="dietType[]" value="Balanced Diet" hidden />
                        <label for="balanced"><i class="bx bx-heart"></i> Balanced Diet</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="all" name="dietType[]" value="All" hidden />
                        <label for="all"><i class="bx bx-list-ul"></i> All</label>
                    </div>
                </div>
            </div>

            <!-- Health Goal Suitable -->
            <div class="form-group">
                <label>Health Goal Suitable</label>
                <div class="chip-group">
                    <div class="chip">
                        <input type="checkbox" id="weightLoss" name="healthGoal[]" value="Weight Loss" hidden />
                        <label for="weightLoss"><i class="bx bx-trending-down"></i> Weight Loss</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="muscleGain" name="healthGoal[]" value="Muscle Gain" hidden />
                        <label for="muscleGain"><i class="bx bx-trending-up"></i> Muscle Gain</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="energyLevels" name="healthGoal[]" value="Improve Energy Levels"
                            hidden />
                        <label for="energyLevels"><i class="bx bx-battery"></i> Improve Energy Levels</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="digestion" name="healthGoal[]" value="Better Digestion" hidden />
                        <label for="digestion"><i class="bx bx-happy"></i> Better Digestion</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="allHealth" name="healthGoal[]" value="All" hidden />
                        <label for="allHealth"><i class="bx bx-list-ul"></i> All</label>
                    </div>
                </div>
            </div>

            <!-- Allergens -->
            <div class="form-group">
                <label>Allergens</label>
                <div class="chip-group">
                    <div class="chip">
                        <input type="checkbox" id="dairy" name="allergens[]" value="Dairy" hidden />
                        <label for="dairy"><i class="bx bx-error"></i> Dairy</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="gluten" name="allergens[]" value="Gluten" hidden />
                        <label for="gluten"><i class="bx bx-error"></i> Gluten</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="nuts" name="allergens[]" value="Nuts" hidden />
                        <label for="nuts"><i class="bx bx-error"></i> Nuts</label>
                    </div>
                    <div class="chip">
                        <input type="checkbox" id="none" name="allergens[]" value="None" hidden />
                        <label for="none"><i class="bx bx-check-circle"></i> None</label>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="form-group">
                <label for="description">Details</label>
                <textarea id="description" name="description" placeholder="About the meal" rows="4" required></textarea>
            </div>

            <!-- Save Button -->
            <div class="form-group">
                <button type="submit" class="save-btn" id="save-btn">
                    <span id="spinner" class="spinner-border spinner-border-sm" role="status"
                        style="display: none;"></span>
                    SAVE CHANGES
                </button>
            </div>
        </form>

    </main>
    <!-- Main End -->
    <!-- Add this popup HTML somewhere in your document, outside of any forms -->
<!-- Add this popup HTML somewhere in your document -->
<div id="custom-popup" class="popup">
    <div class="popup-content">
        <h2>Success!</h2>
        <p>Your item has been added successfully.</p>
        <a href="menu_list.php" id="confirm-btn" class="btn">OK</a>
    </div>
</div>


    <!-- Footer Start -->
    <?php include 'includes/appbar.php'; ?>
    <!-- Footer End -->

    <!-- Pwa Install App Popup Start -->
    <?php include 'includes/scripts.php'; ?>

    <script src="custom_scripts/add_item.js"></script>

    <script>


    </script>
</body>
<!-- Body End -->

</html>