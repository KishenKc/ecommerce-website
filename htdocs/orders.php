<?php
require 'db.php';
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<h2>🧾 All Orders</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Image</th>
        <th>Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td>$<?= number_format($row['price'], 2) ?></td>
            <td><img src="<?= htmlspecialchars($row['image_url']) ?>" width="50"></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>
<a href="index.php">⬅ Back</a>
<?php
require 'db.php';
$result = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
?>
<h2>🧾 All Orders</h2>
<table border="1" cellpadding="10">
    <tr>
        <th>Product</th>
        <th>Price</th>
        <th>Image</th>
        <th>Date</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['product_name']) ?></td>
            <td>$<?= number_format($row['price'], 2) ?></td>
            <td><img src="<?= htmlspecialchars($row['image_url']) ?>" width="50"></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
    <?php endwhile; ?>
</table>
<a href="index.php">⬅ Back</a>
