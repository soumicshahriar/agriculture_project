<?php
// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get start and end date from the URL parameters (if any)
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';


// Query to get product names and their total quantities for consumer demand data

// Build the query for filtering based on the date range
$query = "
    SELECT p.product_name, cph.price, SUM(cph.quantity) AS total_quantity, cph.purchase_date
    FROM customer_purchase_history cph
    JOIN product p ON cph.product_id = p.product_id
";
// If both start and end date are provided, add the WHERE clause
if ($startDate && $endDate) {
    $query .= " WHERE cph.purchase_date BETWEEN '$startDate' AND '$endDate'";
}

// Continue the query with GROUP BY and ORDER BY
$query .= "
 GROUP BY p.product_name, cph.price, cph.purchase_date
 ORDER BY p.product_name, cph.price, cph.purchase_date
";


$result = $conn->query($query);

// Display message if a date range is selected
if ($startDate && $endDate) {
    echo "<p>Showing data from $startDate to $endDate</p>";
}

$productNames = [];
$quantities = [];
$prices = [];
$priceElasticities = [];
$purchaseDates = []; // Array to store purchase dates

$priceChanges = [];
$quantityChanges = [];
$elasticityTypes = [];



if ($result->num_rows > 0) {
    $previousData = [];
    while ($row = $result->fetch_assoc()) {
        $productName = $row['product_name'];
        $price = $row['price'];
        $quantity = $row['total_quantity'];
        $purchaseDate = $row['purchase_date'];  // Fetch the purchase date

        // Check if we already have data for this product for price elasticity calculation
        if (isset($previousData[$productName])) {
            $previousPrice = $previousData[$productName]['price'];
            $previousQuantity = $previousData[$productName]['quantity'];

            // Calculate % change in price and quantity
            $priceChange = (($price - $previousPrice) / $previousPrice) * 100;
            $quantityChange = (($quantity - $previousQuantity) / $previousQuantity) * 100;

            // Calculate price elasticity of demand (PED)
            if ($priceChange != 0) {
                $ped = $quantityChange / $priceChange;
            } else {
                $ped = 0; // If no price change, PED is 0
            }


            // Determine the Elasticity Type based on PED
            $elasticityType = 'Unitary';  // Default to 'Unitary'
            if ($ped > 1) {
                $elasticityType = 'Elastic';
            } elseif ($ped < 1) {
                $elasticityType = 'Inelastic';
            }

            // Store the data
            $productNames[] = $productName;
            $prices[] = $price;
            $quantities[] = $quantity;
            $purchaseDates[] = $purchaseDate;  // Store purchase date
            $priceElasticities[] = round($ped, 2);  // Rounded to 2 decimal places
            $priceChanges[] = round($priceChange, 2) . '%';  // Rounded to 2 decimal places
            $quantityChanges[] = round($quantityChange, 2) . '%';  // Rounded to 2 decimal places
            $elasticityTypes[] = $elasticityType;  // Store elasticity type
        }

        // Store current data for next iteration
        $previousData[$productName] = ['price' => $price, 'quantity' => $quantity];
    }
}

// Query to get product names and their total quantities for consumer demand data
$queryConsumerDemand = "SELECT product_name, SUM(quantity) AS total_quantity FROM customer_purchase_history GROUP BY product_name";
$resultConsumerDemand = $conn->query($queryConsumerDemand);

$consumerProductNames = [];
$consumerQuantities = [];

