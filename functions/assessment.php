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

    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $height = $_POST['height'];
    $height_unit = $_POST['height-unit'];
    $weight = $_POST['weight'];
    $weight_unit = $_POST['weight-unit'];
    $allergies = isset($_POST['allergens']) ? implode(',', $_POST['allergens']) : '';  // Ensure it's set, or default to an empty string
    $diet_type = isset($_POST['diet_type']) ? $_POST['diet_type'] : null; // Handle empty diet type case
    $health_goal = isset($_POST['health_goal']) ? $_POST['health_goal'] : null; // Handle empty health goal case

    // Check if all necessary fields are filled
    if (empty($diet_type) || empty($health_goal)) {
        echo json_encode(['success' => false, 'message' => 'Please select both diet type and health goal.']);
        exit;
    }

    // Prepare and bind the SQL query
    $stmt = $conn->prepare("INSERT INTO nutritional_assessments (customer_id, age, gender, height, height_unit, weight, weight_unit, allergies, diet_type, health_goal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdssisss", $customer_id, $age, $gender, $height, $height_unit, $weight, $weight_unit, $allergies, $diet_type, $health_goal);

    // Execute and return response
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Nutritional assessment submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
