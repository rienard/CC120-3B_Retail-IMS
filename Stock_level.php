<?php
include 'db_connect.php';

// Fetch products from database
$sql = "SELECT name, quantity FROM products";
$result = $conn->query($sql);

// Prepare data
$low = $medium = $high = 0;
$products = [];

function getStockLevel($quantity) {
    if ($quantity <= 10) return 'Low';
    elseif ($quantity <= 20) return 'Medium';
    else return 'High';
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $level = getStockLevel($row['quantity']);
        $products[] = ['name' => $row['name'], 'level' => $level, 'quantity' => $row['quantity']];

        if ($level === 'Low') $low++;
        elseif ($level === 'Medium') $medium++;
        else $high++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Level</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; }
        .top-bar {
            display: flex;
            align-items: center;
            background: #f4f4f4;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
        }
        .back-arrow { font-size: 20px; cursor: pointer; margin-right: 10px; }
        .title { font-size: 18px; font-weight: bold; flex-grow: 1; }
        .controls { margin-left: auto; display: flex; align-items: center; gap: 10px; }
        .container {
            display: flex;
            padding: 20px;
        }
        .chart-box {
            width: 200px;
        }
        canvas {
            width: 100% !important;
            height: auto !important;
        }
        .table-box {
            margin-left: 40px;
            flex: 1;
        }
        h3 {
            margin-bottom: 10px;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="back-arrow" onclick="window.location.href='dashboard.php';">&#8592;</div>
        <div class="title">STOCK LEVEL</div>
        <div class="controls">
            <label for="filter-date">View</label>
            <select id="filter-date">
                <option value="today">Date</option>
            </select>
        </div>
    </div>

    <div class="container">
        <div class="chart-box">
            <canvas id="stockChart"></canvas>
        </div>

        <div class="table-box">
            <h3>Product Stock Overview</h3>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Stock Level</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo $product['level']; ?></td>
                                <td><?php echo $product['quantity']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No products found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('stockChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Low', 'Medium', 'High'],
                datasets: [{
                    label: 'Stock Level Count',
                    data: [<?php echo json_encode($low); ?>, <?php echo json_encode($medium); ?>, <?php echo json_encode($high); ?>],
                    backgroundColor: ['#ff6666', '#ffcc66', '#66cc66'],
                    borderWidth: 1,
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: function (value) {
                            return value;
                        }
                    }
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Stock Level Overview'
                    },
                    datalabels: {
                        display: true,
                        font: {
                            weight: 'bold',
                            size: 12
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>
