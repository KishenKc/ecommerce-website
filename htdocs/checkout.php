<?php
session_start();
require 'db.php';

// Redirect if cart empty
if (empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

// Get supplier info if logged in (optional)
$supplierId = $_SESSION['supplier_id'] ?? null;

// Fetch product info
$productIds = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$stmt = $conn->prepare("SELECT ProductID, Name, Price FROM products WHERE ProductID IN ($placeholders)");
$stmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
$stmt->execute();
$result = $stmt->get_result();
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[$row['ProductID']] = $row;
}
$stmt->close();

$errors = [];
$successMsg = '';
$shippingCost = 0;
$shippingMethod = 'delivery'; // default

// Calculate subtotal
$subtotal = 0;
foreach ($_SESSION['cart'] as $pid => $qty) {
    if (isset($products[$pid])) {
        $subtotal += $products[$pid]['Price'] * $qty;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address1 = trim($_POST['address1'] ?? '');
    $address2 = trim($_POST['address2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $postalCode = trim($_POST['postal_code'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $shippingMethod = $_POST['shipping_method'] ?? 'delivery';

    // Validate required fields
    if (!$fullName) $errors[] = 'Full Name is required.';
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid Email is required.';
    if (!$phone) $errors[] = 'Phone number is required.';
    if (!$address1) $errors[] = 'Address Line 1 is required.';
    if (!$city) $errors[] = 'City is required.';
    if (!$postalCode) $errors[] = 'Postal Code is required.';
    if (!$country) $errors[] = 'Country is required.';
    if (!in_array($shippingMethod, ['delivery', 'courier'])) $errors[] = 'Invalid shipping method selected.';

    // Set shipping cost
    if ($shippingMethod === 'courier') {
        $shippingCost = 30.00;
    } else {
        $shippingCost = 10.00;
    }

    $total = $subtotal + $shippingCost;

    if (empty($errors)) {
        // Insert into orders table
        $insertOrder = $conn->prepare("INSERT INTO orders
            (SupplierID, FullName, Email, Phone, AddressLine1, AddressLine2, City, PostalCode, Country, ShippingMethod, ShippingCost, Subtotal, Total)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insertOrder->bind_param("issssssssdidd",
            $supplierId,
            $fullName,
            $email,
            $phone,
            $address1,
            $address2,
            $city,
            $postalCode,
            $country,
            $shippingMethod,
            $shippingCost,
            $subtotal,
            $total
        );
        $insertOrder->execute();
        $orderId = $insertOrder->insert_id;
        $insertOrder->close();

        // Insert order items
        $insertItem = $conn->prepare("INSERT INTO order_items (OrderID, ProductID, Quantity, Price) VALUES (?, ?, ?, ?)");
        foreach ($_SESSION['cart'] as $pid => $qty) {
            if (!isset($products[$pid])) continue;
            $price = $products[$pid]['Price'];
            $insertItem->bind_param("iiid", $orderId, $pid, $qty, $price);
            $insertItem->execute();
        }
        $insertItem->close();

        // Optionally: Send confirmation email (configure your mail settings)
        /*
        $to = $email;
        $subject = "Order Confirmation #$orderId";
        $message = "Thank you for your order!\nOrder ID: $orderId\nTotal: $" . number_format($total, 2);
        $headers = "From: no-reply@yourshop.com";
        mail($to, $subject, $message, $headers);
        */

        // Clear cart
        $_SESSION['cart'] = [];

        // Set success message and redirect
        $_SESSION['checkout_success'] = "Thank you for your order, $fullName! Your Order ID is #$orderId.";
        header("Location: order_success.php?order_id=$orderId");
        exit;
    }
}

function h($str) {
    return htmlspecialchars($str);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Checkout - Professional Shop</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <style>
    :root {
        --primary-color: #2c3e50;
        --secondary-color: #2980b9;
        --background-color: #f4f7fa;
        --card-bg: #ffffff;
        --text-color: #34495e;
        --button-bg: var(--secondary-color);
        --button-hover-bg: #1c5d8b;
        --border-radius: 12px;
        --box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        --danger-color: #e74c3c;
        /* Added for footer consistency */
        --footer-bg: #333;
        --footer-text-color: #e0e0e0;
        --footer-link-hover: #ff8c00; /* Example accent for footer links */
    }
    body {
        font-family: 'Inter', sans-serif;
        background: var(--background-color);
        margin: 0;
        padding: 40px 20px;
        color: var(--text-color);
        display: flex;
        flex-direction: column; /* Added for sticky footer */
        align-items: center;
        min-height: 100vh; /* Added for sticky footer */
    }
    .checkout-container {
        background: var(--card-bg);
        border-radius: var(--border-radius);
        padding: 30px 40px;
        max-width: 700px;
        width: 100%;
        box-shadow: var(--box-shadow);
        margin-bottom: 30px; /* Space before footer */
    }
    h1 {
        font-family: 'Roboto Slab', serif;
        font-size: 2.5rem;
        margin-bottom: 25px;
        color: var(--primary-color);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 25px;
    }
    th, td {
        padding: 14px 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        font-weight: 700;
        color: var(--secondary-color);
    }
    .price {
        font-weight: 600;
        color: var(--primary-color);
    }
    .total-row td {
        font-weight: 700;
        font-size: 1.2rem;
        border-top: 2px solid var(--secondary-color);
    }
    label {
        display: block;
        margin: 15px 0 8px 0;
        font-weight: 600;
    }
    input[type="text"],
    input[type="email"],
    input[type="tel"],
    select {
        width: 100%;
        padding: 12px;
        border-radius: var(--border-radius);
        border: 1.5px solid #ccc;
        font-size: 1rem;
        margin-bottom: 20px;
        box-sizing: border-box;
    }
    select {
        appearance: none;
        background-color: white;
        cursor: pointer;
    }
    button {
        width: 100%;
        padding: 16px;
        background: var(--button-bg);
        border: none;
        border-radius: var(--border-radius);
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
        cursor: pointer;
        box-shadow: 0 3px 10px rgba(41, 128, 185, 0.5);
        transition: background-color 0.3s ease;
    }
    button:hover {
        background: var(--button-hover-bg);
    }
    .note {
        font-size: 0.9rem;
        color: #666;
        margin-top: -15px;
        margin-bottom: 20px;
    }
    a.back-link {
        display: inline-block;
        margin-bottom: 30px;
        font-weight: 600;
        color: var(--secondary-color);
        text-decoration: none;
        font-size: 1rem;
    }
    a.back-link:hover {
        text-decoration: underline;
    }
    .errors {
        background: #fce4e4;
        border: 1px solid var(--danger-color);
        padding: 15px 20px;
        margin-bottom: 25px;
        border-radius: var(--border-radius);
        color: var(--danger-color);
        font-weight: 600;
    }

    /* Footer Styles - Copied from your provided footer */
    footer {
        background-color: #333; /* Darker background for the new footer */
        color: white;
        padding: 40px 20px;
        text-align: center;
        margin-top: auto; /* Pushes footer to the bottom */
        width: 100%; /* Ensure footer spans full width */
    }

    footer h5 {
        font-size: 1.2rem;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    footer p {
        font-size: 1rem;
        margin: 5px 0;
        color: #bbb; /* Lighter text for paragraphs */
    }

    footer .list-unstyled li a {
        color: #bbb;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    footer .list-unstyled li a:hover {
        color: white;
    }

    .footer-label {
        font-weight: bold;
        color: #fff;
    }

    .social-icons a {
        color: white;
        font-size: 1.5rem;
        margin: 0 10px;
        transition: color 0.3s ease;
    }

    .social-icons a:hover {
        color: var(--footer-link-hover); /* Using a new variable for consistency, or adjust as needed */
    }
</style>
</head>
<body>

<div class="checkout-container">
    <a href="cart.php" class="back-link">&larr; Back to Cart</a>
    <h1>Checkout</h1>

    <?php if ($errors): ?>
        <div class="errors" role="alert">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= h($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <table aria-label="Cart items summary">
        <thead>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Price Each</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['cart'] as $pid => $qty):
                if (!isset($products[$pid])) continue;
                $product = $products[$pid];
                $totalPrice = $product['Price'] * $qty;
            ?>
            <tr>
                <td><?= h($product['Name']) ?></td>
                <td><?= $qty ?></td>
                <td class="price">R<?= number_format($product['Price'], 2) ?></td>
                <td class="price">R<?= number_format($totalPrice, 2) ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="total-row">
                <td colspan="3">Subtotal</td>
                <td class="price">R<?= number_format($subtotal, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="3">Shipping Cost</td>
                <td class="price" id="shipping-cost">R<?= number_format($shippingCost, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="3">Total</td>
                <td class="price" id="total-cost">R<?= number_format($subtotal + $shippingCost, 2) ?></td>
            </tr>
        </tbody>
    </table>

    <form method="post" action="checkout.php" novalidate id="checkout-form">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" required value="<?= h($_POST['full_name'] ?? '') ?>">

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" required value="<?= h($_POST['email'] ?? '') ?>">

        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" required value="<?= h($_POST['phone'] ?? '') ?>">

        <label for="address1">Address Line 1</label>
        <input type="text" id="address1" name="address1" required value="<?= h($_POST['address1'] ?? '') ?>">

        <label for="address2">Address Line 2 (optional)</label>
        <input type="text" id="address2" name="address2" value="<?= h($_POST['address2'] ?? '') ?>">

        <label for="city">City</label>
        <input type="text" id="city" name="city" required value="<?= h($_POST['city'] ?? '') ?>">

        <label for="postal_code">Postal Code</label>
        <input type="text" id="postal_code" name="postal_code" required value="<?= h($_POST['postal_code'] ?? '') ?>">

        <label for="country">Country</label>
        <input type="text" id="country" name="country" required value="<?= h($_POST['country'] ?? '') ?>">

        <label for="shipping_method">Shipping Method</label>
        <select name="shipping_method" id="shipping_method" required>
            <option value="delivery" <?= $shippingMethod === 'delivery' ? 'selected' : '' ?>>Collection - R0.00</option>
            <option value="courier" <?= $shippingMethod === 'courier' ? 'selected' : '' ?>>Delivery - R30.00</option>
        </select>

        <button type="submit">Confirm and Place Order</button>
    </form>
</div>

<script>
    // Update shipping and total cost dynamically
    const shippingSelect = document.getElementById('shipping_method');
    const shippingCostElem = document.getElementById('shipping-cost');
    const totalCostElem = document.getElementById('total-cost');
    const subtotal = <?= json_encode($subtotal) ?>;

    shippingSelect.addEventListener('change', function() {
        let cost = 0.00; // Updated to match "Collection - R0.00"
        if (this.value === 'courier') {
            cost = 30.00;
        } else { // 'delivery' option
            cost = 0.00; // Ensure collection is R0.00
        }
        shippingCostElem.textContent = 'R' + cost.toFixed(2);
        totalCostElem.textContent = 'R' + (subtotal + cost).toFixed(2);
    });
</script>

<footer class="mt-5 py-5">
  <div class="container">
    <div class="row">

      <div class="col-lg-4 col-md-6 col-sm-12 mb-4 text-center text-md-start">
        <img class="Logo mb-3" src="logo.jpeg" alt="Company Logo" style="width: 120px;">
        <p>We provide the best products for the most affordable prices</p>
      </div>

      <div class="col-lg-4 col-md-6 col-sm-12 mb-4 text-center">
        <h5 class="pb-2">Contact Us</h5>
        <p><span class="footer-label">Address</span><br>12 Long Street, Johannesburg</p>
        <p><span class="footer-label">Phone</span><br>0790805676</p>
        <p><span class="footer-label">Email</span><br>KC'sShopinfo@gmail.com</p>
      </div>

      <div class="col-lg-4 col-md-12 col-sm-12 mb-4 text-center text-lg-end">
        <h5 class="pb-2">Featured</h5>
        <ul class="list-unstyled mb-3">
          <li><a href="#">SHOES</a></li>
          <li><a href="#">TOOLS</a></li>
          <li><a href="#">BODY PRODUCTS</a></li>
          <li><a href="#">CLOTHES</a></li>
          <li><a href="#">NEW ARRIVALS</a></li>
        </ul>
        <div class="social-icons">
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-facebook-f"></i></a> </div>
      </div>
    </div>

    <div class="copyright mt-3">
        <div class="row container mx-auto">
            <div class="col-lg-12 text-center"> <p>eCommerce @ 2025 All Rights Reserved</p>
            </div>
        </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>