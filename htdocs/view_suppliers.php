<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

// Fetch all suppliers (users with role = 'supplier')
$result = $conn->query("SELECT id, full_name, username, email FROM users WHERE role = 'supplier' ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>View Suppliers</title>
    <style>
        /* same theme as above */
        :root {
            --navy: #0f0f1a;
            --gold: #d4af37;
            --white: #ffffff;
            --text: #e4e4e4;
            --card: #1a1a2e;
            --hover-gold: #b89128;
        }
        body {
            background: var(--navy);
            color: var(--text);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 30px;
        }
        h1 {
            color: var(--gold);
            margin-bottom: 20px;
            text-shadow: 0 0 6px var(--gold);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(212,175,55,0.3);
        }
        th, td {
            padding: 12px 18px;
            border-bottom: 1px solid #444;
            text-align: left;
        }
        th {
            background: var(--gold);
            color: var(--navy);
        }
        tr:hover {
            background: #33334d;
        }
        a.action-btn {
            color: var(--gold);
            text-decoration: none;
            font-weight: 600;
            margin-right: 15px;
            transition: color 0.3s ease;
        }
        a.action-btn:hover {
            color: var(--hover-gold);
        }
    </style>
</head>
<body>

<h1>Suppliers</h1>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Username</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($supplier = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($supplier['id']) ?></td>
                    <td><?= htmlspecialchars($supplier['full_name']) ?></td>
                    <td><?= htmlspecialchars($supplier['username']) ?></td>
                    <td><?= htmlspecialchars($supplier['email']) ?></td>
                    <td>
                        <a class="action-btn" href="edit_supplier.php?id=<?= $supplier['id'] ?>">Edit</a>
                        <a class="action-btn" href="delete_supplier.php?id=<?= $supplier['id'] ?>" onclick="return confirm('Delete this supplier?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">No suppliers found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
