<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Handle Admin Delete Request
if (isset($_GET['delete_admin'])) {
    $deleteId = intval($_GET['delete_admin']);
    if ($deleteId !== $_SESSION['admin_id']) { // Prevent self-deletion
        $stmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->bind_param("i", $deleteId);
        $stmt->execute();
    }
    header("Location: role_management.php");
    exit;
}

// Handle Supplier Delete Request
if (isset($_GET['delete_supplier'])) {
    $deleteId = intval($_GET['delete_supplier']);
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    header("Location: role_management.php");
    exit;
}

// Fetch All Admins
$adminsResult = $conn->query("SELECT id, username FROM admins ORDER BY id ASC");

// Fetch All Suppliers
$suppliersResult = $conn->query("SELECT id, username FROM suppliers ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Role Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #1c1c1c;
            color: #f5f5f5;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 40px;
        }

        h1 {
            color: gold;
            text-align: center;
            margin-bottom: 40px;
        }

        h2 {
            color: gold;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 50px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        th {
            background-color: #333;
            color: gold;
        }

        td {
            background-color: #2e2e2e;
        }

        .btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: 0.3s ease;
            display: inline-block;
            text-align: center;
        }

        .btn-edit {
            background-color: #f5d47a;
            color: #1c1c1c;
        }

        .btn-delete {
            background-color: #ff4c4c;
            color: white;
        }

        .btn-create {
            background-color: #00c176;
            color: white;
            margin-bottom: 20px;
        }

        .btn-logout {
            background-color: #777;
            color: white;
            float: right;
            padding: 10px 20px;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .section-container {
            max-width: 900px;
            margin: 0 auto 60px auto;
        }

        /* Clear float fix */
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

<div class="top-bar clearfix">
    <a href="admin_create.php" class="btn btn-create">➕ Create Admin</a>
    <a href="supplier_create.php" class="btn btn-create">➕ Create Supplier</a>
    <a href="logout.php" class="btn-logout">🚪 Logout</a>
</div>

<div class="section-container">
    <h1>🛠 Role Management</h1>

    <h2>Admin Management</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($admin = $adminsResult->fetch_assoc()): ?>
            <tr>
                <td><?= $admin['id'] ?></td>
                <td><?= htmlspecialchars($admin['username']) ?></td>
                <td class="actions">
                    <a href="admin_edit.php?id=<?= $admin['id'] ?>" class="btn btn-edit">✏️ Edit</a>
                    <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                        <a href="role_management.php?delete_admin=<?= $admin['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this admin?')">🗑 Delete</a>
                    <?php else: ?>
                        <span style="color:#888;">(You)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="section-container">
    <h2>Supplier Management</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($supplier = $suppliersResult->fetch_assoc()): ?>
            <tr>
                <td><?= $supplier['id'] ?></td>
                <td><?= htmlspecialchars($supplier['username']) ?></td>
                <td class="actions">
                    <a href="supplier_edit.php?id=<?= $supplier['id'] ?>" class="btn btn-edit">✏️ Edit</a>
                    <a href="role_management.php?delete_supplier=<?= $supplier['id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this supplier?')">🗑 Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
