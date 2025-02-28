<?php
// Include necessary files
require_once '../connection/db.php';
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'token' => ''
];

try {
    
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Get OTP from POST data
    $otp = isset($_POST['otp']) ? trim($_POST['otp']) : '';
    $userId = isset($_POST['user_id']) ? (int)$_POST['user_id'] : 0;
    $userType = isset($_POST['user_type']) ? trim($_POST['user_type']) : '';
    
    // Validate inputs
    if (empty($otp)) {
        throw new Exception('OTP is required');
    }
    
    if (empty($userId) || empty($userType)) {
        throw new Exception('Invalid user information');
    }
    
    // Check if we have the right session variables
    if (!isset($_SESSION['otp_user_id']) || !isset($_SESSION['otp_user_type']) || !isset($_SESSION['reset_password'])) {
        throw new Exception('Session expired. Please restart the password reset process.');
    }
    
    // Verify that the user ID and type match what's in session
    if ($_SESSION['otp_user_id'] != $userId || $_SESSION['otp_user_type'] != $userType) {
        throw new Exception('Invalid session data');
    }
    
    // Query the database to get the stored OTP
    $table = 'otp_codes';
    $stmt = $conn->prepare("SELECT otp_code, expires_at FROM $table WHERE user_id = ? AND user_type = ? AND is_used = 0 ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("is", $userId, $userType);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('OTP not found or already used. Please request a new code.');
    }
    
    $otpData = $result->fetch_assoc();
    $storedOtp = $otpData['otp_code'];
    $expiresAt = new DateTime($otpData['expires_at']);
    $now = new DateTime();
    
    // Check if OTP has expired
    if ($now > $expiresAt) {
        throw new Exception('OTP has expired. Please request a new code.');
    }
    
    // Check if OTP matches
    if ($otp !== $storedOtp) {
        throw new Exception('Invalid OTP. Please try again.');
    }
    
    // OTP is valid - mark it as used
    $updateStmt = $conn->prepare("UPDATE $table SET is_used = 1, used_at = NOW() WHERE user_id = ? AND user_type = ? AND otp_code = ?");
    $updateStmt->bind_param("iss", $userId, $userType, $otp);
    $updateStmt->execute();
    
    // Generate a reset token
    $resetToken = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
    
    // Store reset token in database
    $tokenTable = 'password_reset_tokens';
    $tokenStmt = $conn->prepare("INSERT INTO $tokenTable (user_id, user_type, token, expires_at) VALUES (?, ?, ?, ?)");
    $tokenStmt->bind_param("isss", $userId, $userType, $resetToken, $expiry);
    $tokenStmt->execute();
    
    // Store token in session for added security
    $_SESSION['reset_token'] = $resetToken;
    $_SESSION['token_expiry'] = $expiry;
    
    // Return success response with token
    $response = [
        'success' => true,
        'message' => 'OTP verified successfully',
        'token' => $resetToken
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("OTP Verification Error: " . $e->getMessage());
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($updateStmt)) $updateStmt->close();
    if (isset($tokenStmt)) $tokenStmt->close();
    if (isset($conn)) $conn->close();
}

// Send JSON response
echo json_encode($response);
?>