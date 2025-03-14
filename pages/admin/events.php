<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}


define('BASE_PATH', dirname(__DIR__, 2));
require_once BASE_PATH . '/database/connection.php';


// Fetch events dynamically
try {
    $stmt = $conn->prepare("SELECT * FROM events ORDER BY schedule DESC");
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching events: " . $e->getMessage();
    $events = [];
}

// Display success or error messages
$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$errorMessage = isset($_SESSION['error']) ? $_SESSION['error'] : null;
unset($_SESSION['success'], $_SESSION['error']); // Clear messages after displaying
?>


<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

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
<!-- FullCalendar CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">

<!-- FullCalendar JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>


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

.card {
    transition: transform 0.2s ease-in-out, box-shadow 0.3s;
    border-radius: 10px;
}

.card:hover {
    transform: scale(1.03);
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-size: 18px;
    font-weight: bold;
}

.card-text {
    font-size: 14px;
}

.btn-sm {
    font-size: 13px;
    padding: 5px 10px;
}

/* Dark Mode Support */
body.dark-mode .card {
    background-color: #2c2c2c;
    color: #e0e0e0;
}

body.dark-mode .btn-warning {
    background-color: #f0ad4e;
    border-color: #eea236;
    color: #fff;
}

body.dark-mode .btn-danger {
    background-color: #d9534f;
    border-color: #d43f3a;
    color: #fff;
}



</style>
  
  </head>

  <div id="calendar"></div>

    
 
  <body class="vertical  light">
    <div class="wrapper">
    

    <?php include('navbar.php')?>
      <?php include('sidebar.php')?>
      <main role="main" class="main-content">
        
        <!--For Notification header naman ito-->

        <div class="modal fade modal-notif modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="defaultModalLabel">Notifications</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>


              <div class="modal-body">
  <div class="list-group list-group-flush my-n3">
   
      <div class="col-12 mb-4">
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="notification">
          <img class="fade show" src="../../assets/images/unified-lgu-logo.png" width="35" height="35">
          <strong style="font-size:12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></strong> 
          <button type="button" class="close" data-dismiss="alert" aria-label="Close" onclick="removeNotification()">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
      </div> <!-- /. col -->

    <div id="no-notifications" style="display: none; text-align:center; margin-top:10px;">No notifications</div>
  </div> <!-- / .list-group -->
 
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary btn-block" onclick="clearAllNotifications()">Clear All</button>
              </div>
            </div>
          </div>
        </div>
      <!--YOUR CONTENTHERE-->
      <div id="page-content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Events</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Create</button>
    </div>
    <div class="row">
        <!-- Notification Messages (Placed at the Top) -->
<div class="container mt-3">
    <?php if ($successMessage) : ?>
        <div class="alert alert-success alert-dismissible fade show text-center fw-bold" role="alert">
            <?php echo htmlspecialchars($successMessage); ?>
          
        </div>
    <?php endif; ?>

    <?php if ($errorMessage) : ?>
        <div class="alert alert-danger alert-dismissible fade show text-center fw-bold" role="alert">
            <?php echo htmlspecialchars($errorMessage); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Rest of your events.php content -->
<div class="container">
   
    <!-- Event listing and other content here -->
</div>
    <?php if (!empty($events)) : ?>
    <?php foreach ($events as $event) : ?>
        <div class="col-md-4 col-sm-6 col-xs-12 mb-4">
            <div class="card">
                <?php if (!empty($event['poster_base64'])) : ?>
                    <img src="data:image/jpeg;base64,<?php echo htmlspecialchars_decode($event['poster_base64']); ?>" class="card-img-top">

                         
                <?php else : ?>
                    <img src="../../assets/images/default.jpg" 
                         alt="Default Image" 
                         class="card-img-top">
                <?php endif; ?>
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                    <p class="card-text"><strong>Schedule:</strong> <?php echo date("F d, Y h:i A", strtotime($event['schedule'])); ?></p>

                    <!-- Buttons -->
                    <button class="btn btn-warning btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal"
                            data-id="<?php echo $event['id']; ?>"
                            data-title="<?php echo htmlspecialchars($event['title']); ?>"
                            data-description="<?php echo htmlspecialchars($event['description']); ?>"
                            data-schedule="<?php echo $event['schedule']; ?>">
                        Edit
                    </button>

                    <button class="btn btn-danger btn-sm" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal"
                            data-id="<?php echo $event['id']; ?>">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    <p class="text-center w-100">No events available at the moment.</p>
