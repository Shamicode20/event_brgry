<?php
require '../../database/connection.php';

// Fetch only approved and rejected requests
$processedRequests = [];
try {
    $stmt = $conn->prepare("SELECT * FROM requests WHERE status IN ('Approved', 'Rejected') ORDER BY created_at DESC");
    $stmt->execute();
    $processedRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching processed requests: " . $e->getMessage();
}
?>
<?php include('navbar.php'); ?>
<?php include('sidebar.php'); ?>
<main role="main" class="main-content">
    <div class="container mt-4">
        <h2>Processed Requests (Approved & Rejected)</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Contact</th>
                    <th>Items</th>
                    <th>Delivery Date</th>
                    <th>Location</th>
                    <th>Purpose</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($processedRequests as $request) : ?>
                    <tr>
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo htmlspecialchars($request['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($request['items']); ?></td>
                        <td><?php echo htmlspecialchars($request['delivery_datetime']); ?></td>
                        <td><?php echo htmlspecialchars($request['location']); ?></td>
                        <td><?php echo htmlspecialchars($request['event_purpose']); ?></td>
                        <td>
                            <span class="badge bg-<?php echo ($request['status'] == 'Approved') ? 'success' : 'danger'; ?>">
                                <?php echo $request['status']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include('script.php'); ?>
