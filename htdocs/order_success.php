<?php
session_start();

$orderId = $_GET['order_id'] ?? null;
$message = $_SESSION['checkout_success'] ?? '';

if (!$orderId || !$message) {
    header("Location: index.php");
    exit;
}

// Clear success message from session after showing
unset($_SESSION['checkout_success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Order Success</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet" />
<style>
    body {
        font-family: 'Inter', sans-serif;
        background: #f4f7fa;
        color: #34495e;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        padding: 20px;
    }
    .success-container {
        background: #fff;
        padding: 40px 60px;
        border-radius: 15px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 600px;
    }
    h1 {
        font-family: 'Roboto Slab', serif;
        color: #27ae60;
        margin-bottom: 20px;
        font-size: 2.8rem;
    }
    p {
        font-size: 1.2rem;
        margin-bottom: 30px;
    }
    a {
        display: inline-block;
        padding: 14px 30px;
        background: #2980b9;
        color: white;
        text-decoration: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }
    a:hover {
        background: #1c5d8b;
    }
</style>
</head>
<body>

<div class="success-container" role="alert">
    <h1>Order Confirmed!</h1>
    <p><?= htmlspecialchars($message) ?></p>
    <a href="index.php">Continue Shopping</a>
</div>

</body>
</html>
