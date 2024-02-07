<?php
session_start();
include('includes/config.php');

class ItemManager {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addItem($name, $quantity, $image) {
        $name = mysqli_real_escape_string($this->conn, $name);
    
        // Generate a serial number for product_num with a maximum length of 10 characters
        $identifier = "PR-";
        $product_num = $identifier . strtoupper(substr(preg_replace('/-/', '', uniqid('', true)), 0, 10));
    
        // Check if the product with the same name or image already exists
        if ($this->isProductExists($product_num)) {
            return ['success' => false, 'error' => "Product with the same name or image already exists."];
        }
    
        $uploadResult = $this->uploadImage($image);
    
        if ($uploadResult['success']) {
            $target_file = $uploadResult['target_file'];
    
            $insertItemSql = $this->conn->prepare("INSERT INTO items (product_num, name, quantity, image) VALUES (?, ?, ?, ?)");
            $insertItemSql->bind_param("ssis", $product_num, $name, $quantity, $target_file);

            if ($insertItemSql->execute()) {
                return ['success' => true, 'message' => "Item added successfully."];
            } else {
                return ['success' => false, 'error' => "Error: " . $this->conn->error];
            }
    
            $insertItemSql->close();
        } else {
            return ['success' => false, 'error' => $uploadResult['error']];
        }
    }
    
    private function isProductExists($product_num) {
        $checkProductSql = $this->conn->prepare("SELECT id FROM items WHERE product_num = ?");
        $checkProductSql->bind_param("s", $product_num);
        $checkProductSql->execute();
        $result = $checkProductSql->get_result();

        return $result->num_rows > 0;
    }

    private function uploadImage($image) {
        $targetDir = "uploads/";
        $imageFileType = strtolower(pathinfo($image["name"], PATHINFO_EXTENSION));

        // Hash the file name to ensure uniqueness
        $hashedFilename = md5(uniqid()) . '.' . $imageFileType;
        $targetFile = $targetDir . $hashedFilename;

        // Check if image file is a valid image
        $check = getimagesize($image["tmp_name"]);
        if ($check === false) {
            return ['success' => false, 'error' => "File is not a valid image."];
        }

        // Check file size
        if ($image["size"] > 5000000) {
            return ['success' => false, 'error' => "Sorry, your file is too large."];
        }

        // Allow only certain file formats
        $allowedFormats = array("jpg", "jpeg", "png", "gif", "webp");
        if (!in_array($imageFileType, $allowedFormats)) {
            return ['success' => false, 'error' => "Sorry, only JPG, JPEG, PNG, WEBP and GIF files are allowed."];
        }

        // Upload the file
        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            return ['success' => true, 'target_file' => $targetFile];
        } else {
            return ['success' => false, 'error' => "Sorry, there was an error uploading your file."];
        }
    }
}

// Create ItemManager instance
$itemManager = new ItemManager($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if it's an AJAX request
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        // Process AJAX request
        $name = $_POST['name'];
        $quantity = intval($_POST['quantity']);
        $image = $_FILES["image"];

        $response = $itemManager->addItem($name, $quantity, $image);
        echo json_encode($response);
        exit();
    } else {
        // Non-AJAX request, redirect to the form page
        header("Location: add_item.php");
        exit();
    }
}

$conn->close();
?>

<!-- HTML CODE -->

<!DOCTYPE html>
<html>
<head>
    <title>Add Inventory Item</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <?php include('includes/side_navbar.php'); ?>
    <h2>Add Inventory Item</h2>
    <form id="addItemForm" enctype="multipart/form-data">
        <label>Name:</label>
        <input type="text" id="name" name="name" required><br>
        <label>Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br>
        <label>Image:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br>
        <button type="submit">Add Item</button>
    </form>
    <div id="response"></div>
    <script src="assets/js/index.js"></script>
</body>
</html>

