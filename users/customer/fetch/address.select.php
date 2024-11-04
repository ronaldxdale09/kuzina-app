<?php
// Database connection

// Assuming you have the customer ID in a session or variable

// Fetch the customer's address from the database
$sql = "SELECT address_id, label, street_address, apartment, city, state, zip_code, country, is_default 
        FROM customer_addresses 
        WHERE customer_id = ?";
$isDefault= 1;
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all addresses
$addresses = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $addresses[] = $row;
    }
}
$stmt->close();
?>

<style>
.address-box-link {
    text-decoration: none;
    color: inherit;
    display: block;
}
</style>
<div class="address2-page">
    <div class="address-wrap">
        <?php foreach ($addresses as $address) : ?>
        <div class="address-box <?= $address['is_default'] ? 'active' : '' ?>"
            data-address-id="<?= $address['address_id'] ?>">
            <div class="conten-box">
                <div class="heading">
                    <i class="iconly-Work icli"></i>
                    <h2 class="title-color font-md"><?= htmlspecialchars($address['label']) ?></h2>
                    <?php if ($address['is_default']) : ?>
                    <span class="badges-round font-white bg-theme-theme font-xs">Default</span>
                    <?php endif; ?>
                </div>
                <h3 class="title-color font-sm"><?php echo $_COOKIE['user_fname']; ?></h3> <!-- Customer name -->
                <p class="content-color font-sm">
                    <?= htmlspecialchars($address['street_address']) ?>
                    <?php if (!empty($address['apartment'])) : ?>
                    , <?= htmlspecialchars($address['apartment']) ?>
                    <?php endif; ?>
                    <br>
                    <?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['state']) ?>
                    <?= htmlspecialchars($address['zip_code']) ?>
                </p>
            </div>
            <img src="assets/images/map/map.jpg" alt="map" />
        </div>
        <?php endforeach; ?>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener for each address box
    document.querySelectorAll('.address-box').forEach(box => {
        box.addEventListener('click', function() {
            const addressId = this.getAttribute('data-address-id');
            selectAddress(addressId);
        });
    });

    // Function to send the AJAX request
    function selectAddress(addressId) {
        fetch('functions/update_default_address.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    'address_id': addressId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the UI to mark the selected address as active
                    document.querySelectorAll('.address-box').forEach(box => {
                        box.classList.remove('active');
                    });
                    document.querySelector(`.address-box[data-address-id="${addressId}"]`).classList.add(
                        'active');
                } else {
                    alert(data.message || 'An error occurred while updating the address.');
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Confirm button to go back to cart
    document.getElementById('confirm-address').addEventListener('click', function() {
        window.location.href = 'cart.php';
    });
});
</script>