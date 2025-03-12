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
    <title>Available Items</title>

    <!-- Simple bar CSS (for scrollbar)-->
    <link rel="stylesheet" href="../../css/simplebar.css">
    <link href="https://fonts.googleapis.com/css2?family=Overpass:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../css/feather.css">
    <link rel="stylesheet" href="../../css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>

<body class="vertical light">
    
    <div class="wrapper">
        
        <nav class="topnav navbar navbar-light">
            <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
                <i class="fe fe-menu navbar-toggler-icon"></i>
            </button>
            <form class="form-inline mr-auto searchform text-muted">
                <input class="form-control bg-transparent border-0 pl-4" type="search" placeholder="Search..." aria-label="Search">
            </form>
            <ul class="nav">
                <li class="nav-item">
                    <section class="nav-link text-muted my-2 circle-icon" data-toggle="modal" data-target=".modal-shortcut">
                        <span class="fe fe-message-circle fe-16"></span>
                    </section>
                </li>
                <li class="nav-item nav-notif">
                    <section class="nav-link text-muted my-2 circle-icon" data-toggle="modal" data-target=".modal-notif">
                        <span class="fe fe-bell fe-16"></span>
                        <span id="notification-count" class="notification-badge"></span>
                    </section>
                </li>
                <li class="nav-item dropdown">
                    <span class="nav-link text-muted pr-0 avatar-icon" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown">
                        <span class="avatar avatar-sm mt-2">
                            <div class="avatar-img rounded-circle avatar-initials-min"></div>
                        </span>
                    </span>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#"><i class="fe fe-user"></i> Profile</a>
                        <a class="dropdown-item" href="#"><i class="fe fe-settings"></i> Settings</a>
                        <a class="dropdown-log-out" href="logout.php"><i class="fe fe-log-out"></i> Log Out</a>
                    </div>
                </li>
            </ul>
        </nav>

        <aside class="sidebar-left border-right bg-white" id="leftSidebar" data-simplebar>
            <nav class="vertnav navbar-side navbar-light">
                <div class="w-100 mb-4 d-flex">
                    <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="#">
                        <img src="../../assets/images/unified-lgu-logo.png" width="45">
                        <div class="brand-title"><br><span>Barangay Event Management</span></div>
                    </a>
                </div>

                <ul class="navbar-nav active flex-fill w-100 mb-2">
                    <li class="nav-item dropdown">
                        <a class="nav-link" href="home.php">
                            <i class="fas fa-chart-line"></i>
                            <span class="ml-3 item-text">Home</span>

                        </a>
                    </li>
                </ul>
                <p class="text-muted-nav nav-heading mt-4 mb-1">
                    <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">MAIN COMPONENTS</span>
                </p>
                <ul class="navbar-nav flex-fill w-100 mb-2">

                    <ul class="navbar-nav flex-fill w-100 mb-2">
                        <li class="nav-item w-100">
                            <a class="nav-link" href="event_dashboard.php">
                                <i class="fa-solid fa-wrench"></i>
                                <span class="ml-3 item-text">Event</span>
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav flex-fill w-100 mb-2">
                        <li class="nav-item w-100">
                            <a class="nav-link" href="event_dashboard.php">
                                <i class="fa-solid fa-wrench"></i>
                                <span class="ml-3 item-text">Event Dashboard</span>
                            </a>
                        </li>
                    </ul>



                    <ul class="navbar-nav flex-fill w-100 mb-2">
                        <li class="nav-item w-100">
                            <a class="nav-link" href="contact.php">
                                <i class="fa-solid fa-wrench"></i>
                                <span class="ml-3 item-text">Contact Us</span>
                            </a>
                        </li>
                    </ul>



                    <p class="text-muted-nav nav-heading mt-4 mb-1">
                        <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">OTHER COMPONENTS</span>
                    </p>
                    <ul class="navbar-nav flex-fill w-100 mb-2">
                        <li class="nav-item w-100">
                            <a class="nav-link" href="logistic.php">
                                <i class="fa-solid fa-wrench"></i>
                                <span class="ml-3 item-text">Logistic</span>
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav flex-fill w-100 mb-2">
                        <li class="nav-item w-100">
                            <a class="nav-link" href="emergency.php">
                                <i class="fa-solid fa-wrench"></i>
                                <span class="ml-3 item-text">Emergency Hotline</span>
                            </a>
                        </li>
                    </ul>

                    <ul class="navbar-nav flex-fill w-100 mb-2">

                        <li class="nav-item w-100">
                            <a class="nav-link" href="event_registration.php">
                                <i class="fa-solid fa-wrench"></i>
                                <span class="ml-3 item-text">Event Registration</span>
                            </a>
                        </li>
                    </ul>

                    <p class="text-muted-nav nav-heading mt-4 mb-1">
                        <span style="font-size: 10.5px; font-weight: bold; font-family: 'Inter', sans-serif;">SETTINGS</span>
                    </p>

                    <ul class="navbar-nav flex-fill w-100 mb-2">
                        <li class="nav-item w-100">
                            <a class="nav-link" href="settings.php">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                                <span class="ml-3 item-text">Settings</span>
                            </a>
                        </li>
                    </ul>


            </nav>
        </aside>
        </aside>

        <main role="main" class="main-content">
            <div class="container">
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

