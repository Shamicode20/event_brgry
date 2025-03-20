<?php
session_start();
require '../../database/connection.php';

// Fetch available items
$items = [];
try {
    $stmt = $conn->prepare("SELECT id, name, quantity, image_url FROM inventory ORDER BY created_at DESC");
    $stmt->execute();
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching items: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" href="../assets/images/unified-lgu-logo.png">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <title>Logistic</title>
  <!-- Simple bar CSS (for scrollbar) -->
  <link rel="stylesheet" href="../../css/simplebar.css">
  <!-- Fonts CSS -->
  <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@100;200;300;400;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">
  <!-- Icons CSS -->
  <link rel="stylesheet" href="../../css/feather.css">
  <!-- App CSS -->
  <link rel="stylesheet" href="../../css/main.css">
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <style>
    /* Your custom styles */
    /* Container for inventory display */
.dashboard-container {
  display: flex;
  flex-wrap: wrap;
  gap: 20px;
  justify-content: center;
  padding: 20px;
}

/* Card Styles */
.card {
  border-radius: 10px;
  box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.2s ease-in-out;
}

.card:hover {
  transform: scale(1.02);
}

.card-img-top {
  width: 100%;
  height: 200px;
  object-fit: cover;
}

.card-body {
  text-align: center;
}

/* Modal Customization */
.modal-header {
  background-color: #007bff;
  color: white;
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}

.modal-footer {
  background-color: #f8f9fa;
}

/* Item Selection Container */
.item-selection {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 10px;
  background-color: #f9f9f9;
}

/* Checkboxes & Labels */
.item-checkbox {
  transform: scale(1.2);
  cursor: pointer;
}

.item-label {
  font-weight: 600;
  min-width: 120px;
}

/* Quantity Input */
.quantity-input {
  width: 70px;
  font-size: 14px;
  padding: 5px;
  border-radius: 5px;
  text-align: center;
  border: 1px solid #ccc;
  transition: border 0.2s ease-in-out;
}

.quantity-input:focus {
  border: 1px solid #007bff;
  outline: none;
}

/* Disclaimer */
.disclaimer-container {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 15px;
}

.disclaimer-checkbox {
  transform: scale(1.2);
  cursor: pointer;
}

  </style>
</head>
<body class="vertical light">
  <div class="wrapper">
    <?php include('navbar.php'); ?>
    <?php include('sidebar.php'); ?>
    <main role="main" class="main-content">
      <!-- Header and Request Button -->
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2>Available Equipments</h2>
          <p class="text-muted">Below are the available items and their stock.</p>
        </div>
        
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestModal">
          Request Equipments
        </button>
      </div>
      
      <!-- Inventory Display -->
      <div class="container">
        <div class="row">
          <?php if (!empty($items)) : ?>
            <?php foreach ($items as $item) : ?>
              <div class="col-md-4 col-sm-6 col-xs-12 mb-4">
                <div class="card">
                  <img src="../../assets/img/<?php echo htmlspecialchars($item['image_url'] ?? 'default.jpg'); ?>"
                       alt="<?php echo htmlspecialchars($item['name']); ?>"
                       class="card-img-top">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                    <p class="card-text">Available: <?php echo htmlspecialchars($item['quantity']); ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else : ?>
            <p class="text-center">No Equipments available.</p>
          <?php endif; ?>
        </div>
      </div>
      
     <!-- Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="requestModalLabel">Request Equipments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_request.php" method="POST">
                <div class="modal-body">
                    <div class="m-4">
                        <p class="fw-semibold text-center">Fill in your details and select Equipments:</p>
                        <!-- Personal Information -->
                        <div class="mb-2">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" name="contact_number" class="form-control" placeholder="Enter your contact number" required>
                        </div>

                        <!-- Item Selection -->
                        <label class="form-label mt-3">Select Equipments</label>
                        <div class="border rounded p-3 bg-light">
                        <?php foreach ($items as $item) : ?>
        <div class="d-flex align-items-center justify-content-between p-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <input class="form-check-input item-checkbox" type="checkbox"
                       name="items[]" value="<?php echo htmlspecialchars($item['id']); ?>"
                       id="item_<?php echo $item['id']; ?>"
                       <?php echo ($item['quantity'] <= 0) ? 'disabled' : ''; ?>
                       onchange="toggleQuantity(this)">
                <label class="form-check-label fw-bold" for="item_<?php echo $item['id']; ?>">
                    <?php echo htmlspecialchars($item['name']); ?>
                </label>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">
                    Available: <span class="fw-bold text-dark"><?php echo $item['quantity']; ?></span>
                </span>
                <input type="number" name="quantity[<?php echo htmlspecialchars($item['id']); ?>]"
                       class="form-control quantity-input text-center"
                       placeholder="Qty" min="1" max="<?php echo $item['quantity']; ?>" disabled>
            </div>
        </div>
    <?php endforeach; ?>
                        </div>

                        <!-- Date and Time of Delivery -->
                        <div class="mt-3">
                       

                            <label for="editSchedule" class="form-label">Schedule</label>
                        <input type="datetime-local" class="form-control" id="editSchedule" name="delivery_datetime" required>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        let now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset()); // Adjust for timezone
        document.getElementById("editSchedule").setAttribute("min", now.toISOString().slice(0, 16));
    });
