<?php
// Database connection
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
        // $name = $_POST["seedName"];
        // $category = $_POST["seedCategory"];

        $seed = $_POST["seed_id"];
        $season = $_POST["seedSeason"];
        $region = $_POST["seedRegion"];
        $quantity = $_POST["seedQuantity"];
        $price = $_POST["seedPrice"];
        $totalPrice = $_POST["totalPrice"];
        $inventory = $_POST["seedInventory"];
        $storage = $_POST["seedStorage"];
        $logistics = $_POST["seedLogistics"];

        $sql = "INSERT INTO seedstable (warehouse_manager_employee_id,  season, region, quantity, price, totalPrice, inventory, storage, logistics, seed_id) 
            VALUES ('$managerID', '$season', '$region', '$quantity', '$price', '$totalPrice', '$inventory', '$storage', '$logistics', ' $seed')";

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
         name='$name', category='$category', season='$season', region='$region', quantity='$quantity', price='$price', totalPrice='$totalPrice',
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
        $seedsQuery = "
    SELECT s.seed_name, s.category, ss.quantity, ss.price, ss.totalPrice, ss.inventory, ss.storage, ss.logistics
    FROM seedstable ss
    JOIN seeds s ON ss.seed_id = s.seed_id
    WHERE ss.warehouse_manager_employee_id = $selectedManagerID
";
        $seeds = $conn->query($seedsQuery);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Seed Management</title>
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

                <!-- Seed Form -->
                <div class="form-container">
                        <a href="./index.php">
                                <button class="btn">ADD CROPS</button>
                        </a>
                        <?php
                        // Database connection and other code remains the same

                        // Seed form part with updated functionality
                        ?>
                        <?php
                        // Database connection and other code remain the same

                        // Seed form part with updated functionality
                        ?>
                        <form id="seedForm" method="POST" action="">
                                <h2>Add Seed</h2>
                                <label for="managerIDseed">Manager ID:</label>
                                <input type="number" id="managerIDseed" name="managerIDseed" value="<?= $selectedManagerID; ?>" readonly required>

                                <label for="seed_id">Product ID:</label>
                                <select name="seed_id" id="seed_id" required>
                                        <option value="">Select Seed</option>
                                        <?php
                                        // Fetch product IDs and categories from the seeds table
                                        $product_query = "SELECT seed_id, seed_name, category FROM seeds";
                                        $product_result = $conn->query($product_query);
                                        if ($product_result->num_rows > 0) {
                                                while ($row = $product_result->fetch_assoc()) {
                                                        echo "<option value='" . $row['seed_id'] . "' data-category='" . $row['category'] . "'>" . $row['seed_id'] . " - " . $row['seed_name'] . "</option>";
                                                }
                                        }
                                        ?>
                                </select><br><br>

                                <!-- <label for="seedCategory">Category:</label>
                                <select id="seedCategory" name="seedCategory" required>
                                        <option value="">Select</option> -->
                                <!-- Categories will be populated based on selected seed -->
                                <!-- </select><br> -->

                                <label for="seedSeason">Season:</label>
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
                                </select>

                                <label for="seedQuantity">Quantity</label>
                                <input type="number" id="seedQuantity" name="seedQuantity" required>

                                <label for="seedPrice">Price per Quantity</label>
                                <input type="number" id="seedPrice" name="seedPrice" required>

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

                                <button type="submit" name="add_seed" class="btn">Submit Seed</button>
                        </form>

                        <!-- Seed Table -->
                        <?php if ($seeds && $seeds->num_rows > 0): ?>
                                <h2>Seed Inventory for Manager: <?= htmlspecialchars($managerInfo['employee_name']); ?></h2>
                                <table>
                                        <thead>
                                                <tr>
                                                        <th>Seed Name</th>
                                                        <th>Category</th>
                                                        <th>Quantity</th>
                                                        <th>Price</th>
                                                        <th>Total Price</th>
                                                        <th>Inventory</th>
                                                        <th>Storage</th>
                                                        <th>Logistics</th>
                                                </tr>
                                        </thead>
                                        <tbody>
                                                <?php while ($row = $seeds->fetch_assoc()): ?>
                                                        <tr>
                                                                <td><?= htmlspecialchars($row['seed_name']); ?></td>
                                                                <td><?= htmlspecialchars($row['category']); ?></td>
                                                                <td><?= htmlspecialchars($row['quantity']); ?></td>
                                                                <td><?= htmlspecialchars($row['price']); ?></td>
                                                                <td><?= htmlspecialchars($row['totalPrice']); ?></td>
                                                                <td><?= htmlspecialchars($row['inventory']); ?></td>
                                                                <td><?= htmlspecialchars($row['storage']); ?></td>
                                                                <td><?= htmlspecialchars($row['logistics']); ?></td>
                                                        </tr>
                                                <?php endwhile; ?>
                                        </tbody>
                                </table>
                        <?php else: ?>
                                <p>No seeds found for the selected manager.</p>
                        <?php endif; ?>


                        <script>
                                // JavaScript to update the category field automatically when a seed is selected
                                document.getElementById('seed_id').addEventListener('change', function() {
                                        var selectedSeed = this.options[this.selectedIndex];
                                        var category = selectedSeed.getAttribute('data-category');

                                        // Update category field
                                        var categoryField = document.getElementById('seedCategory');
                                        categoryField.innerHTML = ""; // Clear previous options
                                        var newOption = document.createElement("option");
                                        newOption.value = category;
                                        newOption.textContent = category;
                                        categoryField.appendChild(newOption);
                                        categoryField.disabled = false; // Enable the category select field

                                        // Reload the page only when a new item is selected
                                        window.location.reload();
                                });

                                // Optionally: Handle page reload preservation of the previous selection
                                window.addEventListener('load', function() {
                                        var selectedSeedId = localStorage.getItem('seed_id');
                                        if (selectedSeedId) {
                                                var seedSelect = document.getElementById('seed_id');
                                                seedSelect.value = selectedSeedId;

                                                // Update category based on saved seed ID
                                                var selectedOption = seedSelect.options[seedSelect.selectedIndex];
                                                var category = selectedOption.getAttribute('data-category');
                                                var categoryField = document.getElementById('seedCategory');
                                                categoryField.innerHTML = "";
                                                var newOption = document.createElement("option");
                                                newOption.value = category;
                                                newOption.textContent = category;
                                                categoryField.appendChild(newOption);
                                                categoryField.disabled = false;
                                        }
                                });

                                // Save the seed_id to localStorage when it changes
                                document.getElementById('seed_id').addEventListener('change', function() {
                                        localStorage.setItem('seed_id', this.value);
                                });
                        </script>

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


                </div>
        </div>


</body>

</html>