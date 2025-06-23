<?php
require 'db.php';

// Fetch all orders
$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Orders</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --navy: #0f0f1a;
            --gold: #d4af37;
            --white: #ffffff;
            --gray: #f1f1f1;
            --text: #e4e4e4;
            --card: #1a1a2e;
            --danger: #e63946;
            --hover-gold: #b89128;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--navy);
            color: var(--text);
        }
        header {
            background: var(--navy);
            padding: 50px 20px;
            text-align: center;
            box-shadow: 0 0 30px rgba(0,0,0,0.8);
        }
        header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3em;
            color: var(--gold);
            text-shadow: 0 0 10px var(--gold);
        }
        .container {
            max-width: 1100px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .top-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .top-actions a {
            background: var(--gold);
            color: var(--navy);
            padding: 10px 20px;
            font-weight: bold;
            font-size: 1em;
            border-radius: 30px;
            text-decoration: none;
            transition: background 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        }
        .top-actions a:hover {
            background: var(--hover-gold);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.15);
        }
        th, td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background: var(--gold);
            color: var(--navy);
        }
        .actions a {
            padding: 6px 12px;
            margin-right: 6px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            color: var(--white);
            font-size: 0.9em;
        }
        .view-btn {
            background: #10b981;
        }
        .view-btn:hover {
            background: #0e9f6e;
        }
        .edit-btn {
            background: #3b82f6;
        }
        .edit-btn:hover {
            background: #2563eb;
        }
        .delete-btn {
            background: var(--danger);
        }
        .delete-btn:hover {
            background: #a4161a;
        }
        footer {
            background: var(--navy);
            color: var(--gold);
            padding: 24px;
            text-align: center;
            margin-top: 80px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<header>
    <h1>📦 Order Management</h1>
</header>

<div class="container">
    <div class="top-actions">
        <a href="admin_dashboard.php">← Back to Dashboard</a>
        <a href="create_order.php">➕ Add Order</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Status</th>
                <th>Order Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= (int)$row['quantity'] ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['order_date']) ?></td>
                    <td class="actions">
                        <a href="view_order.php?id=<?= $row['id'] ?>" class="view-btn">View</a>
                        <a href="edit_order.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                        <a href="confirm_delete_order.php?id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<footer>
    © <?= date("Y") ?> Order Panel · All rights reserved.
</footer>

</body>
</html>
