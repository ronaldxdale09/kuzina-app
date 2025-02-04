<?php
include '../../../connection/db.php';

$response = ['success' => false, 'message' => ''];
try {
    $kitchen_id = $_COOKIE['kitchen_id'] ?? null;
    $food_id = $_POST['food_id'] ?? null;

    // Check if this food item belongs to the kitchen
    $checkStmt = $conn->prepare("SELECT kitchen_id FROM food_listings WHERE food_id = ?");
    $checkStmt->bind_param("i", $food_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $food = $result->fetch_assoc();

    if (!$food || $food['kitchen_id'] != $kitchen_id) {
        $response['message'] = 'Unauthorized access';
        echo json_encode($response);
        exit;
    }

    // Validate and sanitize input data
    $itemName = trim($_POST['itemName'] ?? '');
    $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
    $description = trim($_POST['description'] ?? '');
    $pickupDelivery = trim($_POST['pickupDelivery'] ?? 'Pick Up');
    $dietType = $_POST['dietType'] ?? [];
    $mealType = $_POST['mealType'] ?? 'All';
    $healthGoal = $_POST['healthGoal'] ?? [];
    $allergens = $_POST['allergens'] ?? [];
    $protein = filter_var($_POST['protein'] ?? 0, FILTER_VALIDATE_FLOAT);
    $carbs = filter_var($_POST['carbs'] ?? 0, FILTER_VALIDATE_FLOAT);
    $fat = filter_var($_POST['fat'] ?? 0, FILTER_VALIDATE_FLOAT);
    $totalCalories = filter_var($_POST['totalCalories'] ?? 0, FILTER_VALIDATE_FLOAT);
    $category_id = filter_var($_POST['category_id'] ?? 0, FILTER_VALIDATE_INT);
    // Check for required fields
    if (empty($itemName) || !$price || empty($description)) {
        $response['message'] = 'Item name, price, and description are required.';
        echo json_encode($response);
        exit;
    }
    if (!$category_id) {
        $response['message'] = 'Valid category is required.';
        echo json_encode($response);
        exit;
    }

    // Convert arrays to strings
    $dietType = is_array($dietType) ? implode(',', $dietType) : $dietType;
    $healthGoal = is_array($healthGoal) ? implode(',', $healthGoal) : $healthGoal;
    $allergens = is_array($allergens) ? implode(',', $allergens) : $allergens;

    // Get existing photos
    $photoStmt = $conn->prepare("SELECT photo1, photo2, photo3 FROM food_listings WHERE food_id = ?");
    $photoStmt->bind_param("i", $food_id);
    $photoStmt->execute();
    $photoResult = $photoStmt->get_result();
    $existingPhotos = $photoResult->fetch_assoc();

    // Handle photo uploads
    $uploadDir = '../../../uploads/';
    $photo1 = $existingPhotos['photo1'];
    $photo2 = $existingPhotos['photo2'];
    $photo3 = $existingPhotos['photo3'];

    // Ensure upload directory exists
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            $response['message'] = 'Failed to create directory for image upload.';
            echo json_encode($response);
            exit;
        }
    }

    // Process new photo uploads
    for ($i = 1; $i <= 3; $i++) {
        $fileKey = "photo$i";
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $fileTmpName = $_FILES[$fileKey]['tmp_name'];
            $fileName = uniqid() . '-' . preg_replace('/[^a-zA-Z0-9.\-_]/', '_', basename($_FILES[$fileKey]['name']));
            $fileDestination = $uploadDir . $fileName;

            // Validate image type
            $fileMimeType = mime_content_type($fileTmpName);
            if (in_array($fileMimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
                // Delete old photo if exists
                $oldPhoto = $existingPhotos["photo$i"];
                if ($oldPhoto && file_exists($uploadDir . $oldPhoto)) {
                    unlink($uploadDir . $oldPhoto);
                }

                // Move new file
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    ${"photo$i"} = $fileName;
                } else {
                    $response['message'] = "Failed to upload image: " . $_FILES[$fileKey]['name'];
                    echo json_encode($response);
                    exit;
                }
            } else {
                $response['message'] = 'Invalid file type for: ' . $_FILES[$fileKey]['name'];
                echo json_encode($response);
                exit;
            }
        }
    }

    // Update the database query
    $stmt = $conn->prepare("UPDATE food_listings SET 
    food_name = ?,
    meal_type = ?,
    price = ?,
    description = ?,
    category = ?,
    photo1 = ?,
    photo2 = ?,
    photo3 = ?,
    diet_type_suitable = ?,
    health_goal_suitable = ?,
    allergens = ?,
    protein = ?,
    carbs = ?,
    fat = ?,
    calories = ?,
    category_id = ?
    WHERE food_id = ? AND kitchen_id = ?");

    if ($stmt) {
        $stmt->bind_param(
            "ssdssssssssddddiii",  // Updated type string to match parameters
            $itemName,          // s (string)
            $mealType,          // s (string)
            $price,            // d (double)
            $description,      // s (string)
            $pickupDelivery,   // s (string)
            $photo1,           // s (string)
            $photo2,           // s (string)
            $photo3,           // s (string)
            $dietType,         // s (string)
            $healthGoal,       // s (string)
            $allergens,        // s (string)
            $protein,          // d (double)
            $carbs,            // d (double)
            $fat,              // d (double)
            $totalCalories,    // d (double)
            $category_id,      // i (integer)
            $food_id,          // i (integer)
            $kitchen_id        // i (integer)
        );

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Item updated successfully!';
        } else {
            $response['message'] = 'Database update failed: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = 'Database statement preparation failed: ' . $conn->error;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
