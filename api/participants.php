<?php
session_start();
require_once '../database/connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$event_id = $_POST['event_id'];

try {
    // Check if the user already joined
    $stmt = $conn->prepare("SELECT * FROM event_participants WHERE user_id = ? AND event_id = ?");
    $stmt->execute([$user_id, $event_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "You already joined this event."]);
        exit;
    }

    // Insert into event_participants
    $stmt = $conn->prepare("INSERT INTO event_participants (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$user_id, $event_id]);

    echo json_encode(["status" => "success", "message" => "Successfully joined the event."]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
