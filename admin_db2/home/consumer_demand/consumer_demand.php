<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriculture_product_data";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Initialize variables for filtering
    $filter_month = isset($_POST['month']) ? $_POST['month'] : date('m'); // Default to current month
    $filter_year = isset($_POST['year']) ? $_POST['year'] : date('Y');    // Default to current year
    $filter_product_name = isset($_POST['product_name']) ? $_POST['product_name'] : ''; // Filter by product name

    // SQL query to calculate PED with filters for month, year, and product name
    $sql = "
        WITH PriceQuantityChange AS (
            SELECT
                product_id,
                product_name,
                LAG(price) OVER (PARTITION BY product_id ORDER BY purchase_date) AS previous_price,
                price AS current_price,
                LAG(quantity) OVER (PARTITION BY product_id ORDER BY purchase_date) AS previous_quantity,
                quantity AS current_quantity
            FROM customer_purchase_history
            WHERE MONTH(purchase_date) = :month 
            AND YEAR(purchase_date) = :year
            AND (:product_name = '' OR product_name LIKE :product_name)
        )
        SELECT
            product_id,
            product_name,
            current_price,
            previous_price,
            current_quantity,
            previous_quantity,
            (current_quantity - previous_quantity) * 100.0 / NULLIF(previous_quantity, 0) AS percent_change_quantity,
            (current_price - previous_price) * 100.0 / NULLIF(previous_price, 0) AS percent_change_price,
            ( (current_quantity - previous_quantity) * 1.0 / NULLIF(previous_quantity, 0) ) / 
            ( (current_price - previous_price) * 1.0 / NULLIF(previous_price, 0) ) AS PED
        FROM PriceQuantityChange
        WHERE previous_price IS NOT NULL AND previous_quantity IS NOT NULL
    ";

    // Execute the query with parameters
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':month', $filter_month, PDO::PARAM_INT);
    $stmt->bindParam(':year', $filter_year, PDO::PARAM_INT);
    $stmt->bindValue(':product_name', '%' . $filter_product_name . '%', PDO::PARAM_STR); // Filter by product name
    $stmt->execute();

    // Fetch all results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Data for charts
    $labels = [];
    $ped_values = [];
    $quantity_changes = [];
    $price_changes = [];

    // Start HTML output
    echo "<!DOCTYPE html>";
    echo "<html lang='en'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Price Elasticity of Demand (PED)</title>";
    echo "<link rel='stylesheet'  href='../admin_db2/home/consumer_demand/styles.css'>"; // Linking the CSS file

    // Chart.js script
    echo "<script src='https://cdn.jsdelivr.net/npm/chart.js'></script>";
    echo "</head>";
    echo "<body>";

    // Filter form
    echo "<h1>Price Elasticity of Demand (PED) Calculation</h1>";
    echo "<form method='POST' action=''>";
    echo "<label for='product_name'>Product Name:</label>";
    echo "<input type='text' name='product_name' id='product_name' value='" . htmlspecialchars($filter_product_name) . "'>";

    echo "<label for='month'>Month:</label>";
    echo "<select name='month' id='month'>";
    for ($m = 1; $m <= 12; $m++) {
        $selected = ($m == $filter_month) ? "selected" : "";
        echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 10)) . "</option>";
    }
    echo "</select>";

    echo "<label for='year'>Year:</label>";
    echo "<select name='year' id='year'>";
    for ($y = 2020; $y <= date('Y'); $y++) { // Adjust start year as needed
        $selected = ($y == $filter_year) ? "selected" : "";
        echo "<option value='$y' $selected>$y</option>";
    }
    echo "</select>";

    echo "<button type='submit'>Calculate PED</button>";
    echo "</form>";

    // Display results in a table
    echo "<table>";
    echo "<tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Current Price</th>
            <th>Previous Price</th>
            <th>Current Quantity</th>
            <th>Previous Quantity</th>
            <th>% Change in Quantity</th>
            <th>% Change in Price</th>
            <th>Price Elasticity of Demand (PED)</th>
            <th>Elasticity Type</th>
          </tr>";

    foreach ($results as $row) {
        $ped = $row['PED'];

        // Determine elasticity type
        if ($ped === null) {
            $elasticity_type = "Undefined";
        } elseif ($ped == 0) {
            $elasticity_type = "Perfectly Inelastic";
        } elseif ($ped == 1) {
            $elasticity_type = "Unit Elastic";
        } elseif ($ped > 1) {
            $elasticity_type = "Elastic";
        } elseif ($ped < 1) {
            $elasticity_type = "Inelastic";
        } else {
            $elasticity_type = "Perfectly Elastic";
        }

        // Prepare data for the chart
        $labels[] = $row['product_name'];
        $ped_values[] = $ped;
        $quantity_changes[] = $row['percent_change_quantity'];
        $price_changes[] = $row['percent_change_price'];

        echo "<tr>
                <td>{$row['product_id']}</td>
                <td>{$row['product_name']}</td>
                <td>{$row['current_price']}</td>
                <td>{$row['previous_price']}</td>
                <td>{$row['current_quantity']}</td>
                <td>{$row['previous_quantity']}</td>
                <td>" . number_format($row['percent_change_quantity'], 2) . "%</td>
                <td>" . number_format($row['percent_change_price'], 2) . "%</td>
                <td>" . number_format($ped, 2) . "</td>
                <td>$elasticity_type</td>
              </tr>";
    }

    echo "</table>";

    // Prepare chart data in JavaScript
    echo "<div class='chart'>";
    echo "<canvas id='pedChart'></canvas>";
    echo "<canvas id='comparisonChart' style='margin-top: 50px;'></canvas>";
    echo "</div>";

    echo "<script>
        const ctxPed = document.getElementById('pedChart').getContext('2d');
        const ctxComparison = document.getElementById('comparisonChart').getContext('2d');

        // Create a gradient for the bars (for PED chart)
        function createGradient(ctx) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400); // Vertical gradient
            gradient.addColorStop(0, 'rgba(75, 192, 192, 1)');   // Start color (top of the bar)
            gradient.addColorStop(0.5, 'rgba(75, 192, 192, 1)');  // Middle color
            gradient.addColorStop(1, 'rgba(75, 192, 192, 5)');  // End color (bottom of the bar)
            return gradient;
        }

        const pedChart = new Chart(ctxPed, {
            type: 'bar',
            data: {
                labels: " . json_encode($labels) . ",
                datasets: [{
                    label: 'Price Elasticity of Demand (PED)',
                    data: " . json_encode($ped_values) . ",
                    backgroundColor: createGradient(ctxPed),  // Applying gradient as the background color
                    borderColor: 'black',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });

        const comparisonChart = new Chart(ctxComparison, {
            type: 'line',
            data: {
                labels: " . json_encode($labels) . ",
                datasets: [{
                    label: 'Price Change (%)',
                    data: " . json_encode($price_changes) . ",
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: false,
                    tension: 0.1
                }, {
                    label: 'Quantity Change (%)',
                    data: " . json_encode($quantity_changes) . ",
                    borderColor: 'rgba(153, 102, 255, 1)',
                    fill: false,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    </script>";

    echo "</body>";
    echo "</html>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
