<?php
// Database connection
// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
}

// Fetch all warehouse managers by joining with the employee table
$managersResult = $conn->query("
    SELECT wm.warehouse_manager_employee_id, e.employee_id, e.employee_name
    FROM warehouse_managers wm
    INNER JOIN employee e ON wm.employee_id = e.employee_id
    WHERE e.role = 'warehouse manager'
");



// Insert seed data into seedstable
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_seed"])) {
        $managerID = $_POST["managerIDseed"];
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

        $sql = "INSERT INTO seedstable (warehouse_manager_employee_id, name, category, season, region, quantity, price, totalPrice, inventory, storage, logistics) 
            VALUES ('$managerID', '$name', '$category', '$season', '$region', '$quantity', '$price', '$totalPrice', '$inventory', '$storage', '$logistics')";

        if ($conn->query($sql) === TRUE) {
                header("Location: index2.php"); // Refresh the page to update the table
                exit();
        } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
        }
}


// Update seed data in seedstable
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_seed"])) {
        $seedID = $_POST["seedID"];
        $name = $_POST["seedName"];
        $category = $_POST["seedCategory"];
        $season = $_POST["seedSeason"];
        $region = $_POST["seedRegion"];
        $quantity = $_POST["seedQuantity"];
        $price = $_POST["seedPrice"];
        $totalPrice = $_POST["seedTotalPrice"];
        $inventory = $_POST["seedInventory"];
        $storage = $_POST["seedStorage"];
        $logistics = $_POST["seedLogistics"];

        $sql = "UPDATE seedstable SET 
         name='$name', category='$category', season='$season', region='$region', quantity='$quantity', price='$price',  totalPrice='$totalPrice',
         inventory='$inventory', storage='$storage', logistics='$logistics' 
         WHERE id='$seedID'";

        if ($conn->query($sql) === TRUE) {
                header("Location: index2.php"); // Refresh the page to update the table
                exit();
        } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
        }
}

// Delete seed from seedstable
if (isset($_GET["delete_seed"])) {
        $seedID = $_GET["delete_seed"];

        $sql = "DELETE FROM seedstable WHERE id='$seedID'";

        if ($conn->query($sql) === TRUE) {
                header("Location: index2.php"); // Refresh the page to update the table
                exit();
        } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
        }
}

// Fetch seeds for a selected manager
$selectedManagerID = isset($_POST['managerID']) ? $_POST['managerID'] : null;
$seeds = null;
$managerInfo = null;

// if ($selectedManagerID) {
//         // Fetch manager information
//         $managerQuery = "SELECT * FROM warehouse_managers WHERE warehouse_manager_employee_id = $selectedManagerID";
//         $managerResult = $conn->query($managerQuery);
//         if ($managerResult->num_rows > 0) {
//                 $managerInfo = $managerResult->fetch_assoc();
//         }


//         // Fetch seeds related to the selected manager
//         $seedsQuery = "SELECT * FROM seedstable WHERE warehouse_manager_employee_id = $selectedManagerID";
//         $seeds = $conn->query($seedsQuery);
// }

