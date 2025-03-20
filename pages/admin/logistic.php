<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

define('BASE_PATH', dirname(__DIR__, 2));
require_once BASE_PATH . '/database/connection.php';

try {
    $stmt = $conn->prepare("SELECT * FROM inventory");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error'] = "Error fetching products: " . $e->getMessage();
    $products = [];
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
    <title>Logistic</title>

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

.add-item-btn,
.btn-primary {
    background-color: #007bff !important; /* Parehong asul na kulay */
    color: white !important;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}

.add-item-btn:hover,
.btn-primary:hover {
    background-color: #0056b3 !important; /* Darker blue para sa hover effect */
}
.add-item-btn,
.btn-secondary {
    background-color: #007bff !important; /* Parehong asul na kulay */
    color: white !important;
    border: none;
    padding: 10px 20px;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: background 0.3s;
}

.add-item-btn:hover,
.btn-secondary:hover {
    background-color: #0056b3 !important; /* Darker blue para sa hover effect */
}



</style>
  
  </head>

    
    
 
  <body class="vertical  light">
  <div class="wrapper">
        <?php include('navbar.php'); ?>
        <?php include('sidebar.php'); ?>
        <main role="main" class="main-content">
            <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Event Inventory</h2>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">+ Add Equipment</button>
        <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editInventoryModal">Edit Inventory</button>
    </div>
</div>

                <div class="row">
    <?php if (!empty($products)) : ?>
        <?php foreach ($products as $product) : ?>
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card shadow-sm border-0 custom-card">
                    <img src="../../assets/img/<?php echo htmlspecialchars($product['image_url'] ?? 'default.jpg'); ?>" 
                         class="card-img-top product-image">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-primary fw-bold"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-muted">Quantity: <span class="fw-semibold"><?php echo htmlspecialchars($product['quantity']); ?></span></p>

                       
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p class="text-center">No products found. Add a new product!</p>
    <?php endif; ?>
</div>

            <!-- Add Product Modal -->
            <div class="modal fade" id="addProductModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="logistics.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Equipment</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal">X</button>
                            </div>
                            <div class="modal-body">
                                <label>Equipment</label>
                                <input type="text" name="name" class="form-control" required>
                                <label>Quantity</label>
                                <input type="number" name="quantity" class="form-control" required>
                                <label>Equipment Image</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Add</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
  <!-- Edit Inventory Modal -->
  <div class="modal fade" id="editInventoryModal">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form action="process_edit_inventory.php" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Inventory</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product) : ?>
                                            <tr>
                                                <td><input type="text" name="names[]" value="<?php echo htmlspecialchars($product['name']); ?>" class="form-control"></td>
                                                <td><input type="number" name="quantities[]" value="<?php echo htmlspecialchars($product['quantity']); ?>" class="form-control"></td>
                                                <td><input type="hidden" name="ids[]" value="<?php echo $product['id']; ?>"></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        </main>
    </div>
      
      
      
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <?php include('script.php')?>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        let editModal = document.getElementById("editProductModal");
        editModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            document.getElementById("editProductId").value = button.getAttribute("data-id");
            document.getElementById("editName").value = button.getAttribute("data-name");
            document.getElementById("editQuantity").value = button.getAttribute("data-quantity");
        });

        let deleteModal = document.getElementById("deleteProductModal");
        deleteModal.addEventListener("show.bs.modal", function (event) {
            let button = event.relatedTarget;
            document.getElementById("deleteProductId").value = button.getAttribute("data-id");
        });
    });
    $(document).ready(function() {
        $('#editInventoryModal').on('show.bs.modal', function(event) {
            let button = $(event.relatedTarget);
            let modal = $(this);
            modal.find('form')[0].reset();
        });
    });
    </script>
  </body>
</html>

