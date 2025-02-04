<?php
$kitchen_id = $_COOKIE['kitchen_id'] ?? null;

if (!$kitchen_id) {
    echo "Kitchen ID not found. Please log in.";
    exit();
}
// Fetch all menu items for the kitchen with extended details
$query = "SELECT 
    food_id, 
    food_name, 
    description, 
    price, 
    meal_type, 
    photo1,
    diet_type_suitable,
    health_goal_suitable,
    allergens,
    protein,
    carbs,
    fat,
    calories,
    category,
    listed,
    available,
    isApproved  /* Add this line */
FROM food_listings 
WHERE available = 1 AND kitchen_id = ?
ORDER BY created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$result = $stmt->get_result();

$menuItems = [
    'all' => [],
    'breakfast' => [],
    'lunch' => [],
    'dinner' => [],
    'snacks' => []  // Added snacks category
];

while ($row = $result->fetch_assoc()) {
    // Process the data before storing
    $row['diet_types'] = !empty($row['diet_type_suitable']) ?
        array_map('trim', explode(',', $row['diet_type_suitable'])) : [];

    $row['health_goals'] = !empty($row['health_goal_suitable']) ?
        array_map('trim', explode(',', $row['health_goal_suitable'])) : [];

    $row['allergen_list'] = !empty($row['allergens']) && $row['allergens'] !== 'None' ?
        array_map('trim', explode(',', $row['allergens'])) : [];

    // Convert numerical values to appropriate format
    $row['price'] = number_format($row['price'], 2);
    $row['protein'] = is_numeric($row['protein']) ? $row['protein'] . 'g' : $row['protein'];
    $row['carbs'] = is_numeric($row['carbs']) ? $row['carbs'] . 'g' : $row['carbs'];
    $row['fat'] = is_numeric($row['fat']) ? $row['fat'] . 'g' : $row['fat'];

    // Add to all items
    $menuItems['all'][] = $row;

    // Add to specific meal type category
    $mealType = strtolower($row['meal_type']);
    if (isset($menuItems[$mealType])) {
        $menuItems[$mealType][] = $row;
    }
}

// Add total counts for each category
$menuCounts = array_map('count', $menuItems);

// Clean up resources
$stmt->close();
$conn->close();
// Function to render menu items for a specific category
function renderMenuItems($items)
{
    if (empty($items)) {
        echo "<p>No items found.</p>";
        return;
    }

    foreach ($items as $item): ?>
        <!-- Modified menu item structure -->
        <div id="menu-item-<?= $item['food_id'] ?>" class="menu-item">
            <div class="menu-content">
                <div class="menu-image">
                    <img src="../../uploads/<?= htmlspecialchars($item['photo1']) ?>"
                        alt="<?= htmlspecialchars($item['food_name']) ?>" />
                </div>
                <div class="menu-info">
                    <h5><?= htmlspecialchars($item['food_name']) ?></h5>

                    <div class="menu-badges">
                        <span class="badge meal-type"><?= htmlspecialchars($item['meal_type']) ?></span>
                        <span class="badge category"><?= htmlspecialchars($item['category']) ?></span>
                        <!-- Add approval status badge -->
                        <span class="badge approval-status <?= $item['isApproved'] ? 'approved' : 'pending' ?>">
                            <?= $item['isApproved'] ? 'Approved' : 'Pending Approval' ?>
                        </span>
                    </div>

                    <p class="description">
                        <?= nl2br(htmlspecialchars(substr($item['description'], 0, 100) . (strlen($item['description']) > 100 ? '...' : ''))); ?>
                    </p>

                    <div class="nutrition-summary">
                        <span class="nutrition-item">
                            <i class='bx bx-bowl-hot'></i> <?= htmlspecialchars($item['calories']) ?> cal
                        </span>
                        <span class="nutrition-item">
                            <i class='bx bx-donate-heart'></i> <?= htmlspecialchars($item['protein']) ?>g protein
                        </span>
                    </div>

                    <div class="price">PHP <?= number_format($item['price'], 2) ?></div>

                    <div class="action-buttons">
                        <button class="action-btn edit" onclick="window.location.href='add_menu.php?food_id=<?= $item['food_id'] ?>'">
                            <i class='bx bx-edit'></i> Edit
                        </button>
                        <button class="action-btn remove" onclick="openRemoveModal('<?= $item['food_id'] ?>', '<?= htmlspecialchars($item['food_name']) ?>', '<?= number_format($item['price'], 2) ?>')">
                            <i class='bx bx-trash'></i> Remove
                        </button>
                        <button class="action-btn availability <?= $item['listed'] ? 'available' : 'unavailable' ?>"
                            onclick="toggleAvailability(<?= $item['food_id'] ?>, '<?= htmlspecialchars($item['food_name']) ?>', <?= $item['listed'] ?>)">
                            <i class='bx <?= $item['listed'] ? 'bx-check-circle' : 'bx-x-circle' ?>'></i>
                            <?= $item['listed'] ? 'Listed' : 'Unlisted' ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

<?php endforeach;
} ?>

