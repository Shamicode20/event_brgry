<?php
session_start();
require '../database/connection.php';

// Get form data
$full_name         = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$contact_number    = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$delivery_datetime = isset($_POST['delivery_datetime']) ? $_POST['delivery_datetime'] : '';
$location          = isset($_POST['location']) ? $_POST['location'] : '';
$other_location    = isset($_POST['other_location']) ? trim($_POST['other_location']) : '';
$event_purpose     = isset($_POST['event_purpose']) ? $_POST['event_purpose'] : '';
$other_event       = isset($_POST['other_event']) ? trim($_POST['other_event']) : '';

// If "Others" is selected, use the user input for location
if ($location === "Others" && !empty($other_location)) {
    $location = $other_location;
}

// Process selected items and quantities
$items      = isset($_POST['items']) ? $_POST['items'] : array();
$quantities = isset($_POST['quantity']) ? $_POST['quantity'] : array();

$itemData = array();
foreach ($items as $itemId) {
    $qty = isset($quantities[$itemId]) ? intval($quantities[$itemId]) : 1;
    if ($qty > 0) {
        $itemData[$itemId] = $qty;
    }
}

// Convert arrays to JSON for storage (optional)
$items_json      = json_encode(array_keys($itemData));
$quantities_json = json_encode(array_values($itemData));

// Start transaction
$conn->begin_transaction();

try {
    // Insert request record into 'requests' table
    $sql = "INSERT INTO requests (full_name, contact_number, items, quantities, delivery_datetime, location, event_purpose, other_event, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ssssssss", $full_name, $contact_number, $items_json, $quantities_json, $delivery_datetime, $location, $event_purpose, $other_event);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $stmt->close();

    // For each requested item, update the inventory
    foreach ($itemData as $itemId => $requestedQty) {
        // Get current quantity from inventory
        $query = $conn->prepare("SELECT quantity FROM inventory WHERE id = ?");
        if (!$query) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $query->bind_param("i", $itemId);
        $query->execute();
        $query->bind_result($currentQty);
        $query->fetch();
        $query->close();

        // Check if enough stock is available
        if ($currentQty === null || $currentQty < $requestedQty) {
            throw new Exception("Not enough stock for item ID " . $itemId);
        }

        $newQty = $currentQty - $requestedQty;
        $update = $conn->prepare("UPDATE inventory SET quantity = ? WHERE id = ?");
        if (!$update) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $update->bind_param("ii", $newQty, $itemId);
        if (!$update->execute()) {
            throw new Exception("Execute failed: " . $update->error);
        }
        $update->close();
    }

    $conn->commit();
    $_SESSION['success'] = "Your request has been submitted successfully!";
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error submitting request: " . $e->getMessage();
}

$conn->close();
header("Location: logistic.php");
exit();
?>
