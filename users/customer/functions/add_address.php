<?php
include '../../../connection/db.php'; // Include your DB connection

// Get the JSON data from the AJAX request
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $customer_id = $_COOKIE['user_id'];
    $street = $data['street'];
    $apartment = $data['apartment'];
    $city = $data['city'];
    $state = $data['state'];
    $zip = $data['zip'];
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];
    $label = $data['label'];

    // Prepare SQL to insert address into the database
    $sql = "INSERT INTO user_addresses (user_id, street_address, apartment, city, state, zip_code, latitude, longitude, label, is_default) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0)"; // Initially not default

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssdds", $customer_id, $street, $apartment, $city, $state, $zip, $latitude, $longitude, $label);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Address added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error adding address: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}
?>