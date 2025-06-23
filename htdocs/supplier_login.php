<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT SupplierID, FullName, PasswordHash FROM suppliers WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($supplierId, $fullName, $passwordHash);
            $stmt->fetch();

            if (password_verify($password, $passwordHash)) {
                $_SESSION['supplier_id'] = $supplierId;
                $_SESSION['supplier_name'] = $fullName;
                header("Location: product_list.php");
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "No account found with that email.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Supplier Login</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
<style>
    :root {
        --dark-bg: #1f1f1f;
        --card-bg: #2b2b2b;
        --text-color: #e0e0e0;
        --border-color: #444;
        --accent: #ffa500;
        --accent-hover: #cc8400;
        --error-bg: #3b1f1f;
        --error-text: #ff6b6b;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif;
        background-color: var(--dark-bg);
        color: var(--text-color);
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        background-color: var(--card-bg);
        padding: 40px 30px;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        width: 100%;
        max-width: 400px;
        text-align: center;
    }

    h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 30px;
        color: var(--accent);
    }

    label {
        display: block;
        text-align: left;
        font-weight: 600;
        margin-bottom: 6px;
        font-size: 0.9rem;
    }

    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 12px 14px;
        font-size: 1rem;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        background-color: #1f1f1f;
        color: var(--text-color);
        margin-bottom: 20px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    input[type="email"]:focus,
    input[type="password"]:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 6px var(--accent);
    }

    input[type="submit"] {
        width: 100%;
        background-color: var(--accent);
        color: black;
        font-weight: 700;
        font-size: 1.1rem;
        padding: 14px;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin-top: 10px;
    }

    input[type="submit"]:hover {
        background-color: var(--accent-hover);
    }

    .error {
        background-color: var(--error-bg);
        color: var(--error-text);
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .links {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
    }

    .links a {
        color: var(--accent);
        text-decoration: none;
        font-weight: 600;
    }

    .links a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Supplier Login</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="supplier_login.php" novalidate>
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" autocomplete="username" autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">

        <input type="submit" value="Login">
    </form>

    <div class="links">
        <a href="supplier_create.php">Register</a>
        <a href="index.php">Back to Home</a>
    </div>
</div>
</body>
</html>
