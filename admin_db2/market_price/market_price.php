<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriculture_product_data";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// Handle filters
$product_name_filter = isset($_GET['product_name']) ? $_GET['product_name'] : '';
$year_filter = isset($_GET['year']) ? $_GET['year'] : '';
$month_filter = isset($_GET['month']) ? $_GET['month'] : '';
$start_date_filter = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date_filter = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// Fetch product names for filtering
$sql_products = "SELECT DISTINCT product_name FROM product";
$result_products = $conn->query($sql_products);

// Fetch historical data with filters
$sql_historical = "SELECT hd.id, hd.product_id, p.product_name, hd.quantity, hd.price AS old_price, hd.total_price, hd.date
                   FROM historical_data hd
                   JOIN product p ON hd.product_id = p.product_id
                   WHERE (p.product_name LIKE '%$product_name_filter%' OR '$product_name_filter' = '')
                   AND (YEAR(hd.date) LIKE '%$year_filter%' OR '$year_filter' = '')
                   AND (MONTH(hd.date) = '$month_filter' OR '$month_filter' = '')
                   AND (hd.date BETWEEN '$start_date_filter' AND '$end_date_filter' OR '$start_date_filter' = '' OR '$end_date_filter' = '')
                   ORDER BY hd.date DESC";

$result_historical = $conn->query($sql_historical);

// Fetch current data with filters
$sql_current = "SELECT pi.id, pi.product_id, p.product_name, pi.quantity, pi.new_price, pi.production_date
                FROM product_info pi
                JOIN product p ON pi.product_id = p.product_id
                WHERE (p.product_name LIKE '%$product_name_filter%' OR '$product_name_filter' = '')
                AND (YEAR(pi.production_date) LIKE '%$year_filter%' OR '$year_filter' = '')
                AND (MONTH(pi.production_date) = '$month_filter' OR '$month_filter' = '')
                AND (pi.production_date BETWEEN '$start_date_filter' AND '$end_date_filter' OR '$start_date_filter' = '' OR '$end_date_filter' = '')
                ORDER BY pi.production_date DESC";

$result_current = $conn->query($sql_current);

// Prepare data for charts
$historical_dates = [];
$historical_prices = [];
if ($result_historical->num_rows > 0) {
 while ($row = $result_historical->fetch_assoc()) {
  $historical_dates[] = $row['date'];
  $historical_prices[] = $row['old_price'];
 }
}

