<?php
require '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $items = $_POST['items'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $delivery_date = $_POST['delivery_date'];

    if (empty($items)) {
        echo "<script>alert('Please select at least one item.'); window.history.back();</script>";
        exit;
    }

    $conn = new mysqli();

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert each requested item into the database
    foreach ($items as $item) {
        $quantity = isset($quantities[$item]) ? intval($quantities[$item]) : 0;
        
        if ($quantity > 0) {
            $stmt = $conn->prepare("INSERT INTO requests (item_name, quantity, delivery_date) VALUES (?, ?, ?)");
            $stmt->bind_param("sis", $name, $quantity, $delivery_date);
            $stmt->execute();
            $stmt->close();
        }
    }

    $conn->close();
    
    echo "<script>alert('Request submitted successfully!'); window.location.href = 'index.php';</script>";
}
?>
