<?php
session_start();
include('includes/config.php');

class UserLogin {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function loginUser($username, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(?) AND status = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['is_admin'] = $row['is_admin'];
                return array('redirect' => 'dashboard.php'); // Return redirect URL
            } else {
                return array('error' => 'Invalid password'); // Return error message
            }
        } else {
            return array('error' => 'Invalid username or password'); // Return error message
        }

        $stmt->close();
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

// Check if the request is AJAX
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Ensure the request method is POST
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $userLogin = new UserLogin($conn);
        $response = $userLogin->loginUser($username, $password);

        $userLogin->closeConnection();

        echo json_encode($response); // Return response as JSON
    } else {
        // If the request method is not POST, return an error
        echo json_encode(array('error' => 'Invalid request method'));
    }
} else {
    // If the request is not AJAX, return an error
    echo json_encode(array('error' => 'Invalid request'));
}
?>
