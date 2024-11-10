<?php

include '../connection/db.php';
include 'customer.registration.php';
include 'kitchen.registration.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'] ?? null;

    if ($type === 'customer') {
        // Prepare customer data and call the customer registration handler
        $data = [
            'first_name' => $_POST['fname'],
            'last_name' => $_POST['lname'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'location' => $_POST['location'],
            'latitude' => $_POST['latitude'],
            'longitude' => $_POST['longitude'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT)
        ];

        $response = registerCustomerHandler($data, $conn);
        if ($response['success']) {
            // Save address if registration is successful
            $addressData = [
                'user_id' => $response['user_id'], // Assuming user_id is returned by registerCustomerHandler
                'type' => 'customer',
                'label' => 'Home',
                'street_address' => $_POST['location'],
                'city' => $_POST['city'] ?? 'Zamboanga City',
                'country' => $_POST['country'] ?? 'Philippines',
                'postal_code' => $_POST['postal_code'] ?? null,
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude']
            ];
            $addressResponse = insertUserAddress($addressData, $conn);
            echo json_encode($addressResponse);
        } else {
            echo json_encode($response);
        }

    } elseif ($type === 'kitchen') {
        // Prepare kitchen data and call the kitchen registration handler
        $data = [
            'fname' => $_POST['fname'],
            'lname' => $_POST['lname'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'location' => $_POST['location'],
            'latitude' => $_POST['latitude'],
            'longitude' => $_POST['longitude'],
            'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
            'city' => $_POST['city'] ?? 'Zamboanga City',
            'country' => $_POST['country'] ?? 'Philippines',
            'postal_code' => $_POST['postal_code'] ?? null,
            'description' => $_POST['description'] ?? null,
            'photo' => saveProfilePicture($_FILES['profilePhoto'])
        ];

        $response = registerKitchenHandler($data, $conn);
        if ($response['success']) {
            // Save address if registration is successful
            $addressData = [
                'user_id' => $response['kitchen_id'], // Assuming kitchen_id is returned by registerKitchenHandler
                'type' => 'kitchen',
                'label' => 'Kitchen Location',
                'street_address' => $_POST['location'],
                'city' => $_POST['city'] ?? 'Zamboanga City',
                'country' => $_POST['country'] ?? 'Philippines',
                'postal_code' => $_POST['postal_code'] ?? null,
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude']
            ];
            $addressResponse = insertUserAddress($addressData, $conn);
            echo json_encode($addressResponse);
        } else {
            echo json_encode($response);
        }

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid registration type specified.']);
    }

    $conn->close();
}

// Function to insert address into user_addresses table
function insertUserAddress($data, $conn) {
    $stmt = $conn->prepare("INSERT INTO user_addresses (user_id, label, street_address, city, country, zip_code, latitude, longitude, is_default) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    $stmt->bind_param(
        "isssssss",
        $data['user_id'],
        $data['label'],
        $data['street_address'],
        $data['city'],
        $data['country'],
        $data['postal_code'],
        $data['latitude'],
        $data['longitude']
    );

    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Address saved successfully'];
    } else {
        return ['success' => false, 'message' => 'Failed to save address'];
    }
}
