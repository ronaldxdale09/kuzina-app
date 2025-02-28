<?php
include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/login.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<style>
  .forgot-card {
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
</style>

<body>
  <main class="main-wrap login-page login mb-xxl">
    <div class="header">
      <img src="assets/images/banner/bg-pattern2.png" class="bg-img" alt="pattern" />
      <br>
      <div class="header-content">
        <div class="badge">
          <i class='bx bx-lock-open-alt'></i>
          <span>Password Recovery</span>
        </div>
      </div>
    </div>

    <section class="login-section p-0">
      <div class="info-card">
        <img class="logo" style="margin-bottom:20px" src="assets/images/logo/logo-w2.png" alt="logo" /> <br>
        <h2>Recover Your Account</h2>
        <p class="font-sm content-color">We'll help you reset your password and get back to enjoying delicious,
          nutritious meals from your favorite home cooks.</p>
      </div>

      <form id="forgot-password-form" class="custom-form form" method="POST">
        <div class="form-header">
          <h2>Forgot Password</h2>
          <p>Enter your phone number below and we'll send you a verification code.</p>
        </div>

        <div class="form-group-wrapper">
          <div class="input-box">
            <input type="number" id="phone" name="phone" placeholder="Phone Number (e.g. 09123456789)"
              class="form-control" autocomplete="off" required />
            <i class="bx bx-phone"></i>
          </div>
        </div>

        <button type="submit" id="submit-btn" class="btn-solid btn">
          <i class='bx bx-message-detail'></i>
          Send OTP Code
        </button>

        <a href="index.php" class="back-button">
          <i class='bx bx-arrow-back'></i>
          Back to Login
        </a>

        <div id="success-message" class="success-message">
          <i class='bx bx-check-circle'></i>
          <h3>Verification Code Sent!</h3>
          <p>We've sent a verification code to your phone. Please check your SMS messages.</p>
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
      // Form submission handling
      document.getElementById('forgot-password-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const phone = document.getElementById('phone').value;

        if (!phone) {
          showErrorModal('Please enter your phone number');
          return;
        }

        // Enhanced validation for phone number
        if (phone.length < 10) {
          showErrorModal('Please enter a valid phone number');
          return;
        }

        // Show loading overlay
        document.getElementById('loading-overlay').style.display = 'flex';
        const formData = new FormData(this);

        // Send request to backend with improved error handling
        fetch('functions/forgot_pass_otp.php', {
            method: 'POST',
            body: formData
          })
          .then(response => {
            if (!response.ok) {
              throw new Error(`Server error: ${response.status}`);
            }
            return response.json();
          })
          .then(data => {
            // Hide loading overlay
            document.getElementById('loading-overlay').style.display = 'none';

            if (data.success) {
              // Improved success message display
              document.querySelector('.form-group-wrapper').style.display = 'none';
              document.querySelector('.form-header').style.display = 'none';
              document.getElementById('submit-btn').style.display = 'none';
              document.getElementById('success-message').style.display = 'block';

              // If development mode, show OTP for testing
              if (data.test_otp) {
                console.log('Test OTP:', data.test_otp);
              }

              // If the response contains a redirect URL, redirect after a delay
              if (data.redirect) {
                setTimeout(() => {
                  window.location.href = data.redirect;
                }, 2000);
              }
            } else {
              showErrorModal(data.message || 'An error occurred. Please try again.');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            document.getElementById('loading-overlay').style.display = 'none';
            showErrorModal('Failed to connect to the server. Please try again later.');
          });
      });

      // Enhanced Error Modal Functions
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

      // Close error modal when clicking outside or pressing Escape
      window.addEventListener('click', function(event) {
        const errorModal = document.getElementById('error-modal');
        if (event.target === errorModal) {
          errorModal.classList.remove('show');
        }
      });

      // Add keyboard support for closing modal with Escape key
      document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
          document.getElementById('error-modal').classList.remove('show');
        }
      });

      // Improved phone number formatting and validation
      const phoneInput = document.getElementById('phone');
      if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
          // Remove non-numeric characters
          this.value = this.value.replace(/\D/g, '');

          // Limit to reasonable length
          if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
          }

          // Auto-format for Philippine numbers
          if (this.value.length > 0) {
            // First, normalize input to strip any existing formatting
            const normalizedInput = this.value.replace(/\D/g, '');

            // Handle formatting based on input pattern
            if (normalizedInput.startsWith('63') && normalizedInput.length > 2) {
              // Philippines international format (6391234...)
              // Keep as is
            } else if (normalizedInput.startsWith('0') && normalizedInput.length > 1) {
              // Philippines local format (0912345...)
              // Keep as is
            } else if (normalizedInput.startsWith('9') && normalizedInput.length <= 10) {
              // Philippines mobile without prefix (9123456...)
              // Keep as is - user may be entering a local number
            }
          }
        });

        // Add focus to phone input on page load
        phoneInput.focus();
      }
    });
  </script>

  <?php include 'includes/script.php'; ?>
</body>

</html>