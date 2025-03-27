<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h2>
    
    <nav>
        <ul>
            <li><a href="product_entry.php">Product Entry</a></li>
            <li><a href="view_products.php">View Products</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <p>Select an option from the menu.</p>
</body>
</html>
