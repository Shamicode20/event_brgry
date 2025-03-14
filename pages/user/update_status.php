<?php
require '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $requestId = $_POST['id'];
    $newStatus = $_POST['status'];

    try {
        $stmt = $conn->prepare("UPDATE requests SET status = :status WHERE id = :id");
        $stmt->execute([':status' => $newStatus, ':id' => $requestId]);
        echo "success";
    } catch (Exception $e) {
        echo "error";
    }
} else {
    echo "invalid_request";
}
?>
