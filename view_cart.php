<?php
session_start();
include 'db_connect.php';

$user_id = $_SESSION['user_id'] ?? 0;
$today = date('Y-m-d');

// Handle return request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    // Get the cart item info
    $cart_query = "SELECT uc.id AS cart_id, p.id AS product_id, p.name, p.expiry_date, uc.quantity 
                   FROM user_cart uc 
                   JOIN products p ON uc.product_id = p.id 
                   WHERE uc.id = ? AND uc.user_id = ?";
    $stmt = $conn->prepare($cart_query);
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item) {
        $reason = "Expired product";
        $return_date = date('Y-m-d H:i:s');

        // Insert into return_table
        $return_query = "INSERT INTO return_table (product_id, quantity_returned, return_date, reason) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($return_query);
        $stmt->bind_param("iiss", $item['product_id'], $item['quantity'], $return_date, $reason);
        $stmt->execute();

        // Restore product quantity
        $restore_query = "UPDATE products SET quantity = quantity + ? WHERE id = ?";
        $stmt = $conn->prepare($restore_query);
        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt->execute();

        // Remove from user_cart
        $delete_query = "DELETE FROM user_cart WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $cart_id);
        $stmt->execute();

        echo "<script>alert('Expired product returned successfully.'); window.location.href='cart.php';</script>";
        exit();
    }
}

// Fetch cart items for the current user
$cart_query = "SELECT uc.id AS cart_id, p.name, p.expiry_date, uc.quantity, p.price 
               FROM user_cart uc 
               JOIN products p ON uc.product_id = p.id 
               WHERE uc.user_id = ?";
$stmt = $conn->prepare($cart_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_result = $stmt->get_result();
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
    <div class="back-btn" onclick="window.location.href='dashboard.php';">&#8592; Back to Dashboard</div>
    <h2>Your Cart</h2>

    <?php if ($cart_result->num_rows > 0): ?>
        <table>
            <tr>
                <th>Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Expiry Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php while ($item = $cart_result->fetch_assoc()): ?>
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
                                <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                                <button type="submit" class="return-btn">Return</button>
                            </form>
                        <?php else: ?>
                            <span style="color: gray;">No Action</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>
</body>
</html>

<?php $conn->close(); ?>
