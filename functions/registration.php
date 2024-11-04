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
        echo json_encode($response);

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
            'city' => $_POST['city'] ?? null,
            'country' => $_POST['country'] ?? null,
            'postal_code' => $_POST['postal_code'] ?? null,
            'description' => $_POST['description'] ?? null,
            'photo' => saveProfilePicture($_FILES['profilePhoto'])
        ];

        $response = registerKitchenHandler($data, $conn);
        echo json_encode($response);

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid registration type specified.']);
    }

    $conn->close();
}