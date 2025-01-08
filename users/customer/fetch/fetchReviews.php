<?php
include '../../../connection/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'reviews' => [], 'message' => ''];

$foodId = isset($_GET['food_id']) ? intval($_GET['food_id']) : 0;

if ($foodId > 0) {
    try {
        // Fetch all reviews for the food item
        $sql = "SELECT r.review_id, r.rating, r.comment, r.created_at, 
                       c.first_name, c.last_name, c.photo,
                       o.order_id, oi.food_id,
                       k.fname as kitchen_name, k.lname as kitchen_lname
                FROM reviews r
                JOIN customers c ON r.customer_id = c.customer_id
                JOIN orders o ON r.order_id = o.order_id
                JOIN order_items oi ON o.order_id = oi.order_id AND r.food_id = oi.food_id
                JOIN kitchens k ON o.kitchen_id = k.kitchen_id
                WHERE r.food_id = ?
                ORDER BY r.created_at DESC";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $foodId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $reviews = [];

            while ($row = $result->fetch_assoc()) {
                // Process customer photo path
                $customerPhoto = !empty($row['photo']) 
                    ? "../../uploads/profile/" . htmlspecialchars($row['photo']) 
                    : "assets/images/avatar/avatar2.png";

                $reviews[] = [
                    'review_id' => $row['review_id'],
                    'order_id' => $row['order_id'],
                    'customer_name' => htmlspecialchars($row['first_name'] . ' ' . $row['last_name']),
                    'customer_photo' => $customerPhoto,
                    'kitchen_name' => htmlspecialchars($row['kitchen_name'] . ' ' . $row['kitchen_lname']),
                    'rating' => intval($row['rating']),
                    'comment' => htmlspecialchars($row['comment']),
                    'created_at' => date("F j, Y", strtotime($row['created_at']))
                ];
            }

            $response['success'] = true;
            $response['reviews'] = $reviews;
        } else {
            $response['message'] = 'No reviews found for this food item.';
        }

        // Get average rating
        $avgSql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count 
                   FROM reviews 
                   WHERE food_id = ?";
        $avgStmt = $conn->prepare($avgSql);
        $avgStmt->bind_param("i", $foodId);
        $avgStmt->execute();
        $avgResult = $avgStmt->get_result()->fetch_assoc();

        $response['avg_rating'] = round($avgResult['avg_rating'], 1);
        $response['review_count'] = (int)$avgResult['review_count'];

    } catch (Exception $e) {
        $response['message'] = 'Error fetching reviews: ' . $e->getMessage();
    } finally {
        if (isset($stmt)) $stmt->close();
        if (isset($avgStmt)) $avgStmt->close();
    }
} else {
    $response['message'] = 'Invalid food ID.';
}

echo json_encode($response);
?>