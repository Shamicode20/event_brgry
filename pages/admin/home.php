<?php
session_start();
// Redirect non-admin users to the login page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

require '../../database/connection.php';

// Set the page title
$title = "Admin Dashboard";



// Function to fetch count from a table with PDO
function fetchCount($conn, $tableName)
{
    try {
        $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM $tableName");
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['count'] ?? 0;
    } catch (Exception $e) {
        error_log("Error fetching count from $tableName: " . $e->getMessage());
        return 0;
    }
}

// Fetch counts for total users, total events, and total participants
$totalUsers = fetchCount($conn, 'users');
$totalEvents = fetchCount($conn, 'events');
$totalParticipants = fetchCount($conn, 'event_participants');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.6.0/css/fontawesome.min.css">
    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Home</title>

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
    <div class="container mt-4">
        <h1 class="mb-4 text-primary fw-bold">Welcome to Admin Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="row g-4">
            <!-- Total Users Card -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow border-0 text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h5 class="fw-bold">Total Users</h5>
                        <h3 class="text-dark fw-bold"><?php echo htmlspecialchars($totalUsers); ?></h3>
                        <small class="text-muted">Last updated: <?php echo htmlspecialchars(date('Y-m-d H:i:s')); ?></small>
                    </div>
                </div>
            </div>

            <!-- Total Events Card -->
            <div class="col-lg-4 col-md-6">
                <div class="card shadow border-0 text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-calendar-alt fa-3x text-success mb-3"></i>
                        <h5 class="fw-bold">Total Events</h5>
                        <h3 class="text-dark fw-bold"><?php echo htmlspecialchars($totalEvents); ?></h3>
                        <small class="text-muted">Last updated: <?php echo htmlspecialchars(date('Y-m-d H:i:s')); ?></small>
                    </div>
                </div>
            </div>

            <!-- Total Participants Card -->
            <div class="col-lg-4 col-md-12 mt-2">
                <div class="card shadow border-0 text-center p-4">
                    <div class="card-body">
                        <i class="fas fa-user-check fa-3x text-warning mb-3"></i>
                        <h5 class="fw-bold">Total Participants</h5>
                        <h3 class="text-dark fw-bold"><?php echo htmlspecialchars($totalParticipants); ?></h3>
                        <small class="text-muted">Last updated: <?php echo htmlspecialchars(date('Y-m-d H:i:s')); ?></small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analytics Chart -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow border-0">
                    <div class="card-body">
                        <h5 class="fw-bold text-primary mb-3">Analytics Overview</h5>
                        <div id="analyticsChart" style="min-height: 350px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

      
      
      
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include('script.php')?>
  <script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Detect if the system prefers dark mode
        const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

        const chartOptions = {
            chart: {
                type: 'column',
                backgroundColor: isDarkMode ? '#1c1e21' : '#ffffff', // Dynamic background
                style: {
                    fontFamily: 'Arial, sans-serif'
                }
            },
            title: {
                text: 'Analytics Overview',
                style: {
                    color: isDarkMode ? '#ffffff' : '#333333' // Dynamic title color
                }
            },
            xAxis: {
                categories: ['Total Users', 'Total Events', 'Total Participants'],
                title: {
                    text: 'Metrics',
                    style: {
                        color: isDarkMode ? '#ffffff' : '#333333' // Dynamic axis title color
                    }
                },
                labels: {
                    style: {
                        color: isDarkMode ? '#ffffff' : '#333333' // Dynamic label color
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Count',
                    style: {
                        color: isDarkMode ? '#ffffff' : '#333333' // Dynamic axis title color
                    }
                },
                labels: {
                    style: {
                        color: isDarkMode ? '#ffffff' : '#333333' // Dynamic label color
                    }
                },
                gridLineColor: isDarkMode ? '#555555' : '#e6e6e6' // Grid line color for better contrast
            },
            tooltip: {
                backgroundColor: isDarkMode ? '#333333' : '#ffffff', // Dynamic tooltip background
                style: {
                    color: isDarkMode ? '#ffffff' : '#333333' // Dynamic tooltip text color
                },
                borderColor: isDarkMode ? '#555555' : '#dddddd' // Dynamic tooltip border
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            exporting: {
                buttons: {
                    contextButton: {
                        menuItems: [
                            'downloadPNG',
                            'downloadJPEG',
                            'downloadPDF',
                            'downloadCSV',
                            'downloadXLS',
                            'viewData'
                        ]
                    }
                }
            },
            credits: {
                enabled: false // Disable Highcharts credits
            },
            series: [{
                name: 'Counts',
                data: [
                    <?php echo htmlspecialchars($totalUsers); ?>,
                    <?php echo htmlspecialchars($totalEvents); ?>,
                    <?php echo htmlspecialchars($totalParticipants); ?>
                ],
                color: isDarkMode ? '#00aaff' : '#007bff' // Dynamic column color
            }]
        };

        // Initialize the chart
        Highcharts.chart('analyticsChart', chartOptions);

        // Listen for changes to the system's color scheme
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            chartOptions.chart.backgroundColor = e.matches ? '#1c1e21' : '#ffffff';
            chartOptions.title.style.color = e.matches ? '#ffffff' : '#333333';
            chartOptions.xAxis.title.style.color = e.matches ? '#ffffff' : '#333333';
            chartOptions.xAxis.labels.style.color = e.matches ? '#ffffff' : '#333333';
            chartOptions.yAxis.title.style.color = e.matches ? '#ffffff' : '#333333';
            chartOptions.yAxis.labels.style.color = e.matches ? '#ffffff' : '#333333';
            chartOptions.yAxis.gridLineColor = e.matches ? '#555555' : '#e6e6e6';
            chartOptions.tooltip.backgroundColor = e.matches ? '#333333' : '#ffffff';
            chartOptions.tooltip.style.color = e.matches ? '#ffffff' : '#333333';
            chartOptions.tooltip.borderColor = e.matches ? '#555555' : '#dddddd';
            chartOptions.series[0].color = e.matches ? '#00aaff' : '#007bff';

            // Re-render the chart
            Highcharts.chart('analyticsChart', chartOptions);
        });
    });
</script>

  </body>
</html>