<?php endif; ?>

</div>



<script>
$(document).ready(function() {
    $('.request-item-btn').on('click', function() {
        var itemId = $(this).data('id');
        var itemName = $(this).data('name');
        var maxQuantity = $(this).data('max');
        var requestedQuantity = $(this).siblings('.item-quantity').val();

        // Validate input
        if (!requestedQuantity || requestedQuantity <= 0 || requestedQuantity > maxQuantity) {
            alert('Please enter a valid quantity (1 to ' + maxQuantity + ').');
            return;
        }

        // Send request (AJAX or form submission can be added here)
        alert('You have requested ' + requestedQuantity + ' of ' + itemName + '.');
    });
});
</script>

    </div>
</div>

<!-- Modals -->
<!-- Create Event Modal -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="../../api/create_event.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="schedule" class="form-label">Schedule</label>
                        <input type="datetime-local" class="form-control" id="schedule" name="schedule" required>
                    </div>
                    <div class="mb-3">
                        <label for="poster" class="form-label">Poster</label>
                        <input type="file" class="form-control" id="poster" name="poster">
                    </div>
                </div>
                <div class="mb-3">
    <label for="location" class="form-label">Location</label>
    <input type="text" class="form-control" id="location" name="location" required>
</div>
<div class="mb-3">
    <label for="organizer_name" class="form-label">Organizer Name</label>
    <input type="text" class="form-control" id="organizer_name" name="organizer_name" required>
</div>
<div class="mb-3">
    <label for="organizer_contact" class="form-label">Organizer Contact</label>
    <input type="text" class="form-control" id="organizer_contact" name="organizer_contact" required>
</div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form action="../../api/edit_event.php" method="POST">
                <form id="editEventForm" enctype="multipart/form-data">
                    <input type="hidden" id="editEventId" name="event_id">
                    
                    <div class="mb-3">
                        <label for="editTitle" class="form-label">Event Title</label>
                        <input type="text" class="form-control" id="editTitle" name="title" required>
                    </div>

                    <div class="mb-3">
                        <label for="editDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="editDescription" name="description" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="editSchedule" class="form-label">Schedule</label>
                        <input type="datetime-local" class="form-control" id="editSchedule" name="schedule" required>
                    </div>

                    <div class="mb-3">
                        <label for="editLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="editLocation" name="location" required>
                    </div>

                    <div class="mb-3">
                        <label for="editOrganizerName" class="form-label">Organizer Name</label>
                        <input type="text" class="form-control" id="editOrganizerName" name="organizer_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="editOrganizerContact" class="form-label">Organizer Contact</label>
                        <input type="text" class="form-control" id="editOrganizerContact" name="organizer_contact" required>
                    </div>

              
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Delete Event Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="../../api/delete_event.php" method="POST">
                <input type="hidden" id="deleteEventId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this event?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>
     </main>
     </div>

      
      
      
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include('script.php')?>
  <script>
 document.addEventListener("DOMContentLoaded", function () {
    let editModal = document.getElementById("editModal");
    if (editModal) {
        editModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            document.getElementById("editEventId").value = button.getAttribute("data-id");
            document.getElementById("editTitle").value = button.getAttribute("data-title");
            document.getElementById("editDescription").value = button.getAttribute("data-description");
            document.getElementById("editSchedule").value = button.getAttribute("data-schedule");
            document.getElementById("editLocation").value = button.getAttribute("data-location");
            document.getElementById("editOrganizerName").value = button.getAttribute("data-organizer_name");
            document.getElementById("editOrganizerContact").value = button.getAttribute("data-organizer_contact");
        });
    }
});


    // Delete Event Modal
    let deleteModal = document.getElementById("deleteModal");
    if (deleteModal) {
        deleteModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            document.getElementById("deleteEventId").value = button.getAttribute("data-id");
        });
    }
;

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: 'fetch_events.php', // Fetch events from database
    });

    calendar.render();

});
</script>
  </body>
</html>


