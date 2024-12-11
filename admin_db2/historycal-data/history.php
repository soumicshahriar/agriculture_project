<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Historical Data</title>
  <link rel="stylesheet" href="/admin_db2/historycal-data/style.css">
  <!-- Include Chart.js library -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>

<body>

  <!-- Search Button -->
  <div class="search-btn-container">
    <button class="search-btn" onclick="toggleFilterForm()">Search</button>
  </div>

  <!-- Date Filter Form -->
  <div class="filter-container">
    <form method="GET" action="">
      <input type="hidden" name="page" value="history">
      <label for="start_date">Start Date:</label>
      <input type="date" id="start_date" name="start_date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : ''; ?>">

      <label for="end_date">End Date:</label>
      <input type="date" id="end_date" name="end_date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : ''; ?>">

      <input type="submit" value="Filter">
    </form>
  </div>

  <?php
  // Database connection
  $servername = "localhost";  // Adjust with your database server
  $username = "root";         // Your database username
  $password = "";             // Your database password
  $dbname = "agriculture_product_data";  // Your database name

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Define the query condition for filtering by date range
  $whereClause = "";
  if (isset($_GET['start_date']) && !empty($_GET['start_date']) && isset($_GET['end_date']) && !empty($_GET['end_date'])) {
    $start_date = $conn->real_escape_string($_GET['start_date']);
    $end_date = $conn->real_escape_string($_GET['end_date']);
    $whereClause = " WHERE h.date BETWEEN '$start_date' AND '$end_date'";  // Filter by date range
  }

  // Query to fetch historical data along with product name
  $sql = "SELECT h.product_id, p.product_name, h.quantity, h.price, h.total_price, h.date 
            FROM historical_data h
            JOIN product p ON h.product_id = p.product_id" . $whereClause;

  $result = $conn->query($sql);

  // Arrays to store table data (for table display)
  $table_data = [];

  // Arrays to store chart data (grouped by product name)
  $product_names = [];
  $quantities = [];

  // Check if there are records
  if ($result->num_rows > 0) {
    // Start table
    echo "<table>
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total Price</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>";

    // Output data of each row and prepare chart data (grouped by product)
    while ($row = $result->fetch_assoc()) {
      // Add row to table data
      $table_data[] = $row;

      // Prepare chart data (sum quantities for each product)
      if (isset($product_names[$row['product_name']])) {
        $product_names[$row['product_name']] += $row['quantity'];  // Sum the quantity
      } else {
        $product_names[$row['product_name']] = $row['quantity'];  // First entry for the product
      }
    }

    // Display table rows
    foreach ($table_data as $row) {
      echo "<tr>
                <td>" . $row['product_id'] . "</td>
                <td>" . $row['product_name'] . "</td>
                <td>" . $row['quantity'] . "</td>
                <td>" . $row['price'] . "</td>
                <td>" . $row['total_price'] . "</td>
                <td>" . $row['date'] . "</td>
              </tr>";
    }

    echo "</tbody></table>";
  } else {
    echo "<p>No records found for the selected date range.</p>";
  }

  // Prepare chart data for Chart.js
  $chart_labels = array_keys($product_names);
  $chart_data = array_values($product_names);

  // Close connection
  $conn->close();
  ?>

  <!-- Chart.js canvas -->
  <div style="width: 30%; margin: 20px auto;">
    <canvas id="demandChart"></canvas>
  </div>

  <script>
    // Prepare chart data from PHP arrays
    const productNames = <?php echo json_encode($chart_labels); ?>;
    const quantities = <?php echo json_encode($chart_data); ?>;

    // Create pie chart
    const ctx = document.getElementById('demandChart').getContext('2d');
    const demandChart = new Chart(ctx, {
      type: 'pie', // Pie chart type
      data: {
        labels: productNames, // Product names on the pie chart
        datasets: [{
          data: quantities, // Total quantities sold for each product
          backgroundColor: ['gray', 'black', 'red', 'orange', '#FF9F40', '#C7B9F1', '#FF5757'], // Colors for each slice
          hoverBackgroundColor: ['#FF4064', '#1E8EC3', '#D7A238', '#42AFAF', '#FF7E34', '#B1A3DC', '#F74040'],
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
            labels: {
              font: {
                size: 14, // Font size for the legend labels
                family: 'Arial', // Font family for the labels
                weight: 'bold', // Font weight
                lineHeight: 1.5, // Line height for the labels
              },
              color: '#333', // Color of the legend text
              padding: 15, // Space between legend items
            }
          },
          tooltip: {
            callbacks: {
              label: function(tooltipItem) {
                return tooltipItem.label + ': ' + tooltipItem.raw + ' units'; // Show quantity in tooltip
              }
            }
          }
        }
      }
    });

    // Function to toggle the visibility of the filter form
    function toggleFilterForm() {
      const filterForm = document.querySelector('.filter-container');
      if (filterForm.style.display === "none" || filterForm.style.display === "") {
        filterForm.style.display = "block"; // Show the filter form
      } else {
        filterForm.style.display = "none"; // Hide the filter form
      }
    }
  </script>

  

</body>

</html>