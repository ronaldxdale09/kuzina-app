<?php
include '../../../connection/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $customer_id = (int)$_POST['customer_id'];
   $kitchen_id = (int)$_POST['kitchen_id'];
   $message = trim($_POST['message']);
   $sender_role = $_POST['sender_role'];

   if (!empty($message) && in_array($sender_role, ['customer', 'kitchen'])) {
       $stmt = $conn->prepare("INSERT INTO kitchen_customer_messages 
           (customer_id, kitchen_id, message, sender_role) 
           VALUES (?, ?, ?, ?)");
       $stmt->bind_param("iiss", $customer_id, $kitchen_id, $message, $sender_role);
       
       $success = $stmt->execute();
       
       // Update read status for previous messages
       if ($success) {
           $other_role = $sender_role === 'customer' ? 'kitchen' : 'customer';
           $read_stmt = $conn->prepare("UPDATE kitchen_customer_messages 
               SET is_read = 1 
               WHERE customer_id = ? AND kitchen_id = ? AND sender_role = ?");
           $read_stmt->bind_param("iis", $customer_id, $kitchen_id, $other_role);
           $read_stmt->execute();
       }
       
       echo json_encode(['success' => $success]);
   } else {
       http_response_code(400);
       echo json_encode(['error' => 'Invalid message or sender role']);
   }
} else {
   http_response_code(405);
   echo json_encode(['error' => 'Method not allowed']);
}