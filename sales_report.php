<?php
include 'db_connect.php';

// Fetch products from database
$sql = "SELECT name, entity FROM products";
$result = $conn->query($sql);

function getSalesLevel($entity) {
    if ($entity <= 10) {
        return 'Low';
    } elseif ($entity <= 20) {
        return 'Medium';
    } else {
        return 'High';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Report</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; padding: 0; }
        .container { width: 80%; margin: auto; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 10px; text-align: center; }
        th { background-color: #f4f4f4; }
        .back-arrow { cursor: pointer; font-size: 20px; display: inline-block; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: left; margin-bottom: 10px;">
            <div class="back-arrow" onclick="window.location.href='dashboard.php';">&#8592; </div>
        </div>
        <h2>Sales Report</h2>
        <table>
            <tr>
                <th>Product</th>
                <th>Sales Level</th>
            </tr>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo getSalesLevel($row['entity']); ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="2">No products found</td></tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>