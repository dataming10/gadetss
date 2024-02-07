<?php
session_start();
include('includes/config.php');

class InventoryManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getInventoryItems($isAdmin) {
        // Fetch only active items with quantity greater than 0
        $sql_items = "SELECT * FROM items WHERE status = 1 AND quantity > 0 ORDER BY id DESC";
        $result_items = $this->conn->query($sql_items);
        return $result_items;
    }
}

$inventoryManager = new InventoryManager($conn);
$result_items = $inventoryManager->getInventoryItems($_SESSION['is_admin']);

$data = array();
if ($result_items->num_rows > 0) {
    while ($row = $result_items->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
$conn->close();
?>
