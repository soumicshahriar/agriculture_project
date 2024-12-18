<?php
// Include the database connection file
include('../config/connect.php');

// Query for pie chart: Consumer Purchase Trends
$queryPie = "SELECT product_name, SUM(quantity) AS total_quantity 
             FROM customer_purchase_history 
             GROUP BY product_name";
$resultPie = $conn->query($queryPie);

$productNamesPie = [];
$quantitiesPie = [];

if ($resultPie->num_rows > 0) {
    while ($row = $resultPie->fetch_assoc()) {
        $productNamesPie[] = $row['product_name'];
        $quantitiesPie[] = $row['total_quantity'];
    }
}

// Query for bar chart: Market Price Data
$queryBar = "
    SELECT p.product_name, pi.new_price, pi.old_price, pi.production_cost
    FROM product_info pi
    INNER JOIN product p ON pi.product_id = p.product_id
";
$resultBar = $conn->query($queryBar);

$productNamesBar = [];
$newPrices = [];
$oldPrices = [];
$productionCosts = [];

if ($resultBar->num_rows > 0) {
    while ($row = $resultBar->fetch_assoc()) {
        $productNamesBar[] = $row['product_name'];
        $newPrices[] = $row['new_price'];
        $oldPrices[] = $row['old_price'];
        $productionCosts[] = $row['production_cost'];
    }
}

// Query for Forecasting Graph: Daily Sales Data
$queryForecast = "
    SELECT purchase_date, SUM(total_price) AS daily_sales
    FROM customer_purchase_history
    GROUP BY purchase_date
    ORDER BY purchase_date
";
$resultForecast = $conn->query($queryForecast);

$datesForecast = [];
$salesForecast = [];

if ($resultForecast->num_rows > 0) {
    while ($row = $resultForecast->fetch_assoc()) {
        $datesForecast[] = $row['purchase_date'];
        $salesForecast[] = $row['daily_sales'];
    }
}

// Calculate future sales estimates
$futureDays = 7; // Number of future days to estimate
$lastDate = new DateTime(end($datesForecast));
$averageDailySales = array_sum($salesForecast) / count($salesForecast);

for ($i = 1; $i <= $futureDays; $i++) {
    $lastDate->modify('+1 day');
    $datesForecast[] = $lastDate->format('Y-m-d');
    $salesForecast[] = $averageDailySales; // Simple average projection
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="/navbar/nav.css">
    <link rel="stylesheet" href="adminStyle.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <div class="auth-buttons">
                <button class="btn create-account">Create Account</button>
                <button class="btn logout">Logout</button>
            </div>
        </div>

        <div class="dashboard-grid">
            <a href="../agriculture_db/index.php" class="dashboard-card">
                <h3>Agriculture Database</h3>
                <p>Manage agricultural data and records</p>
            </a>
            <a href="../consumer_db/index.php" class="dashboard-card">
                <h3>Consumer Database</h3>
                <p>View and manage consumer information</p>
            </a>
            <a href="../employee_db/employeeInfo.php" class="dashboard-card">
                <h3>Employee Database</h3>
                <p>Handle employee records and management</p>
            </a>
            <a href="../index.html" class="dashboard-card">
                <h3>Landing Page</h3>
                <p>Edit landing page content</p>
            </a>
            <a href="../product_db/product_info.php" class="dashboard-card">
                <h3>Product Database</h3>
                <p>Manage product inventory and details</p>
            </a>
        </div>

        <!-- Pie Chart Section -->
        <div class="chart-container">
            <h2>Unveiling Consumer Trends Through Purchase History Analysis</h2>
            <canvas id="productChart"></canvas>
        </div>

        <!-- Bar Chart Section -->
        <div class="chart-container">
            <h2>Market Price Data</h2>
            <canvas id="priceChart"></canvas>
        </div>

        <!-- Forecasting Chart Section -->
        <div class="chart-container">
            <h2>Sales Forecast</h2>
            <canvas id="forecastChart"></canvas>
        </div>
    </div>

    <script>
        // Data for the pie chart
        const productNamesPie = <?php echo json_encode($productNamesPie); ?>;
        const quantitiesPie = <?php echo json_encode($quantitiesPie); ?>;

        const pieCtx = document.getElementById('productChart').getContext('2d');
        const productChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: productNamesPie,
                datasets: [{
                    label: 'Quantity',
                    data: quantitiesPie,
                    backgroundColor: [
                        '#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#2ecc71'
                    ],
                    borderColor: ['#ffffff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'top' }, tooltip: { enabled: true } }
            }
        });

        // Data for the bar chart
        const productNamesBar = <?php echo json_encode($productNamesBar); ?>;
        const newPrices = <?php echo json_encode($newPrices); ?>;
        const oldPrices = <?php echo json_encode($oldPrices); ?>;
        const productionCosts = <?php echo json_encode($productionCosts); ?>;

        const barCtx = document.getElementById('priceChart').getContext('2d');
        const priceChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: productNamesBar,
                datasets: [
                    { label: 'New Price', data: newPrices, backgroundColor: '#36a2eb' },
                    { label: 'Old Price', data: oldPrices, backgroundColor: '#ffce56' },
                    { label: 'Production Cost', data: productionCosts, backgroundColor: '#ff6384' }
                ]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'top' }, tooltip: { enabled: true } }
            }
        });

        // Data for the forecasting chart
        const datesForecast = <?php echo json_encode($datesForecast); ?>;
        const salesForecast = <?php echo json_encode($salesForecast); ?>;

        const forecastCtx = document.getElementById('forecastChart').getContext('2d');
        const forecastChart = new Chart(forecastCtx, {
            type: 'line',
            data: {
                labels: datesForecast,
                datasets: [{
                    label: 'Daily Sales',
                    data: salesForecast,
                    borderColor: '#4caf50',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { position: 'top' }, tooltip: { enabled: true } }
            }
        });
    </script>
</body>
</html>
