<?php
session_start();
include('includes/config.php');

class DashboardManager {
    private $conn;
    private $user_id;
    private $is_admin;

    public function __construct($conn, $user_id, $is_admin) {
        $this->conn = $conn;
        $this->user_id = $user_id;
        $this->is_admin = $is_admin;
    }

    public function redirectNonAdmin() {
        if ($this->is_admin != 0) {
            header("Location: user_view.php");
            exit();
        }
    }

    public function fetchItems() {
        $sql = "SELECT * FROM items WHERE status = 1 ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Failed to get result: " . $this->conn->error);
        }
        return $result;
    }

    public function fetchUsers() {
        $sql = "SELECT * FROM users WHERE status = 1";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if (!$result) {
            throw new Exception("Failed to get result: " . $this->conn->error);
        }
        return $result;
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

// Check if session variables are set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin'])) {
    // Redirect to login page if session variables are not set
    header("Location: login.php");
    exit();
}

// Create DashboardManager instance
$dashboardManager = new DashboardManager($conn, $_SESSION['user_id'], $_SESSION['is_admin']);

try {
    // Redirect if user is not admin
    $dashboardManager->redirectNonAdmin();

    // Fetch items and users
    $result_items = $dashboardManager->fetchItems();
    $result_users = $dashboardManager->fetchUsers();

} catch (Exception $e) {
    // Handle any exceptions
    echo "Error: " . $e->getMessage();
}

// Close database connection
$dashboardManager->closeConnection();
?>

<!-- HTML CODE -->

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Include DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
</head>
<body>
    <?php include('includes/side_navbar.php'); ?>
    <h2>Inventory Items</h2>

    <table id="itemsTable">
        <thead>
            <tr>
                <th>Serial</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Image</th>
                <th>Actions</th>
                <?php if($is_admin) { echo '<th>Action</th>'; } ?>
            </tr>
        </thead>
    </table>

    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Include DataTables JS -->
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="assets/js/search.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
