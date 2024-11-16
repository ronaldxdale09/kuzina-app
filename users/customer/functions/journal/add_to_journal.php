<?php

include '../../../../connection/db.php';
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => ''
];

try {
    // Get JSON data
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Get customer ID from cookie
    $customer_id = $_COOKIE['user_id'] ?? null;
    
    if (!$customer_id) {
        throw new Exception('User not authenticated');
    }

    if (!isset($data['food_id']) || !isset($data['meal_type']) || !isset($data['date'])) {
        throw new Exception('Missing required data');
    }

    $food_id = intval($data['food_id']);
    $meal_type = $data['meal_type'];
    $entry_date = $data['date'];

    // Start transaction
    $conn->begin_transaction();

    // First, get the food and order details
    $food_query = "SELECT 
        f.food_name,
        f.calories,
        f.protein,
        f.carbs,
        f.photo1,

        f.fat,
        oi.order_id,
        oi.quantity
    FROM food_listings f
    JOIN order_items oi ON f.food_id = oi.food_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE f.food_id = ? 
    AND o.customer_id = ?
    AND o.order_status = 'Delivered'
    LIMIT 1";

    $stmt = $conn->prepare($food_query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ii", $food_id, $customer_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }

    $food_result = $stmt->get_result();
    if ($food_result->num_rows === 0) {
        throw new Exception('Food item not found or not ordered');
    }

    $food_data = $food_result->fetch_assoc();

    // Check for duplicate entry
    $duplicate_check = "SELECT COUNT(*) as entry_count 
                       FROM food_journal 
                       WHERE customer_id = ? 
                       AND order_id = ? 
                       AND meal_type = ?
                       AND entry_date = ?";
    $stmt = $conn->prepare($duplicate_check);
    $stmt->bind_param("iiss", $customer_id, $food_data['order_id'], $meal_type, $entry_date);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()['entry_count'] > 0) {
        throw new Exception('This food item is already in your journal for this meal');
    }

    // Insert into food journal with escaped column name
    $insert_sql = "INSERT INTO food_journal (
        customer_id,
        order_id,
        meal_type,
        food_name,
        calories,
        protein,
        carbs,
        fat,
        `portion`,
        entry_date
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($insert_sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "iissddddis",
        $customer_id,
        $food_data['order_id'],
        $meal_type,
        $food_data['food_name'],
        $food_data['calories'],
        $food_data['protein'],
        $food_data['carbs'],
        $food_data['fat'],
        $food_data['quantity'],
        $entry_date
    );

    if (!$stmt->execute()) {
        throw new Exception("Failed to add food to journal: " . $stmt->error);
    }

    // Check if we need to create/update goals
    $goals_check = "SELECT COUNT(*) as goal_count 
                   FROM journal_goals 
                   WHERE customer_id = ? AND is_active = 1";
    $stmt = $conn->prepare($goals_check);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $goals_exist = $stmt->get_result()->fetch_assoc()['goal_count'] > 0;

    // If no goals exist, create default goals
    if (!$goals_exist) {
        $default_goals = "INSERT INTO journal_goals (
            customer_id, 
            daily_calories,
            daily_protein,
            daily_carbs,
            daily_fat,
            is_active
        ) VALUES (?, 2000, 50, 300, 65, 1)";
        
        $stmt = $conn->prepare($default_goals);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Set success response
    $response['success'] = true;
    $response['message'] = 'Food added to journal successfully';
    
    // Add the updated totals to the response
    $totals_query = "SELECT 
        COALESCE(SUM(calories * `portion`), 0) as total_calories,
        COALESCE(SUM(protein * `portion`), 0) as total_protein,
        COALESCE(SUM(carbs * `portion`), 0) as total_carbs,
        COALESCE(SUM(fat * `portion`), 0) as total_fat
    FROM food_journal
    WHERE customer_id = ? 
    AND entry_date = ?";
    
    $stmt = $conn->prepare($totals_query);
    $stmt->bind_param("is", $customer_id, $entry_date);
    $stmt->execute();
    $totals = $stmt->get_result()->fetch_assoc();
    
    $response['totals'] = $totals;

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($conn) && $conn->ping()) {
        $conn->rollback();
    }
    
    $response['message'] = $e->getMessage();
    error_log("Error adding to food journal: " . $e->getMessage());
} finally {
    // Close any open statements
    if (isset($stmt)) {
        $stmt->close();
    }
}
$response = [
    'success' => true,
    'message' => 'Food added to journal successfully',
    'food' => [
        'journal_id' => $conn->insert_id,
        'food_name' => $food_data['food_name'],
        'calories' => $food_data['calories'],
        'protein' => $food_data['protein'],
        'carbs' => $food_data['carbs'],
        'fat' => $food_data['fat'],
        'portion' => $food_data['quantity'],
        // Get photo and kitchen name from the query
        'photo1' => $food_data['photo1'],
        'kitchen_name' => $food_data['kitchen_name'] ?? 'Unknown Kitchen'
    ],
    'totals' => $totals
];

echo json_encode($response);
?>