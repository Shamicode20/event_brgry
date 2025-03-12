
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

       <!--button-->
      <div class="dashboard-header">
    <button class="add-item-btn">+ Add Item</button>
</div>

      <div class="dashboard-container">
    <div class="dashboard-item">
        <h2>Tent</h2>
        <h3>50</h3>
        <div class="chart-container">
            <img src="../../assets/img/tent.jpg" alt="Tent">
        </div>
    </div>

    <div class="dashboard-item">
        <h2>Chair</h2>
        <h3>1,000</h3>
        <div class="chart-container">
            <img src="../../assets/img/chairs.jpg" alt="Chair">
        </div>
    </div>

    <div class="dashboard-item">
        <h2>Table</h2>
        <h3>500</h3>
        <div class="chart-container">
            <img src="../../assets/img/monoblock_table.jpg" alt="Table">
        </div>
    </div>

    <div class="dashboard-item">
        <h2>Sound System</h2>
        <h3>5</h3>
        <div class="chart-container">
            <img src="../../assets/img/sound_system.jpg" alt="Sound System">
        </div>
    </div>

    <div class="dashboard-item">
        <h2>Lighting</h2>
        <h3>100</h3>
        <div class="chart-container">
            <img src="../../assets/img/lighting.jpg" alt="Lighting">
        </div>
    </div>

    <div class="dashboard-item">
        <h2>Microphones</h2>
        <h3>50</h3>
        <div class="chart-container">
            <img src="../../assets/img/microphone.jpg" alt="Microphones">
        </div>
    </div>
</div>

     </main>
     </div>

      
      
      
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include('script.php')?>
  </body>
</html>

