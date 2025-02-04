<?php
include '../../../connection/db.php';

$response = ['success' => false, 'message' => ''];

try {
   $user_id = $_COOKIE['user_id'] ?? null;
   if (!$user_id) throw new Exception('Not authenticated');

   $user_type = $_POST['user_type'];
   $page_location = trim($_POST['page_location']);
   $description = trim($_POST['description']);

   // Create bug_reports directory if it doesn't exist
   $uploadDir = '../../../uploads/bug_reports/';
   if (!file_exists($uploadDir)) {
       mkdir($uploadDir, 0777, true);
   }

   $screenshot = null;
   if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
       $fileName = uniqid() . '-' . $_FILES['screenshot']['name'];
       $filePath = $uploadDir . $fileName;

       // Verify image type
       $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
       $fileType = mime_content_type($_FILES['screenshot']['tmp_name']);
       
       if (!in_array($fileType, $allowedTypes)) {
           throw new Exception('Invalid file type. Please upload JPG, PNG or GIF');
       }

       if (move_uploaded_file($_FILES['screenshot']['tmp_name'], $filePath)) {
           $screenshot = $fileName;
       } else {
           throw new Exception('Failed to upload screenshot');
       }
   }

   $stmt = $conn->prepare("INSERT INTO bug_reports (user_id, user_type, page_location, description, screenshot) VALUES (?, ?, ?, ?, ?)");
   $stmt->bind_param("issss", $user_id, $user_type, $page_location, $description, $screenshot);

   if ($stmt->execute()) {
       $response['success'] = true;
       $response['message'] = 'Bug report submitted successfully';
   } else {
       throw new Exception('Failed to submit report');
   }

} catch (Exception $e) {
   $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>