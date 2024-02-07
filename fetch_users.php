<?php
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
}

$userManager = new UserManager($conn);
$result_users = $userManager->getUsers();

$data = [];
while ($row = $result_users->fetch_assoc()) {
    $data[] = [
        'id' => $row['id'],
        'username' => $row['username'],
        'status' => $row['status'] == 1 ? 'Active' : 'Inactive',
        'is_admin' => $row['is_admin'] == 0 ? 'Yes' : 'Not Admin',
        'actions' => '<form method="post" action=""><input type="hidden" name="user_id" value="'.$row['id'].'"><label>Change Status:</label><select name="is_admin"><option value="0" '.($row['is_admin'] == 0 ? 'selected' : '').' onclick="return confirm(\'Are you sure you want to make this an admin?\')">Admin</option><option value="1" '.($row['is_admin'] == 1 ? 'selected' : '').'>User</option></select><select name="status"><option value="1" '.($row['status'] == 1 ? 'selected' : '').'>Active</option><option value="0" '.($row['status'] == 0 ? 'selected' : '').'>Inactive</option></select><button type="submit" name="submit">Update Status</button><button type="submit" name="delete" onclick="return confirm(\'Are you sure you want to delete this user?\')">Delete User</button></form>'
    ];
}

echo json_encode(['data' => $data]);
?>
