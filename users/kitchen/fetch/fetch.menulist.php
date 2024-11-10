<?php 
$kitchen_id = $_COOKIE['kitchen_id'] ?? null;

if (!$kitchen_id) {
    echo "Kitchen ID not found. Please log in.";
    exit();
}


// Fetch all menu items for the kitchen
$query = "SELECT food_id, food_name, description, price, meal_type, photo1 
          FROM food_listings 
          WHERE available = 1 AND kitchen_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $kitchen_id);
$stmt->execute();
$result = $stmt->get_result();

$menuItems = [
    'all' => [],
    'breakfast' => [],
    'lunch' => [],
    'dinner' => []
];

while ($row = $result->fetch_assoc()) {
    $mealType = strtolower($row['meal_type']);
    $menuItems['all'][] = $row;
    if (isset($menuItems[$mealType])) {
        $menuItems[$mealType][] = $row; // Group items by category (e.g., breakfast, lunch)
    }
}

$stmt->close();
$conn->close();

// Function to render menu items for a specific category
function renderMenuItems($items) {
    if (empty($items)) {
        echo "<p>No items found.</p>";
        return;
    }

    foreach ($items as $item): ?>
<div id="menu-item-<?= $item['food_id'] ?>" class="menu-item">
    <img src="../../uploads/<?= htmlspecialchars($item['photo1']) ?>"
        alt="<?= htmlspecialchars($item['food_name']) ?>" />
    <div class="menu-info">
        <h5><?= htmlspecialchars($item['food_name']) ?></h5>
        <span class="badge"><?= htmlspecialchars($item['meal_type']) ?></span>
        <div class="price">PHP <?= number_format($item['price'], 2) ?></div>
    </div>
    <div class="action-icons">
        <button class="menu-button" onclick="toggleDropdown(this)">
            <i class="bx bx-dots-horizontal-rounded"></i> <!-- Dots icon for menu -->
        </button>
        <div class="action-dropdown">
            <a href="#" class="dropdown-option">Edit</a>
            <a href="javascript:void(0);" class="dropdown-option"
                onclick="openRemoveModal('<?= $item['food_id'] ?>', '<?= htmlspecialchars($item['food_name']) ?>', '<?= number_format($item['price'], 2) ?>')">
                Remove
            </a>
        </div>

    </div>
</div>

<?php endforeach;
} ?>



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

<script>
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
</script>