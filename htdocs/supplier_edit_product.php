<?php
session_start();
require 'db.php';

if (!isset($_SESSION['supplier_id'])) {
    header("Location: supplier_login.php");
    exit;
}

$supplierId = $_SESSION['supplier_id'];
$error = '';
$product = null;

// Get product ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: product_list.php");
    exit;
}

$productId = (int)$_GET['id'];

// Fetch product and verify ownership
$stmt = $conn->prepare("SELECT ProductID, Name, Description, Price, ImageURL FROM products WHERE ProductID = ? AND SupplierID = ?");
$stmt->bind_param("ii", $productId, $supplierId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: product_list.php");
    exit;
}
$product = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] ?? 0;
    $image_url = trim($_POST['image_url'] ?? '');

    if (!$name || !$price || !$image_url) {
        $error = "Name, price and image URL are required.";
    } else {
        $updateStmt = $conn->prepare("UPDATE products SET Name = ?, Description = ?, Price = ?, ImageURL = ? WHERE ProductID = ? AND SupplierID = ?");
        $updateStmt->bind_param("ssdiii", $name, $description, $price, $image_url, $productId, $supplierId);
        if ($updateStmt->execute()) {
            $updateStmt->close();
            header("Location: product_list.php");
            exit;
        } else {
            $error = "Failed to update product.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Edit Product</title>
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
    body {
        font-family: 'Inter', sans-serif;
        background: var(--dark-bg);
        color: var(--text-color);
        margin: 0;
        padding: 40px 20px;
    }
    h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2.8em;
        color: var(--accent);
        text-align: center;
        margin-bottom: 40px;
    }
    form {
        max-width: 600px;
        margin: 0 auto;
        background: var(--card-bg);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 6px 30px rgba(0, 0, 0, 0.6);
    }
    label {
        display: block;
        margin-top: 20px;
        font-weight: 600;
    }
    input[type="text"],
    input[type="number"],
    input[type="url"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 14px;
        background: var(--input-bg);
        color: var(--text-color);
        margin-top: 6px;
    }
    textarea {
        resize: vertical;
    }
    input[type="submit"] {
        width: 100%;
        margin-top: 30px;
        padding: 14px;
        font-size: 16px;
        background: var(--accent);
        border: none;
        color: #000;
        font-weight: bold;
        border-radius: 10px;
        cursor: pointer;
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
    }
    a.back-link {
        display: block;
        text-align: center;
        margin-top: 25px;
        text-decoration: none;
        color: var(--accent);
        font-weight: bold;
        transition: color 0.3s ease;
    }
    a.back-link:hover {
        color: #fff;
        text-decoration: underline;
    }
</style>
</head>
<body>

<h1>✏️ Edit Product</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" action="">
    <label for="name">Product Name</label>
    <input type="text" id="name" name="name" required value="<?= htmlspecialchars($product['Name']) ?>" />

    <label for="description">Description</label>
    <textarea id="description" name="description" rows="4"><?= htmlspecialchars($product['Description']) ?></textarea>

    <label for="price">Price (ZAR)</label>
    <input type="number" id="price" name="price" step="0.01" required value="<?= htmlspecialchars($product['Price']) ?>" />

    <label for="image_url">Image URL</label>
    <input type="url" id="image_url" name="image_url" required value="<?= htmlspecialchars($product['ImageURL']) ?>" />

    <input type="submit" value="💾 Save Changes" />
</form>

<a href="product_list.php" class="back-link">⬅ Back to My Products</a>

</body>
</html>
