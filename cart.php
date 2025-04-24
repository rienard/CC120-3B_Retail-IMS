<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$today = date('Y-m-d');

// Handle return request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['return_index'])) {
    $index = $_POST['return_index'];

    if (isset($_SESSION['cart'][$index])) {
        $item = $_SESSION['cart'][$index];

        // Insert into return_table
        $return_query = "INSERT INTO return_table (product_id, quantity_returned, return_date, reason) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($return_query);
        $reason = 'Expired product';
        $return_date = date('Y-m-d H:i:s');
        $stmt->bind_param("iiss", $item['product_id'], $item['quantity'], $return_date, $reason);
        $stmt->execute();

        // Restore product quantity
        $update_stock = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
        $stmt = $conn->prepare($update_stock);
        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt->execute();

        // Remove item from cart
        unset($_SESSION['cart'][$index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex array

        echo "<script>alert('Expired product returned successfully.'); window.location.href='cart.php';</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 900px; margin: auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; }
        th { background-color: #f4f4f4; }
        .expired { color: red; font-weight: bold; }
        .return-btn { background-color: red; color: white; border: none; padding: 5px 10px; cursor: pointer; }
        .back-btn { text-align: left; margin-bottom: 10px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
<div class="container">
    <!-- Back Button Redirect to Dashboard -->
    <div class="back-btn" onclick="window.location.href='dashboard.php';">&#8592; Back to Dashboard</div>

    <h2>Your Cart</h2>

    <?php if (count($cart) > 0): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Expiry Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($cart as $index => $item): ?>
                <?php
                    $isExpired = (!empty($item['expiry_date']) && $item['expiry_date'] < $today);
                    $status = $isExpired ? 'Expired' : 'Valid';
                ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>&#8369;<?= number_format($item['price'], 2) ?></td>
                    <td><?= !empty($item['expiry_date']) ? $item['expiry_date'] : 'N/A' ?></td>
                    <td class="<?= $isExpired ? 'expired' : '' ?>"><?= $status ?></td>
                    <td>
                        <?php if ($isExpired): ?>
                            <form method="POST" style="margin:0;">
                                <input type="hidden" name="return_index" value="<?= $index ?>">
                                <button type="submit" class="return-btn">Return</button>
                            </form>
                        <?php else: ?>
                            <span style="color: gray;">No Action</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>
</body>
</html>

<?php $conn->close(); ?>
