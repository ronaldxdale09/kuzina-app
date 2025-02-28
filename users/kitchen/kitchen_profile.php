<?php
include 'includes/header.php';

// Fetch kitchen details
$kitchen_id = $_COOKIE['kitchen_id'];
$stmt = $conn->prepare("SELECT * FROM kitchens WHERE kitchen_id = ?");
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$kitchen = $stmt->get_result()->fetch_assoc();
?>

<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<link rel="stylesheet" type="text/css" href="assets/css/kitchen_profile.css">
<?php include 'navbar/main.navbar.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<div class="profile-page">

    <main class="profile-content">
        <form id="profileForm" class="profile-form" enctype="multipart/form-data">
            <div class="profile-photo-section">
                <div class="profile-photo">
                    <img src="<?php echo $kitchen['photo'] ? '../../uploads/kitchen_photos/' . $kitchen['photo'] : 'assets/images/default-kitchen.png'; ?>"
                        alt="Kitchen Profile" id="profilePreview">
                    <div class="photo-overlay">
                        <i class='bx bx-camera'></i>
                        <span>Change Photo</span>
                    </div>
                </div>
                <input type="file" id="photoInput" name="photo" accept="image/*" hidden>
            </div>

            <div class="form-group">
                <label for="fname">First Name</label>
                <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($kitchen['fname']); ?>" required>
            </div>

            <div class="form-group">
                <label for="lname">Last Name</label>
                <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($kitchen['lname']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($kitchen['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($kitchen['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" rows="3" required><?php echo htmlspecialchars($kitchen['address']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="description">Kitchen Description</label>
                <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($kitchen['description']); ?></textarea>
            </div>

            <button type="submit" class="save-btn">Save Changes</button>
        </form>
    </main>
</div>

<?php include 'includes/appbar.php'; ?>
<?php include 'includes/scripts.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileForm = document.getElementById('profileForm');
        const photoInput = document.getElementById('photoInput');
        const profilePreview = document.getElementById('profilePreview');
        const photoOverlay = document.querySelector('.photo-overlay');

        // Handle photo selection
        photoOverlay.addEventListener('click', () => photoInput.click());

        photoInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });

        // Handle form submission
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('functions/update_kitchen_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Profile updated successfully!');
                    } else {
                        alert(data.message || 'Failed to update profile');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the profile');
                });
        });
    });
</script>