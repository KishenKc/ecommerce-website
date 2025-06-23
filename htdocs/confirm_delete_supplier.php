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

// Fetch supplier info to display before deletion
$stmt = $conn->prepare("SELECT SupplierID, FullName, Email FROM suppliers WHERE SupplierID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Supplier not found
    header("Location: admin_dashboard.php");
    exit;
}

$supplier = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Delete supplier after confirmation
    $stmtDel = $conn->prepare("DELETE FROM suppliers WHERE SupplierID = ?");
    $stmtDel->bind_param("i", $id);
    if ($stmtDel->execute()) {
        $stmtDel->close();
        header("Location: admin_dashboard.php?msg=Supplier+deleted+successfully");
        exit;
    } else {
        $error = "Error deleting supplier: " . $conn->error;
    }
    $stmtDel->close();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Delete Supplier</title>
<style>
  body {
    background-color: #1f2630;
    color: #f0f2f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 40px 60px;
  }
  h1 {
    color: #e94b3c;
    margin-bottom: 20px;
  }
  .details {
    background-color: #2c3444;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
  }
  .details p {
    margin: 8px 0;
  }
  form {
    display: inline-block;
  }
  button {
    background-color: #e94b3c;
    border: none;
    padding: 12px 25px;
    border-radius: 6px;
    font-weight: 700;
    color: white;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 4px 10px rgba(233,75,60,0.5);
    transition: background-color 0.3s ease;
    margin-right: 15px;
  }
  button:hover {
    background-color: #b73027;
  }
  a.cancel-link {
    font-weight: 600;
    color: #4a90e2;
    text-decoration: none;
    font-size: 1rem;
  }
  a.cancel-link:hover {
    text-decoration: underline;
  }
  .error {
    color: #e94b3c;
    font-weight: 600;
    margin-bottom: 20px;
  }
</style>
</head>
<body>

<h1>Confirm Delete Supplier</h1>

<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="details">
    <p><strong>Supplier ID:</strong> <?= $supplier['SupplierID'] ?></p>
    <p><strong>Full Name:</strong> <?= htmlspecialchars($supplier['FullName']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($supplier['Email']) ?></p>
</div>

<p>Are you sure you want to delete this supplier? This action cannot be undone.</p>

<form method="post" action="">
    <button type="submit">Yes, Delete</button>
</form>
<a href="admin_dashboard.php" class="cancel-link">Cancel</a>

</body>
</html>
