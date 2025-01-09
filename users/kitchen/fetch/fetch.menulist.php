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
    status,
    available
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
        
        <div class="menu-badges">
            <span class="badge meal-type"><?= htmlspecialchars($item['meal_type']) ?></span>
            <span class="badge category"><?= htmlspecialchars($item['category']) ?></span>
        </div>
        
        <!-- Description -->
        <p class="description">
            <?= nl2br(htmlspecialchars(substr($item['description'], 0, 100) . (strlen($item['description']) > 100 ? '...' : ''))); ?>
        </p>
        
        <!-- Nutritional Summary -->
        <div class="nutrition-summary">
            <span class="nutrition-item">
                <i class='bx bx-bowl-hot'></i> <?= htmlspecialchars($item['calories']) ?> cal
            </span>
            <span class="nutrition-item">
                <i class='bx bx-donate-heart'></i> <?= htmlspecialchars($item['protein']) ?>g protein
            </span>
        </div>

        <div class="item-bottom">
            <div class="price">PHP <?= number_format($item['price'], 2) ?></div>
            <div class="action-buttons">
                <button class="action-btn edit" onclick="window.location.href='add_menu.php?food_id=<?= $item['food_id'] ?>'">
                    <i class='bx bx-edit'></i> Edit
                </button>
                <button class="action-btn remove" onclick="openRemoveModal('<?= $item['food_id'] ?>', '<?= htmlspecialchars($item['food_name']) ?>', '<?= number_format($item['price'], 2) ?>')">
                    <i class='bx bx-trash'></i> Remove
                </button>
                <button class="action-btn availability <?= $item['status'] ? 'available' : 'unavailable' ?>" 
                        onclick="toggleAvailability(<?= $item['food_id'] ?>, '<?= htmlspecialchars($item['food_name']) ?>', <?= $item['status'] ?>)">
                    <i class='bx <?= $item['status'] ? 'bx-check-circle' : 'bx-x-circle' ?>'></i>
                    <?= $item['status'] ? 'Available' : 'Unavailable' ?>
                </button>
            </div>
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