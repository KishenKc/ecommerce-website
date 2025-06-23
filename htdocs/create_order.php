<?php
require 'db.php';

$errors = [];
$success = false;

// Fetch products to populate product dropdown
$productsResult = $conn->query("SELECT id, name FROM products ORDER BY name");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $product_id = intval($_POST['product_id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $order_date = trim($_POST['order_date'] ?? '');

    // Validation
    if ($customer_name === '') {
        $errors[] = "Customer name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if ($product_id <= 0) {
        $errors[] = "Please select a product.";
    }
    if ($quantity <= 0) {
        $errors[] = "Quantity must be at least 1.";
    }
    if ($status === '') {
        $errors[] = "Status is required.";
    }
    if ($order_date === '') {
        $errors[] = "Order date is required.";
    }

    // If no errors, insert order
    if (!$errors) {
        // Get product name from product id for easier order tracking (optional)
        $productStmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
        $productStmt->bind_param("i", $product_id);
        $productStmt->execute();
        $productStmt->bind_result($product_name);
        $productStmt->fetch();
        $productStmt->close();

        $stmt = $conn->prepare("INSERT INTO orders (customer_name, email, product_id, product_name, quantity, status, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisiss", $customer_name, $email, $product_id, $product_name, $quantity, $status, $order_date);

        if ($stmt->execute()) {
            $success = true;
            // Clear form values
            $customer_name = $email = $status = $order_date = '';
            $product_id = $quantity = 0;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create New Order</title>
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
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: var(--card);
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.15);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        label {
            font-weight: 600;
            font-size: 1.1em;
            margin-bottom: 6px;
            color: var(--gold);
        }
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        select {
            padding: 10px;
            border-radius: 10px;
            border: none;
            font-size: 1em;
            background: var(--navy);
            color: var(--text);
            box-shadow: inset 0 0 5px #000;
            transition: box-shadow 0.3s ease;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 8px var(--gold);
            background: #0a0a1a;
        }
        button {
            background: var(--gold);
            color: var(--navy);
            font-weight: 700;
            padding: 14px;
            border: none;
            border-radius: 30px;
            font-size: 1.1em;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(180, 140, 0, 0.7);
            transition: background 0.3s ease;
        }
        button:hover {
            background: var(--hover-gold);
        }
        .messages {
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .errors {
            background: var(--danger);
            color: var(--white);
        }
        .success {
            background: #22c55e;
            color: var(--white);
        }
        a.back-link {
            display: inline-block;
            margin-bottom: 30px;
            color: var(--gold);
            font-weight: 600;
            text-decoration: none;
        }
        a.back-link:hover {
            color: var(--hover-gold);
        }
    </style>
</head>
<body>

<header>
    <h1>➕ Create New Order</h1>
</header>

<div class="container">
    <a href="view_orders.php" class="back-link">← Back to Orders</a>

    <?php if ($errors): ?>
        <div class="messages errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php elseif ($success): ?>
        <div class="messages success">
            Order created successfully!
        </div>
    <?php endif; ?>

    <form method="post" action="create_order.php" novalidate>
        <label for="customer_name">Customer Name</label>
        <input type="text" id="customer_name" name="customer_name" required
               value="<?= htmlspecialchars($customer_name ?? '') ?>" />

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required
               value="<?= htmlspecialchars($email ?? '') ?>" />

        <label for="product_id">Product</label>
        <select id="product_id" name="product_id" required>
            <option value="">-- Select a Product --</option>
            <?php while ($prod = $productsResult->fetch_assoc()): ?>
                <option value="<?= $prod['id'] ?>"
                    <?= (isset($product_id) && $product_id == $prod['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($prod['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" min="1" required
               value="<?= htmlspecialchars($quantity ?? '') ?>" />

        <label for="status">Status</label>
        <input type="text" id="status" name="status" placeholder="e.g., Pending, Shipped" required
               value="<?= htmlspecialchars($status ?? '') ?>" />

        <label for="order_date">Order Date</label>
        <input type="date" id="order_date" name="order_date" required
               value="<?= htmlspecialchars($order_date ?? '') ?>" />

        <button type="submit">Create Order</button>
    </form>
</div>

<footer>
    © <?= date("Y") ?> Admin Panel · All rights reserved.
</footer>

</body>
</html>
