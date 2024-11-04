<?php

function isKitchenEmailRegistered($email, $conn) {
    $checkEmail = $conn->prepare("SELECT email FROM kitchens WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    $isRegistered = $checkEmail->num_rows > 0;
    $checkEmail->close();

    return $isRegistered;
}

function saveProfilePicture($file) {
    $defaultFilename = 'default.png'; // Just the filename for the default image

    // Check if a file is uploaded
    if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
        $filename = basename($file['name']);
        $uploadPath = '../uploads/profile/' . $filename;

        // Move the uploaded file to the correct directory
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $filename; // Return only the filename
        } else {
            // Handle upload error
            return $defaultFilename;
        }
    }

    // Return the default filename if no file was uploaded
    return $defaultFilename;
}


function registerKitchen($data, $conn) {
    $stmt = $conn->prepare("INSERT INTO kitchens (fname,lname, email, password, phone, address, city, country, postal_code, description, photo, created_at) VALUES (?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssssssssss", 
        $data['fname'], 
        $data['lname'], 

        $data['email'], 
        $data['password'], 
        $data['phone'], 
        $data['location'], 
        $data['city'], 
        $data['country'], 
        $data['postal_code'], 
        $data['description'], 
        $data['photo']
    );

    $success = $stmt->execute();
    $insertId = $stmt->insert_id;
    $stmt->close();

    return $success ? $insertId : false;
}

function setKitchenCookies($userId, $fname, $email) {
    setcookie('kitchen_user_id', $userId, time() + (86400 * 30), "/");
    setcookie('kitchen_user_name', $fname, time() + (86400 * 30), "/");
    setcookie('kitchen_user_email', $email, time() + (86400 * 30), "/");
}

function registerKitchenHandler($data, $conn) {
    // Check if email is already registered
    if (isKitchenEmailRegistered($data['email'], $conn)) {
        return ['success' => false, 'message' => 'Email is already registered. Please use a different email.'];
    } else {
        // Register new kitchen
        $userId = registerKitchen($data, $conn);
        if ($userId) {
            // Set user cookies
            setKitchenCookies($userId, $data['fname'], $data['email']);
            return ['success' => true, 'message' => 'Registration successful!'];
        } else {
            return ['success' => false, 'message' => 'Error: Could not register user.'];
        }
    }
}
