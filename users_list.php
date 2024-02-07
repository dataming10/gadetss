<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
session_start();
include('includes/config.php');

class UserManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getUsers() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $is_admin = $_SESSION['is_admin'];

        // Session fixation protection
        session_regenerate_id(true);

        // Check if the current user is an admin
        if ($is_admin == 1) {
            header("Location: access_denied.php");
            exit();
        }

        // Fetch only non-admin users using prepared statements
        $sql_users = "SELECT * FROM users WHERE is_admin = 1";
        $result_users = $this->conn->query($sql_users);

        return $result_users;
    }

    public function updateAdminStatus($user_id_to_update, $is_admin, $status) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Ensure that the current user is not trying to change their own admin status or status
        if ($user_id_to_update == $user_id) {
            return "You cannot change your own admin status or account status.";
        }

        // Update the user's admin status and account status using prepared statements
        $update_admin_sql = "UPDATE users SET is_admin = ?, status = ? WHERE id = ?";
        $update_admin_stmt = $this->conn->prepare($update_admin_sql);
        $update_admin_stmt->bind_param("iii", $is_admin, $status, $user_id_to_update);

        if ($update_admin_stmt->execute()) {
            return true;
        } else {
            return "Error updating user admin status or account status.";
        }
    }

    public function deleteUser($user_id_to_delete) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];

        // Ensure that the current user is not trying to delete themselves
        if ($user_id_to_delete == $user_id) {
            return "You cannot delete your own account.";
        }

        // Delete the user using prepared statements
        $delete_user_sql = "DELETE FROM users WHERE id = ?";
        $delete_user_stmt = $this->conn->prepare($delete_user_sql);
        $delete_user_stmt->bind_param("i", $user_id_to_delete);

        if ($delete_user_stmt->execute()) {
            return true;
        } else {
            return "Error deleting user.";
        }
    }
}

$userManager = new UserManager($conn);
$result_users = $userManager->getUsers();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $user_id_to_update = $_POST['user_id'];
    $is_admin = $_POST['is_admin'];
    $status = $_POST['status'];

    $updateResult = $userManager->updateAdminStatus($user_id_to_update, $is_admin, $status);
    if ($updateResult === true) {
        header("Location: users_list.php");
        exit();
    } else {
        $error = $updateResult;
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $user_id_to_delete = $_POST['user_id'];

    $deleteResult = $userManager->deleteUser($user_id_to_delete);
    if ($deleteResult === true) {
        header("Location: users_list.php");
        exit();
    } else {
        $error = $deleteResult;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
<?php include('includes/side_navbar.php'); ?>
    <h2>Users List</h2>
    <table id="usersTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Username</th>
                <th>Status</th>
                <th>Admin Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="assets/js/index.js"></script>
</body>
</html>
