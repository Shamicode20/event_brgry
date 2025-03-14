<?php
require '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $requestId = $_POST['id'];

    try {
        $stmt = $conn->prepare("DELETE FROM requests WHERE id = ?");
        $stmt->execute([$requestId]);

        if ($stmt->rowCount() > 0) {
            echo "success"; // Sent back to AJAX
        } else {
            echo "error";
        }
    } catch (Exception $e) {
        echo "error";
    }
} else {
    echo "error";
}
?>
