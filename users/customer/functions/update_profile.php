<?php
include '../../../connection/db.php';

$response = ['success' => false, 'message' => ''];

try {
    $user_id = $_COOKIE['user_id'] ?? null;
    if (!$user_id) throw new Exception('Not authenticated');

    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    $sql = "UPDATE customers SET 
            first_name = ?, 
            last_name = ?, 
            email = ?, 
            phone = ?, 
            address = ?
            WHERE customer_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", 
        $first_name, 
        $last_name, 
        $email, 
        $phone, 
        $address,
        $user_id
    );

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