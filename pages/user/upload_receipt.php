<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include '../database/connection.php'; // Include your database connection
    
    $user_id = $_POST['user_id'];
    $receipt = $_FILES['receipt'];

    // File upload handling
    $target_dir = "uploads/";
    $receipt_url = $target_dir . basename($receipt["name"]);
    
    if (move_uploaded_file($receipt["tmp_name"], $receipt_url)) {
        // Save transaction in the database
        $stmt = $conn->prepare("INSERT INTO transactions (user_id, receipt_url, status) VALUES (?, ?, 'pending')");
        $stmt->bind_param("is", $user_id, $receipt_url);
        if ($stmt->execute()) {
            echo "Receipt uploaded successfully. Awaiting verification.";
        } else {
            echo "Error saving transaction.";
        }
        $stmt->close();
    } else {
        echo "Error uploading receipt.";
    }
    
    $conn->close();
}
?>
