<?php
// Include the database connection
include('../config/connect.php');

// Check if the 'id' is passed in the URL (which is for the cropstable table)
if (isset($_GET['id'])) {
  $cropstable_id = $_GET['id'];

  // Query to get data from both tables (crops and cropstable) using the foreign key crop_id
  $query = "SELECT s.*, ss.* FROM cropstable ss 
              INNER JOIN crops s ON ss.crop_id = s.crop_id 
              WHERE ss.id = '$cropstable_id'";

  $result = $conn->query($query);

  // Check if data is found for the cropstable entry
  if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();  // Store the result into $data
  } else {
    echo "crop not found!";
    exit();
  }
}

// Handle form submission to update crop data
if (isset($_POST['update_crop'])) {
  // Get values from the form
  $cropName = $_POST['cropName'];
  $cropCategory = $_POST['cropCategory'];
  $cropQuantity = $_POST['cropQuantity'];
  $cropPrice = $_POST['cropPrice'];
  $cropTotalPrice = $cropQuantity * $cropPrice;
  $cropInventory = $_POST['cropInventory'];
  $cropstorage = $_POST['cropstorage'];
  $cropLogistics = $_POST['cropLogistics'];

  // Prepare and execute update query for cropstable
  $updatecropstableQuery = "UPDATE cropstable 
                              SET quantity = '$cropQuantity', price = '$cropPrice', totalPrice = '$cropTotalPrice', 
                                  inventory = '$cropInventory', storage = '$cropstorage', logistics = '$cropLogistics'
                              WHERE id = '$cropstable_id'";

  if ($conn->query($updatecropstableQuery) === TRUE) {
    // Update successful for cropstable

    // Prepare and execute update query for crops
    $updatecropsQuery = "UPDATE crops 
                             SET crop_name = '$cropName', category = '$cropCategory'
                             WHERE crop_id = (SELECT crop_id FROM cropstable WHERE id = '$cropstable_id')";

    if ($conn->query($updatecropsQuery) === TRUE) {
      echo "crop updated successfully!";
      header('Location: index.php');
    } else {
      echo "Error updating crop in crops table: " . $conn->error;
    }
  } else {
    echo "Error updating crop in cropstable table: " . $conn->error;
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Update crop</title>
  <link rel="stylesheet" href="updateStyle.css">
</head>

<body>
  <h1>Update crop</h1><br>
  <a href="./index.php"><button style="margin-bottom: 20px; margin-left:20px">Back</button></a>
  <!-- Form to update crop data -->
  <form method="POST" action="">

    <!-- crop Name (from crops table) -->
    <label for="cropName">crop Name:</label>
    <input type="text" id="cropName" name="cropName" value="<?php echo isset($data['crop_name']) ? htmlspecialchars($data['crop_name']) : ''; ?>" readonly><br>

    <!-- Category (from crops table) -->
    <label for="cropCategory">Category:</label>
    <input type="text" id="cropCategory" name="cropCategory" value="<?php echo isset($data['category']) ? htmlspecialchars($data['category']) : ''; ?>" readonly><br>

    <!-- Quantity (from cropstable table) -->
    <label for="cropQuantity">Quantity:</label>
    <input type="number" id="cropQuantity" name="cropQuantity" value="<?php echo isset($data['quantity']) ? htmlspecialchars($data['quantity']) : ''; ?>" required><br>

    <!-- Price (from cropstable table) -->
    <label for="cropPrice">Price:</label>
    <input type="number" id="cropPrice" name="cropPrice" value="<?php echo isset($data['price']) ? htmlspecialchars($data['price']) : ''; ?>" required><br>

    <!-- Total Price (calculated based on quantity and price) -->
    <label for="cropTotalPrice">Total Price:</label>
    <input type="number" id="cropTotalPrice" name="cropTotalPrice" value="<?php echo isset($data['totalPrice']) ? htmlspecialchars($data['totalPrice']) : ''; ?>" readonly><br>

    <!-- Inventory (from cropstable table) -->
    <label for="cropInventory">Inventory:</label>
    <select id="cropInventory" name="cropInventory" required>
      <option value="low" <?php echo (isset($data['inventory']) && $data['inventory'] == 'low') ? 'selected' : ''; ?>>Low (Below 100 units)</option>
      <option value="medium" <?php echo (isset($data['inventory']) && $data['inventory'] == 'medium') ? 'selected' : ''; ?>>Medium (100-500 units)</option>
      <option value="high" <?php echo (isset($data['inventory']) && $data['inventory'] == 'high') ? 'selected' : ''; ?>>High (Above 500 units)</option>
    </select><br>

    <!-- Storage (from cropstable table) -->
    <label for="cropstorage">Storage:</label>
    <select id="cropstorage" name="cropstorage" required>
      <option value="cold_storage" <?php echo (isset($data['storage']) && $data['storage'] == 'cold_storage') ? 'selected' : ''; ?>>Cold Storage</option>
      <option value="dry_warehouse" <?php echo (isset($data['storage']) && $data['storage'] == 'dry_warehouse') ? 'selected' : ''; ?>>Dry Warehouse</option>
      <option value="open_yard" <?php echo (isset($data['storage']) && $data['storage'] == 'open_yard') ? 'selected' : ''; ?>>Open Yard</option>
    </select><br>

    <!-- Logistics (from cropstable table) -->
    <label for="cropLogistics">Logistics:</label>
    <select id="cropLogistics" name="cropLogistics" required>
      <option value="road" <?php echo (isset($data['logistics']) && $data['logistics'] == 'road') ? 'selected' : ''; ?>>Road Transport</option>
      <option value="rail" <?php echo (isset($data['logistics']) && $data['logistics'] == 'rail') ? 'selected' : ''; ?>>Rail Transport</option>
      <option value="sea" <?php echo (isset($data['logistics']) && $data['logistics'] == 'sea') ? 'selected' : ''; ?>>Sea Freight</option>
      <option value="air" <?php echo (isset($data['logistics']) && $data['logistics'] == 'air') ? 'selected' : ''; ?>>Air Freight</option>
    </select><br>

    <button type="submit" name="update_crop">Update crop</button>
  </form>

</body>

</html>