<?php
// Include the database connection
include('../config/connect.php');

// Check if the 'id' is passed in the URL (which is for the seedstable table)
if (isset($_GET['id'])) {
 $seedstable_id = $_GET['id'];

 // Query to get data from both tables (seeds and seedstable) using the foreign key seed_id
 $query = "SELECT s.*, ss.* FROM seedstable ss 
              INNER JOIN seeds s ON ss.seed_id = s.seed_id 
              WHERE ss.id = '$seedstable_id'";

 $result = $conn->query($query);

 // Check if data is found for the seedstable entry
 if ($result->num_rows > 0) {
  $data = $result->fetch_assoc();  // Store the result into $data
 } else {
  echo "Seed not found!";
  exit();
 }
}

// Handle form submission to update seed data
if (isset($_POST['update_seed'])) {
 // Get values from the form
 $seedName = $_POST['seedName'];
 $seedCategory = $_POST['seedCategory'];
 $seedQuantity = $_POST['seedQuantity'];
 $seedPrice = $_POST['seedPrice'];
 $seedTotalPrice = $seedQuantity * $seedPrice;
 $seedInventory = $_POST['seedInventory'];
 $seedStorage = $_POST['seedStorage'];
 $seedLogistics = $_POST['seedLogistics'];

 // Prepare and execute update query for seedstable
 $updateSeedstableQuery = "UPDATE seedstable 
                              SET quantity = '$seedQuantity', price = '$seedPrice', totalPrice = '$seedTotalPrice', 
                                  inventory = '$seedInventory', storage = '$seedStorage', logistics = '$seedLogistics'
                              WHERE id = '$seedstable_id'";

 if ($conn->query($updateSeedstableQuery) === TRUE) {
  // Update successful for seedstable

  // Prepare and execute update query for seeds
  $updateSeedsQuery = "UPDATE seeds 
                             SET seed_name = '$seedName', category = '$seedCategory'
                             WHERE seed_id = (SELECT seed_id FROM seedstable WHERE id = '$seedstable_id')";

  if ($conn->query($updateSeedsQuery) === TRUE) {
   echo "Seed updated successfully!";
   header('Location: index2.php');
  } else {
   echo "Error updating seed in seeds table: " . $conn->error;
  }
 } else {
  echo "Error updating seed in seedstable table: " . $conn->error;
 }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <title>Update Seed</title>
 <link rel="stylesheet" href="updateStyle.css">
</head>

<body>
 <h1>Update Seed</h1><br>
 <a href="./index2.php"><button style="margin-bottom: 20px; margin-left:20px">Back</button></a>
 <!-- Form to update seed data -->
 <form method="POST" action="">
 
  <!-- Seed Name (from seeds table) -->
  <label for="seedName">Seed Name:</label>
  <input type="text" id="seedName" name="seedName" value="<?php echo isset($data['seed_name']) ? htmlspecialchars($data['seed_name']) : ''; ?>" readonly><br>

  <!-- Category (from seeds table) -->
  <label for="seedCategory">Category:</label>
  <input type="text" id="seedCategory" name="seedCategory" value="<?php echo isset($data['category']) ? htmlspecialchars($data['category']) : ''; ?>" readonly><br>

  <!-- Quantity (from seedstable table) -->
  <label for="seedQuantity">Quantity:</label>
  <input type="number" id="seedQuantity" name="seedQuantity" value="<?php echo isset($data['quantity']) ? htmlspecialchars($data['quantity']) : ''; ?>" required><br>

  <!-- Price (from seedstable table) -->
  <label for="seedPrice">Price:</label>
  <input type="number" id="seedPrice" name="seedPrice" value="<?php echo isset($data['price']) ? htmlspecialchars($data['price']) : ''; ?>" required><br>

  <!-- Total Price (calculated based on quantity and price) -->
  <label for="seedTotalPrice">Total Price:</label>
  <input type="number" id="seedTotalPrice" name="seedTotalPrice" value="<?php echo isset($data['totalPrice']) ? htmlspecialchars($data['totalPrice']) : ''; ?>" readonly><br>

  <!-- Inventory (from seedstable table) -->
  <label for="seedInventory">Inventory:</label>
  <select id="seedInventory" name="seedInventory" required>
   <option value="low" <?php echo (isset($data['inventory']) && $data['inventory'] == 'low') ? 'selected' : ''; ?>>Low (Below 100 units)</option>
   <option value="medium" <?php echo (isset($data['inventory']) && $data['inventory'] == 'medium') ? 'selected' : ''; ?>>Medium (100-500 units)</option>
   <option value="high" <?php echo (isset($data['inventory']) && $data['inventory'] == 'high') ? 'selected' : ''; ?>>High (Above 500 units)</option>
  </select><br>

  <!-- Storage (from seedstable table) -->
  <label for="seedStorage">Storage:</label>
  <select id="seedStorage" name="seedStorage" required>
   <option value="cold_storage" <?php echo (isset($data['storage']) && $data['storage'] == 'cold_storage') ? 'selected' : ''; ?>>Cold Storage</option>
   <option value="dry_warehouse" <?php echo (isset($data['storage']) && $data['storage'] == 'dry_warehouse') ? 'selected' : ''; ?>>Dry Warehouse</option>
   <option value="open_yard" <?php echo (isset($data['storage']) && $data['storage'] == 'open_yard') ? 'selected' : ''; ?>>Open Yard</option>
  </select><br>

  <!-- Logistics (from seedstable table) -->
  <label for="seedLogistics">Logistics:</label>
  <select id="seedLogistics" name="seedLogistics" required>
   <option value="road" <?php echo (isset($data['logistics']) && $data['logistics'] == 'road') ? 'selected' : ''; ?>>Road Transport</option>
   <option value="rail" <?php echo (isset($data['logistics']) && $data['logistics'] == 'rail') ? 'selected' : ''; ?>>Rail Transport</option>
   <option value="sea" <?php echo (isset($data['logistics']) && $data['logistics'] == 'sea') ? 'selected' : ''; ?>>Sea Freight</option>
   <option value="air" <?php echo (isset($data['logistics']) && $data['logistics'] == 'air') ? 'selected' : ''; ?>>Air Freight</option>
  </select><br>

  <button type="submit" name="update_seed">Update Seed</button>
 </form>

</body>

</html>