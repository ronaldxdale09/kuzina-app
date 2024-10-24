<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/product.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<body>
    <!-- Skeleton loader Start -->
    <?php include 'skeleton/sk_product.php'; ?>

    <!-- Skeleton loader End -->

    <!-- Header Start -->
    <?php include 'includes/product.header.php'; ?>

    <!-- Header End -->
    <?php 

// Retrieve the product ID from the URL
$productId = $_GET['prod'] ?? null;

        if ($productId) {
            // Prepare a statement to fetch the product details by food_id
            $stmt = $conn->prepare("SELECT food_name, price, description, photo1, photo2, photo3, diet_type_suitable, allergens FROM food_listings WHERE food_id = ?");
            $stmt->bind_param("i", $productId);
            $stmt->execute();
            $result = $stmt->get_result();

            // Check if the product exists
            if ($result->num_rows > 0) {
                // Fetch product data
                $product = $result->fetch_assoc();

                // Assign values to variables for use in the HTML
                $foodName = htmlspecialchars($product['food_name']);
                $price = number_format($product['price'], 2);
                $description = htmlspecialchars($product['description']);
                $photo1 = $product['photo1'];
                $photo2 = $product['photo2'];
                $photo3 = $product['photo3'];
                $dietType = htmlspecialchars($product['diet_type_suitable']);
                $allergens = htmlspecialchars($product['allergens']);
            } else {
                // Handle case where product is not found
                echo "Product not found.";
                exit;
            }
            $stmt->close();
        } else {
            // Handle case where prod is missing in the URL
            echo "No product specified.";
            exit;
        }
        ?>
    <!-- Main Start -->
    <main class="main-wrap product-page mb-xxl">
        <!-- Banner Section Start -->
        <div class="banner-box product-banner">
            <div class="banner">
                <img src="../../uploads/<?php echo htmlspecialchars($photo1); ?>" alt="<?php echo $foodName; ?>"
                    class="product-image" />
            </div>
            <?php if (!empty($photo2)) { ?>
            <div class="banner">
                <img src="../../uploads/<?php echo htmlspecialchars($photo2); ?>" alt="<?php echo $foodName; ?>"
                    class="product-image" />
            </div>
            <?php } ?>
            <?php if (!empty($photo3)) { ?>
            <div class="banner">
                <img src="../../uploads/<?php echo htmlspecialchars($photo3); ?>" alt="<?php echo $foodName; ?>"
                    class="product-image" />
            </div>
            <?php } ?>
        </div>

        <!-- Banner Section End -->

        <!-- Product Section Start -->
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

            <!-- Product Detail Start -->
            <div class="product-detail section-p-t">
                <div class="product-detail-box">
                    <h2 class="title-color">Product Details</h2>
                    <p class="content-color font-base"><?php echo $description; ?></p>
                </div>

                <!-- Product Detail Accordion Start -->
                <div class="accordion" id="accordionExample">
                    <!-- Manufacturer Details -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Manufacturer Details
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                            data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p class="content-color font-base">
                                    This product is manufactured following the highest standards of quality.
                                </p>
                            </div>
                        </div>
                    </div>

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

        <!-- Product Review Section Start -->
        <?php include 'components/prod.review.php'; ?>
        <!-- Random Product Start -->
        <br>
        <?php include 'components/random.product.php'; ?>
        <section class="recently-viewed">
            <div class="top-content">
                <div>
                    <h4 class="title-color">Lowest Price</h4>
                    <p class="font-xs content-color">Pay less, Get More</p>
                </div>
                <a href="shop.html" class="font-xs font-theme">See all</a>
            </div>
            <div class="product-slider">
                <?php
                fetch_and_render_random_products($conn); 
                ?>
            </div>
        </section>
        <!-- Product Section End -->
    </main>
    <!-- Main End -->

    <!-- Popup for success message -->
    <div id="cart-success-popup" class="cart-popup">
        <div class="cart-popup-content">
            <i class='bx bx-check-circle'></i>
            <p>Item successfully added to the cart!</p>
        </div>
    </div>

    <!-- Popup for error message -->
    <div id="cart-error-popup" class="cart-popup">
        <div class="cart-popup-content">
            <i class='bx bx-error'></i>
            <p>Something went wrong, please try again!</p>
        </div>
    </div>

    <!-- The fly item that will animate to the cart -->
    <div id="fly-item" class="fly-item">
        <i class='bx bx-box'></i> <!-- Icon for the item -->
    </div>

    <footer class="footer-wrap shop">
        <ul class="footer">
            <li class="footer-item">
                <a href="#" class="add-to-cart-btn">
                    <i class='bx bx-cart'></i> Add to Cart
                </a>
            </li>
            <li class="footer-item">
                <a href="cart.php" class="view-cart-btn">
                    <i class='bx bx-cart' id="cart-icon"></i> View Cart
                </a>
            </li>
        </ul>
    </footer>

    <!-- Action Share Grid Start -->
    <script>
    const foodId = <?php echo json_encode($productId); ?>; // Dynamically get the product ID from PHP

    document.querySelector('.add-to-cart-btn').addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default behavior

        const foodId = <?php echo json_encode($productId); ?>; // Dynamically get the product ID from PHP
        const quantity = 1; // Set the default quantity

        const flyItem = document.getElementById('fly-item');

        // Function to handle the fly-to-middle animation and keep it at the center during processing
        function startFlyToMiddleAnimation() {
            const addToCartBtn = document.querySelector('.add-to-cart-btn');

            // Get the bounding rectangle (position) of the Add to Cart button
            const addToCartBtnRect = addToCartBtn.getBoundingClientRect();

            // Set the starting position of the fly-item to the Add to Cart button's position
            flyItem.style.position = 'fixed'; // Make sure it's fixed relative to the viewport
            flyItem.style.left = `${addToCartBtnRect.left}px`;
            flyItem.style.top = `${addToCartBtnRect.top}px`;

            // Make the fly-item visible
            flyItem.style.visibility = 'visible';
            flyItem.style.opacity = '1'; // Fade in

            // Calculate the middle of the screen
            const middleX = window.innerWidth / 2 - flyItem.offsetWidth / 2;
            const middleY = window.innerHeight / 2 - flyItem.offsetHeight / 2;

            // Fly to the middle of the screen
            setTimeout(() => {
                flyItem.style.transform =
                    `translate(${middleX - addToCartBtnRect.left}px, ${middleY - addToCartBtnRect.top}px)`;
                flyItem.classList.add('fly-to-middle'); // Fly to the middle of the screen
            }, 100); // Delay for smooth start
        }

        // Function to show the success popup
        function showSuccessPopup() {
            const successPopup = document.getElementById('cart-success-popup');
            successPopup.style.visibility = 'visible';
            successPopup.style.opacity = '1'; // Fade in

            // Hide the popup after 1.5 seconds, then trigger the fly-to-cart animation
            setTimeout(() => {
                successPopup.style.opacity = '0'; // Fade out
                successPopup.style.visibility = 'hidden'; // Hide after fading out
                startFlyToCartAnimation(); // Fly to cart after success
            }, 1500); // Show popup for 1.5 seconds
        }

        // Function to show the error popup and reset the fly item
        function showErrorPopup() {
            const errorPopup = document.getElementById('cart-error-popup');
            errorPopup.style.visibility = 'visible';
            errorPopup.style.opacity = '1'; // Fade in

            // Hide the error popup after 1.5 seconds, and reset the fly item
            setTimeout(() => {
                errorPopup.style.opacity = '0'; // Fade out
                errorPopup.style.visibility = 'hidden'; // Hide after fading out

                // Reset the fly item after error
                resetFlyItem();
            }, 1500); // Show popup for 1.5 seconds
        }

        // Function to reset the fly item position and hide it after error or success
        function resetFlyItem() {
            setTimeout(() => {
                flyItem.style.opacity = '0'; // Fade out the fly item
                flyItem.style.visibility = 'hidden'; // Make sure it's hidden
                flyItem.style.transform = 'translate(0, 0)'; // Reset the transform to original position
            }, 100); // Delay slightly to ensure smooth transition
        }

        // Function to handle the fly-to-cart animation
        function startFlyToCartAnimation() {
            const cartIcon = document.getElementById('cart-icon');

            // Get the bounding rectangles (positions) of the View Cart button
            const cartIconRect = cartIcon.getBoundingClientRect();

            // Calculate the movement distances (horizontal and vertical) from the middle of the screen to the View Cart button
            const translateX = cartIconRect.left - (window.innerWidth / 2);
            const translateY = cartIconRect.top - (window.innerHeight / 2);

            // Fly item from the middle of the screen to View Cart icon
            setTimeout(() => {
                flyItem.style.transform = `translate(${translateX}px, ${translateY}px)`;
                flyItem.classList.add('fly-to-cart'); // Add a class to animate it
            }, 100); // Delay for smooth start

            // Hide the fly-item after the animation
            setTimeout(() => {
                flyItem.style.opacity = '0'; // Fade out
                flyItem.style.visibility = 'hidden'; // Hide after fading out
                flyItem.style.transform = 'translate(0, 0)'; // Reset the transform
            }, 1000); // Set this time to match the CSS transition duration
        }

        // Start the fly-to-middle animation
        startFlyToMiddleAnimation();

        // Send the data to the server via AJAX
        fetch('functions/addCart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'add',
                    food_id: foodId,
                    quantity: quantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessPopup(); // Show the success popup
                } else {
                    showErrorPopup(); // Show the error popup if something went wrong
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                showErrorPopup(); // Show the error popup if the fetch fails
            });
    });
    </script>

    <?php include 'components/prod.share.php'; ?>

    <!-- Action Share Grid End -->

    <?php include 'includes/scripts.php'; ?>
</body>
<!-- Body End -->

</html>
<!-- Html End -->