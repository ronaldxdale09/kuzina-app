<?php
// Database connection

// Assuming you have the customer ID in a session or variable

// Fetch the customer's address from the database
$sql = "SELECT address_id, label, street_address, apartment, city, state, zip_code, country, is_default 
        FROM user_addresses 
        WHERE user_id = ? and is_default = ?";
$isDefault= 1;
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $customer_id, $isDefault);
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
        <a href="address.php" class="address-box-link">
            <div class="address-box <?= $address['is_default'] ? 'active' : '' ?>">
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
        </a>
        <?php endforeach; ?>
    </div>
</div>