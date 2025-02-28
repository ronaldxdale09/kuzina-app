<?php
require_once 'vendor/autoload.php';

/**
 * 
 * OTP Helper Functions
 * 
 * Contains utility functions for generating, storing, and sending OTP codes
 */

/**
 * Generate a random OTP of specified length
 * 
 * @param int $length Length of the OTP (default: 6)
 * @return string The generated OTP
 */
function generateOTP($length = 6)
{
    // Generate cryptographically secure random bytes
    $bytes = random_bytes($length);

    // Convert to numeric digits only
    $otp = '';
    for ($i = 0; $i < $length; $i++) {
        $otp .= ord($bytes[$i]) % 10; // Modulo 10 to get a digit (0-9)
    }

    return $otp;
}

/**
 * Store OTP in database
 * 
 * @param int $userId User ID
 * @param string $userType Type of user (customer, kitchen, rider)
 * @param string $phone Phone number
 * @param string $otpCode The OTP code
 * @return bool True if OTP was stored successfully, false otherwise
 */
function storeOTP($userId, $userType, $phone, $otpCode)
{
    global $conn;

    // Set expiry time (5 minutes from now)
    $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Insert OTP into database
    $table = 'otp_codes';

    // Check if table exists, create if not
    $tableCheckQuery = "SHOW TABLES LIKE '$table'";
    $tableCheckResult = $conn->query($tableCheckQuery);

    if ($tableCheckResult->num_rows === 0) {
        // Table doesn't exist, create it
        $createTableQuery = "CREATE TABLE $table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_type VARCHAR(20) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            otp_code VARCHAR(10) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            is_used TINYINT(1) DEFAULT 0,
            used_at DATETIME NULL
        )";

        if (!$conn->query($createTableQuery)) {
            error_log("Failed to create OTP table: " . $conn->error);
            return false;
        }
    }

    // Also check if password_reset_tokens table exists
    $tokenTableCheckQuery = "SHOW TABLES LIKE 'password_reset_tokens'";
    $tokenTableCheckResult = $conn->query($tokenTableCheckQuery);

    if ($tokenTableCheckResult->num_rows === 0) {
        // Table doesn't exist, create it
        $createTokenTableQuery = "CREATE TABLE password_reset_tokens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            user_type VARCHAR(20) NOT NULL,
            token VARCHAR(100) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            is_used TINYINT(1) DEFAULT 0,
            used_at DATETIME NULL
        )";

        if (!$conn->query($createTokenTableQuery)) {
            error_log("Failed to create token table: " . $conn->error);
            // Continue anyway since we might not need this table right now
        }
    }

    // Insert OTP into database
    $stmt = $conn->prepare("INSERT INTO $table (user_id, user_type, phone, otp_code, expires_at) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $userId, $userType, $phone, $otpCode, $expiresAt);

    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

/**
 * Send OTP via SMS using Vonage API
 * 
 * @param string $phone Phone number
 * @param string $otpCode The OTP code
 * @return array Success status and message
 *//**
 * Send OTP via SMS using Vonage API
 * 
 * @param string $phone Phone number
 * @param string $otpCode The OTP code
 * @return array Success status and message
 */
function sendOTPViaSMS($phone, $otpCode)
{
    // Development mode flag - set to true to bypass actual SMS sending
    $dev_mode = true; // Change to false in production
    
    // Format phone number for sending
    $formattedPhone = formatPhoneNumber($phone);

    // Log the attempt
    error_log("Sending OTP to: {$formattedPhone}, Code: {$otpCode}");

    // If in development mode, return success without sending SMS
    if ($dev_mode) {
        error_log("DEV MODE: OTP: {$otpCode} for phone: {$formattedPhone} (SMS not actually sent)");
        
        // Log the dev mode SMS to the database for consistency
        logSMSStatus($formattedPhone, 'DEV_MODE', true);
        
        return [
            'success' => true,
            'message' => 'OTP sent successfully (development mode)',
            'test_otp' => $otpCode
        ];
    }

    try {
        // Vonage API Configuration
        $apiKey = 'f0557e71'; // Your actual Vonage API key
        $apiSecret = 'osqkxzzPZ8DFruJH'; // Your actual Vonage API secret
        $brandName = 'KUZINA';
     
        $message = "Your Kuzina verification code is: {$otpCode}. This code will expire in 5 minutes.";

        // Initialize the Vonage client
        $basic = new \Vonage\Client\Credentials\Basic($apiKey, $apiSecret);
        $client = new \Vonage\Client($basic);

        // Send SMS message
        $response = $client->sms()->send(
            new \Vonage\SMS\Message\SMS($formattedPhone, $brandName, $message)
        );

        // Get the first message (there should only be one)
        $messageObj = $response->current();

        // Check if the message was sent successfully
        if ($messageObj->getStatus() == 0) {
            $messageId = $messageObj->getMessageId();
            error_log("SMS sent successfully to {$formattedPhone}, ID: {$messageId}");

            // Log success to database
            logSMSStatus($formattedPhone, $messageId, true);

            return [
                'success' => true,
                'message' => 'OTP sent successfully',
                'message_id' => $messageId
            ];
        } else {
            $status = $messageObj->getStatus();
            $errorMessage = "Failed with status: " . $status;
            error_log("SMS Error: {$errorMessage} for {$formattedPhone}");

            // Log failure to database
            logSMSStatus($formattedPhone, null, false, $status);

            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $errorMessage
            ];
        }
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
        error_log("SMS Exception: {$errorMessage} for {$formattedPhone}");

        // Log exception to database
        logSMSStatus($formattedPhone, null, false, null, $errorMessage);

        return [
            'success' => false,
            'message' => 'Failed to send SMS: ' . $errorMessage
        ];
    }
}

