<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

// Correct path to the database connection file
define('BASE_PATH', realpath(__DIR__ . '/../../'));
require BASE_PATH . '/database/connection.php';

// Fetch users dynamically from the database
try {
    $stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching users: " . $e->getMessage();
    $users = [];
}

// Include layout files
?>

<style>
    /* Responsive Styles for Modals, Table, and Light/Dark Mode */
    .modal-content {
        border-radius: 8px;
        background-color: #ffffff;
        color: #1B4965;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        transition: background-color 0.3s, color 0.3s;
    }
    body.dark-mode .modal-content {
        background-color: #2c2c2c;
        color: #e0e0e0;
    }
    .table-responsive {
        overflow-x: auto;
    }
    body.dark-mode .table {
        color: #e0e0e0;
    }
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .d-flex {
            flex-wrap: wrap;
        }
        .form-control.d-inline-block {
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>
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
              <a class="nav-link" href="events.php">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">Event</span>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav flex-fill w-100 mb-2">
            <li class="nav-item w-100">
              <a class="nav-link" href="users.php">
              <i class="fa-solid fa-wrench"></i>
                <span class="ml-3 item-text">User</span>
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Users</h2>
        <div>
            <input type="text" id="searchInput" class="form-control d-inline-block w-auto" placeholder="Search..." onkeyup="filterTable()">
            <button class="btn btn-secondary ms-2" onclick="exportToCSV()">Export CSV</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">Create User</button>
        </div>
    </div>

    <!-- Display success or error messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['success'];
            unset($_SESSION['success']); ?>
        </div>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped table-hover" id="usersTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users)) : ?>
                    <?php foreach ($users as $index => $user) : ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars(date('F j, Y, g:i A', strtotime($user['created_at']))); ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm edit-user-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?php echo $user['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($user['name']); ?>"
                                    data-email="<?php echo htmlspecialchars($user['email']); ?>"
                                    data-role="<?php echo htmlspecialchars($user['role']); ?>">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm delete-user-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModal"
                                    data-id="<?php echo $user['id']; ?>">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="text-center">No users found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // Search Functionality
    function filterTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const table = document.getElementById('usersTable');
        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
            row.style.display = rowText.includes(input) ? '' : 'none';
        });
    }

    // Export to CSV
    function exportToCSV() {
        const rows = document.querySelectorAll('#usersTable tr');
        let csvContent = "";

        rows.forEach(row => {
            const columns = row.querySelectorAll('th, td');
            const rowData = Array.from(columns).map(column => `"${column.textContent}"`).join(",");
            csvContent += rowData + "\r\n";
        });

        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('href', url);
        a.setAttribute('download', 'users_report.csv');
        a.click();
        window.URL.revokeObjectURL(url);
    }
</script>

<!-- Modals (Create, Edit, Delete) -->
<div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="createUserForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModalLabel">Create User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
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
