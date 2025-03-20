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

$items = [];
try {
    $stmt = $conn->prepare("SELECT id, name, quantity, image_url FROM inventory ORDER BY created_at DESC");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching items: " . $e->getMessage();
}

$successMessage = isset($_SESSION['success']) ? $_SESSION['success'] : null;
$errorMessage   = isset($_SESSION['error'])   ? $_SESSION['error']   : null;
unset($_SESSION['success'], $_SESSION['error']);


?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:ital,wght@0,100;0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/feather.css">
    <link rel="stylesheet" href="../../css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- FullCalendar CSS and JS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.3/main.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Events</title>

    <style>
      .card {
          border-radius: 10px;
          overflow: hidden;
          transition: transform 0.2s ease, box-shadow 0.3s;
      }
      .card:hover {
          transform: scale(1.03);
          box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      }
      .card-img-top {
          width: 100%;
          height: 200px;
          object-fit: cover;
      }
      .card-title {
          font-size: 18px;
          font-weight: bold;
      }
      .card-text {
          font-size: 14px;
      }
      /* Responsive spacing for event cards */
      .event-card {
          margin-bottom: 30px;
      }
    </style>
  </head>

  <body class="vertical light">
    <div class="wrapper">
      <?php include('navbar.php') ?>
      <?php include('sidebar.php') ?>
      <main role="main" class="main-content">
        <!-- Notification Header -->
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
                  </div>
                  <div id="no-notifications" style="display: none; text-align:center; margin-top:10px;">No notifications</div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-block" onclick="clearAllNotifications()">Clear All</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Events</h2>
            <!-- Trigger Create Event Modal -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Create</button>
          </div>
          <div class="container">
            <!-- Notification Messages -->
            <div class="mt-3">
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
            
            <!-- Event Listing (Updated Design) -->
            <div class="row">
              <?php if (!empty($events)) : ?>
                <?php foreach ($events as $event) : ?>
                  <div class="col-md-4 col-sm-6 event-card">
                    <div class="card">
                      <?php if (!empty($event['poster_base64'])) : ?>
                        <img src="data:image/jpeg;base64,<?php echo htmlspecialchars_decode($event['poster_base64']); ?>" class="card-img-top" alt="Event Poster">
                      <?php else : ?>
                        <img src="../../assets/images/default.jpg" alt="Default Image" class="card-img-top">
                      <?php endif; ?>
                      <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                        <p class="card-text"><strong>Schedule:</strong> <?php echo date("F d, Y h:i A", strtotime($event['schedule'])); ?></p>
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
          </div>
        </div>
        
        <!-- MODALS -->

        <!-- Create Event Modal with Equipment Request Fields -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="../../api/create_event.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                  <h5 class="modal-title" id="createModalLabel">Create Event</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <!-- Event Details -->
                  <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                  </div>
                  <div class="mb-3">
                  <label class="form-label">Event Types</label>
                            <select name="event_type" id="event_type" class="form-select" required onchange="toggleOtherEvent()">
                                <option value="" selected disabled>Select Event</option>
                                <option value="medic">Medical Mission</option>
                                <option value="flores">Flores de Mayo</option>
                                <option value="araw">Araw ng Barangay</option>
                                <option value="sports">Sports</option>
                                <option value="feed">Feeding Program</option>
                                <option value="clean">Clean-up Drive</option>
                                <option value="fiesta">Fiesta ng Barangay</option>
                                <option value="zumba">Zumba Session</option>
                                <option value="Others">Others</option>
                            </select>
                  </div>
                  <div class="mt-2" id="otherEventContainer" style="display: none;">
                            <label class="form-label">Specify Event</label>
                            <input type="text" name="other_event" id="otherEventInput" class="form-control" placeholder="Enter event details">
                        </div>
                  <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="dateTimePicker" class="form-label">Schedule Of Event</label>
                    <input type="datetime-local" class="form-control" id="dateTimePicker" name="schedule" required>
                    <script>
                      document.addEventListener("DOMContentLoaded", function () {
                        let now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        document.getElementById("dateTimePicker").setAttribute("min", now.toISOString().slice(0, 16));
                      });
                    </script>
                  </div>
                  <div class="mb-3">
                    <label for="poster" class="form-label">Poster</label>
                    <input type="file" class="form-control" id="poster" name="poster">
                  </div>
                  <!-- Equipment Request Fields -->
                  <div class="mb-3">
                    <h5 class="fw-bold">Request Equipments</h5>
                    <p class="fw-semibold text-center">Fill in your details and select Equipments:</p>
                    <div class="mb-2">
                      <label class="form-label">Full Name</label>
                      <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                    </div>
                    <div class="mb-2">
    <label class="form-label">Contact Number</label>
    <input type="tel" name="contact_number" class="form-control" 
           placeholder="Enter your contact number" 
           required pattern="[0-9]{11}" 
           maxlength="11" id="contact_number">
    <small class="text-danger" id="error-message"></small>
