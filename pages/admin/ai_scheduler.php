<?php
require('../../database/connection.php');

try {
    // Kunin lahat ng events mula sa 'events' table (i-adjust ang query ayon sa structure ng database niyo)
    $stmt = $conn->prepare("SELECT id, title, description, schedule, location FROM events");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sample simple scheduling logic: i-sort ang events ayon sa schedule (ascending)
    usort($events, function($a, $b) {
        return strtotime($a['schedule']) - strtotime($b['schedule']);
    });

    // Optional: Dito ka pwedeng magdagdag ng conflict resolution logic, priority handling, at resource optimization

    // I-output ang optimized schedule sa JSON format
    header('Content-Type: application/json');
    echo json_encode($events);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
}
?>
