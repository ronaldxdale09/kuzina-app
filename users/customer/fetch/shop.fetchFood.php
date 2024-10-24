<?php
include '../../../connection/db.php'; // Include DB connection

header('Content-Type: application/json');

$response = ['success' => false, 'foods' => [], 'message' => ''];

$category = isset($_POST['category']) ? $_POST['category'] : '';

try {
    // Query to fetch all food items if no category is specified, otherwise fetch based on category
    $query = "SELECT food_id,meal_type,category,description, food_name, diet_type_suitable, health_goal_suitable, price, photo1 FROM food_listings WHERE available = 1";
    
    if (!empty($category)) {
        $query .= " AND category = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
    } else {
        $stmt = $conn->prepare($query); // No filtering by category
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $foods = [];

        while ($row = $result->fetch_assoc()) {
            $foods[] = [
                'food_id' => $row['food_id'],
                'food_name' => $row['food_name'],
                'meal_type' => $row['meal_type'],
                'category' => $row['category'],
                'diet_type_suitable' => $row['diet_type_suitable'],
                'health_goal_suitable' => $row['health_goal_suitable'],
                'price' => $row['price'],
                'photo1' => $row['photo1'] ? $row['photo1'] : 'assets/images/default-food.jpg'
            ];
        }

        $response['success'] = true;
        $response['foods'] = $foods;
    } else {
        $response['message'] = 'No food items available';
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
?>