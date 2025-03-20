<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../index.php');
    exit;
}

require '../../database/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ids'])) {
    try {
        // Start transaction
        $conn->beginTransaction();

        $ids = $_POST['ids'];
        $names = $_POST['names'];
        $quantities = $_POST['quantities'];

        foreach ($ids as $index => $id) {
            $name = trim($names[$index]);
            $quantity = trim($quantities[$index]);

            // Validate input
            if (empty($name) || !is_numeric($quantity) || $quantity < 0) {
                $_SESSION['error'] = "Invalid input detected.";
                header('Location: ../admin/logistic.php');
                exit;
            }

            // Update inventory
            $stmt = $conn->prepare("UPDATE inventory SET name = ?, quantity = ? WHERE id = ?");
            $stmt->execute([$name, $quantity, $id]);
        }

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Inventory updated successfully!";
    } catch (Exception $e) {
        $conn->rollBack(); // Rollback transaction in case of an error
        $_SESSION['error'] = "Error updating inventory: " . $e->getMessage();
    }

    header("Location: ../admin/logistic.php");
    exit;
}
?>
