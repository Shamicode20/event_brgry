<?php
session_start();
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    // header('Location: ../../index.php');
    // exit;
// }

// Correct path to the database connection file
// define('BASE_PATH', realpath(__DIR__ . '/../../'));
require '../../database/connection.php';

// Handle event registration
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    try {
        $eventId = $_POST['event_id'];

        // Check if the user is already registered for the event
        $stmt = $conn->prepare("SELECT COUNT(*) FROM event_participants WHERE user_id = :user_id AND event_id = :event_id");
        $stmt->execute([':user_id' => $_SESSION['user_id'], ':event_id' => $eventId]);
        $alreadyRegistered = $stmt->fetchColumn();

        if (!$alreadyRegistered) {
            // Register the user for the event
            $stmt = $conn->prepare("INSERT INTO event_participants (user_id, event_id) VALUES (:user_id, :event_id)");
            $stmt->execute([':user_id' => $_SESSION['user_id'], ':event_id' => $eventId]);

            $_SESSION['success'] = "You have successfully registered for the event!";
        } else {
            $_SESSION['error'] = "You are already registered for this event.";
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error registering for the event: " . $e->getMessage();
    }

    // Reload the page to update the list and show any messages
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Initialize variables
$upcomingEvents = [];
$currentEvents = [];
$recentEvents = [];

try {
    // Fetch upcoming events that the user is not registered for
    $stmt = $conn->prepare("
        SELECT events.id, events.title, events.description, events.schedule, events.poster 
        FROM events 
        WHERE events.schedule > NOW()
        AND events.id NOT IN (
            SELECT event_id 
            FROM event_participants 
            WHERE user_id = :id
        )
        ORDER BY events.schedule ASC
    ");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $upcomingEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch current events (ongoing or happening today) that the user is not registered for
    $stmt = $conn->prepare("
        SELECT events.id, events.title, events.description, events.schedule, events.poster 
        FROM events 
        WHERE DATE(events.schedule) = CURDATE()
        AND events.id NOT IN (
            SELECT event_id 
            FROM event_participants 
            WHERE user_id = :id
        )
        ORDER BY events.schedule ASC
    ");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $currentEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch recent events the user has registered for
    $stmt = $conn->prepare("
        SELECT events.id, events.title, events.schedule, events.poster 
        FROM events 
        JOIN event_participants ON events.id = event_participants.event_id 
        WHERE event_participants.user_id = :id
        ORDER BY events.schedule DESC
        LIMIT 5
    ");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $recentEvents = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching events: " . $e->getMessage();
}

?>
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

</style>
  
  </head>

    
    <div class="loader-mask">
        <div class="loader">
            <div></div>
            <div></div>
        </div>
    </div>
    
 
  <body class="vertical  light">
    <div class="wrapper">
      <nav class="topnav navbar navbar-light">
        <button type="button" class="navbar-toggler text-muted mt-2 p-0 mr-3 collapseSidebar">
          <i class="fe fe-menu navbar-toggler-icon"></i>
        </button>
        <form class="form-inline mr-auto searchform text-muted">
          <input class="form-control  bg-transparent border-0 pl-4 " type="search" placeholder="Type something....." aria-label="Search">
        </form>

        <ul class="nav">
    
          
          <li class="nav-item">
            <section class="nav-link text-muted my-2 circle-icon" href="#" data-toggle="modal" data-target=".modal-shortcut">
              <span class="fe fe-message-circle fe-16"></span>
            </section>
          </li>


          <li class="nav-item nav-notif">
  <section class="nav-link text-muted my-2 circle-icon" href="#" data-toggle="modal" data-target=".modal-notif">
    <span class="fe fe-bell fe-16"></span>
   
      <span id="notification-count" style="
        position: absolute; 
        top: 12px; right: 5px; 
        font-size:13px; color: white;
        background-color: red;
        width:8px;
        height: 8px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 50px;
      ">
      
  </section>
</li>

          <li class="nav-item dropdown">
            <span class="nav-link text-muted pr-0 avatar-icon" href="#" id="navbarDropdownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="avatar avatar-sm mt-2">
  <div class="avatar-img rounded-circle avatar-initials-min text-center position-relative">
  

  </div>
</span>
</span>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#"><i class="fe fe-user"></i>&nbsp;&nbsp;&nbsp;Profile</a>
              <a class="dropdown-item" href="#"><i class="fe fe-settings"></i>&nbsp;&nbsp;&nbsp;Settings</a>
              <a class="dropdown-log-out" href="#"><i class="fe fe-log-out"></i>&nbsp;&nbsp;&nbsp;Log Out</a>
            </div>    
          </li>
        </ul>
      </nav>


      <aside class="sidebar-left border-right bg-white " id="leftSidebar" data-simplebar>
        <a href="#" class="btn collapseSidebar toggle-btn d-lg-none text-muted ml-2 mt-3" data-toggle="toggle">
          <i class="fe fe-x"><span class="sr-only"></span></i>
        </a>

        <nav class="vertnav navbar-side navbar-light">
          <!-- nav bar -->
          <div class="w-100 mb-4 d-flex">
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="#">
              
                
                <img src="../../assets/images/unified-lgu-logo.png" width="45">
              

            <div class="brand-title">
            <br>
              <span>Baranggay Event Management</span>
            </div>
                       
            </a>

          </div>

          <!--Sidebar ito-->

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
              <a class="nav-link" href="event_registration.php">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Event Registration</span>
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


<div id="page-content-wrapper">
    <div class="dashboard-header text-center mb-4">
        <h2 class="dark-text">Event Registration</h2>
        <p class="text-muted">Browse upcoming, current events and view your recent registrations.</p>
    </div>

    <div class="container">
        <!-- Display Success or Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success text-center">
                <?php echo $_SESSION['success'];
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center">
                <?php echo $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Current Events Section -->
        <div class="mb-5">
            <h4 class="text-center mb-3 dark-text">Current Events</h4>
            <div class="row">
                <?php if (!empty($currentEvents)): ?>
                    <?php foreach ($currentEvents as $event): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card border-0 shadow-sm h-100 dark-card">
                                <img src="../../assets/img/<?php echo htmlspecialchars($event['poster'] ?? 'default.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>"
                                    class="card-img-top rounded-top"
                                    style="height: auto; width: 100%; object-fit: contain; background-color: #f8f9fa;">
                                <div class="card-body d-flex flex-column dark-card-body">
                                    <h5 class="card-title text-center dark-text"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="text-muted small text-center dark-text"><?php echo htmlspecialchars($event['description']); ?></p>
                                    <p class="text-muted small text-center dark-text">Schedule: <?php echo date('F j, Y, g:i A', strtotime($event['schedule'])); ?></p>
                                    <div class="mt-auto text-center">
                                        <form method="POST" action="">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" name="register">Register</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted dark-text">No current events available for registration.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Upcoming Events Section -->
        <div class="mb-5">
            <h4 class="text-center mb-3 dark-text">Upcoming Events</h4>
            <div class="row">
                <?php if (!empty($upcomingEvents)): ?>
                    <?php foreach ($upcomingEvents as $event): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card border-0 shadow-sm h-100 dark-card">
                                <img src="../../assets/img/<?php echo htmlspecialchars($event['poster'] ?? 'default.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>"
                                    class="card-img-top rounded-top"
                                    style="height: auto; width: 100%; object-fit: contain; background-color: #f8f9fa;">
                                <div class="card-body d-flex flex-column dark-card-body">
                                    <h5 class="card-title text-center dark-text"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="text-muted small text-center dark-text"><?php echo htmlspecialchars($event['description']); ?></p>
                                    <p class="text-muted small text-center dark-text">Schedule: <?php echo date('F j, Y, g:i A', strtotime($event['schedule'])); ?></p>
                                    <div class="mt-auto text-center">
                                        <form method="POST" action="">
                                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                                            <button type="submit" class="btn btn-primary btn-sm" name="register">Register</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted dark-text">No upcoming events available for registration.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recently Registered Events Section -->
        <div class="mb-5">
            <h4 class="text-center mb-3 dark-text">Recently Registered Events</h4>
            <div class="row">
                <?php if (!empty($recentEvents)): ?>
                    <?php foreach ($recentEvents as $event): ?>
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                            <div class="card border-0 shadow-sm h-100 dark-card">
                                <img src="../../assets/img/<?php echo htmlspecialchars($event['poster'] ?? 'default.jpg'); ?>"
                                    alt="<?php echo htmlspecialchars($event['title']); ?>"
                                    class="card-img-top rounded-top"
                                    style="height: auto; width: 100%; object-fit: contain; background-color: #f8f9fa;">
                                <div class="card-body text-center dark-card-body">
                                    <h5 class="card-title dark-text"><?php echo htmlspecialchars($event['title']); ?></h5>
                                    <p class="text-muted small dark-text">Schedule: <?php echo date('F j, Y, g:i A', strtotime($event['schedule'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-muted dark-text">No recently registered events.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../../js/jquery.min.js"></script>
  <script src="../../js/popper.min.js"></script>
  <script src="../../js/moment.min.js"></script>
  <script src="../../js/bootstrap.min.js"></script>
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
  <script src="../../js/jquery-3.6.0.min.js" crossorigin="anonymous"></script>
  <script src="../../js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src='../../js/jquery.dataTables.min.js'></script>
    <script src='../../js/dataTables.bootstrap4.min.js'></script>
    
  </body>
</html>