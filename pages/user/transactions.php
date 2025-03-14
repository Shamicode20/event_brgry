<?php
include '../database/connection.php';

$result = $conn->query("SELECT * FROM transactions ORDER BY created_at DESC");

echo "<h3>Transaction History</h3>";
echo "<table border='1'>";
echo "<tr><th>Transaction ID</th><th>Amount</th><th>Status</th><th>Receipt</th><th>Date</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . ($row['transaction_id'] ?? 'N/A') . "</td>";
    echo "<td>" . $row['amount'] . "</td>";
    echo "<td>" . $row['status'] . "</td>";
    echo "<td><a href='" . $row['receipt_url'] . "' target='_blank'>View</a></td>";
    echo "<td>" . $row['created_at'] . "</td>";
    echo "</tr>";
}

echo "</table>";
$conn->close();
?>
