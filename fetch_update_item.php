<?php
include('includes/config.php');

class ItemEditor {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function editItem($id, $name, $quantity, $removeImage, $imageFile) {
        // Check if the item exists
        $fetchItemSql = $this->conn->prepare("SELECT * FROM items WHERE id = ?");
        $fetchItemSql->bind_param("i", $id);
        $fetchItemSql->execute();
        $result = $fetchItemSql->get_result();

        if ($result->num_rows == 1) {
            // Fetch the item data
            $row = $result->fetch_assoc();
            $fetchItemSql->close();

            // Prepare the SQL statement based on whether an image is being removed or updated
            if ($removeImage) {
                $updateItemSql = $this->conn->prepare("UPDATE items SET name = ?, quantity = ?, image = NULL WHERE id = ?");
                $updateItemSql->bind_param("sii", $name, $quantity, $id);
            } else {
                // Check if a new image file is provided
                if ($imageFile && $imagePath = $this->uploadImage($imageFile)) {
                    $updateItemSql = $this->conn->prepare("UPDATE items SET name = ?, quantity = ?, image = ? WHERE id = ?");
                    $updateItemSql->bind_param("sssi", $name, $quantity, $imagePath, $id);
                } else {
                    // If no new image file is provided, update without changing the image
                    $updateItemSql = $this->conn->prepare("UPDATE items SET name = ?, quantity = ? WHERE id = ?");
                    $updateItemSql->bind_param("ssi", $name, $quantity, $id);
                }
            }

            // Execute the update query
            if ($updateItemSql->execute()) {
                $updateItemSql->close();
                return "Item updated successfully.";
            } else {
                $error = "Error updating item: " . $this->conn->error;
            }
        } else {
            $error = "Item not found.";
        }
    }

    private function uploadImage($file) {
        if ($file['name'] === ''){
            return false;
        }

        $targetDir = "uploads/";
        $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

        // Hash the file name to ensure uniqueness
        $hashedFilename = md5(uniqid()) . '.' . $imageFileType;
        $targetFile = $targetDir . $hashedFilename;

        // Check if the image file is a valid image
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            return false;
        }

        // Check file size
        if ($file["size"] > 50000000) {
            return false;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "webp"])) {
            return false;
        }

        // If everything is ok, try to upload file
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            return $targetFile;
        } else {
            return false;
        }
    }
}

$itemEditor = new ItemEditor($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $name = htmlspecialchars($_POST['name']);
    // Initialize $quantity variable with a default value of 0
    $quantity = 0;
    // Check if 'quantity' field is present in POST data
    if (isset($_POST['quantity'])) {
        $quantity = intval($_POST['quantity']);
    }
    $removeImage = isset($_POST['remove_image']) ? intval($_POST['remove_image']) : 0;

    $response = $itemEditor->editItem($id, $name, $quantity, $removeImage, $_FILES['image']);
    echo $response;
} else {
    echo "Invalid request.";
}

$conn->close();
?>
