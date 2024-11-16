<?php
include '../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 $phone = $_POST['phone'];
 $password = $_POST['password'];
 
 $stmt = $conn->prepare("SELECT rider_id, first_name, last_name, phone, password
                        FROM delivery_riders 
                        WHERE phone = ?");
 $stmt->bind_param("s", $phone);
 $stmt->execute();
 $stmt->store_result();
 
 if ($stmt->num_rows > 0) {
     $stmt->bind_result($rider_id, $first_name, $last_name, $db_phone, $db_password);
     $stmt->fetch();
     
     if (password_verify($password, $db_password)) {
         setcookie('rider_id', $rider_id, time() + (86400 * 30), "/");
         setcookie('rider_name', $first_name . ' ' . $last_name, time() + (86400 * 30), "/");
         echo json_encode(['success' => true]);
     } else {
         echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
     }
 } else {
     echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
 }
 
 $stmt->close();
 $conn->close();
}
?>