$current_dates = [];
$current_prices = [];
if ($result_current->num_rows > 0) {
 while ($row = $result_current->fetch_assoc()) {
  $current_dates[] = $row['production_date'];
  $current_prices[] = $row['new_price'];
 }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Product Data</title>
 <link rel="stylesheet" href="../admin_db2/market_price/style.css">
 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

 <div class="filters">
  <form method="GET" action="">
   <input type="hidden" name="page" value="market_price">
   <select name="product_name">
    <option value="">Select Product Name</option>
    <?php
    if ($result_products->num_rows > 0) {
     while ($row = $result_products->fetch_assoc()) {
      echo "<option value='" . $row['product_name'] . "' " . ($product_name_filter == $row['product_name'] ? 'selected' : '') . ">" . $row['product_name'] . "</option>";
     }
    }
    ?>
   </select>

   <select name="year">
    <option value="">Select Year</option>
    <?php
    for ($year = 2020; $year <= date('Y'); $year++) {
     echo "<option value='$year' " . ($year_filter == $year ? 'selected' : '') . ">$year</option>";
    }
    ?>
   </select>

   <select name="month">
    <option value="">Select Month</option>
    <?php
    for ($month = 1; $month <= 12; $month++) {
     echo "<option value='$month' " . ($month_filter == $month ? 'selected' : '') . ">" . date('F', mktime(0, 0, 0, $month, 1)) . "</option>";
    }
    ?>
   </select>

   <input type="date" name="start_date" value="<?php echo $start_date_filter; ?>" placeholder="Start Date">
   <input type="date" name="end_date" value="<?php echo $end_date_filter; ?>" placeholder="End Date">

   <button type="submit" class="filter-button">Apply Filters</button>
  </form>
 </div>

 <h2>Historical Data</h2>
 <table>
  <thead>
   <tr>
    <th>ID</th>
    <th>Product ID</th>
    <th>Product Name</th>
    <th>Quantity</th>
    <th>Old Price</th>
    <th>Date</th>
    <th>Action</th> <!-- New column for delete button -->
   </tr>
  </thead>
  <tbody>
   <?php
   if ($result_historical->num_rows > 0) {
    foreach ($result_historical as $row) {
     echo "<tr id='row_{$row['product_id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['product_id']}</td>
                            <td>{$row['product_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['old_price']}</td>
                            <td>{$row['date']}</td>
                             <td><button class='delete-btn' data-id='{$row['id']}' data-type='historical'>Delete</button></td>
                        </tr>";
    }
   } else {
    echo "<tr><td colspan='6'>No historical data found.</td></tr>";
   }
   ?>
  </tbody>
 </table>

 <h2>Current Data</h2>
 <table>
  <thead>
   <tr>
    <th>ID</th>
    <th>Product ID</th>
    <th>Product Name</th>
    <th>Quantity</th>
    <th>New Price</th>
    <th>Date</th>
    <th>Action</th> <!-- New column for delete button -->
   </tr>
  </thead>
  <tbody>
   <?php
   if ($result_current->num_rows > 0) {
    foreach ($result_current as $row) {
     echo "<tr id='row_{$row['product_id']}'>
                            <td>{$row['id']}</td>
                            <td>{$row['product_id']}</td>
                            <td>{$row['product_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['new_price']}</td>
                            <td>{$row['production_date']}</td>
                             <td><button class='delete-btn' data-id='{$row['id']}' data-type='current'>Delete</button></td>


                        </tr>";
    }
   } else {
    echo "<tr><td colspan='6'>No current data found.</td></tr>";
   }
   ?>
  </tbody>
 </table>

 <div class="chart-side">
  <div class="chart-container">
   <canvas id="historicalChart"></canvas>
  </div>

  <div class="chart-container">
   <canvas id="currentChart"></canvas>
  </div>
 </div>

 <script>
  const historicalData = {
   labels: <?php echo json_encode($historical_dates); ?>,
   datasets: [{
    label: 'Historical Prices',
    data: <?php echo json_encode($historical_prices); ?>,
    borderColor: 'rgba(75, 192, 192, 1)',
    backgroundColor: 'rgba(75, 192, 192, 0.2)',
    tension: 0.4
   }]
  };

  const currentData = {
   labels: <?php echo json_encode($current_dates); ?>,
   datasets: [{
    label: 'Current Prices',
    data: <?php echo json_encode($current_prices); ?>,
    borderColor: 'rgba(255, 99, 132, 1)',
    backgroundColor: 'rgba(255, 99, 132, 0.2)',
    tension: 0.4
   }]
  };

  const configHistorical = {
   type: 'line',
   data: historicalData
  };

  const configCurrent = {
   type: 'line',
   data: currentData
  };

  new Chart(document.getElementById('historicalChart'), configHistorical);
  new Chart(document.getElementById('currentChart'), configCurrent);



  document.addEventListener('DOMContentLoaded', function() {
   const deleteButtons = document.querySelectorAll('.delete-btn');

   deleteButtons.forEach(button => {
    button.addEventListener('click', function() {
     const id = this.getAttribute('data-id');
     const type = this.getAttribute('data-type');

     // Send AJAX request to delete the row based on the 'id'
     const xhr = new XMLHttpRequest();
     xhr.open('POST', '../admin_db2/market_price/delete.php', true);
     xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
     xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
       // Handle successful deletion
       alert('Deleted successfully!');
       // Remove the row from the table based on the type and id
       const rowId = type === 'historical' ? `historical_row_${id}` : `current_row_${id}`;
       document.getElementById(rowId).remove();
      }
     };
     xhr.send('id=' + id + '&type=' + type);
    });
   });
  });
 </script>



</body>

</html>