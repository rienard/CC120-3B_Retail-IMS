<?php
session_start();
include 'db_connect.php';

// Get user role
$role = $_SESSION['role'] ?? '';

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart request (only for non-admin users)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id']) && $role !== 'Admin') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    $query = "SELECT * FROM products WHERE id = ? AND quantity >= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $product_id, $quantity);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $added_at = date('Y-m-d H:i:s');

        $_SESSION['cart'][] = [
            'product_id' => $product['id'],
            'name' => $product['name'],
            'quantity' => $quantity,
            'price' => $product['price'],
            'expiry_date' => $product['expiry_date'],
            'added_at' => $added_at
        ];

        // Reduce stock quantity
        $update_query = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();

        // Log sale
        $sale_date = date('Y-m-d H:i:s');
        $sale_query = "INSERT INTO sales (product_id, quantity_sold, sale_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sale_query);
        $stmt->bind_param("iis", $product_id, $quantity, $sale_date);
        $stmt->execute();

        echo "<script>alert('Product added to cart!'); window.location.href='view_product.php';</script>";
        exit();
    } else {
        echo "<script>alert('Not enough stock for this product!');</script>";
    }
}

// Fetch all products
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Products</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 0; }
        .container { width: 80%; margin: auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .add-btn { background-color: black; color: white; padding: 5px 10px; border: none; cursor: pointer; }
        .back-arrow { cursor: pointer; font-size: 18px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: left; margin-bottom: 10px;">
            <div class="back-arrow" onclick="history.back();">&#8592; Back</div>
        </div>
        <h2>Product List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['sku']) ?></td>
                        <td><?= htmlspecialchars($row['category']) ?></td>
                        <td>&#8369;<?= number_format($row['price'], 2) ?></td>
                        <td><?= $row['quantity'] ?></td>
                        <td>
                            <?php if ($row['quantity'] > 0): ?>
                                <?php if ($role !== 'Admin'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                        <input type="number" name="quantity" min="1" max="<?= $row['quantity'] ?>" value="1">
                                        <button type="submit" class="add-btn">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: gray;">Viewing Only</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color: red;">Out of Stock</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">No products found</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
