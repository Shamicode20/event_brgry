<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

// Include database connection
require '../../database/connection.php';

try {
    // Get only archived events
    $stmt = $conn->prepare("SELECT * FROM archived_events ORDER BY archived_at DESC");
    $stmt->execute();
    $archived_events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching archived events: " . $e->getMessage();
    $archived_events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" href="../assets/images/unified-lgu-logo.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <link rel ="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <title>Archived Events</title>

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
</style>
  
  </head>

    
    
 
  <body class="vertical  light">
    <div class="wrapper">
    

    <?php include('navbar.php')?>
      <?php include('sidebar.php')?>
      <main role="main" class="main-content">
    <div class="container">
        <h2 class="text-center mb-4"><i class="fa-solid fa-archive"></i> Archived Events</h2>

        <!-- Flash Messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="message success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Table -->
        <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover text-center align-middle">
        <thead class="table-primary">
            <tr>
                <th style="width: 20%;">Title</th>
                <th style="width: 30%;">Description</th>
                <th style="width: 15%;">Schedule</th>
                <th style="width: 15%;">Poster</th>
                <th style="width: 20%;">Archived At</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($archived_events)): ?>
                <?php foreach ($archived_events as $event): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($event['title']); ?></td>
                        <td><?php echo htmlspecialchars($event['description']); ?></td>
                        <td><?php echo htmlspecialchars($event['schedule']); ?></td>
                        <td>
                            <?php if (!empty($event['poster_base64'])): ?>
                                <img src="data:image/png;base64,<?php echo htmlspecialchars($event['poster_base64']); ?>" 
                                     alt="Event Poster" class="img-thumbnail" style="max-width: 100px; height: auto;">
                            <?php else: ?>
                                <span class="text-muted">No poster</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($event['archived_at']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">No archived events found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


        <!-- Back Button -->
        <div class="text-center">
            <a href="events.php" class="btn-custom"><i class="fa-solid fa-arrow-left"></i> Back to Events</a>
        </div>
    </div>
</main>

      
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

