<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$order_id = (int)$_GET['id'];

// Fetch order to confirm it exists
$stmt = $conn->prepare("SELECT OrderID, FullName, Total, OrderDate FROM orders WHERE OrderID = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Order not found
    header("Location: admin_dashboard.php");
    exit;
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Confirm Delete Order</title>
<style>
  body {
    background: #0a1f2d;
    color: #cfd8dc;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 40px;
  }
  .container {
    max-width: 500px;
    background-color: #17434d;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(38, 166, 154, 0.5);
    margin: auto;
  }
  h2 {
    color: #26a69a;
    margin-bottom: 20px;
  }
  p {
    font-size: 1.1rem;
    margin-bottom: 30px;
  }
  .btn-confirm, .btn-cancel {
    padding: 12px 22px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    border: none;
    margin-right: 15px;
    transition: background-color 0.3s ease;
  }
  .btn-confirm {
    background-color: #ef5350;
    color: #fff;
    box-shadow: 0 4px 10px rgba(239, 83, 80, 0.7);
  }
  .btn-confirm:hover {
    background-color: #d43f3a;
  }
  .btn-cancel {
    background-color: #26a69a;
    color: #0a1f2d;
    box-shadow: 0 4px 10px rgba(38, 166, 154, 0.7);
  }
  .btn-cancel:hover {
    background-color: #1e897f;
  }
  a {
    text-decoration: none;
  }
</style>
</head>
<body>
  <div class="container">
    <h2>Confirm Delete Order</h2>
    <p>Are you sure you want to delete the order placed by <strong><?= htmlspecialchars($order['FullName']) ?></strong> on <strong><?= htmlspecialchars($order['OrderDate']) ?></strong> with total amount <strong>$<?= number_format($order['Total'], 2) ?></strong>?</p>

    <form method="post" action="delete_order.php">
      <input type="hidden" name="id" value="<?= $order['OrderID'] ?>">
      <button type="submit" class="btn-confirm">Yes, Delete</button>
      <a href="admin_dashboard.php" class="btn-cancel">Cancel</a>
    </form>
  </div>
</body>
</html>
