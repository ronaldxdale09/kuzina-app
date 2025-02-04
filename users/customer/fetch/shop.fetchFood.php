<?php
include '../../../connection/db.php';

$response = ['success' => false, 'message' => '', 'foods' => []];

try {
    $category = $_POST['category'] ?? '';
    $search = $_POST['search'] ?? '';

    $query = "SELECT f.*, c.name as category_name 
              FROM food_listings f 
              LEFT JOIN food_categories c ON f.category_id = c.category_id 
              WHERE f.isApproved = 1 AND f.available = 1";

    $params = [];
    $types = "";

    if (!empty($category)) {
        $query .= " AND c.name = ?";
        $params[] = $category;
        $types .= "s";
    }

    if (!empty($search)) {
        $search = "%$search%";
        $query .= " AND (f.food_name LIKE ? OR f.description LIKE ? OR c.name LIKE ? OR f.diet_type_suitable LIKE ?)";
        $params = array_merge($params, [$search, $search, $search, $search]);
        $types .= "ssss";
    }

    $query .= " ORDER BY f.created_at DESC";

    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $foods = [];
    while ($row = $result->fetch_assoc()) {
        $foods[] = $row;
    }

    $response['success'] = true;
    $response['foods'] = $foods;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
