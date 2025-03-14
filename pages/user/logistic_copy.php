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

        .modal-sm {
            max-width: 400px;
            /* Adjusted modal width */
        }

        .form-check-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .form-check {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
        }

        .form-check-label {
            flex-grow: 1;
            margin-left: 42px;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
        }

        .form-check {
            display: flex;
            align-items: center;
        }

        .form-check-inputs {
            width: 18px;
            height: 18px;
            margin-top: 0;
            /* Ensures checkbox stays aligned */
            margin-left: 45px;
        }

        .form-check-label {
            font-size: 14px;
            color: #333;
        }

        #gcash-payment {
            display: none;
            border: 2px solid #ccc;
            padding: 15px;
            border-radius: 10px;
            width: 250px;
            text-align: center;
        }

        /* Style for QR Code */
        #gcash-payment img {
            width: 95px;
            /* Adjusted size */
            height: 65px;
            border-radius: 10px;
            display: block;
            margin: 10px auto;
        }

        /* Align receipt upload button */
        .upload-section {
            text-align: center;
        }
    </style>


</head>

<body class="vertical  light">
    <div class="wrapper">
        <?php include('navbar.php'); ?>
        <?php include('sidebar.php'); ?>
        <main role="main" class="main-content">
            <!-- Request Button -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Available Items</h2>
                    <p class="text-muted">Below are the available items and their stock.</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestModal">
                    Request Items
                </button>
            </div>

            <div class="container">
        <div class="row">
            <!-- Inventory Form -->
            <form method="POST" action="process_request.php" id="requestForm">
                <div class="row">
                    <?php if (!empty($items)) : ?>
                        <?php foreach ($items as $item) : ?>
                            <div class="col-md-4 col-sm-6 col-xs-12 mb-4">
                                <div class="card">
                                    <img src="../../assets/img/<?php echo htmlspecialchars($item['image_url'] ?? 'default.jpg'); ?>"
                                        alt="<?php echo htmlspecialchars($item['name']); ?>"
                                        class="card-img-top">
                                    <div class="card-body text-center">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="card-text">Available: <?php echo htmlspecialchars($item['quantity']); ?></p>

                                        <!-- Request Quantity Input -->
                                        <input type="hidden" name="item_id[]" value="<?php echo $item['id']; ?>">

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p class="text-center">No items available.</p>
                    <?php endif; ?>
                </div>
            </form>
        </div>
  
            <!-- Request Modal -->
            <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold" id="requestModalLabel">Request Items</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="process_request.php" method="POST">
                            <div class="modal-body m-4">
                                <p class="fw-semibold text-center">Fill in your details and select items:</p>

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
                                <label class="form-label mt-3">Select Items</label>
                                <div class="border rounded p-2">
                                    <?php foreach ($items as $item) : ?>
                                        <div class="d-flex align-items-center p-2 border-bottom me-4">
                                            <!-- Checkbox and Item Name (Side by Side) -->
                                            <input class="form-check-input item-checkbox me-2" type="checkbox"
                                                name="items[]" value="<?php echo htmlspecialchars($item['name']); ?>"
                                                id="item_<?php echo $item['id']; ?>"
                                                <?php echo ($item['quantity'] <= 0) ? 'disabled' : ''; ?>
                                                onchange="toggleQuantity(this)">

                                            <label class="form-check-label fw-bold me-3" for="item_<?php echo $item['id']; ?>" style="margin-bottom: 0;">
                                                <?php echo htmlspecialchars($item['name']); ?>
                                            </label>

                                            <!-- Available Quantity (Underneath the Name) -->
                                            <span class="text-muted small ms-auto" style="font-size: 12px;">
                                                Available: <span class="fw-semibold"><?php echo $item['quantity']; ?></span>
                                            </span>

                                            <!-- Quantity Input -->
                                            <input type="number" name="quantity[<?php echo htmlspecialchars($item['name']); ?>]"
                                                class="form-control quantity-input text-center ms-3" placeholder="Qty" min="1"
                                                max="<?php echo $item['quantity']; ?>" disabled
                                                style="width: 70px; font-size: 14px; padding: 3px 5px;"
                                                oninput="validateQuantity(this, <?php echo $item['quantity']; ?>)">
                                        </div>
                                    <?php endforeach; ?>
                                </div>


                                <!-- Date and Time of Delivery -->
                                <div class="mt-3">
                                    <label class="form-label">Date & Time of Delivery</label>
                                    <input type="datetime-local" name="delivery_datetime" class="form-control" required>
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

                                <!-- Hidden Input for "Other" Location -->
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

                                <!-- Hidden Input for "Other" -->
                                <div class="mt-2" id="otherEventContainer" style="display: none;">
                                    <label class="form-label">Specify Event</label>
                                    <input type="text" name="other_event" id="otherEventInput" class="form-control" placeholder="Enter event details">
                                </div>
                            </div>
                            <!-- Payment Method -->
                            <div class="payment-container">
                                <!-- Payment Selection -->
                                <div>
                                    <label for="payment-method">Choose Payment Method:</label>
                                    <select id="payment-method" onchange="toggleGcash()">
                                        <option value="">-- Select Payment Method --</option>
                                        <option value="cod">Cash on Delivery</option>
                                        <option value="gcash">Online Transaction (GCash)</option>
                                    </select>
                                </div>

                                <!-- GCash Payment Section (Hidden by Default) -->
                                <div id="gcash-payment">
                                    <h3>Pay with GCash</h3>
                                    <p>Scan the QR code below using your GCash app.</p>
                                    <img src="gcash.jpg" alt="GCash QR Code">

                                    <form action="upload_receipt.php" method="POST" enctype="multipart/form-data" class="upload-section">
                                        <p>After payment, upload your receipt:</p>
                                        <input type="file" name="receipt" accept="image/*" required>
                                        <input type="hidden" name="user_id" value="1"> <!-- Replace with logged-in user ID -->
                                        <button type="submit">Submit</button>
                                    </form>
                                </div>
                            </div>
                    </div>

                </div>
            </div>
                <!-- Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="requestModalLabel">Request Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="process_request.php" method="POST">
                <div class="modal-body px-4 py-3">
                    <p class="fw-semibold text-center">Fill in your details and select items:</p>

                    <!-- Personal Information -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Contact Number</label>
                            <input type="tel" name="contact_number" class="form-control" placeholder="Enter your contact number" required>
                        </div>
                    </div>

                    <!-- Item Selection -->
                    <label class="form-label fw-semibold">Select Items</label>
                    <div class="border rounded p-3 bg-light">
                        <?php foreach ($items as $item) : ?>
                            <div class="d-flex align-items-center p-2 border-bottom">
                                <input class="form-check-input me-2 item-checkbox" type="checkbox"
                                    name="items[]" value="<?php echo htmlspecialchars($item['name']); ?>"
                                    id="item_<?php echo $item['id']; ?>"
                                    <?php echo ($item['quantity'] <= 0) ? 'disabled' : ''; ?>
                                    onchange="toggleQuantity(this)">

                                <label class="form-check-label fw-semibold me-2" for="item_<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </label>

                                <span class="text-muted small ms-auto">
                                    Available: <span class="fw-bold"><?php echo $item['quantity']; ?></span>
                                </span>

                                <input type="number" name="quantity[<?php echo htmlspecialchars($item['name']); ?>]"
                                    class="form-control text-center ms-3 quantity-input" placeholder="Qty" min="1"
                                    max="<?php echo $item['quantity']; ?>" disabled
                                    style="width: 80px;" oninput="validateQuantity(this, <?php echo $item['quantity']; ?>)">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Date and Time of Delivery -->
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Date & Time of Delivery</label>
                        <input type="datetime-local" name="delivery_datetime" class="form-control" required>
                    </div>

                    <!-- Location Selection -->
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Event Location</label>
                        <select name="location" id="eventLocation" class="form-select" required onchange="toggleOtherLocation()">
                            <option value="" selected disabled>Select location</option>
                            <option value="Court 1">Court 1</option>
                            <option value="Court 2">Court 2</option>
                            <option value="Plaza">Plaza</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- Hidden Input for "Other" Location -->
                    <div class="mt-2" id="otherLocationContainer" style="display: none;">
                        <label class="form-label">Specify Location</label>
                        <input type="text" name="other_location" id="otherLocationInput" class="form-control" placeholder="Enter location details">
                    </div>

                    <!-- Purpose of Event -->
                    <div class="mt-3">
                        <label class="form-label fw-semibold">Purpose of Event</label>
                        <select name="event_purpose" id="eventPurpose" class="form-select" required onchange="toggleOtherEvent()">
                            <option value="" selected disabled>Select Event</option>
                            <option value="Birthday">Birthdays</option>
                            <option value="Funeral">Funerals</option>
                            <option value="Seminars">Weddings</option>
                            <option value="Sports">Baptisms</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>

                    <!-- Hidden Input for "Other" -->
                    <div class="mt-2" id="otherEventContainer" style="display: none;">
                        <label class="form-label">Specify Event</label>
                        <input type="text" name="other_event" id="otherEventInput" class="form-control" placeholder="Enter event details">
                    </div>

                 
                </div>

                <!-- Disclaimer Checkbox -->
                <div class="px-4 py-3 bg-light border-top">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="disclaimerCheckbox" required>
                        <label class="form-check-label fw-bold" for="disclaimerCheckbox">
                            I acknowledge that if there are any lost or damaged items, I am responsible for the additional payment.
                        </label>
                    </div>
                </div>

                

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>





                <!-- Disclaimer Checkbox -->
                <div class="mt-4 p-3 border rounded bg-light">
                    <div class="form-check d-flex align-items-start">
                        <input type="checkbox" class="form-check-inputs mt-1 me-2" id="disclaimerCheckbox" required>
                        <label class="form-check-label fw-bold" for="disclaimerCheckbox" style="line-height: 1.5;">
                            I acknowledge that if there are any lost or damaged items, I am responsible for the additional payment.
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm onclick=" return validateDisclaimer();" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary btn-sm">Submit Request</button>
                </div>
                </form>
            </div>
                        </main>
    </div>


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
                    <p class="mt-3">Your request has been submitted successfully! Wait For Admin's Approval! We'll notify you on your user dashboard</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>


    

    </main>
    </div>



    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <?php include('script.php') ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let editModal = document.getElementById("editProductModal");
            editModal.addEventListener("show.bs.modal", function(event) {
                let button = event.relatedTarget;
                document.getElementById("editProductId").value = button.getAttribute("data-id");
                document.getElementById("editName").value = button.getAttribute("data-name");
                document.getElementById("editQuantity").value = button.getAttribute("data-quantity");
            });

            let deleteModal = document.getElementById("deleteProductModal");
            deleteModal.addEventListener("show.bs.modal", function(event) {
                let button = event.relatedTarget;
                document.getElementById("deleteProductId").value = button.getAttribute("data-id");
            });
        });

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".item-checkbox").forEach(function(checkbox) {
                checkbox.addEventListener("change", function() {
                    let quantityInput = this.closest(".form-check").querySelector(".quantity-input");
                    quantityInput.disabled = !this.checked;
                });
            });
        });

        function toggleQuantity(checkbox) {
            const quantityInput = checkbox.closest('.d-flex').querySelector('.quantity-input');
            quantityInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                quantityInput.value = ''; // Clear value when unchecked
            }
        }

        function toggleOtherEvent() {
            const eventSelect = document.getElementById("eventPurpose");
            const otherEventContainer = document.getElementById("otherEventContainer");
            const otherEventInput = document.getElementById("otherEventInput");

            if (eventSelect.value === "Others") {
                otherEventContainer.style.display = "block";
                otherEventInput.setAttribute("required", "true"); // Make it required when shown
            } else {
                otherEventContainer.style.display = "none";
                otherEventInput.removeAttribute("required"); // Remove required if hidden
                otherEventInput.value = ""; // Clear input when hidden
            }
        }

        function toggleQuantity(checkbox) {
            var quantityInput = checkbox.closest('.d-flex').querySelector('.quantity-input');
            quantityInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                quantityInput.value = "";
            }
        }

        // Show "Other Event" input if "Others" is selected
        function toggleOtherEvent() {
            var eventSelect = document.getElementById("eventPurpose");
            var otherEventContainer = document.getElementById("otherEventContainer");
            var otherEventInput = document.getElementById("otherEventInput");

            if (eventSelect.value === "Others") {
                otherEventContainer.style.display = "block";
                otherEventInput.setAttribute("required", "true");
            } else {
                otherEventContainer.style.display = "none";
                otherEventInput.removeAttribute("required");
                otherEventInput.value = "";
            }
        }

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


        document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($_SESSION['success'])) : ?>
                var successModal = new bootstrap.Modal(document.getElementById("successModal"));
                successModal.show();
            <?php unset($_SESSION['success']);
            endif; ?>
        });

        function toggleQuantity(checkbox) {
            let quantityInput = checkbox.closest('.d-flex').querySelector('.quantity-input');
            quantityInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                quantityInput.value = "";
            }
        }

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


        function toggleGcash() {
            var paymentMethod = document.getElementById("payment-method").value;
            var gcashSection = document.getElementById("gcash-payment");

            if (paymentMethod === "gcash") {
                gcashSection.style.display = "block";
            } else {
                gcashSection.style.display = "none";
            }
        }
    </script>

    </scrip>

    </>
</body>

</html>