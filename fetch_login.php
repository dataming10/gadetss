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
                return "Login successful. Redirecting...";
            } else {
                return "Invalid password";
            }
        } else {
            return "Invalid username or password";
        }

        $stmt->close();
    }

    public function closeConnection() {
        $this->conn->close();
    }
}

// Usage
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $userLogin = new UserLogin($conn);
    $result = $userLogin->loginUser($username, $password);

    $userLogin->closeConnection();

    echo $result;
}
?>
