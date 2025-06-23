<?php
session_start();

// IMPORTANT: Ensure you have robust admin login check
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php"); // Redirect to admin login page
    exit;
}

require 'db.php'; // Include your database connection file

// Check GET id parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php"); // Redirect to correct admin dashboard
    exit;
}

$id = intval($_GET['id']);
$message = ''; // Use $message for consistency
$isError = false; // Flag for error message styling
$deleted = false;

// Fetch admin info
// Note: Your table name for admins might be different (e.g., 'admins' vs 'admin_users')
// And column names (id, full_name, email, username) should match your database.
$stmt = $conn->prepare("SELECT id, full_name, email, username FROM admins WHERE id = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error); // In production, log error, don't die
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_dashboard.php"); // Redirect if admin not found
    exit;
}

$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the currently logged-in admin is trying to delete their own account
    if (!isset($_SESSION['admin_id'])) { // Assuming $_SESSION['admin_id'] is set on admin login
        $message = "Your session is invalid. Please log in again.";
        $isError = true;
    } elseif (intval($_SESSION['admin_id']) === $id) {
        $message = "You cannot delete your own admin account.";
        $isError = true;
    } else {
        $delStmt = $conn->prepare("DELETE FROM admins WHERE id = ?");
        if (!$delStmt) {
            $message = "Database error: Failed to prepare deletion query. " . $conn->error;
            $isError = true;
        } else {
            $delStmt->bind_param("i", $id);
            if ($delStmt->execute()) {
                $deleted = true;
                // Important: Only destroy the current admin's session IF they just deleted someone else's account.
                // The current admin is NOT deleting their own account here, based on the check above.
                // So, no session_destroy() for the *current* admin here unless you have a specific reason.
                // The current admin remains logged in and views the updated list.
            } else {
                $message = "Failed to delete admin: " . $conn->error;
                $isError = true;
            }
            $delStmt->close();
        }
    }
}

$stmt->close();

if ($deleted) {
    // Redirect to the admin dashboard after successful deletion
    // A success message could be passed via session if needed on the dashboard
    header("Location: admin_dashboard.php?status=deleted");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Confirm Delete Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet" />
<style>
    /* Styling based on the provided screenshot for Confirm Delete Admin */
    :root {
        --primary-color: #666666; /* Grey for headings */
        --secondary-color: #888888; /* General text (lighter grey) */
        --background-color: #f4f4f4; /* Light grey background */
        --panel-bg-color: #ffffff; /* White background for the main panel */
        --text-color: #333333; /* Main text color */
        --button-orange-bg: #ff8c00; /* Orange button */
        --button-orange-hover-bg: #e67600; /* Darker orange hover */
        --button-grey-bg: #6c757d; /* Bootstrap grey for cancel button */
        --button-grey-hover-bg: #5a6268; /* Darker grey hover */
        --red-error: #dc3545; /* Bootstrap red for errors */
        --red-error-bg: #f8d7da; /* Light red for error background */
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
    .confirm-container {
        background-color: var(--panel-bg-color);
        padding: 30px;
        border-radius: 8px; /* Slightly rounded corners */
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1); /* Subtle shadow */
        width: 100%;
        max-width: 400px;
        text-align: center;
    }
    .confirm-container h1 {
        font-family: 'Roboto Slab', serif;
        margin-bottom: 25px;
        color: var(--text-color); /* Dark grey for headings */
        font-size: 2rem;
    }
    .details p {
        margin-bottom: 10px;
        font-size: 1.1em;
        color: var(--secondary-color); /* Lighter grey for details */
    }
    .message {
        margin-top: 15px;
        margin-bottom: 15px;
        color: var(--red-error);
        font-weight: bold;
        background-color: var(--red-error-bg);
        border: 1px solid var(--red-error);
        padding: 10px;
        border-radius: 4px;
    }
    p.confirm-text {
        margin-top: 25px;
        margin-bottom: 30px;
        font-size: 1.05rem;
        color: var(--secondary-color); /* Lighter grey for confirmation text */
    }
    .button-group {
        display: flex;
        justify-content: center;
        gap: 15px;
    }
    .btn-delete, .btn-cancel {
        padding: 12px 25px;
        font-weight: bold;
        border-radius: 5px; /* More rounded than 0, to match screenshot */
        transition: background-color 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-grow: 1;
        max-width: 150px;
    }
    .btn-delete {
        background-color: var(--button-orange-bg);
        border-color: var(--button-orange-bg);
        color: white;
    }
    .btn-delete:hover {
        background-color: var(--button-orange-hover-bg);
        border-color: var(--button-orange-hover-bg);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
    .btn-cancel {
        background-color: var(--button-grey-bg);
        border-color: var(--button-grey-bg);
        color: white;
    }
    .btn-cancel:hover {
        background-color: var(--button-grey-hover-bg);
        border-color: var(--button-grey-hover-bg);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    }
</style>
</head>
<body>
    <div class="confirm-container">
        <h1>Confirm Delete Admin</h1>

        <?php if ($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="details">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($admin['full_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($admin['email']) ?></p>
            <p><strong>Username:</strong> <?= htmlspecialchars($admin['username']) ?></p>
        </div>

        <p class="confirm-text">Are you sure you want to delete this admin? This action cannot be undone.</p>

        <form method="post" action="">
            <div class="button-group">
                <button type="submit" class="btn-delete">Yes, Delete</button>
                <a href="admin_dashboard.php" class="btn-cancel">Cancel</a>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>