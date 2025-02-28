<?php
include '../../../connection/db.php';

$response = ['success' => false, 'message' => '', 'foods' => [], 'total' => 0];

try {
    $category = $_POST['category'] ?? '';
    $search = $_POST['search'] ?? '';
    $mealTypes = isset($_POST['mealTypes']) ? json_decode($_POST['mealTypes']) : [];
    $dietTypes = isset($_POST['dietTypes']) ? json_decode($_POST['dietTypes']) : [];
    $minPrice = isset($_POST['minPrice']) ? floatval($_POST['minPrice']) : 0;
    $maxPrice = isset($_POST['maxPrice']) ? floatval($_POST['maxPrice']) : 1000;

    // Main query with improved JOIN for better performance
    $query = "SELECT f.*, c.name as category_name, 
             COALESCE((SELECT AVG(rating) FROM reviews WHERE food_id = f.food_id), 0) as avg_rating,
             (SELECT COUNT(*) FROM reviews WHERE food_id = f.food_id) as review_count
             FROM food_listings f 
             LEFT JOIN food_categories c ON f.category_id = c.category_id 
             WHERE f.isApproved = 1 AND f.available = 1";

    $params = [];
    $types = "";

    // Price range filter
    $query .= " AND f.price BETWEEN ? AND ?";
    $params[] = $minPrice;
    $params[] = $maxPrice;
    $types .= "dd";

    // Category filter
    if (!empty($category)) {
        $query .= " AND c.name = ?";
        $params[] = $category;
        $types .= "s";
    }

    // Meal type filter
    if (!empty($mealTypes)) {
        $placeholders = implode(',', array_fill(0, count($mealTypes), '?'));
        $query .= " AND f.meal_type IN ($placeholders)";
        foreach ($mealTypes as $mealType) {
            $params[] = $mealType;
            $types .= "s";
        }
    }

    // Diet type filter - special handling for comma-separated values
    if (!empty($dietTypes)) {
        $dietConditions = [];
        foreach ($dietTypes as $dietType) {
            $dietConditions[] = "f.diet_type_suitable LIKE ?";
            $params[] = "%$dietType%";
            $types .= "s";
        }
        $query .= " AND (" . implode(" OR ", $dietConditions) . ")";
    }

    // Improved search with relevance scoring and fulltext matching for better matches
    if (!empty($search)) {
        $search = trim($search);
        $searchWords = explode(' ', $search);
        
        // Create relevance scoring
        $query .= " AND (";
        
        // Exact matches first (highest relevance)
        $exactConditions = [];
        $exactConditions[] = "f.food_name LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
        
        $exactConditions[] = "c.name LIKE ?";
        $params[] = "%$search%";
        $types .= "s";
        
        // Then partial matches for each word
        $partialConditions = [];
        foreach ($searchWords as $word) {
            if (strlen($word) > 2) { // Skip very short words
                $partialConditions[] = "f.food_name LIKE ?";
                $params[] = "%$word%";
                $types .= "s";
                
                $partialConditions[] = "f.description LIKE ?";
                $params[] = "%$word%";
                $types .= "s";
                
                $partialConditions[] = "f.diet_type_suitable LIKE ?";
                $params[] = "%$word%";
                $types .= "s";
                
                $partialConditions[] = "f.health_goal_suitable LIKE ?";
                $params[] = "%$word%";
                $types .= "s";
                
                $partialConditions[] = "c.name LIKE ?";
                $params[] = "%$word%";
                $types .= "s";
            }
        }
        
        $query .= implode(" OR ", $exactConditions);
        
        if (!empty($partialConditions)) {
            $query .= " OR " . implode(" OR ", $partialConditions);
        }
        
        $query .= ")";
    }

    // Add pagination support
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $perPage = isset($_POST['perPage']) ? intval($_POST['perPage']) : 10;
    $offset = ($page - 1) * $perPage;

    // Get total count first
    $countQuery = str_replace("SELECT f.*, c.name as category_name", "SELECT COUNT(*) as total", $query);
    $countQuery = preg_replace('/COALESCE\(\(SELECT.*?\)\).*?review_count,?/s', '', $countQuery);
    
    $countStmt = $conn->prepare($countQuery);
    if (!empty($params)) {
        $countStmt->bind_param($types, ...$params);
    }
    $countStmt->execute();
    $countResult = $countStmt->get_result();
    $totalCount = $countResult->fetch_assoc()['total'];
    $response['total'] = $totalCount;

    // Add sorting
    $query .= " ORDER BY 
                CASE 
                    WHEN f.food_name LIKE ? THEN 1
                    WHEN c.name LIKE ? THEN 2
                    ELSE 3
                END,
                f.created_at DESC";
    $params[] = !empty($search) ? "%$search%" : "%%";
    $params[] = !empty($search) ? "%$search%" : "%%";
    $types .= "ss";

    // Add limit and offset for pagination
    $query .= " LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    $types .= "ii";

    // Prepare and execute the main query
    $stmt = $conn->prepare($query);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $foods = [];
    while ($row = $result->fetch_assoc()) {
        // Format the data for the frontend
        $row['avg_rating'] = number_format((float)$row['avg_rating'], 1);
        $foods[] = $row;
    }

    $response['success'] = true;
    $response['foods'] = $foods;
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);