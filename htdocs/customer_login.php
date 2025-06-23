<?php
session_start();
require 'db.php'; // Assuming db.php handles database connection

$email = $password = '';
$message = ''; // Changed from $error to $message for consistency

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation (add more robust validation as needed)
    if (empty($email) || empty($password)) {
        $message = "Please enter both email and password.";
    } else {
        // Prepare and execute the query to find the customer by email
        // Using prepared statements to prevent SQL injection
        $stmt = $conn->prepare("SELECT CustomerID, FullName, PasswordHash FROM customers WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $customer = $result->fetch_assoc();
            // Verify password using password_verify for hashed passwords
            if (password_verify($password, $customer['PasswordHash'])) {
                // Login successful
                $_SESSION['customer_id'] = $customer['CustomerID'];
                $_SESSION['customer_name'] = $customer['FullName']; // Store customer full name

                // --- START: MODIFIED REDIRECTION LOGIC ---
                // Check if there's a specific page to redirect to after login
                if (isset($_SESSION['redirect_after_login'])) {
                    $redirect_page = $_SESSION['redirect_after_login'];
                    unset($_SESSION['redirect_after_login']); // Clear the session variable

                    // If there was POST data for adding to cart, try to re-submit it
                    if (isset($_SESSION['post_data_after_login'])) {
                        // Reconstruct a query string from the POST data for redirection
                        $post_params = http_build_query($_SESSION['post_data_after_login']);
                        unset($_SESSION['post_data_after_login']);
                        header("Location: " . $redirect_page . "?" . $post_params);
                        exit;
                    }
                    header("Location: " . $redirect_page); // Redirect to the stored page
                    exit;
                }
                // --- END: MODIFIED REDIRECTION LOGIC ---

                // Default redirect if no specific page was set (e.g., direct login)
                header("Location: index.php"); // Redirect to homepage
                exit;

            } else {
                $message = "Invalid email or password.";
            }
        } else {
            $message = "Invalid email or password.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
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
        .login-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-container h2 {
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
            background-color: #ffe5e5; /* Light red background for messages */
            border: 1px solid red;
            padding: 10px;
            border-radius: 4px;
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
    <div class="login-container">
        <h2>Customer Login</h2>
        <?php if ($message): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>
        <form action="customer_login.php" method="post">
            <div class="mb-3">
                <input type="email" class="form-control" name="email" placeholder="Email Address" value="<?= htmlspecialchars($email) ?>" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <div class="links">
            <p>Don't have an account? <a href="customer_register.php">Register here</a></p>
            <p><a href="index.php">Back to Home</a></p>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>