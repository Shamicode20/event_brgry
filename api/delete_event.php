<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

// Include database connection
define('BASE_PATH', realpath(__DIR__ . '/../../'));
require '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eventId = $_POST['id'];

    if (!empty($eventId)) {
        try {
            // Start transaction
            $conn->beginTransaction();

            // Get event details before deletion
            $stmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
            $stmt->execute([':id' => $eventId]);
            $event = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($event) {
                // Archive the event
                $stmt = $conn->prepare("INSERT INTO archived_events 
                                        (original_event_id, title, description, schedule, poster_base64, archived_at) 
                                        VALUES (:original_event_id, :title, :description, :schedule, :poster_base64, NOW())");

                $stmt->execute([
                    ':original_event_id' => $event['id'],
                    ':title' => $event['title'],
                    ':description' => $event['description'],
                    ':schedule' => $event['schedule'],
                    ':poster_base64' => $event['poster_base64']
                ]);

                // Delete the event
                $stmt = $conn->prepare("DELETE FROM events WHERE id = :id");
                $stmt->execute([':id' => $eventId]);

                // Commit transaction
                $conn->commit();
                $_SESSION['success'] = "Event archived and deleted successfully.";
            } else {
                $_SESSION['error'] = "Event not found.";
            }
        } catch (Exception $e) {
            $conn->rollBack(); // Rollback if an error occurs
            $_SESSION['error'] = "Error deleting event: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid event ID.";
    }
    
    header('Location: ../pages/admin/events.php');
    exit;
}
?>
