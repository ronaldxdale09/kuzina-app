<?php
include '../../../connection/db.php';

$response = ['success' => false, 'message' => ''];

try {
    $kitchen_id = $_COOKIE['kitchen_id'] ?? null;
    if (!$kitchen_id) {
        throw new Exception('Kitchen ID not found');
    }

    // Validate and sanitize input
    $fname = trim($_POST['fname'] ?? '');
    $lname = trim($_POST['lname'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Handle photo upload
    $photo = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../../../uploads/kitchen_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = uniqid() . '-' . $_FILES['photo']['name'];
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $filePath)) {
            $photo = $fileName;
        }
    }

    // Update database
    $sql = "UPDATE kitchens SET 
            fname = ?, 
            lname = ?, 
            email = ?, 
            phone = ?, 
            address = ?, 
            description = ?";
    $params = [$fname, $lname, $email, $phone, $address, $description];
    $types = "ssssss";

    if ($photo) {
        $sql .= ", photo = ?";
        $params[] = $photo;
        $types .= "s";
    }

    $sql .= " WHERE kitchen_id = ?";
    $params[] = $kitchen_id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);

    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully';
    } else {
        throw new Exception('Failed to update profile');
    }

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>