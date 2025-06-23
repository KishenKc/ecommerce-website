<?php
session_start();
require 'db.php';

if (!isset($_SESSION['supplier_id'])) {
    header("Location: supplier_login.php");
    exit;
}

$supplierId = $_SESSION['supplier_id'];
$supplierName = $_SESSION['supplier_name'] ?? 'Supplier';

try {
    $stmt = $conn->prepare("SELECT ProductID, Name, Description, Price, ImageURL FROM products WHERE SupplierID = ?");
    if (!$stmt) {
        throw new Exception("Failed to prepare statement");
    }
    $stmt->bind_param("i", $supplierId);
    $stmt->execute();
    $result = $stmt->get_result();
} catch (Exception $e) {
    $errorMessage = "Error loading products: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>My Products - Supplier Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
<style>
    :root {
        --dark-bg: #1f1f1f;
        --text-color: #e0e0e0;
        --header-bg: #333333;
        --card-bg: #2a2a2a;
        --border-color: #444;
        --btn-orange: #ffa500;
        --btn-orange-hover: #cc8400;
        --btn-grey: #888;
        --btn-grey-hover: #666;
        --danger: #e74c3c;
        --danger-hover: #c0392b;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: var(--dark-bg);
        color: var(--text-color);
    }

    header {
        background-color: var(--header-bg);
        padding: 20px 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 0 10px rgba(0,0,0,0.4);
    }

    header h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2em;
        margin: 0;
    }

    .logout-btn {
        background: var(--btn-grey);
        color: white;
        padding: 10px 20px;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        text-decoration: none;
        transition: background 0.3s ease;
    }

    .logout-btn:hover {
        background: var(--btn-grey-hover);
    }

    main {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .actions {
        text-align: right;
        margin-bottom: 20px;
    }

    .btn-create {
        background: var(--btn-orange);
        color: #fff;
        padding: 12px 20px;
        font-weight: bold;
        text-decoration: none;
        border-radius: 6px;
        transition: background 0.3s ease;
    }

    .btn-create:hover {
        background: var(--btn-orange-hover);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 14px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    th {
        background-color: #3a3a3a;
        color: #fff;
        font-weight: 600;
    }

    tr:last-child td {
        border-bottom: none;
    }

    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #555;
    }

    .btn-edit {
        background: var(--btn-orange);
        color: #fff;
        padding: 6px 12px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: bold;
        border-radius: 6px;
        margin-right: 8px;
        transition: background 0.3s ease;
    }

    .btn-edit:hover {
        background: var(--btn-orange-hover);
    }

    .btn-delete {
        background: var(--danger);
        color: #fff;
        padding: 6px 12px;
        text-decoration: none;
        font-size: 0.9rem;
        font-weight: bold;
        border-radius: 6px;
        transition: background 0.3s ease;
    }

    .btn-delete:hover {
        background: var(--danger-hover);
    }

    .error-message {
        background: #ffe5e5;
        color: var(--danger);
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-weight: bold;
        text-align: center;
    }

    .empty-message {
        text-align: center;
        font-size: 1.1rem;
        color: #aaa;
        margin-top: 40px;
    }
</style>
</head>
<body>

<header>
    <h1>Welcome, <?= htmlspecialchars($supplierName) ?></h1>
    <a href="supplier_logout.php" class="logout-btn">Logout</a>
</header>

<main>
    <div class="actions">
        <a href="supplier_create_product.php" class="btn-create">+ Add New Product</a>
    </div>

    <?php if (isset($errorMessage)): ?>
        <div class="error-message"><?= htmlspecialchars($errorMessage) ?></div>
    <?php elseif ($result->num_rows === 0): ?>
        <p class="empty-message">You have not added any products yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price (ZAR)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><img src="<?= htmlspecialchars($row['ImageURL']) ?>" alt="<?= htmlspecialchars($row['Name']) ?>" class="product-image" /></td>
                        <td><?= htmlspecialchars($row['Name']) ?></td>
                        <td><?= nl2br(htmlspecialchars($row['Description'])) ?></td>
                        <td>R<?= number_format($row['Price'], 2) ?></td>
                        <td>
                            <a href="supplier_edit_product.php?id=<?= $row['ProductID'] ?>" class="btn-edit">Edit</a>
                            <a href="supplier_delete_product.php?id=<?= $row['ProductID'] ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</main>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
