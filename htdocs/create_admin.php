<?php
session_start();
require 'db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if ($full_name === '') {
        $errors[] = "Full name is required.";
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if ($username === '') {
        $errors[] = "Username is required.";
    }
    if ($password === '') {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    if (!$errors) {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors[] = "Username or Email already exists.";
        } else {
            // Insert new admin
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $insert_stmt = $conn->prepare("INSERT INTO admins (full_name, email, username, password_hash) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param("ssss", $full_name, $email, $username, $password_hash);

            if ($insert_stmt->execute()) {
                $success = true;
            } else {
                $errors[] = "Database error: Could not create admin.";
            }
            $insert_stmt->close();
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create Admin Account</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --blue-dark: #0a1e3f;
            --blue-medium: #1e3a8a;
            --blue-light: #3b82f6;
            --gray-light: #e0e7ff;
            --gray-dark: #374151;
            --danger: #ef4444;
            --success: #22c55e;
            --white: #ffffff;
            --shadow: rgba(0, 0, 0, 0.25);
        }
        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }
        body, html {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--blue-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: var(--gray-light);
        }
        .container {
            background: var(--blue-medium);
            padding: 40px 35px;
            border-radius: 20px;
            width: 400px;
            box-shadow: 0 8px 25px var(--shadow);
            text-align: center;
            color: var(--gray-light);
        }
        h1 {
            font-family: 'Roboto Slab', serif;
            font-size: 2.5rem;
            margin-bottom: 25px;
            color: var(--blue-light);
            text-shadow: 0 0 6px var(--blue-light);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 16px;
            text-align: left;
        }
        label {
            font-weight: 600;
            font-size: 1rem;
            color: var(--gray-light);
            margin-bottom: 4px;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            padding: 10px 12px;
            border-radius: 10px;
            border: none;
            font-size: 1rem;
            color: var(--blue-dark);
            background: var(--gray-light);
            transition: box-shadow 0.3s ease;
            box-shadow: inset 0 0 6px rgba(0,0,0,0.1);
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 10px var(--blue-light);
            background: #f0f4ff;
        }
        button {
            background: var(--blue-light);
            color: var(--blue-dark);
            font-weight: 700;
            padding: 14px;
            border: none;
            border-radius: 30px;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(59,130,246,0.7);
            transition: background 0.3s ease;
            margin-top: 10px;
        }
        button:hover {
            background: #2563eb;
            color: var(--white);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.9);
        }
        .errors {
            background: var(--danger);
            color: var(--white);
            padding: 12px 15px;
            border-radius: 15px;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        .success {
            background: var(--success);
            color: var(--white);
            padding: 12px 15px;
            border-radius: 15px;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 1rem;
        }
        .back-login {
            margin-top: 15px;
            color: var(--blue-light);
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            text-decoration: underline;
        }
        .back-login:hover {
            color: #2563eb;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Create Admin Account</h1>

    <?php if ($errors): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success">
            Admin account created successfully! <br />
            <a href="admin_login.php" style="color: #d0e7ff; text-decoration: underline;">Go to Login</a>
        </div>
    <?php else: ?>
        <form method="post" action="create_admin.php" novalidate>
            <label for="full_name">Full Name</label>
            <input id="full_name" name="full_name" type="text" required autocomplete="name"
                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" />

            <label for="email">Email</label>
            <input id="email" name="email" type="email" required autocomplete="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

            <label for="username">Username</label>
            <input id="username" name="username" type="text" required autocomplete="username"
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />

            <label for="password">Password</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" />

            <label for="confirm_password">Confirm Password</label>
            <input id="confirm_password" name="confirm_password" type="password" required autocomplete="new-password" />

            <button type="submit">Create Account</button>
        </form>
    <?php endif; ?>

    <div class="back-login" onclick="location.href='admin_login.php'">&larr; Back to Login</div>
</div>

</body>
</html>
