<?php
session_start();
require 'db.php';

// Handle quantity updates or item removals from POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        foreach ($_POST['quantities'] as $productId => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0) {
                unset($_SESSION['cart'][$productId]);
            } else {
                $_SESSION['cart'][$productId] = $qty;
            }
        }
    } elseif (isset($_POST['clear'])) {
        unset($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$products = [];
$totalPrice = 0.0;

if (!empty($cart)) {
    $placeholders = implode(',', array_fill(0, count($cart), '?'));
    $types = str_repeat('i', count($cart));
    $stmt = $conn->prepare("SELECT ProductID, Name, Price, ImageURL FROM products WHERE ProductID IN ($placeholders)");
    $stmt->bind_param($types, ...array_keys($cart));
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $products[$row['ProductID']] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Your Cart</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
:root {
    --bg: #ffffff; /* White background */
    --card-bg: #ffffff;
    --text-primary: #222222;
    --text-secondary: #777777;
    --accent: #ff8c00; /* Orange */
    --accent-dark: #e67600;
    --danger: #d9534f;
    --danger-dark: #c9302c;
    --border: #dddddd;
    --radius: 8px;
    --shadow: 0 2px 8px rgba(0,0,0,0.08);
}
* {
    box-sizing: border-box;
}
body {
    margin: 0;
    background: var(--bg);
    color: var(--text-primary);
    font-family: 'Inter', sans-serif;
    padding: 30px 20px;
    display: flex;
    flex-direction: column; /* Added for sticky footer */
    align-items: center;
    min-height: 100vh; /* Added for sticky footer */
}
h1 {
    font-family: 'Playfair Display', serif;
    color: var(--accent);
    margin-bottom: 30px;
    text-align: center;
    font-size: 2.5rem;
}
table {
    width: 100%;
    max-width: 1000px;
    background: var(--card-bg);
    border-radius: var(--radius);
    border-collapse: separate;
    border-spacing: 0;
    margin-bottom: 20px;
    box-shadow: var(--shadow);
    overflow: hidden;
}
thead {
    background: #f8f8f8;
}
th, td {
    padding: 16px 20px;
    text-align: left;
    border-bottom: 1px solid var(--border);
}
tr:last-child td {
    border-bottom: none;
}
th {
    font-weight: 600;
    color: var(--accent);
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 0.05em;
}
img.product-image {
    width: 75px;
    height: 75px;
    border-radius: var(--radius);
    object-fit: cover;
    box-shadow: 0 0 6px rgba(0,0,0,0.1);
}
input[type="number"] {
    width: 60px;
    padding: 8px;
    border-radius: 6px;
    border: 1px solid var(--border);
    background: #f5f5f5;
    color: var(--text-primary);
    font-size: 1em;
    transition: border-color 0.3s ease;
}
input[type="number"]:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 6px var(--accent);
}
.total-price {
    max-width: 1000px;
    width: 100%;
    font-size: 1.5rem;
    font-weight: 600;
    color: var(--accent);
    margin: 20px 0;
    text-align: right;
}
.actions {
    max-width: 1000px;
    width: 100%; /* Ensure actions take full width of container */
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    justify-content: space-between;
    margin-bottom: 40px; /* Space before footer */
}
.btn {
    background: var(--accent);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: var(--radius);
    font-weight: 600;
    cursor: pointer;
    font-size: 1em;
    text-decoration: none;
    box-shadow: var(--shadow);
    transition: background-color 0.3s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.btn:hover {
    background: var(--accent-dark);
}
.btn-danger {
    background: var(--danger);
    color: white;
}
.btn-danger:hover {
    background: var(--danger-dark);
}
.empty-cart {
    color: var(--text-secondary);
    font-size: 1.2rem;
    margin-bottom: 20px;
    text-align: center;
}
.back-link {
    color: var(--accent);
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    margin-top: 15px;
}
.back-link:hover {
    text-decoration: underline;
    color: var(--accent-dark);
}

/* Footer specific styles (copied from index.php) */
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
    color: var(--accent); /* Orange on hover, using accent variable */
}


@media (max-width: 600px) {
    table, .actions {
        font-size: 0.95em;
    }
    .actions {
        flex-direction: column;
        align-items: stretch;
    }
    .total-price {
        text-align: center;
    }
}
</style>
</head>
<body>

<h1>Your Shopping Cart</h1>

<?php if (empty($cart)): ?>
    <p class="empty-cart">Your cart is empty.</p>
    <a href="index.php" class="btn">Continue Shopping</a>
<?php else: ?>
    <form method="post" action="cart.php">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $productId => $qty):
                    if (!isset($products[$productId])) continue;
                    $product = $products[$productId];
                    $subtotal = $product['Price'] * $qty;
                    $totalPrice += $subtotal;
                ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($product['ImageURL']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" class="product-image" /></td>
                    <td><?= htmlspecialchars($product['Name']) ?></td>
                    <td>R<?= number_format($product['Price'], 2) ?></td>
                    <td><input type="number" name="quantities[<?= $productId ?>]" value="<?= $qty ?>" min="0" /></td>
                    <td>R<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-price">Total: R<?= number_format($totalPrice, 2) ?></div>

        <div class="actions">
            <button type="submit" name="update" class="btn">Update Cart</button>
            <button type="submit" name="clear" class="btn btn-danger" onclick="return confirm('Clear the entire cart?')">Clear Cart</button>
            <a href="index.php" class="btn">Continue Shopping</a>
            <a href="checkout.php" class="btn">Proceed to Checkout</a>
        </div>
    </form>
<?php endif; ?>

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