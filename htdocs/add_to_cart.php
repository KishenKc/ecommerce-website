<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = (int)$_POST['product_id'];

    // Validate product exists
    $stmt = $conn->prepare("SELECT ProductID FROM products WHERE ProductID = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add or increment product quantity
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
    }
    $stmt->close();
}

// Redirect back to referring page or index if no referrer
$redirectUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php';
header("Location: $redirectUrl");
exit;
