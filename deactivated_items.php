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
            header("Location: login.php");
            exit();
        }
        
        $user_id = $_SESSION['user_id'];
        $is_admin = $_SESSION['is_admin'];

        // Session fixation protection
        session_regenerate_id(true);

        // Fetch deactivated items using prepared statements
        $sql_deactivated_items = "SELECT * FROM items WHERE status = 0";
        $stmt_deactivated_items = $this->conn->prepare($sql_deactivated_items);

        if ($stmt_deactivated_items) {
            $stmt_deactivated_items->execute();
            $result_deactivated_items = $stmt_deactivated_items->get_result();
        } else {
            // Handle the error appropriately (e.g., log it)
            die("Error in prepared statement: " . $this->conn->error);
        }

        $this->conn->close();

        return $result_deactivated_items;
    }
}

$deactivatedItemsManager = new DeactivatedItemsManager($conn);
$result_deactivated_items = $deactivatedItemsManager->fetchDeactivatedItems();
?>

<!-- HTML CODE -->

<!DOCTYPE html>
<html>
<head>
    <title>Deactivated Items</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
</head>
<body>
    <?php include('includes/side_navbar.php'); ?>
    <h2>Deactivated Items</h2>

    <table id="deactivatedItemsTable" class="display">
        <thead>
            <tr>
                <th>Serial</th>
                <th>Name</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Include DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
