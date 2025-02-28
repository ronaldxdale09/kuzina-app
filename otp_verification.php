<?php 
include 'includes/header.php'; 


// Redirect if session variables are not set
if (!isset($_SESSION['otp_user_id']) || !isset($_SESSION['otp_user_type']) || !isset($_SESSION['reset_password'])) {
    header("Location: forgot-password.php");
    exit();
}

// Get user info from session
$userId = $_SESSION['otp_user_id'];
$userType = $_SESSION['otp_user_type'];
$phone = isset($_SESSION['otp_phone']) ? $_SESSION['otp_phone'] : '';
$maskedPhone = '';

// Mask the phone number for display (show only last 4 digits)
if (!empty($phone)) {
    $length = strlen($phone);
    if ($length <= 4) {
        $maskedPhone = $phone;
    } else {
        $lastFour = substr($phone, -4);
        
        // Format with country code if applicable
        if (substr($phone, 0, 1) === '0') {
            $maskedPhone = '0' . str_repeat('*', $length - 5) . $lastFour;
        } else if (substr($phone, 0, 1) === '9' && $length >= 10) {
            $maskedPhone = '+63 9' . str_repeat('*', $length - 6) . $lastFour;
        } else {
            $maskedPhone = str_repeat('*', $length - 4) . $lastFour;
        }
    }
}
?>


<link rel="stylesheet" href="assets/css/login.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<style>
.otp-container {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
    padding: 30px;
    margin-top: 20px;
    max-width: 450px;
    width: 100%;
}

.form-header {
    margin-bottom: 30px;
    text-align: center;
}

.form-header h2 {
    font-size: 1.5rem;
    color: #502121;
    margin-bottom: 10px;
}

.form-header p {
    color: #666;
    line-height: 1.5;
}

.otp-inputs {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin: 25px 0;
}

.otp-inputs input {
    width: 45px;
    height: 55px;
    text-align: center;
    font-size: 22px;
    font-weight: 600;
    border-radius: 8px;
    border: 2px solid #ddd;
    background: #f8f8f8;
    transition: all 0.3s;
    padding: 0;
    -webkit-appearance: none;
    -moz-appearance: textfield;
    appearance: textfield;
}

.otp-inputs input:focus {
    border-color: #502121;
    box-shadow: 0 0 0 3px rgba(80, 33, 33, 0.2);
    outline: none;
}

/* Fix for iOS devices */
.otp-inputs input::-webkit-outer-spin-button,
.otp-inputs input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.timer {
    text-align: center;
    margin: 15px 0;
    color: #666;
    font-size: 14px;
}

.timer span {
    font-weight: bold;
    color: #502121;
}

.resend-btn {
    background: none;
    border: none;
    color: #502121;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
    margin-left: 5px;
    transition: opacity 0.3s;
}

.resend-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    text-decoration: none;
}

.back-button {
    display: inline-flex;
    align-items: center;
    margin-top: 20px;
    color: #502121;
    font-weight: 600;
    transition: all 0.3s ease;
}

.back-button i {
    margin-right: 8px;
    font-size: 1.2rem;
}

.back-button:hover {
    opacity: 0.8;
    transform: translateX(-3px);
}

.success-message {
    display: none;
    text-align: center;
    padding: 20px;
    color: #4CAF50;
    background-color: rgba(76, 175, 80, 0.1);
    border-radius: 10px;
    margin-top: 20px;
}

.success-message i {
    font-size: 48px;
    display: block;
    margin-bottom: 15px;
}

#loading-overlay {
    position: fixed;
    z-index: 9999;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: none;
    justify-content: center;
    align-items: center;
}

#loading-spinner {
    text-align: center;
    background-color: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
}

.error-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.error-modal.show {
    display: block;
}

.error-modal-content {
    background-color: #fff;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 400px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    position: relative;
}

.error-close {
    position: absolute;
    right: 15px;
    top: 10px;
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
}

.error-modal h2 {
    color: #e74c3c;
    margin-bottom: 15px;
}

