<?php include 'includes/header.php'; ?>

<div class="bug-report-page">
    <?php include 'navbar/main.navbar.php'; ?>

    <!-- Header End -->

    <!-- Sidebar Start -->

    <!-- Navigation Start -->
    <?php include 'includes/sidebar.php'; ?>

    <main class="form-content">
    <h3> Bugs/Error Report </h3> <hr> 
    <form id="bugReportForm">
            <div class="form-group">
                <label>User Type</label>
                <select name="user_type" required>
                    <option value="customer">Customer</option>
                    <option value="kitchen">Kitchen</option>
                    <option value="rider">Rider</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="form-group">
                <label>Page Location</label>
                <input type="text" name="page_location" placeholder="e.g. Menu Page, Checkout" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" rows="4" placeholder="Describe the bug in detail..." required></textarea>
            </div>

            <div class="form-group">
                <label>Screenshot</label>
                <div class="file-upload">
                    <input type="file" id="screenshot" name="screenshot" accept="image/*" required>
                    <label for="screenshot">
                        <i class='bx bx-upload'></i>
                        <span>Choose Screenshot</span>
                    </label>
                    <div id="preview"></div>
                </div>
            </div>

            <button type="submit" class="submit-btn">Submit Report</button>
        </form>
    </main>

    <div id="toast" class="toast">
        <div class="toast-content">
            <i class='bx bx-check-circle'></i>
            <span>Bug report submitted successfully!</span>
        </div>
    </div>
</div>

<style>
    .bug-report-page {
        padding-bottom: 20px;
    }

    .app-header {
        background: #502121;
        padding: 15px;
        color: white;
    }

    .form-content {
        padding: 20px;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
    }

    .file-upload {
        border: 2px dashed #ddd;
        padding: 20px;
        text-align: center;
        border-radius: 8px;
    }

    #preview img {
        max-width: 100%;
        margin-top: 10px;
        border-radius: 8px;
    }

    .submit-btn {
        background: #502121;
        color: white;
        width: 100%;
        padding: 15px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
    }

    .toast {
        position: fixed;
        bottom: -100px;
        left: 50%;
        transform: translateX(-50%);
        background: #333;
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        transition: 0.3s;
    }

    .toast.show {
        bottom: 32px;
    }
</style>

<?php include 'includes/scripts.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('bugReportForm');
        const toast = document.getElementById('toast');

        document.getElementById('screenshot').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    document.getElementById('preview').innerHTML = `<img src="${e.target.result}">`;
                };
                reader.readAsDataURL(file);
            }
        });

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('functions/submit_bug_report.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    toast.classList.add('show');
                    setTimeout(() => {
                        toast.classList.remove('show');
                        window.location.href = 'homepage.php';
                    }, 2000);
                } else {
                    alert(data.message || 'Failed to submit report');
                }
            } catch (error) {
                alert('An error occurred');
            }
        });
    });
</script>