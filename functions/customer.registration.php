<?php

function isCustomerEmailRegistered($email, $conn) {
    $checkEmail = $conn->prepare("SELECT email FROM customers WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    $isRegistered = $checkEmail->num_rows > 0;
    $checkEmail->close();

    return $isRegistered;
}

function registerCustomer($data, $conn) {
    $stmt = $conn->prepare("INSERT INTO customers (first_name, last_name, email, phone, location, latitude, longitude, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", 
        $data['first_name'], 
        $data['last_name'], 
        $data['email'], 
        $data['phone'], 
        $data['location'], 
        $data['latitude'], 
        $data['longitude'], 
        $data['password']
    );

    $success = $stmt->execute();
    $insertId = $stmt->insert_id;
    $stmt->close();

    return $success ? $insertId : false;
}

function setCustomerCookies($userId, $fname, $lname, $email) {
    setcookie('user_id', $userId, time() + (86400 * 30), "/");
    setcookie('user_fname', $fname, time() + (86400 * 30), "/");
    setcookie('user_lname', $lname, time() + (86400 * 30), "/");
    setcookie('user_email', $email, time() + (86400 * 30), "/");
}

function registerCustomerHandler($data, $conn) {
    // Check if email is already registered
    if (isCustomerEmailRegistered($data['email'], $conn)) {
        return ['success' => false, 'message' => 'Email is already registered. Please use a different email.'];
    } else {
        // Register new customer
        $userId = registerCustomer($data, $conn);
        if ($userId) {
            // Set user cookies
            setCustomerCookies($userId, $data['first_name'], $data['last_name'], $data['email']);
            $userId = $conn->insert_id; // Get the last inserted ID for customer
            return ['success' => true, 'user_id' => $userId];
        } else {
            return ['success' => false, 'message' => 'Error: Could not register user.'];
        }
    }
}