<style>
    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
        background-color: #fff;
        margin: 15% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 500px;
        position: relative;
    }

    .close-modal {
        position: absolute;
        right: 20px;
        top: 10px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #666;
    }

    .close-modal:hover {
        color: #333;
    }

    .modal h2 {
        margin-bottom: 15px;
        color: #333;
    }

    .modal-item-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin: 15px 0;
    }

    .modal-item-info p {
        margin: 8px 0;
        color: #333;
    }

    .modal-item-info strong {
        display: inline-block;
        width: 120px;
        color: #666;
    }

    .btn-cancel,
    .btn-confirm {
        padding: 8px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        margin-top: 10px;
    }

    .btn-cancel {
        background-color: #e9ecef;
        color: #333;
        margin-right: 10px;
    }

    .btn-confirm {
        background-color: #502121;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #dee2e6;
    }

    .btn-confirm:hover {
        background-color: #3c1818;
    }

    /* Loading state */
    .loading-spinner {
        text-align: center;
        padding: 20px;
    }

    .loading-spinner i {
        font-size: 40px;
        color: #502121;
    }
</style>

<!-- Main End -->
<div id="removeModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal()">&times;</span>
        <h2>Confirm Removal</h2>
        <p>Are you sure you want to remove the following item?</p>
        <div class="modal-item-info">
            <p><strong>Food ID:</strong> <span id="modalFoodId"></span></p>
            <p><strong>Food Name:</strong> <span id="modalFoodName"></span></p>
            <p><strong>Price:</strong> PHP <span id="modalFoodPrice"></span></p>
        </div>
        <button class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button class="btn-confirm" onclick="confirmRemoval()">Confirm</button>
    </div>
</div>

<!-- Listing Status Modal -->
<div id="listingStatusModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeListingModal()">&times;</span>
        <h2>Update Listing Status</h2>
        <p>Are you sure you want to update the following item?</p>
        <div class="modal-item-info">
            <p><strong>Food Name:</strong> <span id="listingModalFoodName"></span></p>
            <p><strong>Current Status:</strong> <span id="modalCurrentStatus"></span></p>
            <p><strong>New Status:</strong> <span id="modalNewStatus"></span></p>
        </div>
        <button class="btn-cancel" onclick="closeListingModal()">Cancel</button>
        <button class="btn-confirm" onclick="confirmListingUpdate()">Confirm</button>
    </div>
</div>

