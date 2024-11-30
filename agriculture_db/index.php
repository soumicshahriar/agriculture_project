<?php

include('../config/connect.php');
// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// Fetch all warehouse managers
$managersResult = $conn->query("SELECT * FROM warehouse_manager");

// Insert crop data into cropstable
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_crop"])) {
 $managerID = $_POST["managerIDCrop"];
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

 $sql = "INSERT INTO cropstable (warehouse_managerID, name, category, season, region, quantity, price, totalPrice, inventory, storage, logistics) 
            VALUES ('$managerID', '$name', '$category', '$season', '$region', '$quantity', '$price', '$totalPrice', '$inventory', '$storage', '$logistics')";

 if ($conn->query($sql) === TRUE) {
  header("Location: index.php"); // Refresh the page to update the table
  exit();
 } else {
  echo "Error: " . $sql . "<br>" . $conn->error;
 }
}


// Update crop data in cropstable
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_crop"])) {
 $cropID = $_POST["cropID"];
 $name = $_POST["cropName"];
 $category = $_POST["cropCategory"];
 $season = $_POST["cropSeason"];
 $region = $_POST["cropRegion"];
 $quantity = $_POST["cropQuantity"];
 $price = $_POST["cropPrice"];
 $totalPrice = $_POST["cropTotalPrice"];
 $inventory = $_POST["cropInventory"];
 $storage = $_POST["cropStorage"];
 $logistics = $_POST["cropLogistics"];

 $sql = "UPDATE cropstable SET 
         name='$name', category='$category', season='$season', region='$region', quantity='$quantity', price='$price',  totalPrice='$totalPrice',
         inventory='$inventory', storage='$storage', logistics='$logistics' 
         WHERE id='$cropID'";

 if ($conn->query($sql) === TRUE) {
  header("Location: index.php"); // Refresh the page to update the table
  exit();
 } else {
  echo "Error: " . $sql . "<br>" . $conn->error;
 }
}

// Delete crop from cropstable
if (isset($_GET["delete_crop"])) {
 $cropID = $_GET["delete_crop"];

 $sql = "DELETE FROM cropstable WHERE id='$cropID'";

 if ($conn->query($sql) === TRUE) {
  header("Location: index.php"); // Refresh the page to update the table
  exit();
 } else {
  echo "Error: " . $sql . "<br>" . $conn->error;
 }
}

// Fetch crops for a selected manager
$selectedManagerID = isset($_POST['managerID']) ? $_POST['managerID'] : null;
$crops = null;
$managerInfo = null;

