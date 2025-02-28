<?php
// Include necessary files
require_once '../connection/db.php';
require_once 'otp_functions.php';
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => ''
];

try {
    // Start session to access stored OTP data
    session_start();
    
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Check if we have the right session variables
    if (!isset($_SESSION['otp_phone']) || !isset($_SESSION['otp_user_id']) || !isset($_SESSION['otp_user_type'])) {
        throw new Exception('Session expired. Please restart the password reset process.');
    }
    
    // Get user info from session
    $userId = $_SESSION['otp_user_id'];
    $userType = $_SESSION['otp_user_type'];
    $phone = $_SESSION['otp_phone'];
    
    // Generate a new 6-digit OTP
    $otpCode = generateOTP(6);
    
    // Store OTP in database
    if (!storeOTP($userId, $userType, $phone, $otpCode)) {
        throw new Exception('Failed to generate OTP. Please try again.');
    }
    
    // Format phone number for international sending
    $internationalPhone = $phone;
    
    // If it starts with 0, replace with +63 (Philippines)
    if (substr($phone, 0, 1) === '0') {
        $internationalPhone = '+63' . substr($phone, 1);
    } 
    // If it starts with 9, add +63 prefix (Philippines)
    else if (substr($phone, 0, 1) === '9') {
        $internationalPhone = '+63' . $phone;
    }
    // If no country code, assume Philippines
    else if (!preg_match('/^\+/', $phone)) {
        $internationalPhone = '+63' . $phone;
    }
    
    // Send OTP via SMS
    $smsResult = sendOTPViaSMS($internationalPhone, $otpCode);
    
    if ($smsResult['success']) {
        // Return success response
        $response = [
            'success' => true,
            'message' => 'OTP resent successfully. Please check your phone.'
        ];
    } else {
        // If SMS failed but OTP was stored, still provide a way forward for development
        error_log("SMS Resend Error: " . json_encode($smsResult));
        
        if (isset($_SERVER['HTTP_HOST']) && ($_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == '127.0.0.1')) {
            // For local development, show OTP in response
            $response = [
                'success' => true,
                'message' => 'OTP regenerated for testing: ' . $otpCode,
                'test_otp' => $otpCode  // Only include in development
            ];
        } else {
            throw new Exception('Failed to send OTP: ' . $smsResult['message']);
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Resend OTP Error: " . $e->getMessage());
}

// Send JSON response
echo json_encode($response);
?>