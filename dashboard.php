<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get user role
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style3.css"> <!-- Link to external CSS -->
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Dashboard</h2>
            <ul>
                <!-- All users (Admin & User) can access Add Product -->
                <li><a href="product_entry.php">Add Product</a></li>

                <!-- Only non-admins (regular users) can view products -->
                <?php if ($role !== 'Admin'): ?>
                    <li><a href="view_product.php">View Products</a></li>
                    <li><a href="cart.php">View Cart</a></li> <!-- ✅ CART LINK FOR USERS -->
                <?php endif; ?>

                <!-- All users can update products -->
                <li><a href="update_product.php">Update Product</a></li>

                <!-- Only Admins can view the Sales report -->
                <?php if ($role === 'Admin'): ?>
                    <li><a href="Sales_report.php">Sales Report</a></li>
                    <li><a href="sales_history.php">Sales History</a></li>
                    <li><a href="Stock_level.php">Stock Level</a></li>
                <?php endif; ?>

                <!-- Expired products (all users) -->
                
                <li><a href="logout.php" class="logout-btn">Logout</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Welcome to the Hardware Store, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
            </header>
            <p>You are logged in as <strong><?php echo htmlspecialchars($role); ?></strong>.</p>
        </div>
    </div>
</body>
</html>
