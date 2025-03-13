<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bara_event_app");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
$contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
$delivery_datetime = isset($_POST['delivery_datetime']) ? $_POST['delivery_datetime'] : '';
$location = isset($_POST['location']) ? $_POST['location'] : '';
$other_location = isset($_POST['other_location']) ? trim($_POST['other_location']) : NULL;
$event_purpose = isset($_POST['event_purpose']) ? $_POST['event_purpose'] : '';
$other_event = isset($_POST['other_event']) ? trim($_POST['other_event']) : NULL;

// If "Other" location is selected, use the user input instead
if ($location === "Others" && !empty($other_location)) {
    $location = $other_location;
}

// Process selected items and quantities
$items = isset($_POST['items']) ? $_POST['items'] : [];
$quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];

$itemData = [];
foreach ($items as $item) {
    $qty = isset($quantities[$item]) ? intval($quantities[$item]) : 1;
    if ($qty > 0) {
        $itemData[$item] = $qty;
    }
}

// Convert arrays to JSON for storage
$items_json = json_encode(array_keys($itemData));
$quantities_json = json_encode(array_values($itemData));

// Insert request into the requests table
$sql = "INSERT INTO requests (full_name, contact_number, items, quantities, delivery_datetime, location, event_purpose, other_event) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssss", $full_name, $contact_number, $items_json, $quantities_json, $delivery_datetime, $location, $event_purpose, $other_event);

if ($stmt->execute()) {
    // Reduce stock in inventory
    foreach ($itemData as $itemName => $requestedQty) {
        // Get the current stock of the item
        $query = $conn->prepare("SELECT quantity FROM inventory WHERE name = ?");
        $query->bind_param("s", $itemName);
        $query->execute();
        $query->bind_result($currentQty);
        $query->fetch();
        $query->close();

        // Ensure requested quantity is available
        if ($currentQty !== null && $currentQty >= $requestedQty) {
            $newQty = $currentQty - $requestedQty;

            // Update inventory
            $update = $conn->prepare("UPDATE inventory SET quantity = ? WHERE name = ?");
            $update->bind_param("is", $newQty, $itemName);
            $update->execute();
            $update->close();
        } else {
            $_SESSION['error'] = "Not enough stock for item: " . htmlspecialchars($itemName);
            header("Location: logistic.php");
            exit();
        }
    }

    $_SESSION['success'] = "Your request has been submitted successfully!";
} else {
    $_SESSION['error'] = "Error submitting request: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();

// Redirect back to the main page
header("Location: logistic.php");
exit();
