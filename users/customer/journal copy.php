<?php 
include 'includes/header.php';

// Define meal types array at the beginning
$meal_types = array(
    array(
        'name' => 'Breakfast',
        'icon' => 'bx-coffee',
        'time' => '6:00 AM - 10:00 AM'
    ),
    array(
        'name' => 'Lunch',
        'icon' => 'bx-bowl-rice',
        'time' => '11:00 AM - 2:00 PM'
    ),
    array(
        'name' => 'Dinner',
        'icon' => 'bx-restaurant',
        'time' => '6:00 PM - 9:00 PM'
    ),
    array(
        'name' => 'Snacks',
        'icon' => 'bx-cookie',
        'time' => 'Any Time'
    )
);


// Get current date or selected date
$selected_date = $_GET['date'] ?? date('Y-m-d');

try {
    // Fetch customer's nutritional assessment
    $assess_query = "SELECT * FROM nutritional_assessments 
                    WHERE customer_id = ? 
                    ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($assess_query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $assessment = $stmt->get_result()->fetch_assoc();

    // Fetch daily totals from order items
    $daily_totals_query = "SELECT 
    COALESCE(SUM(calories * portion), 0) as total_calories,
    COALESCE(SUM(protein * portion), 0) as total_protein,
    COALESCE(SUM(carbs * portion), 0) as total_carbs,
    COALESCE(SUM(fat * portion), 0) as total_fat
FROM food_journal
WHERE customer_id = ? 
AND entry_date = ?";

    $stmt = $conn->prepare($daily_totals_query);
    $stmt->bind_param("is", $customer_id, $selected_date);
    $stmt->execute();
    $daily_totals = $stmt->get_result()->fetch_assoc();
} catch (Exception $e) {
    error_log("Error in food journal: " . $e->getMessage());
    $daily_totals = array(
        'total_calories' => 0,
        'total_protein' => 0,
        'total_carbs' => 0,
        'total_fat' => 0
    );
}

// Fetch current goals
$goals_query = "SELECT * FROM journal_goals 
                WHERE customer_id = ? 
                AND is_active = 1 
                ORDER BY created_at DESC LIMIT 1";
$stmt = $conn->prepare($goals_query);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$current_goals = $stmt->get_result()->fetch_assoc() ?? [
    'daily_calories' => 2000,
    'daily_protein' => 50,
    'daily_carbs' => 300,
    'daily_fat' => 65
];
?>

<!DOCTYPE html>
<html lang="en">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Food Journal - <?= date('M d, Y', strtotime($selected_date)) ?></title>
<link rel="stylesheet" href="assets/css/journal.css">
<link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
<style>
.meal-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 15px;
}

.food-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border: 1px solid #eee;
}

.food-card-inner {
    display: flex;
    gap: 15px;
    padding: 12px;
}

.food-image-wrapper {
    position: relative;
    width: 80px;
    height: 80px;
    flex-shrink: 0;
}

.food-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 8px;
}

.delete-entry {
    position: absolute;
    top: -6px;
    right: -6px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: white;
    border: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    opacity: 0;
    transition: all 0.2s ease;
}

.food-card:hover .delete-entry {
    opacity: 1;
}

.delete-entry i {
    color: #dc3545;
    font-size: 14px;
}

.food-content {
    flex: 1;
    min-width: 0;
    /* Prevents flex item from overflowing */
}

.food-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
}

.food-title {
    flex: 1;
    min-width: 0;
}

.food-title h4 {
    margin: 0;
    font-size: 0.95rem;
    color: #333;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.kitchen-name {
    margin: 2px 0 0 0;
    font-size: 0.8rem;
    color: #666;
}

.portion-badge {
    background: var(--accent-green);
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    margin-left: 8px;
}

.macro-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.tag-item {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 0.8rem;
    white-space: nowrap;
}

.tag-item i {
    font-size: 12px;
}

.tag-item.calories {
    background: #fff3e0;
    color: var(--primary-orange);
}

.tag-item.protein {
    background: #ffe0e0;
    color: var(--primary-red);
}

.tag-item.carbs {
    background: #e3f2fd;
    color: #1976d2;
}

.tag-item.fat {
    background: #f0f9e8;
    color: var(--accent-green);
}

/* Empty State */
.empty-meal {
    text-align: center;
    padding: 30px 20px;
    background: #f8f9fa;
    border-radius: 12px;
}

.empty-meal i {
    font-size: 24px;
    color: #adb5bd;
    margin-bottom: 8px;
}

.empty-meal p {
    margin: 0 0 12px 0;
    color: #6c757d;
}

/* Mobile Optimization */
@media (max-width: 480px) {
    .food-card-inner {
        gap: 10px;
    }

    .food-image-wrapper {
        width: 60px;
        height: 60px;
    }

    .macro-tags {
        gap: 4px;
    }

    .tag-item {
        padding: 3px 6px;
        font-size: 0.75rem;
    }
}
</style>

<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-wrap">
            <a href="javascript:void(0);" onclick="window.history.back();" class="back-button">
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
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                            stroke-dasharray="<?= min(($daily_totals['total_calories'] ?? 0) / 2000 * 100, 100) ?>, 100" />
                        <text x="18" y="18"
                            class="percentage"><?= number_format($daily_totals['total_calories'] ?? 0) ?></text>
                        <text x="18" y="23" class="label">kcal</text>
                    </svg>
                    <span class="macro-label">Calories</span>
                </div>

                <!-- Macro Stats -->
                <div class="macro-stats">
                    <div class="macro-item">
                        <div class="macro-icon protein-bg">
                            <i class='bx bx-bowl-hot'></i>
                        </div>
                        <div class="macro-info">
                            <span class="macro-value"><?= number_format($daily_totals['total_protein'] ?? 0) ?>g</span>
                            <span class="macro-name">Protein</span>
                        </div>
                        <div class="progress-mini">
                            <div class="progress protein"
                                style="width: <?= min(($daily_totals['total_protein'] ?? 0) / 50 * 100, 100) ?>%"></div>
                        </div>
                    </div>

                    <!-- Carbs -->
                    <div class="macro-item">
                        <div class="macro-icon carbs-bg">
                            <i class='bx bx-baguette  '></i>
                        </div>
                        <div class="macro-info">
                            <span class="macro-value"><?= number_format($daily_totals['total_carbs'] ?? 0) ?>g</span>
                            <span class="macro-name">Carbs</span>
                        </div>
                        <div class="progress-mini">
                            <div class="progress carbs"
                                style="width: <?= min(($daily_totals['total_carbs'] ?? 0) / 300 * 100, 100) ?>%"></div>
                        </div>
                    </div>

                    <!-- Fat -->
                    <div class="macro-item">
                        <div class="macro-icon fat-bg">
                            <i class='bx bx-droplet'></i>
                        </div>
                        <div class="macro-info">
                            <span class="macro-value"><?= number_format($daily_totals['total_fat'] ?? 0) ?>g</span>
                            <span class="macro-name">Fat</span>
                        </div>
                        <div class="progress-mini">
                            <div class="progress fat"
                                style="width: <?= min(($daily_totals['total_fat'] ?? 0) / 65 * 100, 100) ?>%"></div>
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
    </script>

</body>
<?php include 'modal/modal.journal.php'; ?>
<?php include 'modal/modal.journal_goal.php'; ?>

<?php include 'includes/scripts.php'; ?>

</html>