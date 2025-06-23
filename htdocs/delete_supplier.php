<?php
require 'db.php';

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: view_suppliers.php");
exit;
?>
