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

// Fetch existing supplier data
$stmt = $conn->prepare("SELECT SupplierID, FullName, Email, PhoneNumber, CompanyName FROM suppliers WHERE SupplierID = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: admin_dashboard.php");
    exit;
}

$supplier = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data and sanitize
    $fullName = trim($_POST['FullName'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $phoneNumber = trim($_POST['PhoneNumber'] ?? '');
    $companyName = trim($_POST['CompanyName'] ?? '');

    // Basic validation
    if ($fullName === '' || $email === '') {
        $error = "Full Name and Email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email is unique for other suppliers
        $stmtCheck = $conn->prepare("SELECT SupplierID FROM suppliers WHERE Email = ? AND SupplierID != ?");
        $stmtCheck->bind_param("si", $email, $id);
        $stmtCheck->execute();
        $checkResult = $stmtCheck->get_result();
        if ($checkResult->num_rows > 0) {
            $error = "Email already used by another supplier.";
        } else {
            // Update supplier
            $stmtUpdate = $conn->prepare("UPDATE suppliers SET FullName = ?, Email = ?, PhoneNumber = ?, CompanyName = ? WHERE SupplierID = ?");
            if (!$stmtUpdate) {
                die("Prepare update failed: " . $conn->error);
            }
            $stmtUpdate->bind_param("ssssi", $fullName, $email, $phoneNumber, $companyName, $id);
            if ($stmtUpdate->execute()) {
                $success = "Supplier updated successfully.";
                // Refresh supplier data
                $supplier['FullName'] = $fullName;
                $supplier['Email'] = $email;
                $supplier['PhoneNumber'] = $phoneNumber;
                $supplier['CompanyName'] = $companyName;
            } else {
                $error = "Error updating supplier: " . $stmtUpdate->error;
            }
            $stmtUpdate->close();
        }
        $stmtCheck->close();
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Supplier</title>
<style>
  body {
    background-color: #1f2630;
    color: #f0f2f5;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    padding: 40px 60px;
  }
  h1 {
    color: #4a90e2;
    margin-bottom: 30px;
  }
  form {
    max-width: 500px;
    background-color: #2c3444;
    padding: 30px;
    border-radius: 8px;
  }
  label {
    display: block;
    margin-top: 15px;
    font-weight: 600;
  }
  input[type="text"],
  input[type="email"] {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 6px;
    border: none;
    font-size: 1rem;
  }
  button {
    margin-top: 25px;
    background-color: #4a90e2;
    border: none;
    padding: 12px 25px;
    border-radius: 6px;
    font-weight: 700;
    color: white;
    cursor: pointer;
    font-size: 1rem;
    box-shadow: 0 4px 10px rgba(74,144,226,0.4);
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #357abd;
  }
  .message {
    margin-top: 15px;
    font-weight: 600;
  }
  .error {
    color: #e94b3c;
  }
  .success {
    color: #4caf50;
  }
  a.back-link {
    display: inline-block;
    margin-top: 30px;
    color: #4a90e2;
    text-decoration: none;
  }
  a.back-link:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>

<h1>Edit Supplier</h1>

<?php if ($error): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
<?php elseif ($success): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<form method="post" action="">
    <label for="FullName">Full Name *</label>
    <input type="text" id="FullName" name="FullName" required value="<?= htmlspecialchars($supplier['FullName']) ?>" />

    <label for="Email">Email *</label>
    <input type="email" id="Email" name="Email" required value="<?= htmlspecialchars($supplier['Email']) ?>" />

    <label for="PhoneNumber">Phone Number</label>
    <input type="text" id="PhoneNumber" name="PhoneNumber" value="<?= htmlspecialchars($supplier['PhoneNumber']) ?>" />

    <label for="CompanyName">Company Name</label>
    <input type="text" id="CompanyName" name="CompanyName" value="<?= htmlspecialchars($supplier['CompanyName']) ?>" />

    <button type="submit">Update Supplier</button>
</form>

<a href="admin_dashboard.php" class="back-link">&larr; Back to Dashboard</a>

</body>
</html>
