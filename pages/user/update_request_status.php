<?php
session_start();
require '../../database/connection.php';

if (isset($_POST['request_id']) && isset($_POST['status'])) {
    $request_id = $_POST['request_id'];
    $status = $_POST['status'];

    try {
        // Update request status
        $stmt = $conn->prepare("UPDATE requests SET status = :status WHERE id = :request_id");
        $stmt->execute(['status' => $status, 'request_id' => $request_id]);

        // Get user_id from request
        $stmt = $conn->prepare("SELECT user_id FROM requests WHERE id = :request_id");
        $stmt->execute(['request_id' => $request_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_id = $user['user_id'];

        // Create notification
        $message = ($status == 'approved') ? "Your request #$request_id has been approved." : "Your request #$request_id has been rejected.";
        $stmt = $conn->prepare("INSERT INTO notifications (user_id, request_id, message) VALUES (:user_id, :request_id, :message)");
        $stmt->execute(['user_id' => $user_id, 'request_id' => $request_id, 'message' => $message]);

        $_SESSION['success'] = "Request status updated successfully.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating request: " . $e->getMessage();
    }
}

header("Location: user_dashboard.php");
exit();
?>
