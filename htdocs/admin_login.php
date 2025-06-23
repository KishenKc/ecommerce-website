<?php
session_start();
require 'db.php'; // Your DB connection file

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '') {
        $errors[] = "Username is required.";
    }
    if ($password === '') {
        $errors[] = "Password is required.";
    }

    if (!$errors) {
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($admin_id, $db_username, $db_password_hash);
            $stmt->fetch();

            if (password_verify($password, $db_password_hash)) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin_id;
                $_SESSION['admin_username'] = $db_username;

                header("Location: admin_dashboard.php");
                exit;
            } else {
                $errors[] = "Invalid username or password.";
            }
        } else {
            $errors[] = "Invalid username or password.";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --background: #ffffff;
            --container-bg: #f7f7f7;
            --text-dark: #222;
            --text-muted: #666;
            --orange: #ff8c00;
            --orange-hover: #e67600;
            --danger: #d00000;
            --radius: 12px;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0; padding: 0; box-sizing: border-box;
        }

        body, html {
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: var(--container-bg);
            padding: 40px 30px;
            border-radius: var(--radius);
            width: 100%;
            max-width: 400px;
            box-shadow: var(--shadow);
            text-align: center;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            color: var(--orange);
            margin-bottom: 30px;
            font-size: 2.5rem;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-weight: 600;
            font-size: 1rem;
            text-align: left;
            color: var(--text-dark);
        }

        input[type="text"],
        input[type="password"] {
            padding: 12px;
            border-radius: var(--radius);
            border: 1px solid #ccc;
            font-size: 1rem;
            background: #fff;
            color: var(--text-dark);
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 8px rgba(255, 140, 0, 0.3);
        }

        button {
            background: var(--orange);
            color: #fff;
            font-weight: 700;
            padding: 14px;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background: var(--orange-hover);
        }

        .errors {
            background: #ffeaea;
            color: var(--danger);
            padding: 12px 16px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            text-align: left;
        }

        .errors ul {
            padding-left: 20px;
        }

        .back-button {
            display: inline-block;
            margin-top: 25px;
            text-decoration: none;
            color: var(--orange);
            font-weight: 600;
            border: 2px solid var(--orange);
            padding: 10px 28px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: var(--orange);
            color: #fff;
        }

        footer {
            margin-top: 40px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.9em;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h1>Admin Login</h1>

    <?php if ($errors): ?>
        <div class="errors">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="admin_login.php" novalidate>
        <label for="username">Username</label>
        <input id="username" name="username" type="text" required autocomplete="username" autofocus
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required autocomplete="current-password" />

        <button type="submit">Log In</button>
    </form>

    <a href="index.php" class="back-button">← Back to Home</a>
</div>

<footer>© <?= date('Y') ?> Admin Panel</footer>

</body>
</html>
