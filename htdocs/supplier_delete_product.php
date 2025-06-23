<?php
session_start();
require 'db.php';

if (!isset($_SESSION['supplier_id'])) {
    header("Location: supplier_login.php");
    exit;
}

$supplierId = $_SESSION['supplier_id'];

// Get product ID and validate
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: product_list.php");
    exit;
}

$productId = (int)$_GET['id'];

// Verify product belongs to this supplier
$stmt = $conn->prepare("SELECT ProductID FROM products WHERE ProductID = ? AND SupplierID = ?");
$stmt->bind_param("ii", $productId, $supplierId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Not found or unauthorized
    $stmt->close();
    header("Location: product_list.php");
    exit;
}
$stmt->close();

// Delete product
$delStmt = $conn->prepare("DELETE FROM products WHERE ProductID = ? AND SupplierID = ?");
$delStmt->bind_param("ii", $productId, $supplierId);
$delStmt->execute();
$delStmt->close();

header("Location: product_list.php");
exit;
?>
