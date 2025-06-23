<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require 'db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php");
    exit;
}

$id = intval($_GET['id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);

    if (empty($full_name) || empty($email) || empty($username)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check for unique email and username excluding this admin
        $stmt = $conn->prepare("SELECT id FROM admins WHERE (email = ? OR username = ?) AND id != ?");
        $stmt->bind_param("ssi", $email, $username, $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Email or username already in use.";
        } else {
            // Update admin (excluding password)
            $stmt_update = $conn->prepare("UPDATE admins SET full_name = ?, email = ?, username = ? WHERE id = ?");
            $stmt_update->bind_param("sssi", $full_name, $email, $username, $id);
            if ($stmt_update->execute()) {
                $success = "Admin updated successfully.";
            } else {
                $error = "Database error: could not update admin.";
            }
            $stmt_update->close();
        }
        $stmt->close();
    }
}

// Fetch admin details for initial form fill or after post
$stmt = $conn->prepare("SELECT full_name, email, username FROM admins WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    header("Location: admin_dashboard.php");
    exit;
}
$admin = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Admin</title>
<style>
    body {
        background-color: #1f2630;
        color: #f0f2f5;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        padding: 40px 60px;
    }
    h1 {
        color: #4a90e2;
    }
    form {
        background-color: #2e3a4f;
        padding: 20px;
        border-radius: 8px;
        max-width: 500px;
    }
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }
    input[type="text"], input[type="email"] {
        width: 100%;
        padding: 8px 10px;
        margin-bottom: 20px;
        border-radius: 5px;
        border: none;
        font-size: 1rem;
    }
    button {
        background-color: #4a90e2;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        font-size: 1rem;
        box-shadow: 0 4px 10px rgba(74,144,226,0.4);
        transition: background-color 0.3s ease;
    }
    button:hover {
        background-color: #357abd;
    }
    .error {
        color: #e94b3c;
        margin-bottom: 20px;
    }
    .success {
        color: #4caf50;
        margin-bottom: 20px;
    }
    a.back-link {
        color: #4a90e2;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin-top: 20px;
    }
    a.back-link:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

<h1>Edit Admin</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post">
    <label for="full_name">Full Name</label>
    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? $admin['full_name']) ?>" required />

    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $admin['email']) ?>" required />

    <label for="username">Username</label>
    <input type="text" id="username" name="username" value="<?= htmlspecialchars($_POST['username'] ?? $admin['username']) ?>" required />

    <button type="submit">Save Changes</button>
</form>

<a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>

</body>
</html>
