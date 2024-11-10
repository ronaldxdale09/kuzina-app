<section class="product-section">
    <h1 class="font-md"><?php echo $foodName; ?></h1>
    <div class="rating">
        <i data-feather="star"></i>
        <i data-feather="star"></i>
        <i data-feather="star"></i>
        <i data-feather="star"></i>
        <i data-feather="star"></i>
        <span class="font-xs content-color">(150 Ratings)</span>
    </div>
    <div class="price"><span>PHP <?php echo $price; ?></span></div>

    <section class="kitchen-details">
        <div class="kitchen-profile">
            <div class="kitchen-img">
                <img src="assets/images/avatar/avatar.jpg" alt="Kitchen Profile Picture">
            </div>
            <div class="kitchen-info">
                <h3 class="kitchen-name">Chef Maria's Kitchen</h3>
                <p class="kitchen-description">Known for authentic homemade recipes that bring the warmth of
                    home-cooked meals to your table. Taste the difference with our specially crafted dishes.</p>
            </div>
        </div>
    </section>


    <!-- Product Detail Start -->
    <div class="product-detail section-p-t">
        <div class="product-detail-box">
            <h2 class="title-color">Food Details</h2>
            <p class="content-color font-base"><?php echo $description; ?></p>
        </div>

        <!-- Product Detail Accordion Start -->
        <div class="accordion" id="accordionExample">

            <!-- Product Disclaimer -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Product Disclaimer
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                    data-bs-parent="#accordionExample">
                    <div class="accordion-body">
                        <p class="content-color font-base">
                            Please consult with your healthcare provider for more information on potential
                            allergens
                            in this product.
                        </p>
                    </div>
                </div>
            </div>

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