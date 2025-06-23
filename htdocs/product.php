<?php
session_start();
require 'db.php';

// Get product ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$productId = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM products WHERE ProductID = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $qty = max(1, (int)($_POST['quantity'] ?? 1));

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $qty;
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }

    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= htmlspecialchars($product['Name'] ?? 'Product Not Found') ?></title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet" />
<style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #3498db;
        --background-color: #ffffff;
        --text-color: #333333;
        --button-bg: var(--secondary-color);
        --button-hover-bg: #21618c;
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        padding: 20px;
    }

    header {
        margin-bottom: 30px;
    }

    .container {
        max-width: 1000px;
        margin: auto;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .product-detail {
        display: flex;
        flex-direction: column;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        padding: 20px;
        background: white;
    }

    .product-detail img {
        width: 100%;
        max-width: 400px;
        height: auto;
        object-fit: contain;
        margin-bottom: 20px;
    }

    .product-name {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: var(--primary-color);
    }

    .product-price {
        font-size: 1.5rem;
        margin-bottom: 20px;
        color: var(--secondary-color);
    }

    .product-description {
        font-size: 1rem;
        margin-bottom: 30px;
        text-align: center;
        color: #555;
    }

    form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    input[type="number"] {
        width: 70px;
        padding: 8px;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 0;
        background-color: #f9f9f9;
    }

    button {
        padding: 10px 20px;
        background-color: var(--button-bg);
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 0;
        cursor: pointer;
    }

    button:hover {
        background-color: var(--button-hover-bg);
    }

    a.back-link {
        margin-top: 20px;
        display: inline-block;
        color: var(--primary-color);
        text-decoration: none;
        font-weight: bold;
    }

    a.back-link:hover {
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .product-detail {
            padding: 10px;
        }
    }
</style>
</head>
<body>

<header>
    <a href="index.php" class="back-link">&larr; Back to Products</a>
</header>

<div class="container">
    <?php if (!$product): ?>
        <p>Product not found.</p>
    <?php else: ?>
        <div class="product-detail">
            <img src="<?= htmlspecialchars($product['ImageURL']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" />
            <div class="product-name"><?= htmlspecialchars($product['Name']) ?></div>
            <div class="product-price">R<?= number_format($product['Price'], 2) ?></div>
            <div class="product-description"><?= htmlspecialchars($product['Description'] ?? 'No description available.') ?></div>

            <form method="post" action="product.php?id=<?= $product['ProductID'] ?>">
                <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">
                <input type="number" name="quantity" value="1" min="1">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
