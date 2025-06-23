<?php
session_start();
require 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $phoneNumber = trim($_POST['phoneNumber'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Basic validation
    if (!$fullName || !$phoneNumber || !$email || !$password || !$confirmPassword) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT SupplierID FROM suppliers WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email is already registered.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO suppliers (FullName, PhoneNumber, Email, PasswordHash) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullName, $phoneNumber, $email, $passwordHash);

            if ($stmt->execute()) {
                $success = "Supplier registration successful. You can now <a href='supplier_login.php'>login</a>.";
            } else {
                $error = "Error registering supplier. Please try again.";
            }
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
<title>Supplier Registration</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
<style>
    :root {
        --navy: #0f0f1a;
        --gold: #d4af37;
        --white: #ffffff;
        --card: #1a1a2e;
        --text: #e4e4e4;
        --danger: #ff4c4c;
        --success: #4CAF50;
    }
    * {
        box-sizing: border-box;
    }
    body {
        font-family: 'Inter', sans-serif;
        background: var(--navy);
        color: var(--text);
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .container {
        background: var(--card);
        padding: 40px;
        border-radius: 20px;
        max-width: 450px;
        width: 100%;
        box-shadow: 0 8px 30px rgba(255, 215, 0, 0.15);
        text-align: center;
    }
    h1 {
        font-family: 'Playfair Display', serif;
        font-size: 2.5em;
        color: var(--gold);
        margin-bottom: 25px;
        text-shadow: 0 0 10px var(--gold);
    }
    label {
        display: block;
        margin-top: 20px;
        margin-bottom: 6px;
        font-weight: 600;
        text-align: left;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="tel"] {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #555;
        background: #2a2a3b;
        color: var(--text);
        font-size: 14px;
    }
    input[type="submit"] {
        margin-top: 30px;
        width: 100%;
        padding: 14px;
        font-size: 16px;
        background: var(--gold);
        border: none;
        border-radius: 10px;
        font-weight: bold;
        color: var(--navy);
        cursor: pointer;
        transition: background 0.3s ease;
    }
    input[type="submit"]:hover {
        background: #b89128;
    }
    .error {
        background: #ffdddd;
        color: var(--danger);
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }
    .success {
        background: #ddffdd;
        color: var(--success);
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
    }
    a.back-link {
        margin-top: 20px;
        display: inline-block;
        color: var(--gold);
        text-decoration: none;
        font-weight: 600;
    }
    a.back-link:hover {
        text-decoration: underline;
        color: #fff;
    }
</style>
</head>
<body>
<div class="container">
    <h1>Supplier Registration</h1>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="success"><?= $success ?></div>
    <?php endif; ?>

    <form method="post" action="supplier_create.php" novalidate>
        <label for="fullName">Full Name</label>
        <input type="text" id="fullName" name="fullName" required value="<?= htmlspecialchars($_POST['fullName'] ?? '') ?>">

        <label for="phoneNumber">Phone Number</label>
        <input type="tel" id="phoneNumber" name="phoneNumber" required value="<?= htmlspecialchars($_POST['phoneNumber'] ?? '') ?>">

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required>

        <input type="submit" value="Register">
    </form>

    <a href="supplier_login.php" class="back-link">Already have an account? Login here.</a>
</div>
</body>
</html>
