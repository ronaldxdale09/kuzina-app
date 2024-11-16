<?php
// api/get_earnings.php
include '../../../connection/db.php';
header('Content-Type: application/json');

$rider_id = $_COOKIE['rider_id'] ?? null;
if (!$rider_id) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$duration = $_GET['duration'] ?? 'daily';

try {
    switch($duration) {
        case 'daily':
            $sql = "SELECT 
                    DATE_FORMAT(earning_date, '%h:%i %p') as period,
                    SUM(amount) as earnings
                    FROM rider_earnings 
                    WHERE rider_id = ? 
                    AND DATE(earning_date) = CURDATE()
                    GROUP BY HOUR(earning_date)
                    ORDER BY earning_date";
            break;
        
        case 'weekly':
            $sql = "SELECT 
                    DATE_FORMAT(earning_date, '%W') as period,
                    SUM(amount) as earnings
                    FROM rider_earnings 
                    WHERE rider_id = ? 
                    AND YEARWEEK(earning_date) = YEARWEEK(CURDATE())
                    GROUP BY DATE(earning_date)
                    ORDER BY earning_date";
            break;
        
        case 'monthly':
            $sql = "SELECT 
                    DATE_FORMAT(earning_date, '%M %d') as period,
                    SUM(amount) as earnings
                    FROM rider_earnings 
                    WHERE rider_id = ? 
                    AND MONTH(earning_date) = MONTH(CURDATE())
                    AND YEAR(earning_date) = YEAR(CURDATE())
                    GROUP BY DATE(earning_date)
                    ORDER BY earning_date";
            break;
        
        default:
            throw new Exception('Invalid duration');
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $chartData = [];
    while($row = $result->fetch_assoc()) {
        $chartData[] = [
            'period' => $row['period'],
            'earnings' => floatval($row['earnings'])
        ];
    }

    // Get total earnings
    $totalSql = "SELECT SUM(amount) as total 
                 FROM rider_earnings 
                 WHERE rider_id = ? AND ";
    
    switch($duration) {
        case 'daily':
            $totalSql .= "DATE(earning_date) = CURDATE()";
            break;
        case 'weekly':
            $totalSql .= "YEARWEEK(earning_date) = YEARWEEK(CURDATE())";
            break;
        case 'monthly':
            $totalSql .= "MONTH(earning_date) = MONTH(CURDATE()) 
                         AND YEAR(earning_date) = YEAR(CURDATE())";
            break;
    }

    $stmt = $conn->prepare($totalSql);
    $stmt->bind_param("i", $rider_id);
    $stmt->execute();
    $totalResult = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        'success' => true,
        'chartData' => $chartData,
        'totalEarnings' => floatval($totalResult['total'] ?? 0)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>