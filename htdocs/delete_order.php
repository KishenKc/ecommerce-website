<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && is_numeric($_POST['id'])) {
    $order_id = (int)$_POST['id'];

    $stmt = $conn->prepare("DELETE FROM orders WHERE OrderID = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        // Success, redirect back to dashboard
        header("Location: admin_dashboard.php?msg=Order+deleted+successfully");
        exit;
    } else {
        // Something went wrong
        header("Location: admin_dashboard.php?error=Unable+to+delete+order");
        exit;
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}
