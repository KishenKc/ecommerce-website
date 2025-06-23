<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $img = $_POST['image_url'];

    $stmt = $conn->prepare("INSERT INTO products (name, description, price, image_url) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $desc, $price, $img);
    $stmt->execute();

    header("Location: index.php");
    exit;
}
?>
<h2>Add New Product</h2>
<form method="POST">
    Name: <input type="text" name="name"><br>
    Description:<br><textarea name="description"></textarea><br>
    Price: <input type="number" step="0.01" name="price"><br>
    Image URL: <input type="text" name="image_url"><br>
    <input type="submit" value="Create">
</form>
<a href="index.php">⬅ Back</a>
