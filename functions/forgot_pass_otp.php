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
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get phone number from POST data
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    // Validate phone number
    if (empty($phone)) {
        throw new Exception('Phone number is required');
    }

    // Format phone number (remove any non-numeric characters)
    $phone = preg_replace('/[^0-9]/', '', $phone);

    // Find user in database
    $userData = findUserByPhone($conn, $phone);

    if (!$userData) {
        throw new Exception('Phone number not found in our records');
    }

    // Extract user information
    $userId = $userData['id'];
    $userType = $userData['type'];
    $userName = $userData['name'];

    // Generate a 6-digit OTP
    $otpCode = generateOTP(6);

    // Store OTP in database
    if (!storeOTP($userId, $userType, $phone, $otpCode)) {
        throw new Exception('Failed to generate OTP. Please try again.');
    }

    // Send OTP via SMS - let the SMS function handle phone formatting
    $smsResult = sendOTPViaSMS($phone, $otpCode);

    if ($smsResult['success']) {
        // Set session values
        $_SESSION['otp_user_id'] = $userId;
        $_SESSION['otp_user_type'] = $userType;
        $_SESSION['otp_phone'] = $phone;
        $_SESSION['otp_name'] = $userName;
        $_SESSION['reset_password'] = true; // Flag to indicate password reset flow

        // Mask the phone number for display
        $maskedPhone = maskPhoneNumber($phone);

        // Include test OTP in the response if in development mode
        if (isset($smsResult['test_otp'])) {
            $response = [
                'success' => true,
                'message' => 'OTP generated for testing: ' . $smsResult['test_otp'],
                'test_otp' => $smsResult['test_otp'],
                'phone' => $maskedPhone,
                'user_id' => $userId,
                'user_type' => $userType,
                'redirect' => 'otp_verification.php?id=' . $userId . '&type=' . $userType
            ];
        } else {
            // Regular success response
            $response = [
                'success' => true,
                'message' => 'OTP sent successfully. Please check your phone.',
                'phone' => $maskedPhone,
                'user_id' => $userId,
                'user_type' => $userType,
                'redirect' => 'otp_verification.php?id=' . $userId . '&type=' . $userType
            ];
        }
    } else {
        // Return error from SMS sending
        error_log("SMS Send Error: " . json_encode($smsResult));
        throw new Exception('Failed to send OTP: ' . $smsResult['message']);
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log("Forgot Password OTP Error: " . $e->getMessage());
}

/**
 * Find a user by phone number across different tables
 * 
 * @param mysqli $conn Database connection
 * @param string $phone Phone number
 * @return array|null User data or null if not found
 */
function findUserByPhone($conn, $phone)
{
    // Check in customers table
    $stmt = $conn->prepare("SELECT customer_id as id, first_name, last_name FROM customers WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $stmt->close();

        return [
            'id' => $data['id'],
            'type' => 'customer',
            'name' => $data['first_name'] . ' ' . $data['last_name']
        ];
    }

    $stmt->close();

    // Check in kitchens table
    $stmt = $conn->prepare("SELECT kitchen_id as id, fname, lname FROM kitchens WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $stmt->close();

        return [
            'id' => $data['id'],
            'type' => 'kitchen',
            'name' => $data['fname'] . ' ' . $data['lname']
        ];
    }

    $stmt->close();

    // Check in delivery_riders table
    $stmt = $conn->prepare("SELECT rider_id as id, first_name, last_name FROM delivery_riders WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        $stmt->close();

        return [
            'id' => $data['id'],
            'type' => 'rider',
            'name' => $data['first_name'] . ' ' . $data['last_name']
        ];
    }

    $stmt->close();

    // No user found
    return null;
}

/**
 * Mask a phone number for display (show only last 4 digits)
 * 
 * @param string $phone The phone number to mask
 * @return string The masked phone number
 */
function maskPhoneNumber($phone)
{
    $length = strlen($phone);
    if ($length <= 4) return $phone;

    $lastFour = substr($phone, -4);
    $masked = str_repeat('*', $length - 4) . $lastFour;

    // Format with country code if applicable
    if (substr($phone, 0, 1) === '0') {
        return '0' . str_repeat('*', $length - 5) . $lastFour;
    } else if (substr($phone, 0, 1) === '9' && $length >= 10) {
        return '+63 9' . str_repeat('*', $length - 6) . $lastFour;
    }

    return $masked;
}

// Send JSON response
echo json_encode($response);
