<?php
include 'db_connect.php';

$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

$sql = "SELECT 
            p.name AS product_name, 
            p.price AS product_price,
            SUM(s.quantity_sold) AS total_sold,
            (p.price * SUM(s.quantity_sold)) AS total_sales
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

$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="sales_report.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Product Name', 'Price', 'Total Quantity Sold', 'Total Sales']);

// Variables to store totals
$grand_total_qty = 0;
$grand_total_sales = 0.0;

// Write product rows and calculate totals
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['product_name'],
        number_format($row['product_price'], 2),
        $row['total_sold'],
        number_format($row['total_sales'], 2)
    ]);

    $grand_total_qty += $row['total_sold'];
    $grand_total_sales += $row['total_sales'];
}

// Add an empty row for spacing
fputcsv($output, []);

// Add the grand total row
fputcsv($output, ['TOTAL', '', $grand_total_qty, number_format($grand_total_sales, 2)]);

fclose($output);
$stmt->close();
$conn->close();
exit;
?>
