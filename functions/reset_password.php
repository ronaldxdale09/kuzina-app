<?php
// Include necessary files
require_once '../connection/db.php';

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Get inputs from POST data
    $token = isset($_POST['token']) ? trim($_POST['token']) : '';
    $newPassword = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirmPassword = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    
    // Validate inputs
    if (empty($token)) {
        throw new Exception('Invalid reset token');
    }
    
    if (empty($newPassword) || empty($confirmPassword)) {
        throw new Exception('All fields are required');
    }
    
    if ($newPassword !== $confirmPassword) {
        throw new Exception('Passwords do not match');
    }
    
    // Validate password strength
    if (strlen($newPassword) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    
    if (!preg_match('/[A-Z]/', $newPassword)) {
        throw new Exception('Password must contain at least one uppercase letter');
    }
    
    if (!preg_match('/[a-z]/', $newPassword)) {
        throw new Exception('Password must contain at least one lowercase letter');
    }
    
    if (!preg_match('/[0-9]/', $newPassword)) {
        throw new Exception('Password must contain at least one number');
    }
    
    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $newPassword)) {
        throw new Exception('Password must contain at least one special character');
    }
    
    // Check if token exists in database and is not expired
    $tokenTable = 'password_reset_tokens';
    
    // Ensure the token table exists
    ensureTokenTableExists($conn);
    
    $stmt = $conn->prepare("SELECT user_id, user_type, expires_at FROM $tokenTable WHERE token = ? AND is_used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Invalid or expired reset token');
    }
    
    $tokenData = $result->fetch_assoc();
    $userId = $tokenData['user_id'];
    $userType = $tokenData['user_type'];
    $expiresAt = new DateTime($tokenData['expires_at']);
    $now = new DateTime();
    
    // Check if token has expired
    if ($now > $expiresAt) {
        throw new Exception('Reset token has expired. Please restart the password reset process.');
    }
    
    // Optional session check - only if you're using session tokens
    // This can be removed if you're not storing tokens in session
    if (isset($_SESSION['reset_token'])) {
        if ($_SESSION['reset_token'] !== $token) {
            error_log("Token mismatch: Session token does not match provided token");
            // We'll continue anyway since we have a valid token from the database
        }
    } else {
        // Just log this as informational - the database token is the source of truth
        error_log("No reset_token found in session, but continuing with valid database token");
    }
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update the user's password based on user type
    switch ($userType) {
        case 'customer':
            $updateTable = 'customers';
            $idField = 'customer_id';
            break;
        case 'kitchen':
            $updateTable = 'kitchens';
            $idField = 'kitchen_id';
            break;
        case 'rider':
            $updateTable = 'delivery_riders';
            $idField = 'rider_id';
            break;
        default:
            throw new Exception('Invalid user type');
    }
    
    // Use prepared statement with table and column names as part of the query
    $updateQuery = "UPDATE $updateTable SET password = ? WHERE $idField = ?";
    $updateStmt = $conn->prepare($updateQuery);
    
    if (!$updateStmt) {
        error_log("Failed to prepare update query: " . $conn->error);
        throw new Exception('Failed to update password due to database error');
    }
    
    $updateStmt->bind_param("si", $hashedPassword, $userId);
    if (!$updateStmt->execute()) {
        error_log("Failed to execute update: " . $updateStmt->error);
        throw new Exception('Failed to update password');
    }
    
    if ($updateStmt->affected_rows === 0) {
        error_log("No rows updated for user ID $userId of type $userType");
        throw new Exception('Failed to update password - no matching user found');
    }
    
    // Mark the token as used
    $tokenUpdateStmt = $conn->prepare("UPDATE $tokenTable SET is_used = 1, used_at = NOW() WHERE token = ?");
    $tokenUpdateStmt->bind_param("s", $token);
    $tokenUpdateStmt->execute();
    
    // Clear session variables related to password reset
    // Only unset variables that are likely to exist
    $sessionVarsToUnset = [
        'otp_user_id', 'otp_user_type', 'otp_phone', 'otp_name',
        'reset_password', 'reset_token', 'token_expiry'
    ];
    
    foreach ($sessionVarsToUnset as $var) {
        if (isset($_SESSION[$var])) {
            unset($_SESSION[$var]);
        }
    }
    
    // Return success response
    $response = [
        'success' => true,
        'message' => 'Password has been reset successfully',
        'redirect' => 'login.php'  // Redirect to login page after successful reset
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Password Reset Error: " . $e->getMessage());
} finally {
    // Close any open statements
    if (isset($stmt) && $stmt instanceof mysqli_stmt) $stmt->close();
    if (isset($updateStmt) && $updateStmt instanceof mysqli_stmt) $updateStmt->close();
    if (isset($tokenUpdateStmt) && $tokenUpdateStmt instanceof mysqli_stmt) $tokenUpdateStmt->close();
}

/**
 * Ensure the token table exists
 * 
 * @param mysqli $conn Database connection
 */
function ensureTokenTableExists($conn) {
    $table = 'password_reset_tokens';
    $tableCheckQuery = "SHOW TABLES LIKE '$table'";
    $tableCheckResult = $conn->query($tableCheckQuery);
    
    if ($tableCheckResult->num_rows === 0) {
        // Table doesn't exist, create it
        $createTableQuery = "CREATE TABLE $table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_type VARCHAR(20) NOT NULL,
            token VARCHAR(100) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            is_used TINYINT(1) DEFAULT 0,
            used_at DATETIME NULL
        )";
        
        if (!$conn->query($createTableQuery)) {
            error_log("Failed to create token table: " . $conn->error);
            throw new Exception('Failed to setup necessary database tables');
        }
    }
}

// Send JSON response
echo json_encode($response);
?>