</div>
                    <label class="form-label mt-3">Select Equipments</label>
                    <div class="border rounded p-3 bg-light">
    <?php foreach ($items as $request) : ?>
        <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <input class="form-check-input item-checkbox" type="checkbox"
                       name="items[]" value="<?php echo htmlspecialchars($request['id']); ?>"
                       id="item_<?php echo $request['id']; ?>"
                       <?php echo ($request['quantity'] <= 0) ? 'disabled' : ''; ?>
                       onchange="toggleQuantity(this)">
                <label class="form-check-label fw-bold" for="item_<?php echo $request['id']; ?>">
                    <?php echo htmlspecialchars($request['name']); ?>
                </label>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">
                    Available: <span class="fw-bold text-dark"><?php echo $request['quantity']; ?></span>
                </span>
                <input type="number" name="quantity[<?php echo htmlspecialchars($request['id']); ?>]"
                       class="form-control quantity-input text-center"
                       placeholder="Qty" min="1" max="<?php echo $request['quantity']; ?>" disabled>
            </div>
        </div>
    <?php endforeach; ?>
</div>

                    <div class="mt-3">
                      <label for="deliveryDatetime" class="form-label">Delivery Schedule</label>
                      <input type="datetime-local" class="form-control" id="deliveryDatetime" name="delivery_datetime" required>
                      <script>
                        document.addEventListener("DOMContentLoaded", function () {
                          let now = new Date();
                          now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                          document.getElementById("deliveryDatetime").setAttribute("min", now.toISOString().slice(0, 16));
                        });
                      </script>
                    </div>
                  </div>
                  <!-- End Equipment Request Fields -->
                  <div class="mb-3">
                    <!-- Location Selection -->
                    <div class="mt-3">
                      <label class="form-label">Event Location</label>
                      <select name="location" id="eventLocation" class="form-select" required onchange="toggleOtherLocation()">
                        <option value="" selected disabled>Select location</option>
                        <option value="Court 1">Court 1</option>
                        <option value="Court 2">Court 2</option>
                        <option value="Plaza">Plaza</option>
                        <option value="Others">Others</option>
                      </select>
                    </div>
                    <div class="mt-2" id="otherLocationContainer" style="display: none;">
                      <label class="form-label">Specify Location</label>
                      <input type="text" name="other_location" id="otherLocationInput" class="form-control" placeholder="Enter location details">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="organizer_name" class="form-label">Organizer Name</label>
                    <input type="text" class="form-control" id="organizer_name" name="organizer_name" required>
                  </div>
                  <div class="mb-3">
                    <label for="organizer_contact" class="form-label">Organizer Contact</label>
                    <input type="text" class="form-control" id="organizer_contact" name="organizer_contact" required>
                  </div>
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
                <form action="../../api/edit_event.php" method="POST" enctype="multipart/form-data">
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
                    <script>
                      document.addEventListener("DOMContentLoaded", function () {
                        let now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        document.getElementById("editSchedule").setAttribute("min", now.toISOString().slice(0, 16));
                      });
                    </script>
                  </div>
                  <div class="mb-3">
                    <div class="mt-3">
                      <label class="form-label">Event Location</label>
                      <select name="location" id="eventLocation" class="form-select" required onchange="toggleOtherLocation()">
                        <option value="" selected disabled>Select location</option>
                        <option value="Court 1">Court 1</option>
                        <option value="Court 2">Court 2</option>
                        <option value="Plaza">Plaza</option>
                        <option value="Others">Others</option>
                      </select>
                    </div>
                    <div class="mt-2" id="otherLocationContainer" style="display: none;">
                      <label class="form-label">Specify Location</label>
                      <input type="text" name="other_location" id="otherLocationInput" class="form-control" placeholder="Enter location details">
                    </div>
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
        
      </div><!-- /#page-content-wrapper -->
      </main>
    </div>

    <!-- Include jQuery and custom scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php include('script.php') ?>
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
              });
          }
      });

      let deleteModal = document.getElementById("deleteModal");
      if (deleteModal) {
          deleteModal.addEventListener("show.bs.modal", function (event) {
              let button = event.relatedTarget;
              document.getElementById("deleteEventId").value = button.getAttribute("data-id");
          });
      }
      
      function toggleOtherLocation() {
          const locationSelect = document.getElementById("eventLocation");
          const otherLocationContainer = document.getElementById("otherLocationContainer");
          const otherLocationInput = document.getElementById("otherLocationInput");
          if (locationSelect.value === "Others") {
              otherLocationContainer.style.display = "block";
              otherLocationInput.setAttribute("required", "true");
          } else {
              otherLocationContainer.style.display = "none";
              otherLocationInput.removeAttribute("required");
              otherLocationInput.value = "";
          }
      }
      
      function toggleQuantity(checkbox) {
          let parentDiv = checkbox.closest('.d-flex');
          let quantityInput = parentDiv.querySelector('.quantity-input');
          if (checkbox.checked) {
              quantityInput.disabled = false;
              quantityInput.value = 1;
          } else {
              quantityInput.disabled = true;
              quantityInput.value = "";
          }
      }
      
      document.addEventListener('DOMContentLoaded', function() {
          var calendarEl = document.getElementById('calendar');
          var calendar = new FullCalendar.Calendar(calendarEl, {
              initialView: 'dayGridMonth',
              events: 'fetch_events.php'
          });
          calendar.render();
      });
     // Toggle quantity input based on checkbox selection
     function toggleQuantity(checkbox) {
    var quantityInput = checkbox.closest('.d-flex').querySelector('.quantity-input');
    if (checkbox.checked) {
        quantityInput.disabled = false;
        quantityInput.value = 1; // Default quantity kapag pinili
    } else {
        quantityInput.disabled = true;
        quantityInput.value = ''; // I-clear kapag tinanggal
    }
}
// Toggle quantity input based on checkbox selection
function toggleQuantity(checkbox) {
        let parentDiv = checkbox.closest('.d-flex').parentNode; // Get the parent container
        let quantityInput = parentDiv.querySelector('.quantity-input'); // Get the corresponding quantity input
        
        if (checkbox.checked) {
            quantityInput.disabled = false;
            quantityInput.value = 1; // Set default quantity when checked
        } else {
            quantityInput.disabled = true;
            quantityInput.value = ""; // Clear input when unchecked
        }
    }

    // Toggle "Other Event" field visibility
    function toggleOtherEvent() {
      let eventSelect = document.getElementById("event_type");
      let otherEventContainer = document.getElementById("otherEventContainer");
      let otherEventInput = document.getElementById("otherEventInput");
      if (eventSelect.value === "Others") {
        otherEventContainer.style.display = "block";
        otherEventInput.required = true;
      } else {
        otherEventContainer.style.display = "none";
        otherEventInput.required = false;
        otherEventInput.value = "";
      }
    }
    document.getElementById("contact_number").addEventListener("input", function () {
        let input = this.value;
        let errorMessage = document.getElementById("error-message");

        // Alisin ang non-numeric characters
        this.value = input.replace(/\D/g, '');

        // Limitahan sa 11 digits
        if (this.value.length > 11) {
            this.value = this.value.slice(0, 11);
        }

        // Magpakita ng error message kung hindi 11 digits
        if (this.value.length < 11) {
            errorMessage.textContent = "Contact number must be exactly 11 digits.";
        } else {
            errorMessage.textContent = "";
        }
    });
    </script>
  </body>
</html>
