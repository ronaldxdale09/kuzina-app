<?php 
include 'includes/header.php';

$tab = '';
if (isset($_GET['tab'])) {
    $tab = filter_var($_GET['tab']);
}

// Get statistics for each user type
$stats = [
    'kitchens' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN isApproved = 1 THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN isApproved = 0 THEN 1 ELSE 0 END) as pending
        FROM kitchens")),
    'riders' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN isApproved = 1 THEN 1 ELSE 0 END) as active,
        SUM(CASE WHEN isApproved = 0 THEN 1 ELSE 0 END) as pending
        FROM delivery_riders")),
    'customers' => mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM customers"))
];
?>

<link rel="stylesheet" type="text/css" href="assets/css/users.css" />
<link rel="stylesheet" type="text/css" href="assets/css/modal.css" />

<body>
    <?php include 'navbar/main.navbar.php'; ?>
    <?php include 'includes/sidebar.php'; ?>

    <main class="main-wrap users-page mb-xxl">
        <div class="users-list-page section-b-t">
            <!-- Stats Cards -->
            <div class="stats-container container">
                <div class="stats-card">
                    <i class='bx bx-store'></i>
                    <div class="stats-info">
                        <h3><?php echo $stats['kitchens']['total']; ?></h3>
                        <p>Total Kitchens</p>
                        <div class="stats-detail">
                            <span class="active"><?php echo $stats['kitchens']['active']; ?> Active</span>
                            <span class="pending"><?php echo $stats['kitchens']['pending']; ?> Pending</span>
                        </div>
                    </div>
                </div>
                <div class="stats-card">
                    <i class='bx bx-cycling'></i>
                    <div class="stats-info">
                        <h3><?php echo $stats['riders']['total']; ?></h3>
                        <p>Total Riders</p>
                        <div class="stats-detail">
                            <span class="active"><?php echo $stats['riders']['active']; ?> Active</span>
                            <span class="pending"><?php echo $stats['riders']['pending']; ?> Pending</span>
                        </div>
                    </div>
                </div>
                <div class="stats-card">
                    <i class='bx bx-user'></i>
                    <div class="stats-info">
                        <h3><?php echo $stats['customers']['total']; ?></h3>
                        <p>Total Customers</p>
                    </div>
                </div>
            </div>

            <!-- Tabs Section -->
            <ul class="nav nav-tabs" id="usersTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '') ? 'active' : ''; ?>" id="kitchen-tab"
                        data-bs-toggle="tab" href="#kitchen" role="tab"
                        aria-selected="<?php echo ($tab == '') ? 'true' : 'false'; ?>">
                        <i class='bx bx-store'></i>
                        <span>Kitchen Partners</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '1') ? 'active' : ''; ?>" id="rider-tab" data-bs-toggle="tab"
                        href="#rider" role="tab" aria-selected="<?php echo ($tab == '1') ? 'true' : 'false'; ?>">
                        <i class='bx bx-cycling'></i>
                        <span>Delivery Riders</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($tab == '2') ? 'active' : ''; ?>" id="customer-tab"
                        data-bs-toggle="tab" href="#customer" role="tab"
                        aria-selected="<?php echo ($tab == '2') ? 'true' : 'false'; ?>">
                        <i class='bx bx-user'></i>
                        <span>Customers</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content container">
                <!-- Kitchen Partners Tab -->
                <div class="tab-pane fade <?php echo ($tab == '') ? 'show active' : ''; ?>" id="kitchen"
                    role="tabpanel">
                    <div class="table-header">
                        <div class="table-title">
                            <i class='bx bx-store'></i>
                            <h3>Kitchen Partners</h3>
                        </div>
                        <div class="table-actions">
                            <div class="search-box">
                                <i class='bx bx-search'></i>
                                <input type="text" class="form-control" placeholder="Search kitchens...">
                            </div>
                            <div class="filter-box">
                                <select class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    $query = "SELECT * FROM kitchens ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>#{$row['kitchen_id']}</td>";
                        echo "<td>{$row['fname']} {$row['lname']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['phone']}</td>";
                        echo "<td class='address-cell'>{$row['address']}</td>";
                        echo "<td><span class='status-badge " . ($row['isApproved'] ? 'active' : 'pending') . "'>" . 
                             ($row['isApproved'] ? 'Active' : 'Pending') . "</span></td>";
                        echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                        echo "<td class='actions-cell'>
                                <div class='action-buttons'>
                                    <button class='btn-action view' onclick='viewUser({$row['kitchen_id']}, \"kitchen\")'>
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <button class='btn-action edit' onclick='editUser({$row['kitchen_id']}, \"kitchen\")'>
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class='btn-action delete' onclick='deleteUser({$row['kitchen_id']}, \"kitchen\")'>
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <div class="showing-entries">
                            Showing <span>1</span> to <span>10</span> of
                            <span><?php echo $stats['kitchens']['total']; ?></span> entries
                        </div>
                        <div class="pagination">
                            <button class="btn btn-sm" disabled><i class='bx bx-chevron-left'></i></button>
                            <button class="btn btn-sm active">1</button>
                            <button class="btn btn-sm">2</button>
                            <button class="btn btn-sm">3</button>
                            <button class="btn btn-sm"><i class='bx bx-chevron-right'></i></button>
                        </div>
                    </div>
                </div>

                <!-- Delivery Riders Tab -->
                <div class="tab-pane fade <?php echo ($tab == '1') ? 'show active' : ''; ?>" id="rider" role="tabpanel">
                    <div class="table-header">
                        <div class="table-title">
                            <i class='bx bx-cycling'></i>
                            <h3>Delivery Riders</h3>
                        </div>
                        <div class="table-actions">
                            <div class="search-box">
                                <i class='bx bx-search'></i>
                                <input type="text" class="form-control" placeholder="Search riders...">
                            </div>
                            <div class="filter-box">
                                <select class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active">Active</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Vehicle</th>
                                    <th>Status</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    $query = "SELECT * FROM delivery_riders ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>#{$row['rider_id']}</td>";
                        echo "<td>{$row['first_name']} {$row['last_name']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['phone']}</td>";
                        echo "<td>{$row['vehicle_type']}</td>";
                        echo "<td><span class='status-badge " . ($row['isApproved'] ? 'active' : 'pending') . "'>" . 
                             ($row['isApproved'] ? 'Active' : 'Pending') . "</span></td>";
                        echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                        echo "<td class='actions-cell'>
                                <div class='action-buttons'>
                                    <button class='btn-action view' onclick='viewUser({$row['rider_id']}, \"rider\")'>
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <button class='btn-action edit' onclick='editUser({$row['rider_id']}, \"rider\")'>
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class='btn-action delete' onclick='deleteUser({$row['rider_id']}, \"rider\")'>
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <div class="showing-entries">
                            Showing <span>1</span> to <span>10</span> of
                            <span><?php echo $stats['riders']['total']; ?></span> entries
                        </div>
                        <div class="pagination">
                            <button class="btn btn-sm" disabled><i class='bx bx-chevron-left'></i></button>
                            <button class="btn btn-sm active">1</button>
                            <button class="btn btn-sm">2</button>
                            <button class="btn btn-sm">3</button>
                            <button class="btn btn-sm"><i class='bx bx-chevron-right'></i></button>
                        </div>
                    </div>
                </div>

                <!-- Customers Tab -->
                <div class="tab-pane fade <?php echo ($tab == '2') ? 'show active' : ''; ?>" id="customer"
                    role="tabpanel">
                    <div class="table-header">
                        <div class="table-title">
                            <i class='bx bx-user'></i>
                            <h3>Customers</h3>
                        </div>
                        <div class="table-actions">
                            <div class="search-box">
                                <i class='bx bx-search'></i>
                                <input type="text" class="form-control" placeholder="Search customers...">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>Joined Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                    $query = "SELECT * FROM customers ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);
                    
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>#{$row['customer_id']}</td>";
                        echo "<td>{$row['first_name']} {$row['last_name']}</td>";
                        echo "<td>{$row['email']}</td>";
                        echo "<td>{$row['phone']}</td>";
                        echo "<td class='address-cell'>{$row['location']}</td>";
                        echo "<td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>";
                        echo "<td class='actions-cell'>
                                <div class='action-buttons'>
                                    <button class='btn-action view' onclick='viewUser({$row['customer_id']}, \"customer\")'>
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <button class='btn-action edit' onclick='editUser({$row['customer_id']}, \"customer\")'>
                                        <i class='bx bx-edit'></i>
                                    </button>
                                    <button class='btn-action delete' onclick='deleteUser({$row['customer_id']}, \"customer\")'>
                                        <i class='bx bx-trash'></i>
                                    </button>
                                </div>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <div class="showing-entries">
                            Showing <span>1</span> to <span>10</span> of
                            <span><?php echo $stats['customers']['total']; ?></span> entries
                        </div>
                        <div class="pagination">
                            <button class="btn btn-sm" disabled><i class='bx bx-chevron-left'></i></button>
                            <button class="btn btn-sm active">1</button>
                            <button class="btn btn-sm">2</button>
                            <button class="btn btn-sm">3</button>
                            <button class="btn btn-sm"><i class='bx bx-chevron-right'></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Enhanced User Details Modal -->
    <div class="modal fade" id="userDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="user-profile-header">
                        <img src="" alt="Profile" class="user-avatar-large">
                        <div class="user-info-main">
                            <h4 class="user-name"></h4>
                            <p class="user-email"></p>
                            <span class="badge"></span>
                        </div>
                    </div>
                    <div class="user-details-content">
                        <!-- Details will be loaded here via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary edit-user">Edit Details</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced search functionality
        document.querySelectorAll('.search-box input').forEach(input => {
            input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const tableBody = this.closest('.tab-pane').querySelector('tbody');
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        });

        // Status filter functionality
        document.querySelectorAll('.filter-box select').forEach(select => {
            select.addEventListener('change', function() {
                const status = this.value.toLowerCase();
                const tableBody = this.closest('.tab-pane').querySelector('tbody');
                const rows = tableBody.querySelectorAll('tr');

                rows.forEach(row => {
                    const statusCell = row.querySelector('.badge');
                    if (!status || statusCell.textContent.toLowerCase() === status) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });

        // Enhanced view details functionality
        document.querySelectorAll('.view-details').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.dataset.id;
                const userType = document.querySelector('.tab-pane.active').id;
                const modal = document.querySelector('#userDetailsModal');

                // AJAX call to get user details
                fetch(`get_user_details.php?id=${userId}&type=${userType}`)
                    .then(response => response.json())
                    .then(data => {
                        // Update modal content with user details
                        updateModalContent(modal, data);
                        new bootstrap.Modal(modal).show();
                    });
            });
        });

        function updateModalContent(modal, data) {
            // Update user profile section
            modal.querySelector('.user-avatar-large').src = data.photo || 'assets/images/default-avatar.png';
            modal.querySelector('.user-name').textContent = data.fname + ' ' + data.lname;
            modal.querySelector('.user-email').textContent = data.email;

            // Update badge status
            const badge = modal.querySelector('.badge');
            badge.className = 'badge ' + (data.isApproved ? 'bg-success' : 'bg-warning');
            badge.textContent = data.isApproved ? 'Active' : 'Pending';

            // Update details content
            let detailsHtml = '<div class="details-grid">';
            for (const [key, value] of Object.entries(data)) {
                if (!['password', 'photo'].includes(key)) {
                    detailsHtml += `
                        <div class="detail-item">
                            <label>${key.replace(/_/g, ' ').toUpperCase()}</label>
                            <span>${value || '-'}</span>
                        </div>`;
                }
            }
            detailsHtml += '</div>';

            modal.querySelector('.user-details-content').innerHTML = detailsHtml;
        }

        // [Previous tab navigation and delete user code remains the same...]
    });
    </script>

    <?php include 'includes/appbar.php'; ?>
    <?php include 'includes/scripts.php'; ?>
</body>

</html>