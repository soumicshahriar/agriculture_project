<?php
// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
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
   <div class="block-content"><?php
                              // Include another PHP file
                              include('../admin_db/product_table/product_info.php');
                              ?></div>
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
   <div class="block-header">Consumer Demand Data</div>
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
  function showBlock(blockId) {
   // Hide all blocks and show the selected block
   const blocks = document.querySelectorAll('.block');
   blocks.forEach(block => {
    block.classList.remove('active');
   });
   document.getElementById(blockId).classList.add('active');
  }

  // Initial Chart.js setup for different blocks
  window.onload = function() {
   // Product Information Chart
   const productCtx = document.getElementById('productChart').getContext('2d');
   new Chart(productCtx, {
    type: 'bar',
    data: {
     labels: ['Jan', 'Feb', 'Mar', 'Apr'],
     datasets: [{
      label: 'Product Data',
      data: [50, 100, 150, 200],
      backgroundColor: 'rgba(75, 192, 192, 0.2)',
      borderColor: 'rgba(75, 192, 192, 1)',
      borderWidth: 1
     }]
    },
    options: {
     responsive: true
    }
   });

   // Historical Production Chart
   const historicalCtx = document.getElementById('historicalChart').getContext('2d');
   new Chart(historicalCtx, {
    type: 'line',
    data: {
     labels: ['2019', '2020', '2021', '2022'],
     datasets: [{
      label: 'Production Data',
      data: [500, 600, 700, 800],
      borderColor: 'rgba(153, 102, 255, 1)',
      borderWidth: 1
     }]
    },
    options: {
     responsive: true
    }
   });

   // Consumer Demand Chart
   const consumerCtx = document.getElementById('consumerChart').getContext('2d');
   new Chart(consumerCtx, {
    type: 'pie',
    data: {
     labels: ['Low Demand', 'Medium Demand', 'High Demand'],
     datasets: [{
      label: 'Demand Data',
      data: [30, 50, 20],
      backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)'],
      borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)'],
      borderWidth: 1
     }]
    },
    options: {
     responsive: true
    }
   });

   // Real-Time Supply Chart
   const supplyCtx = document.getElementById('supplyChart').getContext('2d');
   new Chart(supplyCtx, {
    type: 'doughnut',
    data: {
     labels: ['Available', 'Low Stock', 'Out of Stock'],
     datasets: [{
      label: 'Supply Levels',
      data: [70, 20, 10],
      backgroundColor: ['rgba(75, 192, 192, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(255, 99, 132, 0.2)'],
      borderColor: ['rgba(75, 192, 192, 1)', 'rgba(255, 159, 64, 1)', 'rgba(255, 99, 132, 1)'],
      borderWidth: 1
     }]
    },
    options: {
     responsive: true
    }
   });

   // Market Price Chart
   const marketPriceCtx = document.getElementById('marketPriceChart').getContext('2d');
   new Chart(marketPriceCtx, {
    type: 'bar',
    data: {
     labels: ['Jan', 'Feb', 'Mar', 'Apr'],
     datasets: [{
      label: 'Market Prices',
      data: [100, 150, 120, 180],
      backgroundColor: 'rgba(255, 99, 132, 0.2)',
      borderColor: 'rgba(255, 99, 132, 1)',
      borderWidth: 1
     }]
    },
    options: {
     responsive: true
    }
   });

   // Analytical Tools Chart
   const analyticalCtx = document.getElementById('analyticalChart').getContext('2d');
   new Chart(analyticalCtx, {
    type: 'radar',
    data: {
     labels: ['Scenario 1', 'Scenario 2', 'Scenario 3', 'Scenario 4'],
     datasets: [{
      label: 'Analysis Data',
      data: [60, 70, 80, 90],
      borderColor: 'rgba(153, 102, 255, 1)',
      borderWidth: 1,
      fill: true
     }]
    },
    options: {
     responsive: true
    }
   });
  };

  // Toggle Sidebar for Mobile/Tablet
  // JavaScript to toggle sidebar visibility
  function toggleSidebar() {
   const sidebar = document.querySelector('.sidebar');
   const content = document.querySelector('.content');
   sidebar.classList.toggle('open');
   content.classList.toggle('open');
  }

  document.querySelectorAll(".sidebar-link").forEach(link => {
   link.addEventListener("click", (event) => {
    event.preventDefault(); // prevent the default link behavior
    let url = event.target.href; // get the link URL
    // Use AJAX to load content without a full page reload
    // Example AJAX request:
    fetch(url)
     .then(response => response.text())
     .then(data => {
      document.getElementById('content').innerHTML = data;
     });
   });
  });
 </script>
</body>

</html>