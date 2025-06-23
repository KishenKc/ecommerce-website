<?php
session_start();
require 'db.php';

if (!isset($_SESSION['supplier_id'])) {
    header("Location: supplier_login.php");
    exit;
}

$supplier_id = $_SESSION['supplier_id'];

// Handle delete request
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $stmtDel = $conn->prepare("DELETE FROM products WHERE id = ? AND supplier_id = ?");
    $stmtDel->bind_param("ii", $deleteId, $supplier_id);
    $stmtDel->execute();
    header("Location: supplier_products.php");
    exit;
}

// Fetch products of logged-in supplier
$stmt = $conn->prepare("SELECT id, product_name, description, price, image_url FROM products WHERE supplier_id = ?");
$stmt->bind_param("i", $supplier_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>My Products - Supplier Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        /* Reset & base */
        * {
            box-sizing: border-box;
        }
        body {
            background-color: #1c1c1c;
            color: #f5f5f5;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px 20px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: gold;
            margin-bottom: 40px;
            font-weight: 700;
            font-size: 2.5rem;
            text-align: center;
            text-shadow: 0 0 5px #b8860b;
        }

        .top-bar {
            width: 100%;
            max-width: 960px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        /* Buttons */
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            border: none;
            transition: background-color 0.25s ease, color 0.25s ease;
            box-shadow: 0 3px 6px rgba(0,0,0,0.4);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            user-select: none;
        }

        .btn-create {
            background-color: #00c176;
            color: #fff;
            box-shadow: 0 0 12px #00c176cc;
        }
        .btn-create:hover {
            background-color: #009a55;
            box-shadow: 0 0 18px #009a55cc;
            color: #e0ffe0;
        }

        .btn-logout {
            background-color: #777;
            color: white;
            box-shadow: 0 0 10px #666;
        }
        .btn-logout:hover {
            background-color: #555;
            box-shadow: 0 0 15px #555;
        }

        table {
            width: 100%;
            max-width: 960px;
            border-collapse: separate;
            border-spacing: 0 12px;
            box-shadow: 0 0 20px #222;
            border-radius: 12px;
            overflow: hidden;
            background-color: #2e2e2e;
        }

        thead th {
            padding: 20px 15px;
            background-color: #3f3f3f;
            color: gold;
            font-weight: 700;
            text-align: left;
            font-size: 1.1rem;
            border-bottom: 2px solid #b8860b;
        }

        tbody td {
            padding: 15px;
            vertical-align: middle;
            font-size: 1rem;
            color: #ddd;
        }

        tbody tr {
            background-color: #252525;
            transition: background-color 0.3s ease;
            border-radius: 10px;
            box-shadow: inset 0 0 10px #0008;
        }
        tbody tr:hover {
            background-color: #3a3a3a;
        }

        img.product-image {
            max-width: 90px;
            max-height: 90px;
            object-fit: contain;
            border-radius: 10px;
            box-shadow: 0 0 8px #000;
        }

        /* Actions buttons */
        .actions {
            display: flex;
            gap: 12px;
        }

        .btn-edit {
            background-color: #f5d47a;
            color: #1c1c1c;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            box-shadow: 0 0 10px #bfa14f;
            transition: background-color 0.3s ease;
        }
        .btn-edit:hover {
            background-color: #d4b858;
            color: #111;
            box-shadow: 0 0 14px #d4b858;
        }

        .btn-delete {
            background-color: #ff4c4c;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 700;
            box-shadow: 0 0 12px #b33b3b;
            transition: background-color 0.3s ease;
        }
        .btn-delete:hover {
            background-color: #cc3b3b;
            box-shadow: 0 0 18px #cc3b3b;
        }

        .no-products {
            text-align: center;
            margin-top: 40px;
            font-size: 1.3rem;
            color: #ccc;
            font-style: italic;
            max-width: 900px;
        }

        /* Responsive tweaks */
        @media (max-width: 720px) {
            .top-bar {
                flex-direction: column;
                gap: 15px;
            }

            table, thead th, tbody td {
                font-size: 0.9rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="top-bar">
    <a href="supplier_create_product.php" class="btn btn-create">➕ Add New Product</a>
    <a href="logout.php" class="btn btn-logout">🚪 Logout</a>
</div>

<h1>My Products</h1>

<?php if ($result->num_rows === 0): ?>
    <p class="no-products">You have no products yet. Click "Add New Product" to get started.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price (ZAR)</th>
                <th style="min-width: 180px;">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if (!empty($row['image_url'])): ?>
                        <img src="<?= htmlspecialchars($row['image_url']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>" class="product-image" />
                    <?php else: ?>
                        <span style="color:#888;">No image</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>R <?= number_format($row['price'], 2) ?></td>
                <td class="actions">
                    <a href="supplier_edit_product.php?id=<?= $row['id'] ?>" class="btn btn-edit" title="Edit Product">✏️ Edit</a>
                    <a href="supplier_delete_product.php?delete=<?= $row['id'] ?>" class="btn btn-delete" title="Delete Product" onclick="return confirm('Are you sure you want to delete this product?')">🗑 Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
