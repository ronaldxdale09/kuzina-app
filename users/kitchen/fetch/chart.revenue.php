<?php
include '../../../connection/db.php';

header('Content-Type: application/json');

try {
    // Get kitchen_id from cookie
    $kitchen_id = $_COOKIE['kitchen_id'] ?? null;
    if (!$kitchen_id || !is_numeric($kitchen_id)) {
        throw new Exception('Invalid kitchen ID');
    }

    // Validate duration parameter
    $duration = $_GET['duration'] ?? 'daily';
    if (!in_array($duration, ['daily', 'weekly', 'monthly'])) {
        $duration = 'daily';
    }

    // Set up time intervals and formats based on duration
    switch ($duration) {
        case 'weekly':
            $timeInterval = "DATE_SUB(CURRENT_DATE(), INTERVAL 1 WEEK)";
            $periodFormat = "DATE_FORMAT(updated_at, '%Y-%m-%d')";
            $periodLabel = "DATE_FORMAT(updated_at, '%M %d')"; // Month DD format
            break;
        case 'monthly':
            $timeInterval = "DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)";
            $periodFormat = "DATE_FORMAT(updated_at, '%Y-%m-%d')";
            $periodLabel = "DATE_FORMAT(updated_at, '%M %d')"; // Month DD format
            break;
        default: // daily
            $timeInterval = "DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $periodFormat = "DATE_FORMAT(updated_at, '%Y-%m-%d %H:00:00')";
            $periodLabel = "DATE_FORMAT(updated_at, '%h:%i %p')"; // 12-hour time format
            break;
    }

    // Get total revenue for completed orders
    $totalQuery = "SELECT COALESCE(SUM(final_total_amount), 0) as total 
                  FROM orders 
                  WHERE kitchen_id = ? 
                  AND order_status = 'Delivered' 
                  AND updated_at >= {$timeInterval}";

    $stmt = $conn->prepare($totalQuery);
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $totalResult = $stmt->get_result()->fetch_assoc();
    $total = $totalResult['total'];
    $stmt->close();

    // Get chart data
    $chartQuery = "SELECT 
                    {$periodFormat} as period,
                    {$periodLabel} as label,
                    COALESCE(SUM(final_total_amount), 0) as revenue
                  FROM orders 
                  WHERE kitchen_id = ? 
                  AND order_status = 'Delivered'
                  AND updated_at >= {$timeInterval}
                  GROUP BY period
                  ORDER BY period ASC";

    $stmt = $conn->prepare($chartQuery);
    if (!$stmt) {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("i", $kitchen_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $chartData = [];
    while ($row = $result->fetch_assoc()) {
        $chartData[] = [
            'period' => $row['label'],
            'revenue' => (float)$row['revenue']
        ];
    }

    // Fill in missing periods with zero values
    if (empty($chartData)) {
        switch ($duration) {
            case 'weekly':
                for ($i = 6; $i >= 0; $i--) {
                    $date = date('M d', strtotime("-$i days"));
                    $chartData[] = ['period' => $date, 'revenue' => 0];
                }
                break;
            case 'monthly':
                for ($i = 29; $i >= 0; $i--) {
                    $date = date('M d', strtotime("-$i days"));
                    $chartData[] = ['period' => $date, 'revenue' => 0];
                }
                break;
            default: // daily
                for ($i = 23; $i >= 0; $i--) {
                    $hour = date('h:i A', strtotime("-$i hours"));
                    $chartData[] = ['period' => $hour, 'revenue' => 0];
                }
                break;
        }
    }

    echo json_encode([
        'success' => true,
        'totalRevenue' => (float)$total,
        'chartData' => $chartData
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}