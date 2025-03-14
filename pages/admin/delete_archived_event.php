<?php
session_start();
require_once '../../database/connection.php';




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = filter_var($_POST['id'], FILTER_VALIDATE_INT); // Ensure it's a valid integer

    if ($id) {
        try {
            $stmt = $conn->prepare("DELETE FROM archived_events WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['success'] = "Event permanently deleted.";
            } else {
                $_SESSION['error'] = "Event not found or already deleted.";
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Error deleting event: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "Invalid event ID.";
    }
} else {
    $_SESSION['error'] = "Invalid request.";
}

header("Location: ../admin/archived_events.php");
exit;
?>
