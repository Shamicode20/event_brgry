<?php
require('../../database/connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];
    $email = trim($_POST['email']);
    $contact_number = trim($_POST['contact_number']);

    if (empty($email) || empty($contact_number)) {
        echo json_encode(["status" => "error", "message" => "Email and contact number are required."]);
        exit;
    }

    try {
        $stmt = $conn->prepare("UPDATE contact_info SET email = ?, contact_number = ? WHERE id = ?");
        $stmt->execute([$email, $contact_number, $id]);

        echo json_encode(["status" => "success"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
    exit;
}
?>
