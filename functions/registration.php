<?php
include '../connection/db.php';
include 'customer.registration.php';
include 'kitchen.registration.php';
include 'rider.registration.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'] ?? null;

    switch($type) {
        case 'customer':
            handleCustomerRegistration();
            break;
        
        case 'kitchen':
            handleKitchenRegistration();
            break;
            
        case 'rider':
            handleRiderRegistration();
            break;
            
        default:
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid registration type specified.'
            ]);
    }

    $conn->close();
}

function handleRiderRegistration() {
    global $conn;
    
    // Prepare rider data
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'vehicle_type' => $_POST['vehicle_type'],
        'license_plate' => $_POST['license_plate']
    ];

    // Handle file uploads
    $files = [
        'profilePhoto' => $_FILES['profilePhoto'] ?? null,
        'id_front' => $_FILES['id_front'] ?? null,
        'id_back' => $_FILES['id_back'] ?? null
    ];

    $response = registerRiderHandler($data, $files, $conn);
    
    if ($response['success']) {
        // Save address if location data is provided
        if (isset($_POST['latitude']) && isset($_POST['longitude'])) {
            $addressData = [
                'user_id' => $response['rider_id'],
                'type' => 'rider',
                'label' => 'Home',
                'street_address' => $_POST['location'] ?? '',
                'city' => $_POST['city'] ?? 'Zamboanga City',
                'country' => $_POST['country'] ?? 'Philippines',
                'postal_code' => $_POST['postal_code'] ?? null,
                'latitude' => $_POST['latitude'],
                'longitude' => $_POST['longitude']
            ];
            
            $addressResponse = insertUserAddress($addressData, $conn);
            if (!$addressResponse['success']) {
                // Log address save failure but don't fail the registration
                error_log("Failed to save rider address for rider_id: " . $response['rider_id']);
            }
        }
    }
    
    echo json_encode($response);
}

function handleCustomerRegistration() {
    global $conn;
    
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
        $addressData = [
            'user_id' => $response['user_id'],
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
}

function handleKitchenRegistration() {
    global $conn;
    
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
        $addressData = [
            'user_id' => $response['kitchen_id'],
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
}

function insertUserAddress($data, $conn) {
    try {
        $stmt = $conn->prepare("INSERT INTO user_addresses (
            user_id, 
            user_type,
            label, 
            street_address, 
            city, 
            country, 
            zip_code, 
            latitude, 
            longitude, 
            is_default
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        
        $stmt->bind_param(
            "issssssss",
            $data['user_id'],
            $data['type'],
            $data['label'],
            $data['street_address'],
            $data['city'],
            $data['country'],
            $data['postal_code'],
            $data['latitude'],
            $data['longitude']
        );

        if ($stmt->execute()) {
            return [
                'success' => true, 
                'message' => 'Registration completed successfully'
            ];
        } else {
            throw new Exception('Failed to save address');
        }
    } catch (Exception $e) {
        error_log("Address insertion error: " . $e->getMessage());
        return [
            'success' => false, 
            'message' => 'Registration completed but failed to save address'
        ];
    } finally {
        $stmt->close();
    }
}