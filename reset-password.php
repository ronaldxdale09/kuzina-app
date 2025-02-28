<?php 
include 'includes/header.php'; 


// Get token from URL
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Validate token - check if token is in session or query database to verify token
$validToken = false;

if (!empty($token)) {
    // Option 1: Check if token matches session token
    if (isset($_SESSION['reset_token']) && $_SESSION['reset_token'] === $token) {
        $validToken = true;
    } else {
        // Option 2: Query database to verify token
        // This is a more secure approach as it doesn't rely on session
        require_once 'connection/db.php';
        
        $tokenTable = 'password_reset_tokens';
        $stmt = $conn->prepare("SELECT user_id, user_type, expires_at FROM $tokenTable WHERE token = ? AND is_used = 0");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $tokenData = $result->fetch_assoc();
            $expiresAt = new DateTime($tokenData['expires_at']);
            $now = new DateTime();
            
            // Check if token has not expired
            if ($now <= $expiresAt) {
                $validToken = true;
                
                // Store user info in session if not already set
                if (!isset($_SESSION['otp_user_id'])) {
                    $_SESSION['otp_user_id'] = $tokenData['user_id'];
                    $_SESSION['otp_user_type'] = $tokenData['user_type'];
                    $_SESSION['reset_token'] = $token;
                }
            }
        }
        
        if (isset($stmt)) $stmt->close();
        if (isset($conn)) $conn->close();
    }
}

// Redirect to forgot password page if token is invalid
if (!$validToken) {
    header("Location: forgot-password.php");
    exit();
}
?>

