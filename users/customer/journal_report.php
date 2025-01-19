<?php
include 'includes/header.php';

// Get date range (default to current month)
$end_date = date('Y-m-d');
$start_date = date('Y-m-01'); // First day of current month

if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
    $start_date = $_GET['start_date'];
    $end_date = $_GET['end_date'];
}

// Fetch journal data
$query = "SELECT 
    fj.*,
    DATE(fj.entry_date) as date,
    ROUND(SUM(fj.calories * fj.portion), 2) as total_calories,
    ROUND(SUM(fj.protein * fj.portion), 2) as total_protein,
    ROUND(SUM(fj.carbs * fj.portion), 2) as total_carbs,
    ROUND(SUM(fj.fat * fj.portion), 2) as total_fat,
    COUNT(*) as total_entries
FROM food_journal fj
WHERE fj.customer_id = ? 
AND fj.entry_date BETWEEN ? AND ?
GROUP BY DATE(fj.entry_date)
ORDER BY fj.entry_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $customer_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journal Report</title>
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    <link rel="stylesheet" href="assets/css/journal_report.css">

</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="logo-wrap">
            <a href="journal.php" class="back-button">
                <i class='bx bx-chevron-left'></i>
            </a>
            <h1 class="title-color font-md">Food Journal Report</h1>
        </div>
    </header>

    <div class="main-content">
        <div class="report-container">
            <!-- Date Filter -->
            <div class="date-filter">
                <form class="filter-form" method="GET">
                    <input type="date" name="start_date" class="date-input" value="<?= $start_date ?>">
                    <span>to</span>
                    <input type="date" name="end_date" class="date-input" value="<?= $end_date ?>">
                    <button type="submit" class="apply-btn">Apply</button>
                </form>
            </div>

            <!-- Period Summary -->
            <?php
            $total_calories = 0;
            $total_protein = 0;
            $total_carbs = 0;
            $total_fat = 0;
            $days_count = 0;

            while ($row = $result->fetch_assoc()) {
                $total_calories += $row['total_calories'];
                $total_protein += $row['total_protein'];
                $total_carbs += $row['total_carbs'];
                $total_fat += $row['total_fat'];
                $days_count++;
            }

            // Reset result pointer
            $result->data_seek(0);
            ?>

            <!-- Period Summary -->
            <div class="period-summary">
                <h2 class="summary-title">Period Summary</h2>
                <div class="summary-list">
                    <div class="summary-item">
                        <div class="summary-label">Avg. Daily Calories</div>
                        <div class="summary-value">
                            <?= $days_count > 0 ? round($total_calories / $days_count) : 0 ?> kcal
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Avg. Daily Protein</div>
                        <div class="summary-value">
                            <?= $days_count > 0 ? round($total_protein / $days_count, 1) : 0 ?>g
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Avg. Daily Carbs</div>
                        <div class="summary-value">
                            <?= $days_count > 0 ? round($total_carbs / $days_count, 1) : 0 ?>g
                        </div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-label">Avg. Daily Fat</div>
                        <div class="summary-value">
                            <?= $days_count > 0 ? round($total_fat / $days_count, 1) : 0 ?>g
                        </div>
                    </div>
                </div>
            </div>

            <!-- Daily Entries -->
            <div class="daily-entries">
                <?php while ($row = $result->fetch_assoc()): ?>
                <div class="daily-entry">
                    <div class="entry-header">
                        <div class="entry-date">
                            <?= date('F j, Y', strtotime($row['date'])) ?>
                        </div>
                        <div class="entry-count">
                            <?= $row['total_entries'] ?> entries
                        </div>
                    </div>

                    <div class="macro-grid">
                        <div class="macro-item">
                            <span class="macro-label">Calories</span>
                            <span class="macro-value"><?= round($row['total_calories']) ?></span>
                        </div>
                        <div class="macro-item">
                            <span class="macro-label">Protein</span>
                            <span class="macro-value"><?= round($row['total_protein'], 1) ?>g</span>
                        </div>
                        <div class="macro-item">
                            <span class="macro-label">Carbs</span>
                            <span class="macro-value"><?= round($row['total_carbs'], 1) ?>g</span>
                        </div>
                        <div class="macro-item">
                            <span class="macro-label">Fat</span>
                            <span class="macro-value"><?= round($row['total_fat'], 1) ?>g</span>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</body>

</html>