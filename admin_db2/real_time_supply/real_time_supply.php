<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriculture_product_data";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product and product_info data
$sql = "SELECT p.product_name, pi.quantity, pi.new_price, pi.old_price, pi.production_cost, pi.production_date, pi.expiration_date 
        FROM product p
        JOIN product_info pi ON p.product_id = pi.product_id";
$result = $conn->query($sql);

$products = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Real-Time Supply Levels</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .chart-container {
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <h1>Real-Time Supply Levels</h1>

    <div class="chart-container">
        <canvas id="supplyChart"></canvas>
    </div>

    <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; margin-top: 20px;">
        <thead>
            <tr>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>New Price</th>
                <th>Old Price</th>
                <th>Production Cost</th>
                <th>Production Date</th>
                <th>Expiration Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['new_price']); ?></td>
                    <td><?php echo htmlspecialchars($product['old_price']); ?></td>
                    <td><?php echo htmlspecialchars($product['production_cost']); ?></td>
                    <td><?php echo htmlspecialchars($product['production_date']); ?></td>
                    <td><?php echo htmlspecialchars($product['expiration_date']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        const ctx = document.getElementById('supplyChart').getContext('2d');

        const chartData = {
            labels: [
                <?php foreach ($products as $product) {
                    echo "'" . $product['product_name'] . "',";
                } ?>
            ],
            datasets: [
                {
                    label: 'Quantity',
                    data: [
                        <?php foreach ($products as $product) {
                            echo $product['quantity'] . ",";
                        } ?>
                    ],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }
            ]
        };

        const supplyChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
