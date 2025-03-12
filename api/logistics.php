<?php
session_start();
require '../database/connection.php'; // Ensure this file correctly connects to the database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Get product details
        $name = trim($_POST['name']);
        $quantity = intval($_POST['quantity']);
        $image_url = null;

        // Handle image upload
        if (!empty($_FILES['image']['name'])) {
            $target_dir = __DIR__ . "/../../assets/img/"; // Use absolute path
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true); // Ensure the directory exists
            }
            
            $image_name = time() . "_" . basename($_FILES["image"]["name"]); // Prevent duplicate names
            $target_file = $target_dir . $image_name;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $allowed_types = ["jpg", "jpeg", "png", "gif"];

            // Validate image
            if (!in_array($imageFileType, $allowed_types)) {
                $_SESSION['error'] = "Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.";
                header("Location: ../../admin/logistic.php");
                exit();
            }

            // Upload file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $image_name;
            } else {
                $_SESSION['error'] = "Error uploading image.";
                header("Location: ../../admin/logistic.php");
                exit();
            }
        }

        // Insert product into the database
        $stmt = $conn->prepare("INSERT INTO inventory (name, quantity, image_url) VALUES (:name, :quantity, :image_url)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':quantity', $quantity);
        $stmt->bindParam(':image_url', $image_url);
        $stmt->execute();

        $_SESSION['success'] = "Product added successfully!";
        header("Location: ../../admin/logistic.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Error adding product: " . $e->getMessage();
        header("Location: ../../admin/logistic.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Invalid request!";
    header("Location: ../../admin/logistic.php");
    exit();
}
