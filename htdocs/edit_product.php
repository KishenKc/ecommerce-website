<?php
require 'db.php';

if (!isset($_GET['id'])) {
    header("Location: admin_products.php");
    exit;
}

$id = intval($_GET['id']);

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);

    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_url = ? WHERE id = ?");
    $stmt->bind_param("ssdsi", $name, $description, $price, $image_url, $id);
    $stmt->execute();

    header("Location: admin_products.php");
    exit;
}

// Load product for editing
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "Product not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@600&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to bottom right, #fffdf5, #f8f1e4);
            color: #2c1f0f;
        }

        header {
            background-color: #1a1a1a;
            color: #ffd700;
            padding: 25px 0;
            text-align: center;
            font-family: 'Playfair Display', serif;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            margin: 0;
            font-size: 2rem;
        }

        .container {
            background: #ffffff;
            margin: 40px auto;
            padding: 40px;
            max-width: 700px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            color: #b8860b;
            font-family: 'Playfair Display', serif;
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 18px;
            color: #5c3e00;
        }

        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 14px;
            margin-top: 6px;
            border: 1px solid #d1b679;
            border-radius: 10px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            transition: border 0.3s ease;
            background-color: #fffaf0;
        }

        input:focus,
        textarea:focus {
            border-color: #daa520;
            outline: none;
        }

        textarea {
            resize: vertical;
        }

        input[type="submit"] {
            margin-top: 30px;
            width: 100%;
            padding: 16px;
            font-size: 16px;
            font-weight: bold;
            background-color: #b8860b;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #8b7500;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 25px;
            color: #8b7500;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .back-link:hover {
            color: #b8860b;
            text-decoration: underline;
        }

        footer {
            background: #1a1a1a;
            color: #ffd700;
            text-align: center;
            padding: 20px;
            margin-top: 60px;
            font-size: 14px;
        }

        @media (max-width: 600px) {
            .container {
                padding: 25px;
                margin: 20px;
            }

            input[type="submit"] {
                padding: 14px;
            }
        }
    </style>
</head>
<body>

<header>
    <h1>🛠️ Admin Dashboard – Edit Product</h1>
</header>

<div class="container">
    <h2>✏️ Update Product Details</h2>

    <form method="post">
        <label for="name">Product Name</label>
        <input type="text" name="name" id="name" value="<?= htmlspecialchars($product['name']) ?>" required>

        <label for="description">Description</label>
        <textarea name="description" id="description" rows="4" required><?= htmlspecialchars($product['description']) ?></textarea>

        <label for="price">Price ($)</label>
        <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($product['price']) ?>" required>

        <label for="image_url">Image URL</label>
        <input type="text" name="image_url" id="image_url" value="<?= htmlspecialchars($product['image_url']) ?>" required>

        <input type="submit" value="💾 Save Changes">
    </form>

    <a href="admin_products.php" class="back-link">⬅ Back to Product List</a>
</div>

<footer>
    &copy; <?= date("Y") ?> Infinity Store Admin Panel
</footer>

</body>
</html>
