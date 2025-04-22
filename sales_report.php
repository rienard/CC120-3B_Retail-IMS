<?php 
include 'db_connect.php';

// Default dates: show all if no filter applied
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

// Build the SQL query with optional date filtering
$sql = "SELECT 
            p.name AS product_name, 
            p.price AS product_price,
            SUM(s.quantity_sold) AS total_sold,
            (p.price * SUM(s.quantity_sold)) AS total_sales,
            MAX(s.sale_date) AS last_sale_date
        FROM sales s
        JOIN products p ON s.product_id = p.id";

$conditions = [];
$params = [];
$types = '';

if (!empty($from)) {
    $conditions[] = "s.sale_date >= ?";
    $params[] = $from . " 00:00:00";
    $types .= 's';
}

if (!empty($to)) {
    $conditions[] = "s.sale_date <= ?";
    $params[] = $to . " 23:59:59";
    $types .= 's';
}

if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " GROUP BY s.product_id ORDER BY total_sold DESC";

// Prepare and execute query
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
    <link rel="stylesheet" href="style4.css">
    <style>
        .filter-box {
            margin-bottom: 20px;
        }
        input[type="date"] {
            padding: 5px;
        }
        .btn {
            padding: 6px 12px;
            margin-left: 5px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #218838;
        }
        .export-btn {
            background-color: #007bff;
        }
        .export-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <a class="back-link" href="dashboard.php">&larr; Back to Dashboard</a>
    <h2>Sales Report</h2>

    <div class="filter-box">
        <form method="GET" action="Sales_report.php">
            <label>From: <input type="date" name="from" value="<?php echo htmlspecialchars($from); ?>"></label>
            <label>To: <input type="date" name="to" value="<?php echo htmlspecialchars($to); ?>"></label>
            <button type="submit" class="btn">Filter</button>
            <a class="btn export-btn" href="export_sales.php?from=<?php echo $from; ?>&to=<?php echo $to; ?>">Export CSV</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Price (₱)</th>
                <th>Total Quantity Sold</th>
                <th>Total Sales (₱)</th>
                <th>Last Sale Date</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $grand_total = 0;
            if ($result->num_rows > 0): 
                while ($row = $result->fetch_assoc()):
                    $grand_total += $row['total_sales'];
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                    <td>₱<?php echo number_format($row['product_price'], 2); ?></td>
                    <td><?php echo $row['total_sold']; ?></td>
                    <td>₱<?php echo number_format($row['total_sales'], 2); ?></td>
                    <td><?php echo htmlspecialchars($row['last_sale_date']); ?></td>
                </tr>
            <?php 
                endwhile; 
            ?>
                <tr class="total-row">
                    <td colspan="4"><strong>Grand Total</strong></td>
                    <td><strong>₱<?php echo number_format($grand_total, 2); ?></strong></td>
                </tr>
            <?php else: ?>
                <tr><td colspan="5">No sales data available.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php 
$stmt->close();
$conn->close();
?>
