<?php

function isRiderEmailRegistered($email, $conn) {
    $checkEmail = $conn->prepare("SELECT email FROM delivery_riders WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    $isRegistered = $checkEmail->num_rows > 0;
    $checkEmail->close();

    return $isRegistered;
}

function saveRiderDocuments($files) {
    $documents = [
        'profile_photo' => 'default.png',
        'id_front' => null,
        'id_back' => null
    ];

    // Handle profile photo
    if (isset($files['profilePhoto']) && $files['profilePhoto']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . '_' . basename($files['profilePhoto']['name']);
        $uploadPath = '../uploads/profile/'.$filename;
        
        if (move_uploaded_file($files['profilePhoto']['tmp_name'], $uploadPath)) {
            $documents['profile_photo'] = $filename;
        }
    }

    // Handle ID front
    if (isset($files['id_front']) && $files['id_front']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . '_' . basename($files['id_front']['name']);
        $uploadPath = '../uploads/riders/' . $filename;
        
        if (move_uploaded_file($files['id_front']['tmp_name'], $uploadPath)) {
            $documents['id_front'] = $filename;
        }
    }

    // Handle ID back
    if (isset($files['id_back']) && $files['id_back']['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . '_' . basename($files['id_back']['name']);
        $uploadPath = '../uploads/riders/' . $filename;
        
        if (move_uploaded_file($files['id_back']['tmp_name'], $uploadPath)) {
            $documents['id_back'] = $filename;
        }
    }

    return $documents;
}

function registerRider($data, $conn) {
    $stmt = $conn->prepare("INSERT INTO delivery_riders (
        first_name, 
        last_name, 
        email, 
        password, 
        phone, 
        vehicle_type, 
        license_plate,
        profile_photo,
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

    $stmt->bind_param("ssssssss", 
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['password'], // Make sure this is hashed before passing
        $data['phone'],
        $data['vehicle_type'],
        $data['license_plate'],
        $data['profile_photo']
    );

    $success = $stmt->execute();
    $riderId = $stmt->insert_id;
    $stmt->close();

    // If rider registration successful, store documents
    if ($success && $riderId) {
        // You might want to create a separate table for rider documents
        $stmtDocs = $conn->prepare("INSERT INTO rider_documents (
            rider_id,
            id_front,
            id_back,
            uploaded_at
        ) VALUES (?, ?, ?, NOW())");

        $stmtDocs->bind_param("iss",
            $riderId,
            $data['id_front'],
            $data['id_back']
        );

        $stmtDocs->execute();
        $stmtDocs->close();
    }

    return $success ? $riderId : false;
}

function setRiderCookies($riderId, $firstName, $email) {
    setcookie('rider_id', $riderId, time() + (86400 * 30), "/");
    setcookie('rider_name', $firstName, time() + (86400 * 30), "/");
    setcookie('rider_email', $email, time() + (86400 * 30), "/");
}

function registerRiderHandler($postData, $files, $conn) {
    // Validate email
    if (isRiderEmailRegistered($postData['email'], $conn)) {
        return [
            'success' => false, 
            'message' => 'Email is already registered. Please use a different email.'
        ];
    }

    // Handle file uploads
    $documents = saveRiderDocuments($files);
    
    // Prepare rider data
    $riderData = [
        'first_name' => $postData['first_name'],
        'last_name' => $postData['last_name'],
        'email' => $postData['email'],
        'password' => password_hash($postData['password'], PASSWORD_DEFAULT), // Hash password
        'phone' => $postData['phone'],
        'vehicle_type' => $postData['vehicle_type'],
        'license_plate' => $postData['license_plate'],
        'profile_photo' => $documents['profile_photo'],
        'id_front' => $documents['id_front'],
        'id_back' => $documents['id_back']
    ];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Register new rider
        $riderId = registerRider($riderData, $conn);
        
        if ($riderId) {
            // Set cookies
            setRiderCookies($riderId, $riderData['first_name'], $riderData['email']);
            
            // Commit transaction
            $conn->commit();
            
            return [
                'success' => true,
                'rider_id' => $riderId,
                'message' => 'Registration successful! Your application is under review.'
            ];
        } else {
            throw new Exception('Could not register rider.');
        }
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}

?>