<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}
require 'db.php';

// Fetch data
$admin_result = $conn->query("SELECT id, full_name, email, username FROM admins");
$supplier_result = $conn->query("SELECT SupplierID, FullName, Email, PhoneNumber, CompanyName, CreatedAt FROM suppliers");
$order_result = $conn->query("SELECT * FROM orders");
$customer_result = $conn->query("SELECT CustomerID, FullName, Email FROM customers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
<style>
    :root {
        --dark-bg: #1f1f1f;
        --text-color: #e0e0e0;
        --header-bg: #333;
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
        background: var(--dark-bg);
        color: var(--text-color);
        font-family: 'Inter', sans-serif;
        padding: 40px 20px;
    }

    h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        color: var(--btn-orange);
        margin-bottom: 30px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .logout-link {
        background-color: var(--btn-grey);
        color: white;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .logout-link:hover {
        background-color: var(--btn-grey-hover);
    }

    button {
        background-color: var(--btn-orange);
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: bold;
        color: #fff;
        font-size: 14px;
        margin: 0 10px 20px 0;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: var(--btn-orange-hover);
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 50px;
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }

    caption {
        caption-side: top;
        font-size: 1.5rem;
        color: var(--btn-orange);
        margin-bottom: 10px;
        font-weight: bold;
    }

    th, td {
        padding: 14px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    th {
        background-color: #3a3a3a;
        color: #fff;
    }

    td {
        background-color: #2b2b2b;
        color: var(--text-color);
        font-size: 14px;
    }

    .action-links a {
        text-decoration: none;
        font-weight: 600;
        margin-right: 8px;
        font-size: 0.9rem;
    }

    .action-links a.edit {
        color: var(--btn-orange);
    }

    .action-links a.delete {
        color: var(--danger);
    }

    .action-links a.delete:hover {
        color: var(--danger-hover);
    }

    form {
        display: inline;
    }

    @media (max-width: 768px) {
        table, th, td {
            font-size: 12px;
        }

        h1 {
            font-size: 1.4rem;
        }

        button {
            font-size: 12px;
        }
    }
</style>
</head>
<body>

<div class="header">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['admin_username']) ?></h1>
    <a href="admin_logout.php" class="logout-link">Log Out</a>
</div>

<!-- Add Buttons -->
<form action="create_admin.php" method="get"><button type="submit">Add New Admin</button></form>
<form action="supplier_create.php" method="get"><button type="submit">Add New Supplier</button></form>

<!-- Admin Users Table -->
<table>
    <caption>Admin Users</caption>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($admin = $admin_result->fetch_assoc()): ?>
            <tr>
                <td><?= $admin['id'] ?></td>
                <td><?= htmlspecialchars($admin['full_name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td><?= htmlspecialchars($admin['username']) ?></td>
                <td class="action-links">
                    <a class="edit" href="edit_admin.php?id=<?= $admin['id'] ?>">Edit</a>
                    <a class="delete" href="confirm_delete_admin.php?id=<?= $admin['id'] ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Customers Table -->

<table>
    <caption>Customers</caption>
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($customer =$customer_result->fetch_assoc()): ?>
            <tr>
                <td><?= $customer['CustomerID'] ?></td>
                <td><?= htmlspecialchars($customer['FullName']) ?></td>
                <td><?= htmlspecialchars($customer['Email']) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>


<!-- Suppliers Table -->
<table>
    <caption>Suppliers</caption>
    <thead>
        <tr>
            <th>SupplierID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone Number</th>
            <th>Company Name</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($supplier = $supplier_result->fetch_assoc()): ?>
            <tr>
                <td><?= $supplier['SupplierID'] ?></td>
                <td><?= htmlspecialchars($supplier['FullName']) ?></td>
                <td><?= htmlspecialchars($supplier['Email']) ?></td>
                <td><?= htmlspecialchars($supplier['PhoneNumber']) ?></td>
                <td><?= htmlspecialchars($supplier['CompanyName']) ?></td>
                <td><?= $supplier['CreatedAt'] ?></td>
                <td class="action-links">
                    <a class="edit" href="edit_supplier.php?id=<?= $supplier['SupplierID'] ?>">Edit</a>
                    <a class="delete" href="confirm_delete_supplier.php?id=<?= $supplier['SupplierID'] ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Orders Table -->
<table>
    <caption>Orders</caption>
    <thead>
        <tr>
            <th>OrderID</th>
            <th>SupplierID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address Line 1</th>
            <th>Address Line 2</th>
            <th>City</th>
            <th>Postal Code</th>
            <th>Country</th>
            <th>Shipping Method</th>
            <th>Shipping Cost</th>
            <th>Subtotal</th>
            <th>Total</th>
            <th>Order Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = $order_result->fetch_assoc()): ?>
            <tr>
                <td><?= $order['OrderID'] ?></td>
                <td><?= $order['SupplierID'] ?></td>
                <td><?= htmlspecialchars($order['FullName']) ?></td>
                <td><?= htmlspecialchars($order['Email']) ?></td>
                <td><?= htmlspecialchars($order['Phone']) ?></td>
                <td><?= htmlspecialchars($order['AddressLine1']) ?></td>
                <td><?= htmlspecialchars($order['AddressLine2']) ?></td>
                <td><?= htmlspecialchars($order['City']) ?></td>
                <td><?= htmlspecialchars($order['PostalCode']) ?></td>
                <td><?= htmlspecialchars($order['Country']) ?></td>
                <td><?= htmlspecialchars($order['ShippingMethod']) ?></td>
                <td><?= $order['ShippingCost'] ?></td>
                <td><?= $order['Subtotal'] ?></td>
                <td><?= $order['Total'] ?></td>
                <td><?= $order['OrderDate'] ?></td>
                <td class="action-links">
                    <a class="delete" href="confirm_delete_order.php?id=<?= $order['OrderID'] ?>">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
