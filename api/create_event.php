<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

require '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $schedule = trim($_POST['schedule']);
    $location = trim($_POST['location']);
    $organizer_name = trim($_POST['organizer_name']);
    $organizer_contact = trim($_POST['organizer_contact']);
    $poster_base64 = null;

    // Validate required fields
    if (empty($title) || empty($description) || empty($schedule)) {
        $_SESSION['error'] = "All fields are required.";
        header('Location: ../pages/admin/events.php');
        exit;
    }

    // Handle image upload (optional)
    if (!empty($_FILES['poster']['tmp_name'])) {
        $image_data = file_get_contents($_FILES['poster']['tmp_name']);
        $poster_base64 = base64_encode($image_data);
    }

    try {
        $stmt = $conn->prepare("INSERT INTO events (title, poster_base64, description, schedule, location, organizer_name, organizer_contact) 
                                VALUES (:title, :poster_base64, :description, :schedule, :location, :organizer_name, :organizer_contact)");
        $stmt->execute([
            ':title' => $title,
            ':poster_base64' => $poster_base64 ?: null,  // Ensure null if no image
            ':description' => $description,
            ':schedule' => $schedule,
            ':location' => $location,
            ':organizer_name' => $organizer_name,
            ':organizer_contact' => $organizer_contact
        ]);

        $_SESSION['success'] = "Event created successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error creating event: " . $e->getMessage();
    }
    header("Location: ../pages/admin/events.php");
    exit;
}
?>
