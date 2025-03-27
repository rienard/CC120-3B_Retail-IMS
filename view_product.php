<?php
session_start();
include 'db_connect.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch products from the database
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="style1.css"> <!-- External CSS -->
</head>
<body>
    <div class="container">
        <h2>Product List</h2>
        <a href="product_entry.php" class="btn">Add New Product</a>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><img src='" . ($row['image'] ? $row['image'] : 'uploads/default.png') . "' alt='Product Image'></td>";
                        echo "<td>{$row['name']}</td>";
                        echo "<td>{$row['category']}</td>";
                        echo "<td>{$row['brand']}</td>";
                        echo "<td>₱{$row['selling_price']}</td>";
                        echo "<td>{$row['stock_quantity']}</td>";
                        echo "<td>
                                <a href='edit_product.php?id={$row['id']}' class='btn edit'>Edit</a>
                                <a href='delete_product.php?id={$row['id']}' class='btn delete' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No products found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
