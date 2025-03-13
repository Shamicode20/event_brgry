<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

require '../database/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $schedule = trim($_POST['schedule']);
    $poster = $_FILES['poster'];

    // Default image (empty if no poster uploaded)
    $posterBase64 = '';

    // Convert uploaded image to Base64
    if (!empty($poster['tmp_name'])) {
        $imageData = file_get_contents($poster['tmp_name']); // Get raw image data
        $posterBase64 = base64_encode($imageData); // Convert to Base64
    }

    try {
        $stmt = $conn->prepare("INSERT INTO events (title, description, schedule, poster_base64, created_by) 
                                VALUES (:title, :description, :schedule, :poster_base64, :created_by)");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':schedule' => $schedule,
            ':poster_base64' => $posterBase64, // Save Base64 string
            ':created_by' => $_SESSION['user_id']
        ]);

        $_SESSION['success'] = "Event created successfully.";
        header('Location: ../pages/admin/events.php');
    } catch (Exception $e) {
        $_SESSION['error'] = "Error creating event: " . $e->getMessage();
        header('Location: ../pages/admin/events.php');
    }
}
