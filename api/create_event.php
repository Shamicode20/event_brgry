<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

require '../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $event_type = trim($_POST['event_type']);
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
        // Start transaction
        $conn->beginTransaction();

        // Insert event into database (PDO)
        $stmt = $conn->prepare("INSERT INTO events (title, event_type, poster_base64, description, schedule, location, organizer_name, organizer_contact) 
                                VALUES (:title, :event_type, :poster_base64, :description, :schedule, :location, :organizer_name, :organizer_contact)");
        $stmt->execute([
            ':title' => $title,
            ':event_type' => $event_type,
            ':poster_base64' => $poster_base64 ?: null,  // Ensure null if no image
            ':description' => $description,
            ':schedule' => $schedule,
            ':location' => $location,
            ':organizer_name' => $organizer_name,
            ':organizer_contact' => $organizer_contact
        ]);

        // Check and update inventory (Using PDO)
        if (isset($_POST['items']) && isset($_POST['quantity'])) {
            $items = $_POST['items'];
            $quantities = $_POST['quantity'];

            foreach ($items as $itemId) {
                $requestedQty = intval($quantities[$itemId]);

                // Get current quantity
                $query = $conn->prepare("SELECT quantity FROM inventory WHERE id = :id");
                $query->execute([':id' => $itemId]);
                $currentQty = $query->fetchColumn();

                // Check stock availability
                if ($currentQty === false || $currentQty < $requestedQty) {
                    throw new Exception("Not enough stock for item ID " . $itemId);
                }

                // Update inventory
                $newQty = $currentQty - $requestedQty;
                $update = $conn->prepare("UPDATE inventory SET quantity = :newQty WHERE id = :id");
                $update->execute([':newQty' => $newQty, ':id' => $itemId]);
            }
        }

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Event created successfully!";
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Error: " . $e->getMessage();
    }

    header("Location: ../pages/admin/events.php");
    exit;
}
?>
