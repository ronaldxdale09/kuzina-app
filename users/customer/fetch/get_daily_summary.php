<?php
header('Content-Type: application/json');
require_once '../../../connection/db.php';

class DailySummaryService {
    private $conn;

    public function __construct($database_connection) {
        $this->conn = $database_connection;
    }

    public function getDailySummary($customer_id, $date) {
        try {
            // Validate inputs
            if (!$customer_id || !$this->isValidDate($date)) {
                throw new InvalidArgumentException('Invalid input parameters');
            }

            // Corrected SQL query with proper syntax
            $query = "SELECT 
                ROUND(COALESCE(SUM(calories * `portion`), 0), 2) AS total_calories,
                ROUND(COALESCE(SUM(protein * `portion`), 0), 2) AS total_protein,
                ROUND(COALESCE(SUM(carbs * `portion`), 0), 2) AS total_carbs,
                ROUND(COALESCE(SUM(fat * `portion`), 0), 2) AS total_fat,
                COUNT(*) AS meal_count
            FROM food_journal 
            WHERE customer_id = ? AND entry_date = ?";

            // Prepare and execute statement
            $stmt = $this->conn->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("is", $customer_id, $date);
            $stmt->execute();
            
            if ($stmt->error) {
                throw new Exception("Execute failed: " . $stmt->error);
            }

            $result = $stmt->get_result();
            $totals = $result->fetch_assoc() ?? $this->getEmptyTotals();

            // Close statement
            $stmt->close();

            return [
                'success' => true,
                'totals' => array_map('floatval', $totals)
            ];

        } catch (Exception $e) {
            // Log the error (recommended)
            error_log("Daily Summary Error: " . $e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function isValidDate($date) {
        return (bool)strtotime($date);
    }

    private function getEmptyTotals() {
        return [
            'total_calories' => 0.0,
            'total_protein' => 0.0,
            'total_carbs' => 0.0,
            'total_fat' => 0.0,
            'meal_count' => 0
        ];
    }
}

// Main execution
try {
    // Input validation
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    $customer_id = $input['customer_id'] ?? null;
    $date = $input['date'] ?? date('Y-m-d');

    // Validate customer ID
    if (!$customer_id) {
        throw new Exception('Customer ID is required');
    }

    // Create service and get summary
    $summaryService = new DailySummaryService($conn);
    $response = $summaryService->getDailySummary($customer_id, $date);

    // Send response
    http_response_code($response['success'] ? 200 : 400);
    echo json_encode($response);

} catch (Exception $e) {
    // Log unexpected errors
    error_log("Unexpected error: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Internal server error: ' . $e->getMessage()
    ]);
} finally {
    // Always close the connection
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}