<!-- Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">  <!-- Adjusted Modal Size -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">Request Items</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="process_request.php" method="POST">
                <div class="modal-body">
                    <p class="fw-semibold text-center">Select items and specify the quantity:</p>

                    <!-- Styled Checkbox List -->
                    <div class="form-check-container">
                        <div class="form-check">
                            <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="Chair" id="chair">
                            <label class="form-check-label" for="chair">Chair</label>
                            <input type="number" name="quantity[Chair]" class="form-control quantity-input" placeholder="Qty" min="1" disabled>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="Tables" id="tables">
                            <label class="form-check-label" for="tables">Tables</label>
                            <input type="number" name="quantity[Tables]" class="form-control quantity-input" placeholder="Qty" min="1" disabled>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="Tent" id="tent">
                            <label class="form-check-label" for="tent">Tent</label>
                            <input type="number" name="quantity[Tent]" class="form-control quantity-input" placeholder="Qty" min="1" disabled>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="Sound System" id="sound">
                            <label class="form-check-label" for="sound">Sound System</label>
                            <input type="number" name="quantity[Sound System]" class="form-control quantity-input" placeholder="Qty" min="1" disabled>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input item-checkbox" type="checkbox" name="items[]" value="Lighting" id="lighting">
                            <label class="form-check-label" for="lighting">Lighting</label>
                            <input type="number" name="quantity[Lighting]" class="form-control quantity-input" placeholder="Qty" min="1" disabled>
                        </div>
                    </div>

                    <!-- Date of Delivery -->
                    <div class="mt-3">
                        <label for="deliveryDate" class="form-label">Date of Delivery</label>
                        <input type="date" name="delivery_date" class="form-control" required>
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

<!-- Enable Quantity Input when Checkbox is Checked -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".item-checkbox").forEach(function (checkbox) {
            checkbox.addEventListener("change", function () {
                let quantityInput = this.closest(".form-check").querySelector(".quantity-input");
                quantityInput.disabled = !this.checked;
            });
        });
    });
</script>

<!-- CSS to Align the Inputs and Reduce Modal Width -->
<style>
    .modal-sm {
        max-width: 400px; /* Adjusted modal width */
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
        margin-left: 10px;
    }
    .quantity-input {
        width: 60px;
        text-align: center;
    }
</style>

            
  


                <div class="container">
                    <div class="row">
                        <!-- Inventory Form -->
                        <form method="POST" action="request_item.php" id="requestForm">
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



                        <!-- Include jQuery & Bootstrap -->
                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                        <script src="../../js/bootstrap.bundle.min.js"></script>
                        <script src="../../js/popper.min.js"></script>
                        <script src="../../js/moment.min.js"></script>
                        <script src="../../js/simplebar.min.js"></script>
                        <script src='../../js/daterangepicker.js'></script>
                        <script src='../../js/jquery.stickOnScroll.js'></script>
                        <script src="../../js/tinycolor-min.js"></script>
                        <script src="../../js/d3.min.js"></script>
                        <script src="../../js/topojson.min.js"></script>
                        <script src="../../js/Chart.min.js"></script>
                        <script src="../../js/gauge.min.js"></script>
                        <script src="../../js/jquery.sparkline.min.js"></script>
                        <script src="../../js/apexcharts.min.js"></script>
                        <script src="../../js/apexcharts.custom.js"></script>
                        <script src='../../js/jquery.mask.min.js'></script>
                        <script src='../../js/select2.min.js'></script>
                        <script src='../../js/jquery.steps.min.js'></script>
                        <script src='../../js/jquery.validate.min.js'></script>
                        <script src='../../js/jquery.timepicker.js'></script>
                        <script src='../../js/dropzone.min.js'></script>
                        <script src='../../js/uppy.min.js'></script>
                        <script src='../../js/quill.min.js'></script>
                        <script src="../../js/apps.js"></script>
                        <script src="../../js/preloader.js"></script>
</body>

</html>