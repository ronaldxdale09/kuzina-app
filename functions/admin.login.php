<?php
include '../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $conn->prepare("SELECT id, username, password, full_name, role, status FROM admin WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($admin_id, $db_username, $db_password, $full_name, $role, $status);
        $stmt->fetch();
        
        if ($status === 'inactive') {
            echo json_encode(['success' => false, 'message' => 'Account is inactive']);
            exit;
        }
        
        // Update last login
        $update = $conn->prepare("UPDATE admin SET last_login = NOW() WHERE id = ?");
        $update->bind_param("i", $admin_id);
        $update->execute();
        
        // Set cookies instead of sessions (30 days expiration)
        setcookie('admin_id', $admin_id, time() + (86400 * 30), "/");
        setcookie('admin_name', $full_name, time() + (86400 * 30), "/");
        setcookie('admin_role', $role, time() + (86400 * 30), "/");
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
    }
    
    $stmt->close();
    $conn->close();
}
?>