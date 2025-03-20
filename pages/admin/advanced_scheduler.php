<?php
require('../../database/connection.php');

try {
    // Kunin ang lahat ng events mula sa 'events' table
    // Siguraduhin na mayroon kang columns: id, title, description, schedule, priority, at location.
    // Ang 'schedule' dito ay magiging basehan lamang ng petsa.
    $stmt = $conn->prepare("SELECT id, title, description, schedule, location FROM events ORDER BY schedule ASC");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "Query failed: " . $e->getMessage()]);
    exit;
}

// Function para malaman kung nagko-conflict ang dalawang events batay lamang sa petsa.
function eventsConflict($event1, $event2) {
    $date1 = date('Y-m-d', strtotime($event1['schedule']));
    $date2 = date('Y-m-d', strtotime($event2['schedule']));
    return ($date1 === $date2);
}

// Simpleng conflict resolution algorithm:
// Kung may conflict (magkaparehong petsa), ililipat ang schedule ng kasalukuyang event sa susunod na araw (dagdag 1 araw) hanggang walang conflict.
$optimizedEvents = [];
foreach ($events as $event) {
    if (empty($optimizedEvents)) {
        $optimizedEvents[] = $event;
    } else {
        $lastEvent = end($optimizedEvents);
        if (eventsConflict($lastEvent, $event)) {
            // Shift ang event sa susunod na araw hanggang walang conflict
            $newSchedule = strtotime($event['schedule']);
            while (eventsConflict($lastEvent, ['schedule' => date('Y-m-d', $newSchedule)])) {
                $newSchedule += 86400; // dagdag 86400 seconds = 1 araw
            }
            $event['schedule'] = date('Y-m-d', $newSchedule);
        }
        $optimizedEvents[] = $event;
    }
}

header('Content-Type: application/json');
echo json_encode($optimizedEvents);
exit;
?>
