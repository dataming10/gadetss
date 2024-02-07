<?php
session_start();
include('includes/config.php');

class ItemManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function fetchItems() {
        $sql = "SELECT * FROM items WHERE status = 1 ORDER BY id DESC";
        $result = $this->conn->query($sql);

        $data = array();
        while ($row = $result->fetch_assoc()) {
            $image = '<img src="'.$row['image'].'" alt="Image" style="width: 100px; height: 100px;">';
            $actions = '<a href="edit_item.php?id='.$row['id'].'">Edit</a> | <a href="delete_item.php?id='.$row['id'].'" onclick="return confirm(\'Are you sure you want to delete this item?\')">Delete</a> | <a href="activate_deactivate_item.php?id='.$row['id'].'" onclick="return confirm(\'Are you sure you want to deactivate this item?\')">'.(($row['status'] == 1) ? 'Deactivate' : 'Activate').'</a>';

            $data[] = array(
                'product_num' => $row['product_num'],
                'name' => $row['name'],
                'quantity' => $row['quantity'],
                'image' => $image,
                'actions' => $actions
            );
        }

        return json_encode(array('data' => $data));
    }
}

// Create ItemManager instance
$itemManager = new ItemManager($conn);

// Fetch items
echo $itemManager->fetchItems();
?>
