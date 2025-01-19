<?php 
include 'includes/header.php';

// Check for onboarding
if (empty($_COOKIE['onboarding_journal'])) {
    header("Location: ob.journal.php");
    exit();
}

// Define meal types with their properties
$meal_types = [
    [
        'name' => 'Breakfast',
        'icon' => 'bx-coffee',
        'time' => '6:00 AM - 10:00 AM'
    ],
    [
        'name' => 'Lunch',
        'icon' => 'bx-bowl-rice',
        'time' => '11:00 AM - 2:00 PM'
    ],
    [
        'name' => 'Dinner',
        'icon' => 'bx-restaurant',
        'time' => '6:00 PM - 9:00 PM'
    ],
    [
        'name' => 'Snacks',
        'icon' => 'bx-cookie',
        'time' => 'Any Time'
    ]
];

// Get selected date with validation
$selected_date = filter_input(INPUT_GET, 'date') ?? date('Y-m-d');
if (!validateDate($selected_date)) {
    $selected_date = date('Y-m-d');
}

// Default nutritional goals
$default_goals = [
    'daily_calories' => 2000,
    'daily_protein' => 50,
    'daily_carbs' => 300,
    'daily_fat' => 65
];

// Initialize daily totals
$daily_totals = [
    'total_calories' => 0,
    'total_protein' => 0,
    'total_carbs' => 0,
    'total_fat' => 0
];

try {
    // Start transaction for consistency
    $conn->begin_transaction();

    // Fetch active nutritional goals
    $goals_query = "SELECT 
        COALESCE(daily_calories, ?) as daily_calories,
        COALESCE(daily_protein, ?) as daily_protein,
        COALESCE(daily_carbs, ?) as daily_carbs,
        COALESCE(daily_fat, ?) as daily_fat
    FROM journal_goals
    WHERE customer_id = ? AND is_active = 1
    ORDER BY created_at DESC
    LIMIT 1";
    
    $stmt = $conn->prepare($goals_query);
    $stmt->bind_param("iiiii", 
        $default_goals['daily_calories'],
        $default_goals['daily_protein'],
        $default_goals['daily_carbs'],
        $default_goals['daily_fat'],
        $customer_id
    );
    $stmt->execute();
    $current_goals = $stmt->get_result()->fetch_assoc() ?? $default_goals;

    // Fetch daily totals with proper NULL and zero handling
    $daily_totals_query = "SELECT
        ROUND(COALESCE(SUM(NULLIF(calories * portion, 0)), 0), 2) AS total_calories,
        ROUND(COALESCE(SUM(NULLIF(protein * portion, 0)), 0), 2) AS total_protein,
        ROUND(COALESCE(SUM(NULLIF(carbs * portion, 0)), 0), 2) AS total_carbs,
        ROUND(COALESCE(SUM(NULLIF(fat * portion, 0)), 0), 2) AS total_fat
    FROM food_journal
    WHERE customer_id = ? 
    AND entry_date = ?
    AND calories IS NOT NULL";
    
    $stmt = $conn->prepare($daily_totals_query);
    $stmt->bind_param("is", $customer_id, $selected_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $daily_totals = array_map('floatval', $row);
    }

    $conn->commit();

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in journal.php: " . $e->getMessage());
    // Keep using default values set above
}

// Safe calculation functions
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function calculatePercentage($value, $goal) {
    if ($goal <= 0 || !is_numeric($value)) return 0;
    $percentage = min(($value / $goal) * 100, 100);
    return is_finite($percentage) ? round($percentage, 1) : 0;
}

// Calculate percentages
$calories_percentage = calculatePercentage($daily_totals['total_calories'], $current_goals['daily_calories']);
$protein_percentage = calculatePercentage($daily_totals['total_protein'], $current_goals['daily_protein']);
$carbs_percentage = calculatePercentage($daily_totals['total_carbs'], $current_goals['daily_carbs']);
$fat_percentage = calculatePercentage($daily_totals['total_fat'], $current_goals['daily_fat']);

// Ensure all values are properly formatted for display
$display_totals = array_map(function($value) {
    return number_format(floatval($value), 1);
}, $daily_totals);

$display_percentages = [
    'calories' => number_format($calories_percentage, 1),
    'protein' => number_format($protein_percentage, 1),
    'carbs' => number_format($carbs_percentage, 1),
    'fat' => number_format($fat_percentage, 1)
];
?>
<!DOCTYPE html>
<html lang="en">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Food Journal - <?= date('M d, Y', strtotime($selected_date)) ?></title>
<link rel="stylesheet" href="assets/css/journal.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">