if ($selectedManagerID) {
 // Fetch manager information
 $managerQuery = "SELECT * FROM warehouse_manager WHERE id = $selectedManagerID";
 $managerResult = $conn->query($managerQuery);
 if ($managerResult->num_rows > 0) {
  $managerInfo = $managerResult->fetch_assoc();
 }


 // Fetch crops related to the selected manager
 $cropsQuery = "SELECT * FROM cropstable WHERE warehouse_managerID = $selectedManagerID";
 $crops = $conn->query($cropsQuery);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Crop Management</title>
 <link rel="stylesheet" href="styles.css">

</head>

<body>
 <div class="container ">
  <h1>Crop Management</h1>

  <!-- Select Warehouse Manager -->
  <form method="POST" action="">
   <h2>Select Warehouse Manager</h2>
   <label for="managerID">Manager ID:</label>
   <select id="managerID" name="managerID" required>
    <option value="">--Select Manager--</option>
    <?php while ($manager = $managersResult->fetch_assoc()): ?>
     <option value="<?= $manager['id']; ?>" <?= (isset($selectedManagerID) && $selectedManagerID == $manager['id']) ? 'selected' : ''; ?>>
      <?= $manager['id']; ?> - <?= htmlspecialchars($manager['name']); ?>
     </option>
    <?php endwhile; ?>
   </select>
   <button type="submit">Load Manager Info</button>
  </form>

  <!-- Display Manager Information -->
  <?php if ($managerInfo): ?>
   <h2>Warehouse Manager Information</h2>
   <p><strong>Name:</strong> <?= htmlspecialchars($managerInfo['name']); ?></p>
   <p><strong>Email:</strong> <?= htmlspecialchars($managerInfo['email']); ?></p>
   <p><strong>Place:</strong> <?= htmlspecialchars($managerInfo['place']); ?></p>
   <p><strong>Phone:</strong> <?= htmlspecialchars($managerInfo['phone']); ?></p>
  <?php endif; ?>

  <!-- Crops Form -->
  <div class="form-container">
   <a href="./index2.php">
    <button class="btn">ADD SEEDS</button>
   </a>
   <form id="cropForm" method="POST" action="">
    <h2>Add Crop</h2>
    <label for="managerIDCrop">Manager ID:</label>
    <input type="number" id="managerIDCrop" name="managerIDCrop" value="<?= $selectedManagerID; ?>" readonly required>

    <label for="cropName">Name:</label>
    <!-- <input type="text" id="cropName" name="cropName" placeholder="Enter crop name" required> -->
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
    </select>

    <label for="cropCategory">Category:</label>
    <!-- <input type="text" id="cropCategory" name="cropCategory" placeholder="Enter category" required> -->
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
    <!-- <input type="text" id="cropSeason" name="cropSeason" placeholder="Enter season" required> -->
    <select id="cropSeason" name="cropSeason" required>
     <option value="Summer">Summer</option>
     <option value="Monsoon">Monsoon</option>
     <option value="Autumn">Autumn</option>
     <option value="Late Autumn">Late Autumn</option>
     <option value="Winter">Winter</option>
     <option value="Spring">Spring</option>
     <option value="All Year Round">All Year Round</option>
    </select>

    <label for="cropRegion">Region of Production</label>
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

    <label for="cropQuantity">Unit(Quantity:)</label>
    <input type="number" id="cropQuantity" name="cropQuantity" placeholder="Enter crop Quantity" required>

    <label for="cropPrice">Price Per Unit:</label>
    <input type="number" id="cropPrice" name="cropPrice" placeholder="Enter inventory count" required>

    <label for="totalPrice">Total Price:</label>
    <input type="number" id="totalPrice" name="totalPrice" placeholder="Total Price" readonly>


    <label for="cropInventory">Inventory:</label>
    <select id="cropInventory" name="cropInventory">
     <option value="low">Low (Below 100 units)</option>
     <option value="medium">Medium (100-500 units)</option>
     <option value="high">High (Above 500 units)</option>
    </select>
    <!-- <input type="number" id="cropInventory" name="cropInventory" placeholder="Enter inventory count" required> -->

    <label for="cropStorage">Storage:</label>
    <select id="cropStorage" name="cropStorage">
     <option value="cold_storage">Cold Storage</option>
     <option value="dry_warehouse">Dry Warehouse</option>
     <option value="open_yard">Open Yard</option>
    </select>
    <!-- <input type="text" id="cropStorage" name="cropStorage" placeholder="Enter storage type" required> -->

    <label for="cropLogistics">Logistics:</label>
    <select id="cropLogistics" name="cropLogistics">
     <option value="road">Road Transport</option>
     <option value="rail">Rail Transport</option>
     <option value="sea">Sea Freight</option>
     <option value="air">Air Freight</option>
    </select>
    <!-- <input type="text" id="cropLogistics" name="cropLogistics" placeholder="Enter logistics details" required> -->

    <button class="add-btn" type="submit" name="add_crop">Add Crop</button>
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


  <!-- Display Crops Table -->
  <?php if ($crops && $crops->num_rows > 0): ?>
   <h2>Crops Table for Manager ID <?= $selectedManagerID ?></h2>
   <table>
    <thead>
     <tr>
      <th>Name</th>
      <th>Category</th>
      <th>Season</th>
      <th>Region</th>
      <th>Quantity</th>
      <th>Price Per Unit</th>
      <th>Total Price</th>
      <th>Inventory</th>
      <th>Storage</th>
      <th>Logistics</th>
      <th>Update</th>
      <th>Delete</th>
     </tr>
    </thead>
    <tbody>
     <?php while ($row = $crops->fetch_assoc()): ?>
      <tr>
       <td><?= htmlspecialchars($row["name"]); ?></td>
       <td><?= htmlspecialchars($row["category"]); ?></td>
       <td><?= htmlspecialchars($row["season"]); ?></td>
       <td><?= htmlspecialchars($row["region"]); ?></td>
       <td><?= htmlspecialchars($row["quantity"]); ?></td>
       <td><?= htmlspecialchars($row["price"]); ?></td>
       <td><?= htmlspecialchars($row["totalPrice"]); ?></td>
       <td><?= htmlspecialchars($row["inventory"]); ?></td>
       <td><?= htmlspecialchars($row["storage"]); ?></td>
       <td><?= htmlspecialchars($row["logistics"]); ?></td>

       <td>
        <a href="update.php?crop_id=<?= $row['id']; ?>"><button class="btn">Update</button></a>
       </td>
       <!-- <td>
        <a href="?delete_crop=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this crop?')">Delete</a>
       </td> -->
       <td>
        <a href="?delete_crop=<?= $row['id']; ?>"
         onclick="return confirmDelete(<?= $row['id']; ?>)"><button class="btn">Delete</button></a>
       </td>

      </tr>
     <?php endwhile; ?>
    </tbody>
   </table>
  <?php elseif ($selectedManagerID): ?>
   <p>No crops found for the selected manager.</p>
  <?php endif; ?>
 </div>

 <!-- JavaScript for Delete Confirmation and Alert -->
 <script>
  function confirmDelete(cropID) {
   if (confirm('Are you sure you want to delete this crop?')) {
    // Redirect to delete the crop
    window.location.href = "?delete_crop=" + cropID;
   }
   return false; // Prevent the default behavior
  }

  // Show alert message after deletion
  <?php if (isset($_SESSION['delete_message'])): ?>
   alert('<?php echo $_SESSION['delete_message']; ?>');
   <?php unset($_SESSION['delete_message']); ?>
  <?php endif; ?>
 </script>
</body>

</html>