if ($resultConsumerDemand->num_rows > 0) {
    while ($row = $resultConsumerDemand->fetch_assoc()) {
        $consumerProductNames[] = $row['product_name'];
        $consumerQuantities[] = $row['total_quantity'];
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

            <!-- Date Filter Form -->
            <form method="GET" id="dateFilterForm" class="date-filter-form">
                <label for="startDate">Start Date:</label>
                <input type="date" id="startDate" name="startDate" value="<?php echo $startDate; ?>">
                <label for="endDate">End Date:</label>
                <input type="date" id="endDate" name="endDate" value="<?php echo $endDate; ?>">
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>

            <!-- Displaying the Table for Price Elasticity -->
            <table id="filteredData" class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Total Quantity</th>
                        <th>Purchase Date</th>
                        <th>Price Elasticity of Demand (PED)</th>
                        <th>Price Change (%)</th>
                        <th>Quantity Change (%)</th>
                        <th>Elasticity Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display the price elasticity data in table format
                    for ($i = 0; $i < count($productNames); $i++) {
                        echo "<tr>";
                        echo "<td>" . $productNames[$i] . "</td>";
                        echo "<td>" . $prices[$i] . "</td>";
                        echo "<td>" . $quantities[$i] . "</td>";
                        echo "<td>" . $purchaseDates[$i] . "</td>"; // Display purchase date
                        echo "<td>" . $priceElasticities[$i] . "</td>";
                        echo "<td>" . $priceChanges[$i] . "</td>";  // Display Price Change
                        echo "<td>" . $quantityChanges[$i] . "</td>";  // Display Quantity Change
                        echo "<td>" . $elasticityTypes[$i] . "</td>";  // Display Elasticity Type
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="chart-container">
                <canvas id="consumerChart"></canvas>
            </div>
        </div>

        <!-- Market Price Data Block -->
        <div id="market-price" class="block">
            <div class="block-header">Market Price Data</div>
            <div class="block-content">Current and historical prices.</div>
            <div class="chart-container">
                <h2>Market Price, Production Cost and Historical Price</h2>
                <canvas id="priceChart"></canvas>
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
        window.onload = function() {
            // Show the first block (default view when page loads)
            showBlock('product-info');
        };

        // Prepare data for the consumer demand chart
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
                    borderColor: ['#ffffff'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });

        // Prepare data for the market price chart
        const productNamesBar = <?php echo json_encode($productNamesBar); ?>;
        const newPrices = <?php echo json_encode($newPrices); ?>;
        const oldPrices = <?php echo json_encode($oldPrices); ?>;
        const productionCosts = <?php echo json_encode($productionCosts); ?>;

        // Create the bar chart for market price data
        const barCtx = document.getElementById('priceChart').getContext('2d');
        const priceChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: productNamesBar,
                datasets: [{
                        label: 'New Price',
                        data: newPrices,
                        backgroundColor: '#36a2eb',
                        borderColor: '#1a73e8',
                        borderWidth: 1
                    },
                    {
                        label: 'Old Price',
                        data: oldPrices,
                        backgroundColor: '#ffce56',
                        borderColor: '#ffa000',
                        borderWidth: 1
                    },
                    {
                        label: 'Production Cost',
                        data: productionCosts,
                        backgroundColor: '#ff6384',
                        borderColor: '#d32f2f',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        beginAtZero: true,
                        stacked: false
                    },
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        enabled: true
                    }
                }
            }
        });



        document.getElementById('dateFilterForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Get the start and end dates from the form
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            // Create an XMLHttpRequest (AJAX) to send the data to filter_data.php
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'filter_data.php?startDate=' + startDate + '&endDate=' + endDate, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    const response = JSON.parse(xhr.responseText);
                    updateTable(response); // Update the table with the response data
                }
            };
            xhr.send();
        });

        // Function to update the table with the filtered data
        function updateTable(data) {
            const tableBody = document.getElementById('filteredData').getElementsByTagName('tbody')[0];
            tableBody.innerHTML = ''; // Clear existing rows

            // Loop through the data and populate the table
            for (let i = 0; i < data.productNames.length; i++) {
                const row = tableBody.insertRow();

                const cell1 = row.insertCell(0);
                const cell2 = row.insertCell(1);
                const cell3 = row.insertCell(2);
                const cell4 = row.insertCell(3);
                const cell5 = row.insertCell(4);

                cell1.textContent = data.productNames[i];
                cell2.textContent = data.prices[i];
                cell3.textContent = data.quantities[i];
                cell4.textContent = data.purchaseDates[i];
                cell5.textContent = data.priceElasticities[i];
            }
        }
    </script>
</body>

</html>