<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: role_management.php");
    exit;
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: role_management.php");
exit;
