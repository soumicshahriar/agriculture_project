<?php
// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get product names and their total quantities for consumer demand data
$query = "SELECT product_name, SUM(quantity) AS total_quantity FROM customer_purchase_history GROUP BY product_name";
$result = $conn->query($query);

$productNames = [];
$quantities = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $productNames[] = $row['product_name'];
        $quantities[] = $row['total_quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Include Navbar -->
    <?php include 'navbar/nav.html'; ?>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar -->
    <div class="sidebar">
        <ul>
            <li onclick="showBlock('product-info')">Product Information</li>
            <li onclick="showBlock('historical-production')">Historical Production Data</li>
            <li onclick="showBlock('consumer-demand')">Consumer Demand Data</li>
            <li onclick="showBlock('real-time-supply')">Real-Time Supply Levels</li>
            <li onclick="showBlock('market-price')">Market Price Data</li>
            <li onclick="showBlock('analytical-tools')">Analytical Tools</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content">
        <!-- Product Information Block -->
        <div id="product-info" class="block">
            <div class="block-header">Comprehensive Product Information</div>
            <div class="block-content">Details on type, variety, and seasonality.</div>
            <div class="block-content">
                <?php include('../admin_db/product_table/product_info.php'); ?>
            </div>
            <div class="chart-container">
                <canvas id="productChart"></canvas>
            </div>
        </div>

        <!-- Historical Production Data Block -->
        <div id="historical-production" class="block">
            <div class="block-header">Historical Production Data</div>
            <div class="block-content">Yields, acreage, and costs over time.</div>
            <div class="chart-container">
                <canvas id="historicalChart"></canvas>
            </div>
        </div>

        <!-- Consumer Demand Data Block -->
        <div id="consumer-demand" class="block">
            <div class="block-header">Unveiling Consumer Trends Through Purchase History Analysis</div>
            <div class="block-content">Consumption patterns, price elasticity.</div>
            <div class="chart-container">
                <canvas id="consumerChart"></canvas>
            </div>
        </div>

        <!-- Real-Time Supply Levels Block -->
        <div id="real-time-supply" class="block">
            <div class="block-header">Real-Time Supply Levels</div>
            <div class="block-content">Inventory, storage, and logistics data.</div>
            <div class="chart-container">
                <canvas id="supplyChart"></canvas>
            </div>
        </div>

        <!-- Market Price Data Block -->
        <div id="market-price" class="block">
            <div class="block-header">Market Price Data</div>
            <div class="block-content">Current and historical prices.</div>
            <div class="chart-container">
                <canvas id="marketPriceChart"></canvas>
            </div>
        </div>

        <!-- Analytical Tools Block -->
        <div id="analytical-tools" class="block">
            <div class="block-header">Analytical Tools</div>
            <div class="block-content">Charts, forecasting, and scenario analysis.</div>
            <div class="chart-container">
                <canvas id="analyticalChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Script to control block visibility and sidebar toggle -->
    <script>
        // Function to show the selected block and hide others
        function showBlock(blockId) {
            // Get all blocks
            const blocks = document.querySelectorAll('.block');

            // Hide all blocks
            blocks.forEach(block => {
                block.classList.remove('active');
            });

            // Show the selected block by adding the 'active' class
            const selectedBlock = document.getElementById(blockId);
            selectedBlock.classList.add('active');
        }

        // Sidebar toggle button functionality
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const content = document.querySelector('.content');
            sidebar.classList.toggle('open');
            content.classList.toggle('open');
        }

        // Optionally: Show the first block by default when the page loads
        window.onload = function () {
            // Show the first block (default view when page loads)
            showBlock('product-info');
        };

        // Prepare data for the chart
        const productNames = <?php echo json_encode($productNames); ?>;
        const quantities = <?php echo json_encode($quantities); ?>;

        // Create the pie chart for consumer demand
        const ctx = document.getElementById('consumerChart').getContext('2d');
        const consumerChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: productNames,
                datasets: [{
                    label: 'Quantity',
                    data: quantities,
                    backgroundColor: [
                        '#ff6384',
                        '#36a2eb',
                        '#cc65fe',
                        '#ffce56',
                        '#2ecc71'
                    ],
                    borderColor: [
                        '#ffffff'
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });
    </script>
</body>

</html>
