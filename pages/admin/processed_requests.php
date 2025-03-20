<?php
session_start();
require '../../database/connection.php';

// Fetch all requests including pending ones
$requests = [];
try {
    $stmt = $conn->prepare("SELECT * FROM requests ORDER BY created_at DESC");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching requests: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>System UI Template</title>

    <!-- Styles -->
    <link rel="stylesheet" href="../../css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/feather.css">
    <link rel="stylesheet" href="../../css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="vertical light">
    <div class="wrapper">
        <?php include('navbar.php'); ?>
        <?php include('sidebar.php'); ?>

        <main role="main" class="main-content">
            <div class="container mt-4">
                <h2>Requests (Approved, Rejected & Pending)</h2>
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
                        <?php foreach ($requests as $request) : ?>
                            <tr id="row-<?php echo $request['id']; ?>">
                                <td><?php echo $request['id']; ?></td>
                                <td><?php echo htmlspecialchars($request['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($request['contact_number'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($request['items'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($request['delivery_datetime'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($request['location'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($request['event_purpose'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <?php
                                    $statusClass = "warning"; // Default yellow for Pending
                                    if ($request['status'] == 'Approved') {
                                        $statusClass = "success";
                                    } elseif ($request['status'] == 'Rejected') {
                                        $statusClass = "danger";
                                    }
                                    ?>
                                    <span class="badge bg-<?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($request['status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>

</html>
