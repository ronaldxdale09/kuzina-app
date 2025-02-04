<?php
include 'includes/header.php';

$user_id = $_COOKIE['user_id'];
$stmt = $conn->prepare("SELECT * FROM customers WHERE customer_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
?>

<link rel="stylesheet" href="assets/css/profile.css">
<?php include 'navbar/main.navbar.php'; ?>

<!-- Header End -->

<!-- Sidebar Start -->

<!-- Navigation Start -->
<?php include 'includes/sidebar.php'; ?>
<div class="profile-page">


   <main class="profile-content">
       <form id="profileForm" class="profile-form">
           <div class="form-group">
               <label>First Name</label>
               <input type="text" name="first_name" value="<?php echo htmlspecialchars($customer['first_name']); ?>" required>
           </div>

           <div class="form-group">
               <label>Last Name</label>
               <input type="text" name="last_name" value="<?php echo htmlspecialchars($customer['last_name']); ?>" required>
           </div>

           <div class="form-group">
               <label>Email</label>
               <input type="email" name="email" value="<?php echo htmlspecialchars($customer['email']); ?>" required>
           </div>

           <div class="form-group">
               <label>Phone</label>
               <input type="tel" name="phone" value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
           </div>

           <button type="submit" class="save-btn">Save Changes</button>
       </form>
   </main>

   <!-- Toast Notification -->
   <div id="toast" class="toast">
       <div class="toast-content">
           <i class='bx bx-check-circle'></i>
           <span>Profile updated successfully!</span>
       </div>
   </div>
</div>

<?php include 'includes/scripts.php'; ?>
<style>
/* Existing styles remain the same */
.profile-page {
   background: #f5f5f5;
   min-height: 100vh;
}

.app-header {
   background: #502121;
   padding: 15px;
   color: white;
}

.header-content {
   display: flex;
   align-items: center;
   gap: 15px;
}

.back-btn {
   color: white;
   font-size: 24px;
}

.profile-content {
   padding: 20px;
}

.form-group {
   margin-bottom: 20px;
}

.form-group label {
   display: block;
   margin-bottom: 8px;
   color: #333;
}

.form-group input,
.form-group textarea {
   width: 100%;
   padding: 12px;
   border: 1px solid #ddd;
   border-radius: 8px;
   font-size: 16px;
}

.save-btn {
   background: #502121;
   color: white;
   width: 100%;
   padding: 15px;
   border: none;
   border-radius: 8px;
   font-size: 16px;
   margin-top: 20px;
}
/* Toast Notification Styles */
.toast {
   position: fixed;
   bottom: -100px;
   left: 50%;
   transform: translateX(-50%);
   background: #333;
   color: white;
   padding: 16px 24px;
   border-radius: 12px;
   box-shadow: 0 4px 12px rgba(0,0,0,0.15);
   display: flex;
   align-items: center;
   justify-content: center;
   z-index: 1000;
   transition: bottom 0.3s ease-in-out;
}

.toast.show {
   bottom: 32px;
}

.toast-content {
   display: flex;
   align-items: center;
   gap: 12px;
}

.toast i {
   font-size: 24px;
   color: #4CAF50;
}

.loading-overlay {
   position: fixed;
   top: 0;
   left: 0;
   right: 0;
   bottom: 0;
   background: rgba(0,0,0,0.5);
   display: flex;
   align-items: center;
   justify-content: center;
   z-index: 999;
}

.loading-spinner {
   width: 40px;
   height: 40px;
   border: 4px solid #f3f3f3;
   border-top: 4px solid #502121;
   border-radius: 50%;
   animation: spin 1s linear infinite;
}

@keyframes spin {
   0% { transform: rotate(0deg); }
   100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
   const profileForm = document.getElementById('profileForm');
   const toast = document.getElementById('toast');

   function showLoading() {
       const overlay = document.createElement('div');
       overlay.className = 'loading-overlay';
       overlay.innerHTML = '<div class="loading-spinner"></div>';
       document.body.appendChild(overlay);
       return overlay;
   }

   function showToast() {
       toast.classList.add('show');
       setTimeout(() => {
           toast.classList.remove('show');
       }, 3000);
   }

   profileForm.addEventListener('submit', async function(e) {
       e.preventDefault();
       
       const loadingOverlay = showLoading();
       const formData = new FormData(this);
       
       try {
           const response = await fetch('functions/update_profile.php', {
               method: 'POST',
               body: formData
           });
           
           const data = await response.json();
           
           if (data.success) {
               showToast();
               setTimeout(() => {
                   window.location.reload();
               }, 2000);
           } else {
               alert(data.message || 'Failed to update profile');
           }
       } catch (error) {
           alert('An error occurred');
       } finally {
           loadingOverlay.remove();
       }
   });
});
</script>