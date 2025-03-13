<?php
session_start();
require '../../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];
    $new_status = isset($_POST['approve']) ? 'Approved' : 'Rejected';

    try {
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $request_id]);
        $_SESSION['success'] = "Request has been " . strtolower($new_status) . ".";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating request: " . $e->getMessage();
    }
}

header("Location: admin_requests.php");
exit;
