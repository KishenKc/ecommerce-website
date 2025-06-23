<?php
session_start();
require 'db.php';

// Handle add to cart form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    // --- START: MODIFIED CODE for customer login check ---
    if (!isset($_SESSION['customer_id'])) {
        // Customer is not logged in, store intended action and redirect
        $_SESSION['redirect_after_login'] = 'index.php'; // Page to return to
        $_SESSION['post_data_after_login'] = $_POST; // Store the form data

        header("Location: customer_login.php");
        exit;
    }
    // --- END: MODIFIED CODE ---

    $productId = (int)$_POST['product_id'];
    $qty = max(1, (int)($_POST['quantity'] ?? 1));

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $qty;
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }

    header("Location: index.php");
    exit;
}

// Fetch all products
$result = $conn->query("SELECT ProductID, Name, Price, ImageURL FROM products ORDER BY Name ASC");
$products = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

$cartCount = 0;
if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cartCount = array_sum($_SESSION['cart']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>E-Commerce Store</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Roboto+Slab:wght@700&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    :root {
        --primary-color: #666666; /* Grey header */
        --secondary-color: #888888; /* Text */
        --background-color: #ffffff;
        --text-color: #333333;
        --button-bg: #ff8c00; /* Orange button */
        --button-hover-bg: #e67600; /* Darker orange hover */
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        font-family: 'Inter', sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }

    header {
        width: 100%;
        padding: 15px 20px;
        background-color: var(--primary-color);
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        flex-wrap: wrap;
    }

    .logo-title {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .logo-title img {
        height: 60px; /* Increased from 40px */
        width: 60px;  /* Increased from 40px */
        margin-right: 12px;
        object-fit: contain; /* Ensures logo scales properly */
    }

    .logo-title h1 {
        font-family: 'Roboto Slab', serif;
        font-size: 2rem;
        margin: 0;
        white-space: nowrap;
    }

    nav {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    nav a {
        color: white;
        text-decoration: none;
        padding: 10px 15px;
        background-color: var(--button-bg);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        font-weight: bold;
        font-size: 1rem;
        border: none;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    nav a:hover {
        background-color: var(--button-hover-bg);
    }

    .cart-link {
        position: relative;
        background-color: var(--button-bg) !important; /* Orange background */
    }

    .cart-link:hover {
        background-color: var(--button-hover-bg) !important; /* Darker orange on hover */
    }

    /* Font Awesome Icon styles */
    .cart-icon {
        font-size: 1.2rem;
        color: white;
    }

    .cart-count {
        background-color: #ff4444; /* Red bubble for count */
        color: white;
        border-radius: 50%;
        padding: 2px 6px;
        font-size: 0.8rem;
        min-width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: absolute; /* Position relative to .cart-link */
        top: -5px; /* Adjust as needed */
        right: -5px; /* Adjust as needed */
    }

    main {
        flex: 1;
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
    }

    .product-card {
        background: #f9f9f9;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .product-card:hover {
        transform: scale(1.02);
    }

    .product-card a {
        text-decoration: none;
        color: inherit;
    }

    .product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        margin-bottom: 12px;
    }

    .product-name {
        font-size: 1.2rem;
        font-weight: bold;
        margin-bottom: 8px;
        color: var(--primary-color);
    }

    .product-price {
        font-size: 1.1rem;
        color: var(--secondary-color);
        margin-bottom: 12px;
    }

    form.add-to-cart {
        display: flex;
        gap: 10px;
    }

    form.add-to-cart input[type="number"] {
        width: 60px;
        padding: 8px;
        font-size: 1rem;
        border: 1px solid #ccc;
        background-color: #f9f9f9;
        border-radius: 0;
    }

    form.add-to-cart button {
        flex: 1;
        padding: 10px;
        background-color: var(--button-bg);
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 0;
        cursor: pointer;
        box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        transition: background-color 0.3s ease;
    }

    form.add-to-cart button:hover {
        background-color: var(--button-hover-bg);
    }

    /* Footer Styles */
    footer {
        background-color: #333; /* Darker background for the new footer */
        color: white;
        padding: 40px 0; /* Remove horizontal padding to prevent overflow */
        text-align: center;
        margin-top: auto; /* Pushes footer to the bottom */
        width: 100%;
        overflow-x: hidden; /* Prevent horizontal scrolling */
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

    .welcome {
        font-size: 1rem;
        color: #ffffff;
        margin-right: 10px;
    }

    .social-icons a {
        color: white;
        font-size: 1.5rem;
        margin: 0 10px;
        transition: color 0.3s ease;
    }

    .social-icons a:hover {
        color: var(--button-bg); /* Orange on hover */
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        header {
            padding: 10px 15px;
        }
        
        .logo-title img {
            height: 50px;
            width: 50px;
        }
        
        .logo-title h1 {
            font-size: 1.5rem;
        }
        
        nav {
            width: 100%;
            justify-content: center;
            margin-top: 10px;
        }
        
        nav a {
            padding: 8px 12px;
            font-size: 0.9rem;
        }
        
        .welcome {
            font-size: 0.9rem;
            margin-right: 5px;
        }
        
        main {
            margin: 20px auto;
            padding: 0 15px;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .product-card {
            padding: 15px;
        }
    }

    @media (max-width: 480px) {
        .logo-title h1 {
            font-size: 1.3rem;
        }
        
        .logo-title img {
            height: 45px;
            width: 45px;
        }
        
        nav a {
            padding: 6px 10px;
            font-size: 0.8rem;
        }
        
        main {
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }
        
        .product-name {
            font-size: 1.1rem;
        }
        
        .product-price {
            font-size: 1rem;
        }
    }
</style>
</head>
<body>

<header>
    <div class="logo-title">
        <img src="logo.jpeg" alt="Logo">
        <h1>Buy & Sell</h1>
    </div>
    <nav>
        <a href="cart.php" class="cart-link">
            <i class="fas fa-shopping-bag cart-icon"></i>
            <?php if ($cartCount > 0): ?>
                <span class="cart-count"><?= $cartCount ?></span>
            <?php endif; ?>
        </a>
        <?php if (isset($_SESSION['customer_id'])): ?>
            <span class="welcome">Welcome, <?= htmlspecialchars($_SESSION['customer_name']) ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="customer_login.php">Customer Login</a>
            <?php /* <a href="customer_register.php">Register</a> */ ?>
        <?php endif; ?>
        <a href="login.php">Admin & Suppliers</a>
    </nav>
</header>

<main>
    <?php if (empty($products)): ?>
        <p>No products available.</p>
    <?php else: ?>
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <a href="product.php?id=<?= $product['ProductID'] ?>">
                    <img src="<?= htmlspecialchars($product['ImageURL']) ?>" alt="<?= htmlspecialchars($product['Name']) ?>" class="product-image">
                    <h2 class="product-name"><?= htmlspecialchars($product['Name']) ?></h2>
                    <div class="product-price">R<?= number_format($product['Price'], 2) ?></div>
                </a>
                <form method="post" action="index.php" class="add-to-cart">
                    <input type="hidden" name="product_id" value="<?= $product['ProductID'] ?>">
                    <input type="number" name="quantity" value="1" min="1">
                    <button type="submit">Buy Now</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

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