<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-wrap">
            <a href="homepage.php" class="back-button">
                <i class='bx bx-chevron-left'></i>
            </a>
            <h1 class="title-color font-md">Food Journal</h1>
        </div>
    </header>

    <main class="main-wrap food-journal-page mb-xxl">
        <!-- Calendar Strip -->
        <div class="calendar-strip">
            <?php
            $today = new DateTime($selected_date);
            $week = [];
            for($i = -3; $i <= 3; $i++) {
                $day = clone $today;
                $day->modify("$i day");
                $week[] = $day;
            }
            ?>
            <div class="days-scroll">
                <?php foreach($week as $day): ?>
                <div class="day-item <?= $day->format('Y-m-d') === $selected_date ? 'active' : '' ?>"
                    onclick="updateDate('<?= $day->format('Y-m-d') ?>')">
                    <span class="day-name"><?= $day->format('D') ?></span>
                    <span class="day-number"><?= $day->format('d') ?></span>
                    <?php if($day->format('Y-m-d') === date('Y-m-d')): ?>
                    <span class="today-marker"></span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Daily Summary Card -->
        <div class="summary-card">
            <h2>Daily Summary</h2>
            <button class="edit-goals-btn" onclick="openGoalsModal()">
                <i class='bx bx-slider'></i>
                Edit Goals
            </button>
            <div class="macro-circles">
                <div class="circle-progress calories">
                    <svg viewBox="0 0 36 36">
                        <path d="M18 2.0845
                    a 15.9155 15.9155 0 0 1 0 31.831
                    a 15.9155 15.9155 0 0 1 0 -31.831" stroke-dasharray="<?= $calories_percentage ?>, 100" />
                        <text x="18" y="18"
                            class="percentage"><?= number_format($daily_totals['total_calories']) ?></text>
                        <text x="18" y="23" class="label">kcal</text>
                    </svg>
                    <span class="macro-label">Calories</span>
                </div>
                <div class="macro-stats">
                    <div class="macro-item">
                        <div class="macro-icon protein-bg">
                            <i class='bx bx-bowl-hot'></i>
                        </div>
                        <div class="macro-info">
                            <span class="macro-value"><?= number_format($daily_totals['total_protein']) ?>g</span>
                            <span class="macro-name">Protein</span>
                        </div>
                        <div class="progress-mini">
                            <div class="progress protein" style="width: <?= $protein_percentage ?>%"></div>
                        </div>
                    </div>
                    <div class="macro-item">
                        <div class="macro-icon carbs-bg">
                            <i class='bx bx-baguette'></i>
                        </div>
                        <div class="macro-info">
                            <span class="macro-value"><?= number_format($daily_totals['total_carbs']) ?>g</span>
                            <span class="macro-name">Carbs</span>
                        </div>
                        <div class="progress-mini">
                            <div class="progress carbs" style="width: <?= $carbs_percentage ?>%"></div>
                        </div>
                    </div>
                    <div class="macro-item">
                        <div class="macro-icon fat-bg">
                            <i class='bx bx-droplet'></i>
                        </div>
                        <div class="macro-info">
                            <span class="macro-value"><?= number_format($daily_totals['total_fat']) ?>g</span>
                            <span class="macro-name">Fat</span>
                        </div>
                        <div class="progress-mini">
                            <div class="progress fat" style="width: <?= $fat_percentage ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Meal Sections -->
        <?php foreach ($meal_types as $meal): ?>
        <div class="meal-section <?= strtolower($meal['name']) ?>">
            <div class="meal-header">
                <div class="meal-icon">
                    <i class='bx <?= $meal['icon'] ?>'></i>
                </div>
                <div class="meal-info">
                    <h3><?= $meal['name'] ?></h3>
                    <span class="meal-time"><?= $meal['time'] ?></span>
                </div>
                <button class="add-meal-btn" onclick="openMealSelector('<?= strtolower($meal['name']) ?>')">
                    <i class='bx bx-plus'></i>
                </button>
            </div>

            <?php
    // Fetch entries from food journal
            $journal_query = "SELECT 
                j.*,
                f.photo1,
                k.fname as kitchen_name
            FROM food_journal j
            LEFT JOIN orders o ON j.order_id = o.order_id
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            LEFT JOIN food_listings f ON oi.food_id = f.food_id
            LEFT JOIN kitchens k ON f.kitchen_id = k.kitchen_id
            WHERE j.customer_id = ? 
            AND j.entry_date = ?
            AND j.meal_type = ?
            ORDER BY j.created_at DESC";

            $stmt = $conn->prepare($journal_query);
            $stmt->bind_param("iss", $customer_id, $selected_date, $meal['name']);
            $stmt->execute();
            $journal_items = $stmt->get_result();
                    ?>

            <div class="meal-items">
                <?php if ($journal_items && $journal_items->num_rows > 0): ?>
                <?php while($journal_item = $journal_items->fetch_assoc()): ?>
                <div class="food-card">
                    <div class="food-card-inner">
                        <!-- Food Image -->
                        <div class="food-image-wrapper">
                            <img src="../../uploads/<?= htmlspecialchars($journal_item['photo1'] ?? '') ?>"
                                alt="<?= htmlspecialchars($journal_item['food_name']) ?>" class="food-image"
                                onerror="this.src='assets/img/placeholder.jpg'">
                            <button class="delete-entry"
                                onclick="deleteJournalEntry(<?= $journal_item['journal_id'] ?>)">
                                <i class='bx bx-trash'></i>
                            </button>
                        </div>

                        <!-- Food Info -->
                        <div class="food-content">
                            <div class="food-header">
                                <div class="food-title">
                                    <h4><?= htmlspecialchars($journal_item['food_name']) ?></h4>
                                    <p class="kitchen-name">by
                                        <?= htmlspecialchars($journal_item['kitchen_name'] ?? 'Unknown Kitchen') ?></p>
                                </div>
                                <span class="portion-badge">×<?= $journal_item['portion'] ?></span>
                            </div>

                            <div class="macro-tags">
                                <span class="tag-item calories">
                                    <i class='bx bx-flame'></i>
                                    <?= $journal_item['calories'] ?> kcal
                                </span>
                                <span class="tag-item protein">
                                    <i class='bx bx-bowl-hot'></i>
                                    <?= $journal_item['protein'] ?>g
                                </span>
                                <span class="tag-item carbs">
                                    <i class='bx bx-baguette'></i>
                                    <?= $journal_item['carbs'] ?>g
                                </span>
                                <span class="tag-item fat">
                                    <i class='bx bx-droplet'></i>
                                    <?= $journal_item['fat'] ?>g
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <div class="empty-meal">
                    <i class='bx <?= $meal['icon'] ?>'></i>
                    <p>No <?= strtolower($meal['name']) ?> logged today</p>
                    <button class="add-meal-btn" onclick="openMealSelector('<?= strtolower($meal['name']) ?>')">
                        Add Food
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </main>


    <!-- Add this modal HTML after your main content -->
    <div class="modal" id="addFoodModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add Food to <span id="selectedMealType">Meal</span></h2>
                <span class="close-modal" onclick="closeModal('addFoodModal')">&times;</span>
            </div>
            <div class="modal-body">
                <div class="search-box">
                    <i class='bx bx-search'></i>
                    <input type="text" id="foodSearch" placeholder="Search your ordered food..."
                        onkeyup="filterFoodItems()">
                </div>

                <div class="ordered-foods">
                    <?php
                // Fetch all delivered orders for this customer
                $food_query = "SELECT 
                    f.food_id,
                    f.food_name,
                    f.calories,
                    f.protein,
                    f.carbs,
                    f.fat,
                    f.photo1,
                    k.fname as kitchen_name,
                    o.order_date
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN food_listings f ON oi.food_id = f.food_id
                JOIN kitchens k ON f.kitchen_id = k.kitchen_id
                WHERE o.customer_id = ? 
                AND o.order_status = 'Delivered'
                ORDER BY o.order_date DESC";

                $stmt = $conn->prepare($food_query);
                $stmt->bind_param("i", $customer_id);
                $stmt->execute();
                $foods = $stmt->get_result();
                ?>

                    <?php while($food = $foods->fetch_assoc()): ?>
                    <div class="food-select-item" data-food-id="<?= $food['food_id'] ?>"
                        data-food-name="<?= htmlspecialchars($food['food_name']) ?>"
                        data-calories="<?= $food['calories'] ?>" data-protein="<?= $food['protein'] ?>"
                        data-carbs="<?= $food['carbs'] ?>" data-fat="<?= $food['fat'] ?>" onclick="selectFood(this)">
                        <img src="../../uploads/<?= htmlspecialchars($food['photo1']) ?>"
                            alt="<?= htmlspecialchars($food['food_name']) ?>"
                            onerror="this.src='assets/img/placeholder.jpg'">
                        <div class="food-details">
                            <h4><?= htmlspecialchars($food['food_name']) ?></h4>
                            <p class="kitchen"><?= htmlspecialchars($food['kitchen_name']) ?></p>
                            <div class="macro-info">
                                <span><?= $food['calories'] ?> kcal</span>
                                <span>P: <?= $food['protein'] ?>g</span>
                                <span>C: <?= $food['carbs'] ?>g</span>
                                <span>F: <?= $food['fat'] ?>g</span>
                            </div>
                        </div>
                        <div class="select-indicator">
                            <i class='bx bx-check'></i>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-cancel" onclick="closeModal('addFoodModal')">Cancel</button>
                <button class="btn-add" onclick="addToJournal()">Add to Journal</button>
            </div>
        </div>
    </div>




    <script>
    function updateDate(date) {
        window.location.href = `journal.php?date=${date}`;
    }

    function openMealSelector(mealType) {
        // Implement your meal selection logic
        window.location.href = `add_food.php?meal_type=${mealType}&date=${<?= json_encode($selected_date) ?>}`;
    }

    function deleteJournalEntry(journalId) {
        if (confirm('Are you sure you want to remove this food entry?')) {
            fetch('functions/delete_journal_entry.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        journal_id: journalId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload current page to refresh stats
                        window.location.reload();
                    } else {
                        alert(data.message || 'Failed to delete entry');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the entry');
                });
        }
    }
    </script>

</body>
<?php include 'modal/modal.journal.php'; ?>
<?php include 'modal/modal.journal_goal.php'; ?>

<?php include 'includes/scripts.php'; ?>

</html>