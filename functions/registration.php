<?php

include '../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Splitting the full name into first name and last name from your form input
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Securely hash the password

    // Check if email already exists
    $checkEmail = $conn->prepare("SELECT email FROM customers WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkEmail->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Email is already registered. Please use a different email.']);
    } else {
        // Insert new customer into the database with first_name and last_name
        $stmt = $conn->prepare("INSERT INTO customers (first_name, last_name, email, phone, location, latitude, longitude, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $fname, $lname, $email, $phone, $location, $latitude, $longitude, $password);

        if ($stmt->execute()) {

            $userId = $conn->insert_id; 
            setcookie('user_id', $userId, time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie('user_fname', $fname, time() + (86400 * 30), "/"); // 86400 = 1 day
            setcookie('user_lname', $lname, time() + (86400 * 30), "/");
            setcookie('user_email', $email, time() + (86400 * 30), "/");
            
            echo json_encode(['success' => true, 'message' => 'Registration successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
        }

        $stmt->close();
    }

    $checkEmail->close();
    $conn->close();
}
