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
    header("Location: index.php"); // Redirect back to the crops list
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
  $stmt->bind_param("ssssdddddsi", $name, $category, $season, $region, $quantity, $price, $totalPrice, $inventory, $storage, $logistics, $cropID);

  if ($stmt->execute()) {
    header("Location: index.php"); // Redirect back to the crops list
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
    <h1>Update Crop</h1>
    <a href="./index.php">
      <button class="btn">Back</button>
    </a>
    <form id="cropForm" method="POST" action="">


      <label for="cropName">Name:</label>
      <select id="cropName" name="cropName">
        <option value="Wheat">Wheat</option>
        <option value="Rice">Rice</option>
        <option value="Corn">Corn</option>
        <option value="Soybean">Soybean</option>
        <option value="Barley">Barley</option>
        <option value="Millet">Millet</option>
        <option value="Oats">Oats</option>
        <option value="Sorghum">Sorghum</option>
        <option value="Sunflower">Sunflower</option>
        <option value="Canola">Canola</option>
        <option value="Peas">Peas</option>
        <option value="Lentils">Lentils</option>
        <option value="Alfalfa">Alfalfa</option>
        <option value="Clover">Clover</option>
        <option value="Quinoa">Quinoa</option>
        <option value="Chia">Chia</option>
        <option value="Flaxseed">Flaxseed</option>
        <option value="Hemp">Hemp</option>
        <option value="Sesame">Sesame</option>
        <option value="Safflower">Safflower</option>
      </select><br>
      <!-- <input type="text" id="cropName" name="cropName" value="<?= htmlspecialchars($crop['name']); ?>" required><br> -->

      <label for="cropCategory">Category:</label>
      <select id="cropCategory" name="cropCategory" required>

        <option value="">Select</option>
        <option value="Grains & Cereals">Grains & Cereals</option>
        <option value="Fruits">Fruits</option>
        <option value="Vegetables">Vegetables</option>
        <option value="Dairy Products">Dairy Products</option>
        <option value="Meat & Poultry"> Meat & Poultry</option>
        <option value="Seafood"> Seafood</option>
        <option value=" Herbs & Spices"> Herbs & Spices</option>
        <option value="Nuts & Seeds"> Nuts & Seeds</option>
        <option value=" Beverages"> Beverages</option>
        <option value="Oils & Fats"> Oils & Fats</option>

      </select><br>

      <label for="cropSeason">Season:</label>
      <select id="cropSeason" name="cropSeason" required>
        <option value="Summer">Summer</option>
        <option value="Monsoon">Monsoon</option>
        <option value="Autumn">Autumn</option>
        <option value="Late Autumn">Late Autumn</option>
        <option value="Winter">Winter</option>
        <option value="Spring">Spring</option>
        <option value="All Year Round">All Year Round</option>
      </select>
      <!-- <input type="text" id="cropSeason" name="cropSeason" value="<?= htmlspecialchars($crop['season']); ?>" required><br> -->

      <label for="cropRegion">Region:</label>
      <select id="cropRegion" name="cropRegion" required>
        <option value="Dhaka">Dhaka</option>
        <option value="Chittagong">Chittagong</option>
        <option value="Rajshahi">Rajshahi</option>
        <option value="Khulna">Khulna</option>
        <option value="Barisal">Barisal</option>
        <option value="Sylhet">Sylhet</option>
        <option value="Rangpur">Rangpur</option>
        <option value="Mymensingh">Mymensingh</option>
        <option value="Comilla">Comilla</option>
        <option value="Gazipur">Gazipur</option>
        <option value="Narail">Narail</option>
        <option value="Bogra">Bogra</option>
        <option value="Jessore">Jessore</option>
        <option value="Pabna">Pabna</option>
        <option value="Dinajpur">Dinajpur</option>
        <option value="Faridpur">Faridpur</option>
        <option value="Tangail">Tangail</option>
        <option value="Narayanganj">Narayanganj</option>
        <option value="Jamalpur">Jamalpur</option>
        <option value="Kushtia">Kushtia</option>
      </select><br>
      <!-- <input type="text" id="cropRegion" name="cropRegion" value="<?= htmlspecialchars($crop['region']); ?>" required><br> -->

      <label for="cropQuantity">Unit(Quantity):</label>
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
      </select><br>

      <!-- <input type="number" id="cropInventory" name="cropInventory" value="<?= htmlspecialchars($crop['inventory']); ?>" required><br> -->

      <label for="cropStorage">Storage:</label>
      <select id="cropStorage" name="cropStorage">
        <option value="cold_storage">Cold Storage</option>
        <option value="dry_warehouse">Dry Warehouse</option>
        <option value="open_yard">Open Yard</option>
      </select>

      <!-- <label for="cropStorage">Storage:</label>
 <select id="cropStorage" name="cropStorage" required>
  <option value="cold_storage" <?= ($crop['storage'] == 'cold_storage') ? 'selected' : ''; ?>>Cold Storage</option>
  <option value="dry_warehouse" <?= ($crop['storage'] == 'dry_warehouse') ? 'selected' : ''; ?>>Dry Warehouse</option>
  <option value="open_yard" <?= ($crop['storage'] == 'open_yard') ? 'selected' : ''; ?>>Open Yard</option>
 </select><br> -->
      <!-- <input type="text" id="cropStorage" name="cropStorage" value="<?= htmlspecialchars($crop['storage']); ?>" required><br> -->

      <label for="cropLogistics">Logistics:</label>
      <select id="cropLogistics" name="cropLogistics">
        <option value="road">Road Transport</option>
        <option value="rail">Rail Transport</option>
        <option value="sea">Sea Freight</option>
        <option value="air">Air Freight</option>
      </select><br>
      <!-- <input type="text" id="cropLogistics" name="cropLogistics" value="<?= htmlspecialchars($crop['logistics']); ?>" required><br> -->

      <button type="submit" name="update_crop">Update Crop</button>
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