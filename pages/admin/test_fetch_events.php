<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "event_management";
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch Scheduled Events
$sql = "SELECT id, title, schedule, location, organizer_name FROM events WHERE schedule IS NOT NULL";
$result = $conn->query($sql);
$events = [];

while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

header('Content-Type: application/json');
echo json_encode($events, JSON_PRETTY_PRINT);
$conn->close();
?>
