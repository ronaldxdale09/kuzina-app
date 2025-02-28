<section class="product-section">
    <h1 class="font-md"><?php echo $foodName; ?></h1>
    <!-- Dynamic Star Rating and Review Count -->
    <div class="rating">
        <?php
            // Display filled stars based on the average rating
            for ($i = 1; $i <= 5; $i++) {
                if ($i <= round($avg_rating)) {
                    echo '<i data-feather="star" class="filled"></i>';
                } else {
                    echo '<i data-feather="star" class="empty"></i>';
                }
            }
            ?>
        <span class="font-xs content-color">(<?php echo $review_count; ?> Ratings)</span>
    </div>
    <div class="price"><span>PHP <?php echo $price; ?></span></div>


    <div class="nutritional-info">
        <h4>Nutritional Information</h4>
        <div class="nutrition-grid">
            <div class="nutrition-item">
                <span class="label">Calories</span>
                <span class="value"><?php echo $calories; ?></span>
            </div>
            <div class="nutrition-item">
                <span class="label">Protein</span>
                <span class="value"><?php echo $protein; ?>g</span>
            </div>
            <div class="nutrition-item">
                <span class="label">Carbs</span>
                <span class="value"><?php echo $carbs; ?>g</span>
            </div>
            <div class="nutrition-item">
                <span class="label">Fat</span>
                <span class="value"><?php echo $fat; ?>g</span>
            </div>
        </div>
    </div>
    <div class="dietary-info">
        <p class="content-color font-base">
            <strong>Diet Type:</strong> <?php echo $dietType; ?><br>
            <strong>Allergens:</strong> <?php echo $allergens; ?>
        </p>
    </div>
    <section class="kitchen-details">
    <a href="kitchen.php?id=<?php echo $kitchen_id; ?>" class="kitchen-profile-link">
        <div class="kitchen-profile">
            <div class="kitchen-img">
                <img src="../../uploads/kitchen_photos/<?php echo htmlspecialchars($kitchenPhoto); ?>"
                    alt="<?php echo $kitchenName; ?>'s Kitchen">
            </div>
            <div class="kitchen-info">
                <h3 class="kitchen-name"><?php echo $kitchenName; ?>'s Kitchen</h3>
                <p class="kitchen-description"><?php echo $kitchenDesc; ?></p>
                <span class="view-kitchen">
                    <i class='bx bx-right-arrow-alt'></i>
                    Explore the Kitchen
                </span>
            </div>
        </div>
    </a>
</section>

    <!-- Product Detail Start -->
    <div class="product-detail section-p-t">
        <div class="product-detail-box">
            <h2 class="title-color">Food Details</h2>
            <p class="content-color font-base"><?php echo $description; ?></p>
        </div>

        <!-- Product Detail Accordion Start -->
        <div class="accordion" id="accordionExample">

            <!-- Features & Details -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Features & Details
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <p class="content-color font-base">
                            Diet Type: <?php echo $dietType; ?><br>
                            Allergens: <?php echo $allergens; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Product Detail Accordion End -->
    </div>
    <!-- Product Detail End -->
</section>

<script>
function getKitchenDetails(foodId) {
    fetch(`functions/get_kitchen_details.php?food_id=${foodId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate kitchen details in the DOM
                document.querySelector('.kitchen-img img').src = data.kitchen.photo;
                document.querySelector('.kitchen-name').textContent = data.kitchen.kitchen_name;
                document.querySelector('.kitchen-description').textContent = data.kitchen.description;
            } else {
                console.error('Failed to fetch kitchen details:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching kitchen details:', error);
        });
}
</script>