<script>
    // Add this to your existing JavaScript
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchFood');
        const menuItems = document.querySelectorAll('.menu-item');

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            let hasResults = false;

            menuItems.forEach(item => {
                const foodName = item.querySelector('h5').textContent.toLowerCase();
                const description = item.querySelector('.description').textContent.toLowerCase();
                const category = item.querySelector('.category').textContent.toLowerCase();

                if (foodName.includes(searchTerm) ||
                    description.includes(searchTerm) ||
                    category.includes(searchTerm)) {
                    item.style.display = '';
                    hasResults = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide no results message
            let noResultsMsg = document.querySelector('.no-results');
            if (!hasResults) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('div');
                    noResultsMsg.className = 'no-results';
                    noResultsMsg.textContent = 'No food items found';
                    document.querySelector('.menu-list').appendChild(noResultsMsg);
                }
                noResultsMsg.style.display = 'block';
            } else if (noResultsMsg) {
                noResultsMsg.style.display = 'none';
            }
        });

        // Clear search when changing tabs
        document.querySelectorAll('.nav-link').forEach(tab => {
            tab.addEventListener('click', function() {
                searchInput.value = '';
                menuItems.forEach(item => item.style.display = '');
                const noResultsMsg = document.querySelector('.no-results');
                if (noResultsMsg) {
                    noResultsMsg.style.display = 'none';
                }
            });
        });
    });

    function toggleDropdown(button) {
        const dropdown = button.nextElementSibling;
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";

        // Close the dropdown if clicked outside
        document.addEventListener('click', function handleOutsideClick(event) {
            if (!button.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
                document.removeEventListener('click', handleOutsideClick);
            }
        });
    }

    function openRemoveModal(foodId, foodName, foodPrice) {
        // Set modal content with item details
        document.getElementById('modalFoodId').textContent = foodId;
        document.getElementById('modalFoodName').textContent = foodName;
        document.getElementById('modalFoodPrice').textContent = foodPrice;

        // Display the modal
        document.getElementById('removeModal').style.display = 'flex';
    }

    function closeModal() {
        // Hide the modal
        document.getElementById('removeModal').style.display = 'none';
    }

    function confirmRemoval() {
        const foodId = document.getElementById('modalFoodId').textContent;

        // Proceed with the item removal process (e.g., AJAX call to remove the item from the server)
        console.log(`Removing item with ID: ${foodId}`);

        // Close the modal after confirmation
        closeModal();
    }

    function confirmRemoval() {
        const foodId = document.getElementById('modalFoodId').textContent;

        // Send AJAX request to remove the item
        fetch('functions/remove.food.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `food_id=${encodeURIComponent(foodId)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();

                    // Optionally, remove the item from the DOM (if necessary)
                    document.getElementById(`menu-item-${foodId}`).remove();
                } else {
                    alert(data.message); // Show error message
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the item.');
            });
    }


    let updateData = null;

    function toggleAvailability(foodId, foodName, currentStatus) {
        updateData = {
            foodId: foodId,
            newStatus: currentStatus ? 0 : 1
        };

        // Update modal content
        document.getElementById('listingModalFoodName').textContent = foodName;
        document.getElementById('modalCurrentStatus').textContent = currentStatus ? 'Listed' : 'Unlisted';
        document.getElementById('modalNewStatus').textContent = updateData.newStatus ? 'Listed' : 'Unlisted';

        // Show modal
        document.getElementById('listingStatusModal').style.display = 'block';
    }

    function closeListingModal() {
        document.getElementById('listingStatusModal').style.display = 'none';
    }

    function confirmListingUpdate() {
        const modalContent = document.querySelector('.modal-content');
        const originalContent = modalContent.innerHTML;

        // Show loading state
        modalContent.innerHTML = `
        <div class="loading-spinner">
            <i class='bx bx-loader-alt bx-spin'></i>
            <p>Updating status...</p>
        </div>
    `;

        fetch('functions/update_Foodlisting.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `food_id=${updateData.foodId}&listed=${updateData.newStatus}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    modalContent.innerHTML = `
                <div class="loading-spinner">
                    <i class='bx bx-check-circle' style="color: #28a745;"></i>
                    <p>Status updated successfully!</p>
                </div>
            `;
                    setTimeout(() => {
                        closeListingModal();
                        location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.error || 'Failed to update listing status');
                }
            })
            .catch(error => {
                modalContent.innerHTML = `
            <div class="loading-spinner">
                <i class='bx bx-error-circle' style="color: #dc3545;"></i>
                <p>Error updating status</p>
                <p style="color: #dc3545; font-size: 0.9em;">${error.message}</p>
                <button class="btn-cancel" onclick="closeListingModal()">Close</button>
                <button class="btn-confirm" onclick="confirmListingUpdate()">Retry</button>
            </div>
        `;
            });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('listingStatusModal');
        if (event.target == modal) {
            closeListingModal();
        }
    }
</script>