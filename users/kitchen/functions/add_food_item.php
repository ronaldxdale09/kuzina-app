<?php
include '../../../connection/db.php';

$response = ['success' => false, 'message' => ''];
try {
    $kitchen_id = $_COOKIE['kitchen_id'] ?? null;

    // Validate and sanitize input data
    $itemName = trim($_POST['itemName'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $description = trim($_POST['description'] ?? '');
    $pickupDelivery = trim($_POST['pickupDelivery'] ?? 'Pick Up');
    $dietType = $_POST['dietType'] ?? 'All';
    $mealType = $_POST['mealType'] ?? 'All';

    $healthGoal = $_POST['healthGoal'] ?? 'All';
    $allergens = $_POST['allergens'] ?? 'None';

    $protein = filter_var($_POST['protein'] ?? 0, FILTER_VALIDATE_FLOAT);
    $carbs = filter_var($_POST['carbs'] ?? 0, FILTER_VALIDATE_FLOAT);
    $fat = filter_var($_POST['fat'] ?? 0, FILTER_VALIDATE_FLOAT);
    $totalCalories = filter_var($_POST['totalCalories'] ?? 0, FILTER_VALIDATE_FLOAT);


    // Check for required fields
    if (empty($itemName) || !$price || empty($description)) {
        $response['message'] = 'Item name, price, and description are required.';
        echo json_encode($response);
        exit;
    }

    // Convert arrays to strings if they are arrays
    if (is_array($dietType)) {
        $dietType = implode(',', $dietType);
    }
    if (is_array($mealType)) {
        $mealType = implode(',', $mealType);
    }

    if (is_array($healthGoal)) {
        $healthGoal = implode(',', $healthGoal);
    }

    if (is_array($allergens)) {
        $allergens = implode(',', $allergens);
    }

    // Image upload processing (expecting files from input type="file")
    $uploadDir = '../../../uploads/';
    $photo1 = $photo2 = $photo3 = null;

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            $response['message'] = 'Failed to create directory for image upload.';
            echo json_encode($response);
            exit;
        }
    }

    // Handle photo1, photo2, and photo3 inputs
    for ($i = 1; $i <= 3; $i++) {
        $fileKey = "photo$i";
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $fileTmpName = $_FILES[$fileKey]['tmp_name'];
            $fileName = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9.\-_]/', '_', basename($_FILES[$fileKey]['name']));
            $fileDestination = $uploadDir . $fileName;

            // Validate image type
            $fileMimeType = mime_content_type($fileTmpName);
            if (in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
                // Move the file to the destination
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    ${"photo$i"} = $fileName; // Save the file name into the respective variable (photo1, photo2, photo3)
                } else {
                    error_log("Failed to move uploaded file: $fileTmpName to $fileDestination");
                    $response['message'] = "Failed to upload image: " . $_FILES[$fileKey]['name'];
                    echo json_encode($response);
                    exit;
                }
            } else {
                error_log('Invalid file type for: ' . $_FILES[$fileKey]['name'] . ' - MIME type: ' . $fileMimeType);
                $response['message'] = 'Invalid file type for: ' . $_FILES[$fileKey]['name'];
                echo json_encode($response);
                exit;
            }
        } elseif (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] !== UPLOAD_ERR_NO_FILE) {
            error_log('File upload error (' . $_FILES[$fileKey]['error'] . ') for: ' . $fileKey);
            $response['message'] = 'Error uploading file: ' . $_FILES[$fileKey]['name'];
            echo json_encode($response);
            exit;
        }
    }

    // Insert into the database, saving photo1, photo2, and photo3 into separate columns
    $stmt = $conn->prepare("INSERT INTO food_listings 
    (kitchen_id,food_name,meal_type, price, description, category, photo1, photo2, photo3, diet_type_suitable, health_goal_suitable, allergens,protein,carbs,fat,calories) 
    VALUES (?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssdssssssssssss",$kitchen_id,  $itemName,  $mealType, $price, $description, $pickupDelivery, $photo1, $photo2, $photo3, $dietType, $healthGoal, $allergens, $protein,$carbs,$fat,$totalCalories);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Item added successfully!';
        } else {
            error_log('Database insertion failed: ' . $stmt->error);
            $response['message'] = 'Database insertion failed: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        error_log('Database statement preparation failed: ' . $conn->error);
        $response['message'] = 'Database statement preparation failed: ' . $conn->error;
    }
} catch (Exception $e) {
    error_log('Error: ' . $e->getMessage());
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);