if ($selectedManagerID) {
        // Fetch manager information and employee_id from employee table based on the role 'warehouse manager'
        $managerQuery = "SELECT wm.*, e.employee_id, e.employee_name AS employee_name, e.email, e.phone, e.road_no 
                         FROM warehouse_managers wm
                         JOIN employee e ON wm.warehouse_manager_employee_id = e.employee_id
                         WHERE wm.warehouse_manager_employee_id = $selectedManagerID
                         AND e.role = 'warehouse manager'";

        $managerResult = $conn->query($managerQuery);

        if ($managerResult->num_rows > 0) {
                $managerInfo = $managerResult->fetch_assoc();
        }

        // Fetch seeds related to the selected manager
        $seedsQuery = "SELECT * FROM seedstable WHERE warehouse_manager_employee_id = $selectedManagerID";
        $seeds = $conn->query($seedsQuery);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>seed Management</title>
        <link rel="stylesheet" href="styles.css">

</head>

<body>
        <div class="container">
                <h1>Seed Management</h1>

                <!-- Select Warehouse Manager -->
                <form method="POST" action="">
                        <h2>Select Warehouse Manager</h2>
                        <label for="managerID">Manager ID:</label>
                        <select id="managerID" name="managerID" required>
                                <option value="">--Select Manager--</option>
                                <?php while ($manager = $managersResult->fetch_assoc()): ?>
                                        <option value="<?= $manager['warehouse_manager_employee_id']; ?>" <?= (isset($selectedManagerID) && $selectedManagerID == $manager['warehouse_manager_employee_id']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($manager['employee_id']); ?> - <?= htmlspecialchars($manager['employee_name']); ?>
                                        </option>
                                <?php endwhile; ?>
                        </select>
                        <button type="submit">Load Manager Info</button>
                </form>


                <!-- Display Manager Information -->
                <?php if ($managerInfo): ?>
                        <h2>Warehouse Manager Information</h2>
                        <p><strong>Name:</strong> <?= htmlspecialchars($managerInfo['employee_name']); ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($managerInfo['email']); ?></p>
                        <p><strong>Place:</strong> <?= htmlspecialchars($managerInfo['road_no']); ?></p>
                        <p><strong>Phone:</strong> <?= htmlspecialchars($managerInfo['phone']); ?></p>
                <?php endif; ?>

                <!-- seeds Form -->
                <div class="form-container">
                        <a href="./index.php">
                                <button class="btn">ADD CROPS</button>
                        </a>
                        <form id="seedForm" method="POST" action="">
                                <h2>Add Seed</h2>
                                <label for="managerIDseed">Manager ID:</label>
                                <input type="number" id="managerIDseed" name="managerIDseed" value="<?= $selectedManagerID; ?>" readonly required>

                                <label for="seedName">Name:</label>
                                <!-- <input type="text" id="seedName" name="seedName" placeholder="Enter seed name" required> -->
                                <select id="seedName" name="seedName">
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

                                <label for="seedCategory">Category:</label>
                                <!-- <input type="text" id="seedCategory" name="seedCategory" placeholder="Enter category" required> -->
                                <select id="seedCategory" name="seedCategory" required>
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

                                <label for="seedSeason">Season:</label>
                                <!-- <input type="text" id="seedSeason" name="seedSeason" placeholder="Enter season" required> -->
                                <select id="seedSeason" name="seedSeason" required>
                                        <option value="Summer">Summer</option>
                                        <option value="Monsoon">Monsoon</option>
                                        <option value="Autumn">Autumn</option>
                                        <option value="Late Autumn">Late Autumn</option>
                                        <option value="Winter">Winter</option>
                                        <option value="Spring">Spring</option>
                                        <option value="All Year Round">All Year Round</option>
                                </select>

                                <label for="seedRegion">Region of Production</label>
                                <select id="seedRegion" name="seedRegion" required>
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

                                <label for="seedQuantity">Quantity:</label>
                                <input type="number" id="seedQuantity" name="seedQuantity" placeholder="Enter seed Quantity" required>

                                <label for="seedPrice">Price Per Unit:</label>
                                <input type="number" id="seedPrice" name="seedPrice" placeholder="Enter inventory count" required>

                                <label for="totalPrice">Total Price:</label>
                                <input type="number" id="totalPrice" name="totalPrice" placeholder="Total Price" readonly>


                                <label for="seedInventory">Inventory:</label>
                                <select id="seedInventory" name="seedInventory">
                                        <option value="low">Low (Below 100 units)</option>
                                        <option value="medium">Medium (100-500 units)</option>
                                        <option value="high">High (Above 500 units)</option>
                                </select>
                                <!-- <input type="number" id="seedInventory" name="seedInventory" placeholder="Enter inventory count" required> -->

                                <label for="seedStorage">Storage:</label>
                                <select id="seedStorage" name="seedStorage">
                                        <option value="cold_storage">Cold Storage</option>
                                        <option value="dry_warehouse">Dry Warehouse</option>
                                        <option value="open_yard">Open Yard</option>
                                </select>
                                <!-- <input type="text" id="seedStorage" name="seedStorage" placeholder="Enter storage type" required> -->

                                <label for="seedLogistics">Logistics:</label>
                                <select id="seedLogistics" name="seedLogistics">
                                        <option value="road">Road Transport</option>
                                        <option value="rail">Rail Transport</option>
                                        <option value="sea">Sea Freight</option>
                                        <option value="air">Air Freight</option>
                                </select>
                                <!-- <input type="text" id="seedLogistics" name="seedLogistics" placeholder="Enter logistics details" required> -->

                                <button type="submit" name="add_seed">Add seed</button>
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


                <!-- Display seeds Table -->
                <?php if ($seeds && $seeds->num_rows > 0): ?>
                        <h2>seeds Table for Manager ID <?= $selectedManagerID ?></h2>
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
                                        <?php while ($row = $seeds->fetch_assoc()): ?>
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
                                                                <a href="seedupdate.php?seed_id=<?= $row['id']; ?>"><button class="btn">Update</button></a>
                                                        </td>
                                                        <!-- <td>
        <a href="?delete_seed=<?= $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this seed?')">Delete</a>
       </td> -->
                                                        <td>
                                                                <a href="?delete_seed=<?= $row['id']; ?>"
                                                                        onclick="return confirmDelete(<?= $row['id']; ?>)"><button class="btn">Delete</button></a>
                                                        </td>

                                                </tr>
                                        <?php endwhile; ?>
                                </tbody>
                        </table>
                <?php elseif ($selectedManagerID): ?>
                        <p>No seeds found for the selected manager.</p>
                <?php endif; ?>
        </div>

        <!-- JavaScript for Delete Confirmation and Alert -->
        <script>
                function confirmDelete(seedID) {
                        if (confirm('Are you sure you want to delete this seed?')) {
                                // Redirect to delete the seed
                                window.location.href = "?delete_seed=" + seedID;
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