<?php
session_start();
require 'db.php';

if (!isset($_SESSION['supplier_id'])) {
    header("Location: supplier_login.php");
    exit;
}

$supplier_id = $_SESSION['supplier_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: supplier_products.php");
    exit;
}

$product_id = intval($_GET['id']);
$error = '';
$success = '';

// Fetch product to edit
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND supplier_id = ?");
$stmt->bind_param("ii", $product_id, $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    header("Location: supplier_products.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);

    if (empty($name) || empty($price)) {
        $error = "Please fill in the required fields (Name and Price).";
    } else {
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image_url = ? WHERE id = ? AND supplier_id = ?");
        $stmt->bind_param("ssdssi", $name, $description, $price, $image_url, $product_id, $supplier_id);
        if ($stmt->execute()) {
            $success = "Product updated successfully.";
            // Refresh product info after update
            $product['name'] = $name;
            $product['description'] = $description;
            $product['price'] = $price;
            $product['image_url'] = $image_url;
        } else {
            $error = "Error updating product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Inter', sans-serif;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            max-width: 500px;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-weight: 700;
            cursor: pointer;
            font-size: 1.1rem;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            margin-bottom: 15px;
            padding: 12px;
            border-radius: 5px;
        }
        .error {
            background-color: #f8d7da;
            color: #842029;
        }
        .success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .product-image {
            max-width: 150px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<h1>✏️ Edit Product</h1>

<?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if (!empty($product['image_url'])): ?>
    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product Image" class="product-image">
<?php endif; ?>

<form method="post" action="supplier_edit_product.php?id=<?= $product_id ?>">
    <label for="name">Product Name *</label>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>

    <label for="description">Description</label>
    <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>

    <label for="price">Price (R) *</label>
    <input type="number" id="price" name="price" step="0.01" min="0" value="<?= htmlspecialchars($product['price']) ?>" required>

    <label for="image_url">Image URL</label>
    <input type="text" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" value="<?= htmlspecialchars($product['image_url']) ?>">

    <input type="submit" value="Update Product">
</form>

<a href="supplier_products.php" class="back-link">← Back to My Products</a>

</body>
</html>
