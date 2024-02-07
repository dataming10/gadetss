<?php
session_start();
include('includes/config.php');

class ItemSearch {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function searchItems($searchQuery) {
        // Fetch items based on search query
        $sql = "SELECT * FROM items WHERE (product_num LIKE ? OR name LIKE ?) AND status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ['error' => 'Failed to prepare statement: ' . $this->conn->error];
        }
        $searchParam = "%$searchQuery%";
        $stmt->bind_param("ss", $searchParam, $searchParam);
        if (!$stmt->execute()) {
            return ['error' => 'Failed to execute statement: ' . $stmt->error];
        }
        $result = $stmt->get_result();
        if (!$result) {
            return ['error' => 'Failed to get result: ' . $this->conn->error];
        }
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        // If no items found, try to suggest a correction
        if (empty($items)) {
            $suggestions = $this->suggestCorrection($searchQuery);
            if (!empty($suggestions)) {
                return ['error' => 'No items found.', 'suggestions' => $suggestions];
            } else {
                return ['error' => 'No items found. No suggestions available.'];
            }
        }   

        return $items;
    }

    private function suggestCorrection($searchQuery) {
        // Fetch all items from the database
        $sql = "SELECT product_num, name FROM items ORDER BY id DESC";
        $result = $this->conn->query($sql);
        if (!$result) {
            return ['error' => 'Failed to get items: ' . $this->conn->error];
        }
        $allItems = [];
        while ($row = $result->fetch_assoc()) {
            $allItems[] = $row['product_num'];
            $allItems[] = $row['name'];
        }

        // Calculate Levenshtein distance for each item name and product number
        $suggestions = [];
        foreach ($allItems as $item) {
            $levenshteinDistance = levenshtein(strtolower($searchQuery), strtolower($item));
            // Consider a suggestion if the Levenshtein distance is below a certain threshold
            if ($levenshteinDistance <= 1) {
                $suggestions[] = $item;
            }
        }

        return $suggestions;
    }
}

// Check if session variables are set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    echo json_encode(['error' => 'User session not found']);
    exit();
}

// Create ItemSearch instance
$itemSearch = new ItemSearch($conn);

// Fetch search query
$searchQuery = isset($_GET['q']) ? $_GET['q'] : '';

// Search for items based on the search query
$items = $itemSearch->searchItems($searchQuery);

if(isset($items['error'])) {
    echo json_encode(['error' => $items['error']]);
} else {
    echo json_encode($items);
}
exit();
?>
