<?php
 // Default to home if no page is selected
 $page = isset($_GET['page']) ? $_GET['page'] : 'home';
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Admin Dashboard</title>
 <link rel="stylesheet" href="styles.css">
</head>

<body>
 <div class="dashboard-container">
  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
   <ul>
    <li><a href="?page=home" class="<?php echo ($page == 'home') ? 'active' : ''; ?>">Home (Graph & Charts)</a></li>
    <li><a href="?page=product" class="<?php echo ($page == 'product') ? 'active' : ''; ?>">Product</a></li>
    <li><a href="?page=history" class="<?php echo ($page == 'history') ? 'active' : ''; ?>">History</a></li>
    <li><a href="?page=consumer_demand" class="<?php echo ($page == 'consumer_demand') ? 'active' : ''; ?>">Consumer Demand</a></li>
    <li><a href="?page=real_time_supply" class="<?php echo ($page == 'real_time_supply') ? 'active' : ''; ?>">Real-Time Supply</a></li>
    <li><a href="?page=market_price" class="<?php echo ($page == 'market_price') ? 'active' : ''; ?>">Market Price</a></li>
   </ul>
  </div>

  <!-- Main Content -->
  <div class="content">
   <!-- Toggle Button -->
   <button class="toggle-btn" id="toggle-btn">☰</button>
   <?php
   switch ($page) {
    case 'product':
     include('../admin_db2/product_table/product_info.php');
     break;
    case 'history':
     include('../admin_db2/historycal-data/history.php');
     break;
    case 'consumer_demand':
     include('../admin_db2/consumer_demand/consumer_demand.php');
     break;
    case 'real_time_supply':
     include('real_time_supply.php');
     break;
    case 'market_price':
     include('market_price.php');
     break;
    case 'home':
    default:
     include('home.php');
     break;
   }
   ?>
  </div>
 </div>

 <script src="script.js"></script>
</body>

</html>