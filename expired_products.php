<?php 
session_start();
include 'db_connect.php';

// Redirect if user is not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the user role
$role = $_SESSION['role'];

// Handle return form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['productId'])) {
    $productId = $_POST['productId'];

    // Update product status to 'returned'
    $stmt = $conn->prepare("UPDATE products SET status = 'returned' WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();

    // Insert into return_table
    $returnDate = date('Y-m-d');
    $insertStmt = $conn->prepare("INSERT INTO return_table (product_id, return_date) VALUES (?, ?)");
    $insertStmt->bind_param("is", $productId, $returnDate);
    $insertStmt->execute();

    echo "<script>alert('Product returned successfully!'); window.location='expired_products.php';</script>";
    exit();
}

// Fetch expired products from the database
function getExpiredProducts($conn) {
    $currentDate = date('Y-m-d');
    $stmt = $conn->prepare("SELECT id, name, expiry_date FROM products WHERE expiry_date < ? AND status != 'returned'");
    $stmt->bind_param("s", $currentDate);
    $stmt->execute();
    return $stmt->get_result();
}

$expiredProducts = getExpiredProducts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Products</title>
    <link rel="stylesheet" href="style3.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Dashboard</h2>
        <ul>
            <li><a href="product_entry.php">Add Product</a></li>
            <?php if ($role !== 'Admin'): ?>
                <li><a href="view_product.php">View Products</a></li>
            <?php endif; ?>
            <li><a href="update_product.php">Update Product</a></li>
            <?php if ($role === 'Admin'): ?>
                <li><a href="Sales_report.php">Sales report</a></li>
                <li><a href="sales_history.php">Sales history</a></li>
                <li><a href="Stock_level.php">Stock level</a></li>
            <?php endif; ?>
            <li><a href="expired_products.php">Expired Products</a></li>
            <li><a href="logout.php" class="logout-btn">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <header>
            <h1>Expired Products</h1>
        </header>

        <?php if ($expiredProducts->num_rows > 0): ?>
            <ul>
                <?php while ($product = $expiredProducts->fetch_assoc()): ?>
                    <li>
                        <?php echo htmlspecialchars($product['name']); ?> 
                        (Expiry Date: <?php echo htmlspecialchars($product['expiry_date']); ?>)
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="productId" value="<?php echo $product['id']; ?>">
                            <button type="submit">Return</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No expired products found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
