<?php
session_start(); // Start the session at the very beginning
require 'db.php';

$name = $email = $password = $confirm = '';
$message = ''; // Using $message for consistency with other files
$isSuccess = false; // Flag to determine if it's a success message

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($password !== $confirm) {
        $message = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email address.";
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT CustomerID FROM customers WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result(); // Store result to check num_rows
        if ($stmt->num_rows > 0) {
            $message = "Email already registered.";
        } else {
            // Hash the password and insert into database
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $conn->prepare("INSERT INTO customers (FullName, Email, PasswordHash) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $name, $email, $hash);

            if ($insert->execute()) {
                // Registration successful, now automatically log them in
                $newCustomerId = $insert->insert_id; // Get the ID of the newly inserted customer
                $_SESSION['customer_id'] = $newCustomerId;
                $_SESSION['customer_name'] = $name; // Use the registered name

                $isSuccess = true; // Set success flag
                // Redirect to homepage after successful registration and login
                header("Location: index.php");
                exit;
            } else {
                $message = "Something went wrong during registration.";
            }
        }
        $stmt->close(); // Close the select statement
        if (isset($insert)) { // Close the insert statement if it was prepared
            $insert->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --primary-color: #666666; /* Grey header - though not directly used on this page, consistent for reference */
            --secondary-color: #888888; /* Text */
            --background-color: #f4f4f4; /* Light grey background */
            --text-color: #333333;
            --button-bg: #ff8c00; /* Orange button */
            --button-hover-bg: #e67600; /* Darker orange hover */
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--background-color);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-color); /* Apply general text color */
        }
        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .register-container h2 {
            font-family: 'Roboto Slab', serif; /* Consistent with header title */
            margin-bottom: 25px; /* More space below heading */
            color: var(--primary-color); /* Darker grey for headings */
            font-size: 2rem; /* Make it stand out a bit */
        }
        .form-control {
            margin-bottom: 15px;
            border-radius: 0; /* Match other components' sharp corners */
            border: 1px solid #ccc;
            padding: 10px 12px; /* Add some padding */
        }
        .btn-primary {
            background-color: var(--button-bg); /* Orange */
            border-color: var(--button-bg);
            width: 100%;
            padding: 12px; /* Slightly larger button */
            font-weight: bold;
            border-radius: 0;
            transition: background-color 0.3s ease, border-color 0.3s ease; /* Smooth transition */
        }
        .btn-primary:hover {
            background-color: var(--button-hover-bg); /* Darker orange */
            border-color: var(--button-hover-bg);
        }
        .message {
            margin-top: 15px;
            margin-bottom: 15px; /* Add margin below message */
            color: red;
            font-weight: bold;
            background-color: #ffe5e5; /* Light red background for errors */
            border: 1px solid red;
            padding: 10px;
            border-radius: 4px;
        }
        .message.success { /* Specific style for success messages */
            color: green;
            background-color: #e5ffe5; /* Light green background */
            border-color: green;
        }
        .links {
            margin-top: 20px; /* Space above links */
        }
        .links p {
            margin-bottom: 8px; /* Space between paragraphs in links */
        }
        .links a {
            color: var(--button-bg); /* Orange for links */
            text-decoration: none;
            transition: color 0.3s ease; /* Smooth transition */
        }
        .links a:hover {
            color: var(--button-hover-bg); /* Darker orange on hover */
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Create Account</h2>

        <?php if ($message): ?>
            <p class="message <?= $isSuccess ? 'success' : '' ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" action="customer_register.php">
            <div class="mb-3">
                <input type="text" class="form-control" name="name" placeholder="Full Name" value="<?= htmlspecialchars($name) ?>" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="confirm" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <div class="links">
            <p>Already have an account? <a href="customer_login.php">Login here</a></p>
            <p><a href="index.php">Back to Home</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>