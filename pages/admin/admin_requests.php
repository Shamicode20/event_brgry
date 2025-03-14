<?php
session_start();
require '../../database/connection.php';

// Fetch all requests
$requests = [];
try {
    $stmt = $conn->prepare("SELECT * FROM requests ORDER BY created_at DESC");
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching requests: " . $e->getMessage();
}
?>

 <?php include('navbar.php')?>
      <?php include('sidebar.php')?>
      <main role="main" class="main-content">
      <!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>System UI Template</title>

    <!-- Simple bar CSS (for scvrollbar)-->
    <link rel="stylesheet" href="../../css/simplebar.css">
    <!-- Fonts CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <!-- Icons CSS -->
    <link rel="stylesheet" href="../../css/feather.css">
    <!-- App CSS -->
    <link rel="stylesheet" href="../../css/main.css">   
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>


    <style>
    .avatar-initials {
    width: 165px;
    height: 165px;
    border-radius: 50%;
    display: flex;
    margin-left: 8px;
    justify-content: center;
    align-items: center;
    font-size: 50px;
    font-weight: bold;
    color: #fff;
    
    }

    .avatar-initials-min {
    width: 40px;
    height: 40px;
    background: #75e6da;
    border-radius: 50%;
    display: flex;
    margin-left: 8px;
    justify-content: center;
    align-items: center;
    font-size: 14px;
    font-weight: bold;
    color: #fff;
    
  }

    .upload-icon {
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  font-size: 24px;
  color: #fff;
  opacity: 0;
  transition: opacity 0.3s ease-in-out;
  background-color: #333;
  padding: 10px;
  border-radius: 50%;
  z-index: 1;
}

.avatar-img:hover .upload-icon {
  opacity: 1;
}

.avatar-img {
  position: relative;
  transition: background-color 0.3s ease-in-out;
}

.avatar-img:hover {
  background-color: #a0f0e6;
}

.dashboard-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

.dashboard-item {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 250px;
    transition: transform 0.3s ease-in-out;
}

.dashboard-item:hover {
    transform: scale(1.05);
}

.dashboard-item h2 {
    font-size: 18px;
    color: #333;
    margin-bottom: 5px;
}

.dashboard-item h3 {
    font-size: 22px;
    color: #007bff;
    margin-bottom: 10px;
}

.chart-container img {
    width: 100%;
    height: auto;
    border-radius: 5px;
}

.dashboard-header {
    display: flex;
    justify-content: flex-end;
    padding: 10px 20px;
}

.add-item-btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}

.add-item-btn:hover {
    background-color: #0056b3;
}

</style>
  
  </head>

    
    
 
  <body class="vertical  light">
    <div class="wrapper">
    

    <div class="container mt-4">
        <h2>Submitted Requests</h2>
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
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request) : ?>
                    <tr id="row-<?php echo $request['id']; ?>">
                        <td><?php echo $request['id']; ?></td>
                        <td><?php echo htmlspecialchars($request['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($request['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($request['items']); ?></td>
                        <td><?php echo htmlspecialchars($request['delivery_datetime']); ?></td>
                        <td><?php echo htmlspecialchars($request['location']); ?></td>
                        <td><?php echo htmlspecialchars($request['event_purpose']); ?></td>
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
                                <?php echo htmlspecialchars($request['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($request['status'] == 'Pending') : ?>
                                <form action="update_request_status.php" method="POST" class="d-inline">
                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success btn-sm">Approve</button>
                                    <button type="submit" name="reject" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            <?php else : ?>
                                <button class="btn btn-danger btn-sm delete-request" data-id="<?php echo $request['id']; ?>">Delete</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

</tbody>


    </table>
</div>

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php include('script.php'); ?>

<script>
    $(document).ready(function() {
    $(".delete-request").on("click", function() {
        var requestId = $(this).data("id");

        if (confirm("Are you sure you want to delete this request?")) {
            $.ajax({
                url: "delete_request.php",
                type: "POST",
                data: { id: requestId },
                dataType: "text", // Ensure correct response type
                success: function(response) {
                    console.log("Server response:", response);
                    if (response.trim() === "success") {
                        $("#row-" + requestId).fadeOut("slow", function() {
                            $(this).remove(); // Remove the row from the table
                        });
                    } else {
                        alert("Failed to delete the request.");
                    }
                },
                error: function(xhr, status, error) {
                    console.log("AJAX Error:", error);
                    alert("An error occurred while deleting the request.");
                }
            });
        }
    });
});

</script>