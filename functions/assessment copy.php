<?php
include '../connection/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate that customer ID exists in the cookie
    if (isset($_COOKIE['user_id'])) {
        $customer_id = $_COOKIE['user_id']; // Retrieve customer ID from the cookie
    } else {
        echo json_encode(['success' => false, 'message' => 'User not authenticated.']);
        exit; // Stop further execution if user is not logged in
    }

    // Basic information
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $height = $_POST['height'];
    $height_unit = $_POST['height-unit'];
    $weight = $_POST['weight'];
    $weight_unit = $_POST['weight-unit'];
    $allergies = isset($_POST['allergens']) ? implode(',', $_POST['allergens']) : '';  // Ensure it's set, or default to an empty string
    
    // Diet type and health goals
    $diet_type = isset($_POST['diet_type']) ? $_POST['diet_type'] : null; // Handle empty diet type case
    $health_goal = isset($_POST['health_goal']) ? $_POST['health_goal'] : null; // Handle empty health goal case
    
    // Nutritional targets
    $daily_calories = isset($_POST['daily_calories']) ? intval($_POST['daily_calories']) : 2000;
    $daily_protein = isset($_POST['daily_protein']) ? intval($_POST['daily_protein']) : 50;
    $daily_carbs = isset($_POST['daily_carbs']) ? intval($_POST['daily_carbs']) : 300;
    $daily_fat = isset($_POST['daily_fat']) ? intval($_POST['daily_fat']) : 65;

    // Check if all necessary fields are filled
    if (empty($diet_type) || empty($health_goal)) {
        echo json_encode(['success' => false, 'message' => 'Please select both diet type and health goal.']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert into nutritional_assessments table
        $stmt = $conn->prepare("INSERT INTO nutritional_assessments (customer_id, age, gender, height, height_unit, weight, weight_unit, allergies, diet_type, health_goal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisdssisss", $customer_id, $age, $gender, $height, $height_unit, $weight, $weight_unit, $allergies, $diet_type, $health_goal);
        $stmt->execute();
        $assessment_id = $conn->insert_id;
        $stmt->close();
        
        // Set all previous goals to inactive
        $update_stmt = $conn->prepare("UPDATE journal_goals SET is_active = 0 WHERE customer_id = ?");
        $update_stmt->bind_param("i", $customer_id);
        $update_stmt->execute();
        $update_stmt->close();
        
        // Insert into journal_goals table
        $goals_stmt = $conn->prepare("INSERT INTO journal_goals (customer_id, daily_calories, daily_protein, daily_carbs, daily_fat, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $goals_stmt->bind_param("iiiii", $customer_id, $daily_calories, $daily_protein, $daily_carbs, $daily_fat);
        $goals_stmt->execute();
        $goals_stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'Nutritional assessment submitted successfully!']);
    } catch (Exception $e) {
        // Roll back the transaction if any query fails
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    $conn->close();
}
?>