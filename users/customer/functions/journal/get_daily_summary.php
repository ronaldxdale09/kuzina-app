<?php
include '../../../../ connection/db.php';
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $customer_id = $_COOKIE['user_id'] ?? null;
    $date = $input['date'] ?? date('Y-m-d');

    if (!$customer_id) {
        throw new Exception('User not authenticated');
    }

    // Get active goals
    $goals_query = "SELECT 
        COALESCE(daily_calories, 2000) as daily_calories,
        COALESCE(daily_protein, 50) as daily_protein,
        COALESCE(daily_carbs, 300) as daily_carbs,
        COALESCE(daily_fat, 65) as daily_fat
    FROM journal_goals 
    WHERE customer_id = ? AND is_active = 1 
    ORDER BY created_at DESC LIMIT 1";

    $stmt = $conn->prepare($goals_query);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $goals = $stmt->get_result()->fetch_assoc() ?? [
        'daily_calories' => 2000,
        'daily_protein' => 50,
        'daily_carbs' => 300,
        'daily_fat' => 65
    ];

    // Get daily totals with corrected SQL syntax
    $totals_query = "SELECT
        ROUND(COALESCE(SUM(calories), 0), 2) AS total_calories,
        ROUND(COALESCE(SUM(protein), 0), 2) AS total_protein,
        ROUND(COALESCE(SUM(carbs), 0), 2) AS total_carbs,
        ROUND(COALESCE(SUM(fat), 0), 2) AS total_fat
    FROM food_journal
    WHERE customer_id = ? 
    AND entry_date = ?
    AND calories IS NOT NULL";

    $stmt = $conn->prepare($totals_query);
    $stmt->bind_param("is", $customer_id, $date);
    $stmt->execute();
    $totals = $stmt->get_result()->fetch_assoc();

    // Ensure we have valid numbers
    $totals = array_map(function($value) {
        return floatval($value);
    }, $totals);

    echo json_encode([
        'success' => true,
        'totals' => $totals,
        'goals' => $goals
    ]);

} catch (Exception $e) {
    error_log("Error in get_daily_summary.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?>