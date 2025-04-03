<?php
session_start();
include 'db_connect.php';

// Initialize cart session if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Get product details
    $query = "SELECT * FROM products WHERE id = ? AND entity > 0";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    if ($product) {
        // Add to cart
        $_SESSION['cart'][] = $product;

        // Decrease product entity in database
        $update_query = "UPDATE products SET entity = entity - 1 WHERE id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();

        echo "<script>alert('Product added to cart!'); window.location.href='view_product.php';</script>";
        exit();
    } else {
        echo "<script>alert('Product is out of stock!');</script>";
    }
}

// Fetch products from database
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 0; }
        .container { width: 80%; margin: auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .add-btn { background-color: black; color: white; padding: 5px 10px; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: left; margin-bottom: 10px;">
            <div class="back-arrow" onclick="history.back();">&#8592;</div>
        </div>
        <h2>Product List</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th>Category</th>
                <th>Price</th>
                <th>Entity</th>
                <th>Action</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['sku']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td>&#8369;<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['entity']; ?></td>
                        <td>
                            <?php if ($row['entity'] > 0): ?>
                                <form method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" class="add-btn">Add to Cart</button>
                                </form>
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