</script>
                        </div>

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

                        <!-- Purpose of Event -->
                        <div class="mt-3">
                            <label class="form-label">Purpose of Event</label>
                            <select name="event_purpose" id="eventPurpose" class="form-select" required onchange="toggleOtherEvent()">
                                <option value="" selected disabled>Select Event</option>
                                <option value="Birthday">Birthdays</option>
                                <option value="Funeral">Funerals</option>
                                <option value="Seminars">Weddings</option>
                                <option value="Sports">Baptisms</option>
                                <option value="Others">Others</option>
                            </select>
                        </div>

                        <div class="mt-2" id="otherEventContainer" style="display: none;">
                            <label class="form-label">Specify Event</label>
                            <input type="text" name="other_event" id="otherEventInput" class="form-control" placeholder="Enter event details">
                        </div>

                        <!-- Disclaimer -->
                        <div class="mt-4 p-3 border rounded bg-light">
                            <div class="form-check d-flex align-items-start">
                                <input type="checkbox" class="form-check-input mt-1 me-2" id="disclaimerCheckbox" required>
                                <label class="form-check-label fw-bold" for="disclaimerCheckbox">
                                    I acknowledge that if there are any lost or damaged items, I am responsible for the additional payment.
                                </label>
                            </div>
                        </div>
                    </div><!-- .m-4 -->
                </div><!-- .modal-body -->

                <!-- Corrected modal-footer placement -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div><!-- .modal-content -->
    </div><!-- .modal-dialog -->
</div><!-- #requestModal -->
      
      <!-- Success Modal -->
      <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title text-success fw-bold" id="successModalLabel">Request Submitted</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 50px;"></i>
              <p class="mt-3">Your request has been submitted successfully! Wait For Admin's Approval! .</p>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
            </div>
          </div>
        </div>
      </div>
      
    </main>
  </div>
  
  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include('script.php') ?>
  <script>
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

    // Toggle "Other Location" field visibility
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

    // Toggle "Other Event" field visibility
    function toggleOtherEvent() {
      let eventSelect = document.getElementById("eventPurpose");
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

    // Confirm closing the modal (for disclaimer)
    function validateDisclaimer() {
      return confirm("Are you sure you want to close the modal?");
    }

    // Show the success modal if there's a session message
    document.addEventListener("DOMContentLoaded", function() {
      <?php if (isset($_SESSION['success'])) : ?>
        var successModal = new bootstrap.Modal(document.getElementById("successModal"));
        successModal.show();
        <?php unset($_SESSION['success']); endif; ?>
    });
  </script>
</body>
</html>
