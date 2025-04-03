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
                <!-- All users (Admin & User) can access these -->
                <li><a href="product_entry.php">Add Product</a></li>
                <li><a href="view_product.php">View Products</a></li>
                <li><a href="update_product.php">Update Product</a></li>
                <li><a href="delete_product.php">Delete Product</a></li>

                <!-- Only Admins can access Sales Reports -->
                <?php if ($role === 'Admin'): ?>
                    <li><a href="sales_report.php">Sales Report</a></li>
                <?php endif; ?>
                
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
