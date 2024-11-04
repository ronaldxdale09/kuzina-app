<?php
include '../../connection/db.php';

$kitchen_id = $_COOKIE['kitchen_user_id'] ?? null;
$duration = $_GET['duration'] ?? 'daily';
$totalRevenue = 0;
$chartData = [];

// Check for valid kitchen ID
if ($kitchen_id) {
    // Prepare query based on the selected duration
    switch ($duration) {
        case 'weekly':
            $timeInterval = "DATE_SUB(NOW(), INTERVAL 1 WEEK)";
            $groupBy = "DAY";
            break;
        case 'monthly':
            $timeInterval = "DATE_SUB(NOW(), INTERVAL 1 MONTH)";
            $groupBy = "WEEK";
            break;
        case 'daily':
        default:
            $timeInterval = "DATE_SUB(NOW(), INTERVAL 1 DAY)";
            $groupBy = "HOUR";
            break;
    }

    // Total Revenue Query
    $revenueQuery = "SELECT SUM(amount) AS total_revenue FROM kitchen_earnings 
                     WHERE kitchen_id = ? AND earning_date >= $timeInterval";
    $stmt = $conn->prepare($revenueQuery);
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $stmt->bind_result($totalRevenue);
    $stmt->fetch();
    $stmt->close();

    // Chart Data Query
    $chartQuery = "SELECT $groupBy(earning_date) AS period, SUM(amount) AS period_revenue 
                   FROM kitchen_earnings 
                   WHERE kitchen_id = ? AND earning_date >= $timeInterval
                   GROUP BY period ORDER BY period ASC";
    $stmt = $conn->prepare($chartQuery);
    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $chartData[] = ['period' => $row['period'], 'revenue' => $row['period_revenue']];
    }

    $stmt->close();
}

$conn->close();

// Return data as JSON
echo json_encode(['totalRevenue' => $totalRevenue, 'chartData' => $chartData]);
