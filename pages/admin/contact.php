<?php
require('../../database/connection.php');

try {
    $stmt = $conn->prepare("SELECT id, contact_number, email FROM contact_info LIMIT 1");
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $contact_id = $row['id'] ?? '';
    $contact_number = $row['contact_number'] ?? 'Not Available';
    $email = $row['email'] ?? 'Not Available';
} catch (PDOException $e) {
    die("Query failed: " . $e->getMessage());
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
    <title>Contact Us</title>

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
      

        <ul class="nav">
    
          
          
      
  </section>
</li>

          
  

  </div>
</span>
</span>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
              <a class="dropdown-item" href="#"><i class="fe fe-user"></i>&nbsp;&nbsp;&nbsp;Profile</a>
              <a class="dropdown-item" href="#"><i class="fe fe-settings"></i>&nbsp;&nbsp;&nbsp;Settings</a>
              <a class="dropdown-log-out" href="logout.php"><i class="fe fe-log-out"></i>&nbsp;&nbsp;&nbsp;Log Out</a>
            </div>    
          </li>
        </ul>
      </nav>


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
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
   

</head>
<body>

    

    
    <style>
 
 .container {
     max-width: 500px;
     background: #fff;
     padding: 30px;
     border-radius: 12px;
     box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
     border-top: 5px solid #007BFF;
     text-align: center;
     transition: transform 0.3s ease, box-shadow 0.3s ease;
 }

 .container:hover {
     transform: translateY(-5px);
     box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
 }

 h1 {
     font-size: 28px;
     color: #333;
     margin-bottom: 15px;
 }

 p {
     font-size: 16px;
     color: #555;
     margin-bottom: 20px;
     line-height: 1.5;
 }

 h4 {
     font-size: 18px;
     margin-bottom: 10px;
     color: #007BFF;
 }

 .contact-info {
     font-size: 16px;
     color: #333;
     font-weight: bold;
 }

 .social-icons {
     margin-top: 20px;
 }

 .social-icons a {
     text-decoration: none;
     color: white;
     background: #007BFF;
     padding: 10px;
     border-radius: 50%;
     margin: 5px;
     display: inline-block;
     transition: background 0.3s ease;
 }

 .social-icons a:hover {
     background: #0056b3;
 }

 .social-icons i {
     font-size: 18px;
 }
 .btn-secondary {
    background-color: red!important; /* Parehong asul na kulay */
    color: white !important;
    border: none;
    padding: 5px 10px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}
</style>
</head>
<body>

<div class="container mt-5 text-center">
    <h1>Contact Us</h1>
    <p>If you have any questions or concerns, feel free to reach out to us.</p>
    
    <h4>📞 Barangay Event Contact Number:</h4>
    <p class="contact-info" id="contact-number"><?php echo $contact_number; ?></p>

    <h4>📧 Email:</h4>
    <p class="contact-info" id="contact-email"><?php echo $email; ?></p>

    <button class="btn btn-primary" onclick="openEditModal()">Edit</button>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Contact Information</h5>
                
            </div>
            <div class="modal-body">
                <input type="hidden" id="contact-id" value="<?php echo $contact_id; ?>">
                <label for="edit-contact-number">Contact Number:</label>
                <input type="text" id="edit-contact-number" class="form-control" value="<?php echo $contact_number; ?>">
                <label for="edit-contact-email" class="mt-3">Email:</label>
                <input type="email" id="edit-contact-email" class="form-control" value="<?php echo $email; ?>">
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                <button type="button" class="btn btn-primary" onclick="saveChanges()">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
  
  function openEditModal() {
    $('#editModal').modal('show');
}

function saveChanges() {
    var contactID = $("#contact-id").val();
    var contactNumber = $("#edit-contact-number").val();
    var email = $("#edit-contact-email").val();

    $.ajax({
        url: "update_contact.php",
        type: "POST",
        data: {
            id: contactID,
            contact_number: contactNumber,
            email: email
        },
        success: function(response) {
            var result = JSON.parse(response);
            if (result.status === "success") {
                $("#contact-number").text(contactNumber);
                $("#contact-email").text(email);
                $('#editModal').modal('hide');
                alert("Contact updated successfully!");
            } else {
                alert("Error: " + result.message);
            }
        },
        error: function(xhr, status, error) {
            alert("AJAX request failed: " + xhr.responseText);
        }
    });
}


</script>

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
