<?php
// Database connection
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// Check if seed_id is set in the URL
if (isset($_GET['seed_id'])) {
 $seedID = $_GET['seed_id'];

 // Fetch the seed data based on the seed_id
 $sql = "SELECT * FROM seedstable WHERE id = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("i", $seedID); // Bind the seed ID to the query
 $stmt->execute();
 $result = $stmt->get_result();

 // Check if the seed exists
 if ($result->num_rows > 0) {
  $seed = $result->fetch_assoc();
 } else {
  echo "No seed found with ID $seedID";
  exit();
 }
} else {
 echo "No seed ID provided!";
 exit();
}

// Check if seed_id is set in the URL
if (isset($_GET['seed_id'])) {
 $seedID = $_GET['seed_id'];

 // Fetch the seed data based on the seed_id
 $sql = "SELECT * FROM seedstable WHERE id = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("i", $seedID); // Bind the seed ID to the query
 $stmt->execute();
 $result = $stmt->get_result();

 // Check if the seed exists
 if ($result->num_rows > 0) {
  $seed = $result->fetch_assoc();
 } else {
  echo "No seed found with ID $seedID";
  exit();
 }
} else {
 echo "No seed ID provided!";
 exit();
}

// Handle the update request seeds
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_seed"])) {
 $name = $_POST["seedName"];
 $category = $_POST["seedCategory"];
 $season = $_POST["seedSeason"];
 $region = $_POST["seedRegion"];
 $quantity = $_POST["seedQuantity"];
 $price = $_POST["seedPrice"];
 $totalPrice = $_POST["totalPrice"];
 $inventory = $_POST["seedInventory"];
 $storage = $_POST["seedStorage"];
 $logistics = $_POST["seedLogistics"];

 // Update the seed details
 $sql = "UPDATE seedstable SET name=?, category=?, season=?, region=?, quantity=?, price=?, totalPrice=?,inventory=?, storage=?, logistics=? WHERE id=?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("ssssdddsssi", $name, $category, $season, $region, $quantity, $price, $totalPrice, $inventory, $storage, $logistics, $seedID);

 if ($stmt->execute()) {
  header("Location: index2.php"); // Redirect back to the seeds list
  exit();
 } else {
  echo "Error: " . $stmt->error;
 }
}

// Handle the update request for seeds
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_seed"])) {
 $name = $_POST["seedName"];
 $category = $_POST["seedCategory"];
 $season = $_POST["seedSeason"];
 $region = $_POST["seedRegion"];
 $quantity = $_POST["seedQuantity"];
 $price = $_POST["seedPrice"];
 $totalPrice = $_POST["totalPrice"];
 $inventory = $_POST["seedInventory"];
 $storage = $_POST["seedStorage"];
 $logistics = $_POST["seedLogistics"];

 // Update the seed details
 $sql = "UPDATE seedssstable SET name=?, category=?, season=?, region=?, quantity=?, price=?, totalPrice=?,inventory=?, storage=?, logistics=? WHERE id=?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("ssssdddsdsi", $name, $category, $season, $region, $quantity, $price, $totalPrice, $inventory, $storage, $logistics, $seedID);

 if ($stmt->execute()) {
  header("Location: index2.php"); // Redirect back to the seeds list
  exit();
 } else {
  echo "Error: " . $stmt->error;
 }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link rel="stylesheet" href="styles.css">
 <title>Update</title>
</head>

<body>
 <div class="updateform-container">
  <h1>Update Seed</h1>
  <a href="./index2.php">
   <button class="btn">Back</button>
  </a>
  <form id="seedForm" method="POST" action="">
   <label for="seedName">Name:</label>
   <input type="text" id="seedName" name="seedName" value="<?= htmlspecialchars($seed['name']); ?>" required><br>

   <label for="seedCategory">Category:</label>
   <select id="seedCategory" name="seedCategory" required>
    <option value="Grains & Cereals" <?= ($seed['category'] == 'Grains & Cereals') ? 'selected' : ''; ?>>Grains & Cereals</option>
    <option value="Fruits" <?= ($seed['category'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
    <option value="Vegetables" <?= ($seed['category'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
    <!-- Add more options as needed -->
   </select><br>

   <label for="seedSeason">Season:</label>
   <input type="text" id="seedSeason" name="seedSeason" value="<?= htmlspecialchars($seed['season']); ?>" required><br>

   <label for="seedRegion">Region:</label>
   <input type="text" id="seedRegion" name="seedRegion" value="<?= htmlspecialchars($seed['region']); ?>" required><br>

   <label for="seedQuantity">Quantity:</label>
   <input type="number" id="seedQuantity" name="seedQuantity" value="<?= htmlspecialchars($seed['quantity']); ?>" required><br>

   <label for="seedPrice">Price:</label>
   <input type="number" id="seedPrice" name="seedPrice" value="<?= htmlspecialchars($seed['price']); ?>" required><br>

   <label for="totalPrice">Total Price:</label>
   <input type="number" id="totalPrice" name="totalPrice" value="<?= htmlspecialchars($seed['totalPrice']); ?>" readonly><br>

   <label for="seedInventory">Inventory:</label>
   <select id="seedInventory" name="seedInventory">
    <option value="low">Low (Below 100 units)</option>
    <option value="medium">Medium (100-500 units)</option>
    <option value="high">High (Above 500 units)</option>
   </select>
   <!-- <input type="number" id="seedInventory" name="seedInventory" value="<?= htmlspecialchars($seed['inventory']); ?>" required><br> -->

   <label for="seedStorage">Storage:</label>
   <select id="seedStorage" name="seedStorage">
    <option value="cold_storage">Cold Storage</option>
    <option value="dry_warehouse">Dry Warehouse</option>
    <option value="open_yard">Open Yard</option>
   </select>
   <!-- <input type="text" id="seedStorage" name="seedStorage" value="<?= htmlspecialchars($seed['storage']); ?>" required><br> -->

   <label for="seedLogistics">Logistics:</label>
   <select id="seedLogistics" name="seedLogistics">
    <option value="road">Road Transport</option>
    <option value="rail">Rail Transport</option>
    <option value="sea">Sea Freight</option>
    <option value="air">Air Freight</option>
   </select>
   <!-- <input type="text" id="seedLogistics" name="seedLogistics" value="<?= htmlspecialchars($seed['logistics']); ?>" required><br> -->

   <button type="submit" name="update_seed">Update seed</button>
  </form>
 </div>

 <script>
  // Function to calculate total price based on quantity and price per unit
  function calculateTotalPrice() {
   var quantity = document.getElementById('seedQuantity').value;
   var price = document.getElementById('seedPrice').value;
   var totalPriceField = document.getElementById('totalPrice');

   // Calculate total price if both quantity and price are provided
   if (quantity && price) {
    var totalPrice = parseFloat(quantity) * parseFloat(price);
    totalPriceField.value = totalPrice.toFixed(2); // Set the total price (fixed to 2 decimals)
   } else {
    totalPriceField.value = ''; // Clear the total price if input is invalid
   }
  }

  // Add event listeners to update total price when quantity or price changes
  document.getElementById('seedQuantity').addEventListener('input', calculateTotalPrice);
  document.getElementById('seedPrice').addEventListener('input', calculateTotalPrice);
 </script>

</body>

</html>