<?php
session_start();
require 'db.php';

if (!isset($_SESSION['supplier_id'])) {
    header("Location: supplier_login.php");
    exit;
}

$supplier_id = $_SESSION['supplier_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $image_url = trim($_POST['image_url']);

    if (empty($name) || empty($price)) {
        $error = "Please fill in the required fields (Name and Price).";
    } else {
        $stmt = $conn->prepare("INSERT INTO products (supplier_id, name, description, price, image_url) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $supplier_id, $name, $description, $price, $image_url);
        if ($stmt->execute()) {
            $success = "Product added successfully.";
        } else {
            $error = "Error adding product: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Product</title>
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
            background-color: #28a745;
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
            background-color: #218838;
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
    </style>
</head>
<body>

<h1>➕ Add New Product</h1>

<?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" action="supplier_add_product.php">
    <label for="name">Product Name *</label>
    <input type="text" id="name" name="name" required>

    <label for="description">Description</label>
    <textarea id="description" name="description"></textarea>

    <label for="price">Price (R) *</label>
    <input type="number" id="price" name="price" step="0.01" min="0" required>

    <label for="image_url">Image URL</label>
    <input type="text" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">

    <input type="submit" value="Add Product">
</form>

<a href="supplier_products.php" class="back-link">← Back to My Products</a>

</body>
</html>
