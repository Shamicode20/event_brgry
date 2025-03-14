<?php
session_start();
require '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        echo "error";
        exit;
    }

    $requestId = $_POST['id'];

    try {
        $stmt = $conn->prepare("UPDATE requests SET status = 'Cancelled' WHERE id = ?");
        $stmt->execute([$requestId]);

        if ($stmt->rowCount() > 0) {
            echo "success";
        } else {
            echo "error";
        }
    } catch (Exception $e) {
        echo "error";
    }
}
?>