/* Fix for mobile devices */
@media (max-width: 480px) {
    .otp-inputs {
        gap: 5px;
    }
    
    .otp-inputs input {
        width: 40px;
        height: 50px;
        font-size: 20px;
    }
}
</style>
<body>
    <main class="main-wrap login-page login mb-xxl">
        <div class="header">
            <img src="assets/images/banner/bg-pattern2.png" class="bg-img" alt="pattern" />
            <br>
            <div class="header-content">
                <div class="badge">
                    <i class='bx bx-check-shield'></i>
                    <span>Verify Your Identity</span>
                </div>
            </div>
        </div>

        <section class="login-section p-0">
            <div class="info-card">
                <img class="logo" style="margin-bottom:20px" src="assets/images/logo/logo-w2.png" alt="logo" /> <br>
                <h2>OTP Verification</h2>
                <p class="font-sm content-color">We've sent a verification code to your phone. Please enter it below to continue.</p>
            </div>

            <form id="otp-form" class="custom-form form" method="POST">
                <div class="otp-container">
                    <div class="form-header">
                        <h2>Enter Verification Code</h2>
                        <p>We've sent a 6-digit code to <?php echo htmlspecialchars($maskedPhone); ?></p>
                    </div>

                    <div class="otp-inputs">
                        <input type="number" maxlength="1" class="otp-digit" data-index="1" pattern="[0-9]" inputmode="numeric">
                        <input type="number" maxlength="1" class="otp-digit" data-index="2" pattern="[0-9]" inputmode="numeric">
                        <input type="number" maxlength="1" class="otp-digit" data-index="3" pattern="[0-9]" inputmode="numeric">
                        <input type="number" maxlength="1" class="otp-digit" data-index="4" pattern="[0-9]" inputmode="numeric">
                        <input type="number" maxlength="1" class="otp-digit" data-index="5" pattern="[0-9]" inputmode="numeric">
                        <input type="number" maxlength="1" class="otp-digit" data-index="6" pattern="[0-9]" inputmode="numeric">
                    </div>
                    
                    <input type="hidden" id="complete-otp" name="otp">
                    <input type="hidden" id="user-id" name="user_id" value="<?php echo htmlspecialchars($userId); ?>">
                    <input type="hidden" id="user-type" name="user_type" value="<?php echo htmlspecialchars($userType); ?>">

                    <div class="timer">
                        Code expires in <span id="countdown">05:00</span>
                        <button type="button" id="resend-btn" class="resend-btn" disabled>Resend Code</button>
                    </div>

                    <button type="submit" id="verify-btn" class="btn-solid btn" disabled>
                        <i class='bx bx-check-circle'></i>
                        Verify Code
                    </button>

                    <a href="forgot-password.php" class="back-button">
                        <i class='bx bx-arrow-back'></i>
                        Back
                    </a>
                </div>

                <div id="success-message" class="success-message">
                    <i class='bx bx-check-circle'></i>
                    <h3>Verification Successful!</h3>
                    <p>You will be redirected to reset your password.</p>
                </div>
            </form>
        </section>
    </main>

    <!-- Loading Overlay -->
    <div id="loading-overlay">
        <div id="loading-spinner">
            <img src="assets/loader/loader5.gif" alt="Loading...">
        </div>
    </div>

    <!-- Error Modal -->
    <div id="error-modal" class="error-modal">
        <div class="error-modal-content">
            <span id="error-close" class="error-close">&times;</span>
            <h2>Error</h2>
            <p id="error-message"></p>
        </div>
    </div>

    <!-- Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle OTP input
        const otpInputs = document.querySelectorAll('.otp-digit');
        const completeOtpInput = document.getElementById('complete-otp');
        const verifyBtn = document.getElementById('verify-btn');
        
        // Auto-focus first input
        otpInputs[0].focus();
        
        // Handle input in OTP fields
        otpInputs.forEach((input, index) => {
            // Restrict input to only numbers
            input.addEventListener('input', function(e) {
                // Ensure only one digit
                if (this.value.length > 1) {
                    this.value = this.value.slice(0, 1);
                }
                
                // Move to next input if value is entered
                if (this.value !== '' && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
                
                // Update the complete OTP value
                updateCompleteOtp();
            });
            
            // Handle backspace
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    if (this.value === '' && index > 0) {
                        otpInputs[index - 1].focus();
                    }
                }
            });
            
            // Handle paste event
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text');
                
                // If pasted data is exactly 6 digits, distribute across inputs
                if (/^\d{6}$/.test(pastedData)) {
                    otpInputs.forEach((input, i) => {
                        input.value = pastedData.charAt(i);
                    });
                    
                    // Focus on the last input
                    otpInputs[otpInputs.length - 1].focus();
                    
                    // Update the complete OTP
                    updateCompleteOtp();
                }
            });
        });
        
        function updateCompleteOtp() {
            let otp = '';
            otpInputs.forEach(input => {
                otp += input.value;
            });
            
            completeOtpInput.value = otp;
            
            // Enable verify button if all digits are filled
            verifyBtn.disabled = otp.length !== 6;
        }
        
        // Handle countdown timer
        let timerMinutes = 5;
        let timerSeconds = 0;
        const countdownEl = document.getElementById('countdown');
        const resendBtn = document.getElementById('resend-btn');
        
        const countdown = setInterval(() => {
            if (timerSeconds === 0) {
                if (timerMinutes === 0) {
                    clearInterval(countdown);
                    resendBtn.disabled = false;
                    countdownEl.textContent = "Expired";
                    return;
                }
                timerMinutes--;
                timerSeconds = 59;
            } else {
                timerSeconds--;
            }
            
            countdownEl.textContent = `${timerMinutes.toString().padStart(2, '0')}:${timerSeconds.toString().padStart(2, '0')}`;
        }, 1000);
        
        // Handle resend button
        resendBtn.addEventListener('click', function() {
            // Show loading overlay
            document.getElementById('loading-overlay').style.display = 'flex';
            
            // Get user data from hidden inputs
            const userId = document.getElementById('user-id').value;
            const userType = document.getElementById('user-type').value;
            
            // Create form data
            const formData = new FormData();
            formData.append('user_id', userId);
            formData.append('user_type', userType);
            formData.append('resend', true);
            
            // Send request to resend OTP
            fetch('functions/resend_otp.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-overlay').style.display = 'none';
                
                if (data.success) {
                    // Reset the timer
                    timerMinutes = 1;
                    timerSeconds = 0;
                    countdownEl.textContent = "01:00";
                    
                    // Disable the resend button again
                    resendBtn.disabled = true;
                    
                    // Reset OTP inputs
                    otpInputs.forEach(input => {
                        input.value = '';
                    });
                    completeOtpInput.value = '';
                    verifyBtn.disabled = true;
                    
                    // Focus on the first input
                    otpInputs[0].focus();
                    
                    // Show success message
                    showSuccessModal("New verification code sent");
                } else {
                    // Show error message
                    showErrorModal(data.message || 'Failed to resend verification code');
                }
            })
            .catch(error => {
                document.getElementById('loading-overlay').style.display = 'none';
                showErrorModal('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });
        
        // Handle form submission
        document.getElementById('otp-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get the complete OTP
            const otp = completeOtpInput.value;
            
            if (otp.length !== 6) {
                showErrorModal('Please enter all 6 digits of the verification code.');
                return;
            }
            
            // Show loading overlay
            document.getElementById('loading-overlay').style.display = 'flex';
            
            // Send OTP to server for verification
            const formData = new FormData(this);
            
            fetch('functions/verify_otp.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-overlay').style.display = 'none';
                
                if (data.success) {
                    // Show success message
                    document.getElementById('otp-form').style.height = 'auto';
                    document.getElementById('success-message').style.display = 'block';
                    document.querySelector('.otp-container').style.display = 'none';
                    
                    // Redirect to reset password page after a delay
                    setTimeout(() => {
                        window.location.href = 'reset-password.php?token=' + data.token;
                    }, 3000);
                } else {
                    showErrorModal(data.message || 'Invalid verification code. Please try again.');
                }
            })
            .catch(error => {
                document.getElementById('loading-overlay').style.display = 'none';
                showErrorModal('An error occurred. Please try again.');
                console.error('Error:', error);
            });
        });
        
        // Error Modal Functions
        function showErrorModal(message) {
            const errorModal = document.getElementById('error-modal');
            const errorMessage = document.getElementById('error-message');
            document.querySelector('.error-modal-content h2').textContent = 'Error';
            document.querySelector('.error-modal-content h2').style.color = '#e74c3c';
            errorMessage.textContent = message;
            errorModal.classList.add('show');
        }
        
        function showSuccessModal(message) {
            const errorModal = document.getElementById('error-modal');
            const errorMessage = document.getElementById('error-message');
            document.querySelector('.error-modal-content h2').textContent = 'Success';
            document.querySelector('.error-modal-content h2').style.color = '#4CAF50';
            errorMessage.textContent = message;
            errorModal.classList.add('show');
        }
        
        // Close error modal when clicking the X
        document.getElementById('error-close').addEventListener('click', function() {
            document.getElementById('error-modal').classList.remove('show');
        });
        
        // Close error modal when clicking outside
        window.addEventListener('click', function(event) {
            const errorModal = document.getElementById('error-modal');
            if (event.target === errorModal) {
                errorModal.classList.remove('show');
            }
        });
    });
    </script>

    <?php include 'includes/script.php';?>
</body>

</html>