<link rel="stylesheet" href="assets/css/login.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<style>
.reset-container {
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

.password-requirement {
    margin-top: 20px;
    padding: 15px;
    background-color: #f8f8f8;
    border-radius: 10px;
    border-left: 4px solid #502121;
}

.password-requirement h3 {
    font-size: 14px;
    margin-bottom: 10px;
    color: #502121;
}

.requirement-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.requirement-list li {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
    font-size: 13px;
    color: #666;
}

.requirement-list li i {
    margin-right: 8px;
    font-size: 16px;
}

.requirement-list li.valid {
    color: #4CAF50;
}

.requirement-list li.valid i {
    color: #4CAF50;
}

.requirement-list li.invalid {
    color: #666;
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

.input-box.valid-input i.bx-check-circle {
    display: block;
    color: #4CAF50;
}

.input-box.invalid-input i.bx-x-circle {
    display: block;
    color: #e74c3c;
}

.input-box i.validation-icon {
    display: none;
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
}

.input-box {
    position: relative;
}

.input-box .toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #777;
    transition: color 0.3s;
}

.input-box .toggle-password:hover {
    color: #502121;
}
</style>

<body>
    <main class="main-wrap login-page login mb-xxl">
        <div class="header">
            <img src="assets/images/banner/bg-pattern2.png" class="bg-img" alt="pattern" />
            <br>
            <div class="header-content">
                <div class="badge">
                    <i class='bx bx-lock'></i>
                    <span>Reset Password</span>
                </div>
            </div>
        </div>

        <section class="login-section p-0">
            <div class="info-card">
                <img class="logo" style="margin-bottom:20px" src="assets/images/logo/logo-w2.png" alt="logo" /> <br>
                <h2>Create New Password</h2>
                <p class="font-sm content-color">Set a new secure password for your account. Make sure it's strong and easy for you to remember.</p>
            </div>

            <form id="reset-password-form" class="custom-form form" method="POST">
                <div class="reset-container">
                    <div class="form-header">
                        <h2>Reset Your Password</h2>
                        <p>Your new password must be different from previously used passwords.</p>
                    </div>

                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="input-box">
                        <input type="password" id="new-password" name="new_password" placeholder="New Password" required
                            class="form-control" autocomplete="new-password" />
                        <i class="bx bx-lock"></i>
                        <i class="bx bx-show toggle-password"></i>
                        <i class="bx bx-check-circle validation-icon"></i>
                        <i class="bx bx-x-circle validation-icon"></i>
                    </div>

                    <div class="input-box">
                        <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm Password" required
                            class="form-control" autocomplete="new-password" />
                        <i class="bx bx-lock-alt"></i>
                        <i class="bx bx-show toggle-password"></i>
                        <i class="bx bx-check-circle validation-icon"></i>
                        <i class="bx bx-x-circle validation-icon"></i>
                    </div>

                    <div class="password-requirement">
                        <h3>Password Requirements:</h3>
                        <ul class="requirement-list">
                            <li id="length-check"><i class="bx bx-circle"></i> At least 8 characters long</li>
                            <li id="uppercase-check"><i class="bx bx-circle"></i> At least one uppercase letter</li>
                            <li id="lowercase-check"><i class="bx bx-circle"></i> At least one lowercase letter</li>
                            <li id="number-check"><i class="bx bx-circle"></i> At least one number</li>
                            <li id="special-check"><i class="bx bx-circle"></i> At least one special character</li>
                            <li id="match-check"><i class="bx bx-circle"></i> Passwords match</li>
                        </ul>
                    </div>

                    <button type="submit" id="reset-btn" class="btn-solid btn" disabled>
                        <i class='bx bx-check-shield'></i>
                        Reset Password
                    </button>

                    <a href="index.php" class="back-button">
                        <i class='bx bx-arrow-back'></i>
                        Back to Login
                    </a>
                </div>

                <div id="success-message" class="success-message">
                    <i class='bx bx-check-circle'></i>
                    <h3>Password Reset Successful!</h3>
                    <p>Your password has been reset successfully. You can now login with your new password.</p>
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
        const newPasswordInput = document.getElementById('new-password');
        const confirmPasswordInput = document.getElementById('confirm-password');
        const resetBtn = document.getElementById('reset-btn');
        
        // Password requirement checks
        const lengthCheck = document.getElementById('length-check');
        const uppercaseCheck = document.getElementById('uppercase-check');
        const lowercaseCheck = document.getElementById('lowercase-check');
        const numberCheck = document.getElementById('number-check');
        const specialCheck = document.getElementById('special-check');
        const matchCheck = document.getElementById('match-check');
        
        // Toggle password visibility
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('bx-show');
                    this.classList.add('bx-hide');
                } else {
                    input.type = 'password';
                    this.classList.remove('bx-hide');
                    this.classList.add('bx-show');
                }
            });
        });
        
        // Password requirements validation
        function validatePassword() {
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            // Length check
            if (password.length >= 8) {
                lengthCheck.classList.add('valid');
                lengthCheck.classList.remove('invalid');
                lengthCheck.querySelector('i').classList.remove('bx-circle');
                lengthCheck.querySelector('i').classList.add('bx-check-circle');
            } else {
                lengthCheck.classList.remove('valid');
                lengthCheck.classList.add('invalid');
                lengthCheck.querySelector('i').classList.remove('bx-check-circle');
                lengthCheck.querySelector('i').classList.add('bx-circle');
            }
            
            // Uppercase check
            if (/[A-Z]/.test(password)) {
                uppercaseCheck.classList.add('valid');
                uppercaseCheck.classList.remove('invalid');
                uppercaseCheck.querySelector('i').classList.remove('bx-circle');
                uppercaseCheck.querySelector('i').classList.add('bx-check-circle');
            } else {
                uppercaseCheck.classList.remove('valid');
                uppercaseCheck.classList.add('invalid');
                uppercaseCheck.querySelector('i').classList.remove('bx-check-circle');
                uppercaseCheck.querySelector('i').classList.add('bx-circle');
            }
            
            // Lowercase check
            if (/[a-z]/.test(password)) {
                lowercaseCheck.classList.add('valid');
                lowercaseCheck.classList.remove('invalid');
                lowercaseCheck.querySelector('i').classList.remove('bx-circle');
                lowercaseCheck.querySelector('i').classList.add('bx-check-circle');
            } else {
                lowercaseCheck.classList.remove('valid');
                lowercaseCheck.classList.add('invalid');
                lowercaseCheck.querySelector('i').classList.remove('bx-check-circle');
                lowercaseCheck.querySelector('i').classList.add('bx-circle');
            }
            
            // Number check
            if (/\d/.test(password)) {
                numberCheck.classList.add('valid');
                numberCheck.classList.remove('invalid');
                numberCheck.querySelector('i').classList.remove('bx-circle');
                numberCheck.querySelector('i').classList.add('bx-check-circle');
            } else {
                numberCheck.classList.remove('valid');
                numberCheck.classList.add('invalid');
                numberCheck.querySelector('i').classList.remove('bx-check-circle');
                numberCheck.querySelector('i').classList.add('bx-circle');
            }
            
            // Special character check
            if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                specialCheck.classList.add('valid');
                specialCheck.classList.remove('invalid');
                specialCheck.querySelector('i').classList.remove('bx-circle');
                specialCheck.querySelector('i').classList.add('bx-check-circle');
            } else {
                specialCheck.classList.remove('valid');
                specialCheck.classList.add('invalid');
                specialCheck.querySelector('i').classList.remove('bx-check-circle');
                specialCheck.querySelector('i').classList.add('bx-circle');
            }
            
            // Password match check
            if (password && confirmPassword && password === confirmPassword) {
                matchCheck.classList.add('valid');
                matchCheck.classList.remove('invalid');
                matchCheck.querySelector('i').classList.remove('bx-circle');
                matchCheck.querySelector('i').classList.add('bx-check-circle');
            } else {
                matchCheck.classList.remove('valid');
                matchCheck.classList.add('invalid');
                matchCheck.querySelector('i').classList.remove('bx-check-circle');
                matchCheck.querySelector('i').classList.add('bx-circle');
            }
            
            // Check if all requirements are met
            const allRequirementsMet = 
                password.length >= 8 && 
                /[A-Z]/.test(password) && 
                /[a-z]/.test(password) && 
                /\d/.test(password) && 
                /[!@#$%^&*(),.?":{}|<>]/.test(password) && 
                (password === confirmPassword) && 
                password !== '';
            
            // Enable/disable submit button
            resetBtn.disabled = !allRequirementsMet;
            
            // Update input validation UI
            if (password) {
                if (password.length >= 8 && 
                    /[A-Z]/.test(password) && 
                    /[a-z]/.test(password) && 
                    /\d/.test(password) && 
                    /[!@#$%^&*(),.?":{}|<>]/.test(password)) {
                    newPasswordInput.parentElement.classList.add('valid-input');
                    newPasswordInput.parentElement.classList.remove('invalid-input');
                } else {
                    newPasswordInput.parentElement.classList.remove('valid-input');
                    newPasswordInput.parentElement.classList.add('invalid-input');
                }
            } else {
                newPasswordInput.parentElement.classList.remove('valid-input');
                newPasswordInput.parentElement.classList.remove('invalid-input');
            }
            
            if (confirmPassword) {
                if (password === confirmPassword) {
                    confirmPasswordInput.parentElement.classList.add('valid-input');
                    confirmPasswordInput.parentElement.classList.remove('invalid-input');
                } else {
                    confirmPasswordInput.parentElement.classList.remove('valid-input');
                    confirmPasswordInput.parentElement.classList.add('invalid-input');
                }
            } else {
                confirmPasswordInput.parentElement.classList.remove('valid-input');
                confirmPasswordInput.parentElement.classList.remove('invalid-input');
            }
        }
        
        // Add event listeners for password validation
        newPasswordInput.addEventListener('input', validatePassword);
        confirmPasswordInput.addEventListener('input', validatePassword);
        
        // Handle form submission
        document.getElementById('reset-password-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const password = newPasswordInput.value;
            const confirmPassword = confirmPasswordInput.value;
            
            if (password !== confirmPassword) {
                showErrorModal('Passwords do not match');
                return;
            }
            
            // Show loading overlay
            document.getElementById('loading-overlay').style.display = 'flex';
            
            // Send form data to the server
            const formData = new FormData(this);
            
            fetch('functions/reset_password.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loading-overlay').style.display = 'none';
                
                if (data.success) {
                    // Show success message
                    document.getElementById('reset-password-form').style.height = 'auto';
                    document.getElementById('success-message').style.display = 'block';
                    document.querySelector('.reset-container').style.display = 'none';
                    
                    // Redirect to login page after a delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 3000);
                } else {
                    showErrorModal(data.message || 'Failed to reset password. Please try again.');
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