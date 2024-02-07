<?php
session_start();
include('includes/config.php');

class DeactivatedItemsManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function fetchDeactivatedItems() {
        if (!isset($_SESSION['user_id'])) {
            header("HTTP/1.1 401 Unauthorized");
            exit();
        }
        
        // Fetch deactivated items using prepared statements
        $sql_deactivated_items = "SELECT * FROM items WHERE status = 0";
        $stmt_deactivated_items = $this->conn->prepare($sql_deactivated_items);

        if ($stmt_deactivated_items) {
            $stmt_deactivated_items->execute();
            $result_deactivated_items = $stmt_deactivated_items->get_result();
            
            if ($result_deactivated_items->num_rows > 0) {
                $items = array();
                while ($row = $result_deactivated_items->fetch_assoc()) {
                    // Prepare data for DataTables
                    $rowData = array(
                        "product_num" => $row['product_num'],
                        "name" => $row['name'],
                        "quantity" => $row['quantity'],
                        "image" => '<img src="' . $row['image'] . '" alt="Image" style="width: 150px; height: 150px;">',
                        "actions" => '<a href="delete_item.php?id=' . $row['id'] . '" onclick="return confirm(\'Are you sure you want to delete this item?\')">Delete</a> | <a href="activate_item.php?id=' . $row['id'] . '" onclick="return confirm(\'Are you sure want to activate it?\')">Activate</a>'
                    );
                    $items[] = $rowData;
                }

                // Output the JSON response
                echo json_encode(array("data" => $items));
            } else {
                // No deactivated items found
                echo json_encode(array("data" => array()));
            }
        } else {
            // Handle the error appropriately (e.g., log it)
            die("Error in prepared statement: " . $this->conn->error);
        }
    }
}

$deactivatedItemsManager = new DeactivatedItemsManager($conn);
$deactivatedItemsManager->fetchDeactivatedItems();
?>
