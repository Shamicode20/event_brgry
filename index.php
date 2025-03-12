<?php
session_start();

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on user role
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../event_management/pages/admin/home.php');
        exit;
    } elseif ($_SESSION['role'] === 'user') {
        header('Location: ../event_management/pages/user/home.php');
        exit;
    }
} else {
    // Redirect to login page if not logged in
    header('Location: ../login.php');
    exit;
}
