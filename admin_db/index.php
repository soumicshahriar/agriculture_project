<?php
// Include the database connection file
include('../config/connect.php');

// Query to get product names and their total quantities
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

        <div class="chart-container">
            <h2>Unveiling Consumer Trends Through Purchase History Analysis</h2>
            <canvas id="productChart"></canvas>
        </div>
    </div>

    <script>
        // Prepare data for the chart
        const productNames = <?php echo json_encode($productNames); ?>;
        const quantities = <?php echo json_encode($quantities); ?>;

        // Create the pie chart
        const ctx = document.getElementById('productChart').getContext('2d');
        const productChart = new Chart(ctx, {
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
                    borderWidth: 1
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
