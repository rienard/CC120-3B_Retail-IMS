<?php
session_start();
include 'db_connect.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Checkout process
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!empty($_SESSION['cart'])) {
        // Clear the cart after checkout
        $_SESSION['cart'] = [];
        echo "<script>alert('Checkout successful!'); window.location.href='checkout.php';</script>";
        exit();
    } else {
        echo "<script>alert('No products in the cart!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to external CSS -->
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 0; }
        .container { width: 60%; margin: auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .checkout-btn { background-color: green; color: white; padding: 10px; border: none; cursor: pointer; }
        .success-message { color: green; font-weight: bold; margin-top: 10px; }
        .back-arrow { cursor: pointer; font-size: 20px; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: left; margin-bottom: 10px;">
            <div class="back-arrow" onclick="window.location.href='dashboard.php';">&#8592; </div>
        </div>
        <h2>Checkout</h2>
        <?php if (isset($_GET['success'])): ?>
            <p class="success-message">Checkout Successful!</p>
        <?php endif; ?>
        <table>
            <tr>
                <th>Product Name</th>
                <th>Price</th>
            </tr>
            <?php if (!empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $cart_item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($cart_item['name']); ?></td>
                        <td>&#8369;<?php echo number_format($cart_item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">No products in cart</td></tr>
            <?php endif; ?>
        </table>
        <br>
        <form method="POST">
            <button type="submit" name="checkout" class="checkout-btn">Checkout</button>
        </form>
    </div>
</body>
</html>
