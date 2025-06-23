<?php
session_start();
require 'db.php';

if (!isset($_SESSION['supplier_id'])) {
    header("Location: supplier_login.php");
    exit;
}

$error = '';
$supplierId = $_SESSION['supplier_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $image_url = trim($_POST['image_url'] ?? '');

    if (!$name || !$price || !$image_url) {
        $error = "Name, price, and image URL are required.";
    } elseif (!filter_var($image_url, FILTER_VALIDATE_URL)) {
        $error = "Please enter a valid URL for the image.";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (SupplierID, Name, Description, Price, ImageURL) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $supplierId, $name, $description, $price, $image_url);

        if ($stmt->execute()) {
            header("Location: product_list.php");
            exit;
        } else {
            $error = "Database error: Could not save product.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Product - Supplier Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
<style>
    :root {
        --dark-bg: #1f1f1f;
        --card-bg: #2c2c2c;
        --text-color: #e0e0e0;
        --input-bg: #2e2e2e;
        --border-color: #555;
        --accent: #ffa500;
        --accent-hover: #cc8400;
        --error-bg: #3b1f1f;
        --error-text: #ff6b6b;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: var(--dark-bg);
        color: var(--text-color);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px 20px;
    }

    .container {
        max-width: 600px;
        width: 100%;
        background: var(--card-bg);
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 6px 30px rgba(0,0,0,0.7);
        color: var(--text-color);
    }

    h2 {
        font-family: 'Playfair Display', serif;
        color: var(--accent);
        margin-bottom: 30px;
        text-align: center;
    }

    label {
        display: block;
        margin-top: 20px;
        font-weight: 600;
        color: var(--text-color);
    }

    input[type="text"],
    input[type="number"],
    input[type="url"],
    textarea {
        width: 100%;
        padding: 12px;
        margin-top: 6px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--input-bg);
        color: var(--text-color);
        font-size: 14px;
        transition: border 0.3s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="url"]:focus,
    textarea:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 6px var(--accent);
    }

    input[type="submit"] {
        margin-top: 30px;
        width: 100%;
        padding: 14px;
        background: var(--accent);
        border: none;
        border-radius: 10px;
        font-weight: 700;
        color: #000;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s ease;
    }

    input[type="submit"]:hover {
        background: var(--accent-hover);
    }

    .error {
        background: var(--error-bg);
        color: var(--error-text);
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 600;
    }

    a.back-link {
        display: block;
        margin-top: 25px;
        text-align: center;
        color: var(--accent);
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    a.back-link:hover {
        color: var(--accent-hover);
        text-decoration: underline;
    }
</style>
</head>
<body>

<div class="container">
    <h2>➕ Add New Product</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="supplier_create_product.php">
        <label for="name">Product Name *</label>
        <input type="text" id="name" name="name" required placeholder="e.g. Premium Watch" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

        <label for="description">Description</label>
        <textarea id="description" name="description" rows="4" placeholder="Enter product details..."><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>

        <label for="price">Price (ZAR) *</label>
        <input type="number" step="0.01" id="price" name="price" required placeholder="e.g. 199.99" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">

        <label for="image_url">Image URL *</label>
        <input type="url" id="image_url" name="image_url" required placeholder="https://example.com/image.jpg" value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>">

        <input type="submit" value="✅ Add Product">
    </form>

    <a href="product_list.php" class="back-link">⬅ Back to My Products</a>
</div>

</body>
</html>
