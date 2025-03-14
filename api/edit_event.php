<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

require '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $schedule = trim($_POST['schedule']);
    $location = trim($_POST['location']);
    $organizer_name = trim($_POST['organizer_name']);
    $organizer_contact = trim($_POST['organizer_contact']);
    $poster_base64 = null;

    // Validate required fields
    if (empty($title) || empty($description) || empty($schedule) || empty($location) || empty($organizer_name) || empty($organizer_contact)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ../pages/admin/events.php');
        exit;
    }

    try {
        // Start transaction
        $conn->beginTransaction();

        // Fetch current event details
        $stmt = $conn->prepare("SELECT poster_base64 FROM events WHERE id = ?");
        $stmt->execute([$event_id]);
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        $existing_poster = $event['poster_base64'];

        // Check if a new poster is uploaded
        if (!empty($_FILES['poster']['tmp_name'])) {
            $image_data = file_get_contents($_FILES['poster']['tmp_name']);
            $poster_base64 = base64_encode($image_data);
        } else {
            // Keep the existing poster if no new image is uploaded
            $poster_base64 = $existing_poster;
        }

        // Update event details
        $stmt = $conn->prepare("UPDATE events 
                                SET title = ?, 
                                    poster_base64 = ?, 
                                    description = ?, 
                                    schedule = ?, 
                                    location = ?, 
                                    organizer_name = ?, 
                                    organizer_contact = ? 
                                WHERE id = ?");
        $stmt->execute([
            $title,
            $poster_base64,
            $description,
            $schedule,
            $location,
            $organizer_name,
            $organizer_contact,
            $event_id
        ]);

        // Commit transaction
        $conn->commit();

        $_SESSION['success'] = "Event updated successfully!";
    } catch (Exception $e) {
        $conn->rollBack(); // Rollback transaction in case of an error
        $_SESSION['error'] = "Error updating event: " . $e->getMessage();
    }

    header("Location: ../pages/admin/events.php");
    exit;
}
?>