/**
 * Format phone number for SMS sending
 * 
 * @param string $phone The phone number
 * @return string The formatted phone number
 */
function formatPhoneNumber($phone)
{
    // Remove any non-numeric characters except plus
    $phone = preg_replace('/[^0-9+]/', '', $phone);

    // If number already has a plus sign, remove it for consistent formatting
    if (substr($phone, 0, 1) === '+') {
        $phone = substr($phone, 1);
    }

    // Format for Philippines numbers
    // If starts with 0, remove it and add country code
    if (substr($phone, 0, 1) === '0') {
        return '63' . substr($phone, 1);
    }

    // If it's a number starting with 9 (without leading 0)
    if (substr($phone, 0, 1) === '9') {
        return '63' . $phone;
    }

    // If already has country code (63)
    if (substr($phone, 0, 2) === '63') {
        return $phone;
    }

    // Default case - just add country code (assuming Filipino number)
    return '63' . $phone;
}

/**
 * Log SMS status to database for monitoring
 * 
 * @param string $phone Phone number
 * @param string|null $messageId Message ID if successful
 * @param bool $success Whether sending was successful
 * @param string|null $errorCode Error code if unsuccessful
 * @param string|null $errorMessage Error message if unsuccessful
 * @return bool Success of logging operation
 */
function logSMSStatus($phone, $messageId = null, $success = false, $errorCode = null, $errorMessage = null)
{
    global $conn;

    // Ensure SMS logs table exists
    $table = 'sms_logs';

    // Check if table exists, create if not
    $tableCheckQuery = "SHOW TABLES LIKE '{$table}'";
    $tableCheckResult = $conn->query($tableCheckQuery);

    if ($tableCheckResult->num_rows === 0) {
        // Table doesn't exist, create it
        $createTableQuery = "CREATE TABLE {$table} (
            id INT AUTO_INCREMENT PRIMARY KEY,
            phone VARCHAR(20) NOT NULL,
            message_id VARCHAR(50) NULL,
            success TINYINT(1) NOT NULL DEFAULT 0,
            error_code VARCHAR(50) NULL,
            error_message TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";

        if (!$conn->query($createTableQuery)) {
            error_log("Failed to create SMS logs table: " . $conn->error);
            return false;
        }
    }

    try {
        // Insert log record
        $stmt = $conn->prepare("INSERT INTO {$table} (phone, message_id, success, error_code, error_message) 
                                 VALUES (?, ?, ?, ?, ?)");

        $successInt = $success ? 1 : 0;
        $stmt->bind_param("ssiss", $phone, $messageId, $successInt, $errorCode, $errorMessage);

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    } catch (\Exception $e) {
        error_log("Failed to log SMS status: " . $e->getMessage());
        return false;
    }
}
