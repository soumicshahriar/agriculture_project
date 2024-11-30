<?php
// Database connection
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if crop_id is set in the URL
if (isset($_GET['crop_id'])) {
  $cropID = $_GET['crop_id'];

  // Fetch the crop data based on the crop_id
  $sql = "SELECT * FROM cropstable WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $cropID); // Bind the crop ID to the query
  $stmt->execute();
  $result = $stmt->get_result();

  // Check if the crop exists
  if ($result->num_rows > 0) {
    $crop = $result->fetch_assoc();
  } else {
    echo "No crop found with ID $cropID";
    exit();
  }
} else {
  echo "No crop ID provided!";
  exit();
}

// Check if crop_id is set in the URL
if (isset($_GET['crop_id'])) {
  $cropID = $_GET['crop_id'];

  // Fetch the crop data based on the crop_id
  $sql = "SELECT * FROM cropstable WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $cropID); // Bind the crop ID to the query
  $stmt->execute();
  $result = $stmt->get_result();

  // Check if the crop exists
  if ($result->num_rows > 0) {
    $crop = $result->fetch_assoc();
  } else {
    echo "No crop found with ID $cropID";
    exit();
  }
} else {
  echo "No crop ID provided!";
  exit();
}

// Handle the update request crops
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_crop"])) {
  $name = $_POST["cropName"];
  $category = $_POST["cropCategory"];
  $season = $_POST["cropSeason"];
  $region = $_POST["cropRegion"];
  $quantity = $_POST["cropQuantity"];
  $price = $_POST["cropPrice"];
  $totalPrice = $_POST["totalPrice"];
  $inventory = $_POST["cropInventory"];
  $storage = $_POST["cropStorage"];
  $logistics = $_POST["cropLogistics"];

  // Update the crop details
  $sql = "UPDATE cropstable SET name=?, category=?, season=?, region=?, quantity=?, price=?, totalPrice=?,inventory=?, storage=?, logistics=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssssdddsssi", $name, $category, $season, $region, $quantity, $price, $totalPrice, $inventory, $storage, $logistics, $cropID);

  if ($stmt->execute()) {
    header("Location: index2.php"); // Redirect back to the crops list
    exit();
  } else {
    echo "Error: " . $stmt->error;
  }
}

// Handle the update request for crops
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_crop"])) {
  $name = $_POST["cropName"];
  $category = $_POST["cropCategory"];
  $season = $_POST["cropSeason"];
  $region = $_POST["cropRegion"];
  $quantity = $_POST["cropQuantity"];
  $price = $_POST["cropPrice"];
  $totalPrice = $_POST["totalPrice"];
  $inventory = $_POST["cropInventory"];
  $storage = $_POST["cropStorage"];
  $logistics = $_POST["cropLogistics"];

  // Update the crop details
  $sql = "UPDATE cropssstable SET name=?, category=?, season=?, region=?, quantity=?, price=?, totalPrice=?,inventory=?, storage=?, logistics=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ssssdddsdsi", $name, $category, $season, $region, $quantity, $price, $totalPrice, $inventory, $storage, $logistics, $cropID);

  if ($stmt->execute()) {
    header("Location: index2.php"); // Redirect back to the crops list
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
    <h1>Update crop</h1>
    <a href="./index2.php">
      <button class="btn">Back</button>
    </a>
    <form id="cropForm" method="POST" action="">
      <label for="cropName">Name:</label>
      <input type="text" id="cropName" name="cropName" value="<?= htmlspecialchars($crop['name']); ?>" required><br>

      <label for="cropCategory">Category:</label>
      <select id="cropCategory" name="cropCategory" required>
        <option value="Grains & Cereals" <?= ($crop['category'] == 'Grains & Cereals') ? 'selected' : ''; ?>>Grains & Cereals</option>
        <option value="Fruits" <?= ($crop['category'] == 'Fruits') ? 'selected' : ''; ?>>Fruits</option>
        <option value="Vegetables" <?= ($crop['category'] == 'Vegetables') ? 'selected' : ''; ?>>Vegetables</option>
        <!-- Add more options as needed -->
      </select><br>

      <label for="cropSeason">Season:</label>
      <input type="text" id="cropSeason" name="cropSeason" value="<?= htmlspecialchars($crop['season']); ?>" required><br>

      <label for="cropRegion">Region:</label>
      <input type="text" id="cropRegion" name="cropRegion" value="<?= htmlspecialchars($crop['region']); ?>" required><br>

      <label for="cropQuantity">Quantity:</label>
      <input type="number" id="cropQuantity" name="cropQuantity" value="<?= htmlspecialchars($crop['quantity']); ?>" required><br>

      <label for="cropPrice">Price:</label>
      <input type="number" id="cropPrice" name="cropPrice" value="<?= htmlspecialchars($crop['price']); ?>" required><br>

      <label for="totalPrice">Total Price:</label>
      <input type="number" id="totalPrice" name="totalPrice" value="<?= htmlspecialchars($crop['totalPrice']); ?>" readonly><br>

      <label for="cropInventory">Inventory:</label>
      <select id="cropInventory" name="cropInventory">
        <option value="low">Low (Below 100 units)</option>
        <option value="medium">Medium (100-500 units)</option>
        <option value="high">High (Above 500 units)</option>
      </select>
      <!-- <input type="number" id="cropInventory" name="cropInventory" value="<?= htmlspecialchars($crop['inventory']); ?>" required><br> -->

      <label for="cropStorage">Storage:</label>
      <select id="cropStorage" name="cropStorage">
        <option value="cold_storage">Cold Storage</option>
        <option value="dry_warehouse">Dry Warehouse</option>
        <option value="open_yard">Open Yard</option>
      </select>
      <!-- <input type="text" id="cropStorage" name="cropStorage" value="<?= htmlspecialchars($crop['storage']); ?>" required><br> -->

      <label for="cropLogistics">Logistics:</label>
      <select id="cropLogistics" name="cropLogistics">
        <option value="road">Road Transport</option>
        <option value="rail">Rail Transport</option>
        <option value="sea">Sea Freight</option>
        <option value="air">Air Freight</option>
      </select>
      <!-- <input type="text" id="cropLogistics" name="cropLogistics" value="<?= htmlspecialchars($crop['logistics']); ?>" required><br> -->

      <button type="submit" name="update_crop">Update crop</button>
    </form>
  </div>

  <script>
    // Function to calculate total price based on quantity and price per unit
    function calculateTotalPrice() {
      var quantity = document.getElementById('cropQuantity').value;
      var price = document.getElementById('cropPrice').value;
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
    document.getElementById('cropQuantity').addEventListener('input', calculateTotalPrice);
    document.getElementById('cropPrice').addEventListener('input', calculateTotalPrice);
  </script>

</body>

</html>