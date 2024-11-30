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


// Insert crop data into cropstable
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_crop"])) {
        $managerID = $_POST["managerIDcrop"];
        // $name = $_POST["cropName"];
        // $category = $_POST["cropCategory"];

        $crop = $_POST["crop_id"];
        $season = $_POST["cropSeason"];
        $region = $_POST["cropRegion"];
        $quantity = $_POST["cropQuantity"];
        $price = $_POST["cropPrice"];
        $totalPrice = $_POST["totalPrice"];
        $inventory = $_POST["cropInventory"];
        $storage = $_POST["cropStorage"];
        $logistics = $_POST["cropLogistics"];

        $sql = "INSERT INTO cropstable (warehouse_manager_employee_id,  season, region, quantity, price, totalPrice, inventory, storage, logistics, crop_id) 
            VALUES ('$managerID', '$season', '$region', '$quantity', '$price', '$totalPrice', '$inventory', '$storage', '$logistics', ' $crop')";

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
         name='$name', category='$category', season='$season', region='$region', quantity='$quantity', price='$price', totalPrice='$totalPrice',
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

        // Fetch crops related to the selected manager
        $cropsQuery = "
    SELECT s.crop_name, s.category, ss.quantity, ss.price, ss.totalPrice, ss.inventory, ss.storage, ss.logistics
    FROM cropstable ss
    JOIN crops s ON ss.crop_id = s.crop_id
    WHERE ss.warehouse_manager_employee_id = $selectedManagerID
";
        $crops = $conn->query($cropsQuery);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>crop Management</title>
        <link rel="stylesheet" href="styles.css">

</head>

<body>
        <div class="container">
                <h1>crop Management</h1>

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

                <!-- crop Form -->
                <div class="form-container">
                        <a href="./index2.php">
                                <button class="btn">ADD SEEDS</button>
                        </a>
                        <?php
                        // Database connection and other code remains the same

                        // crop form part with updated functionality
                        ?>
                        <?php
                        // Database connection and other code remain the same

                        // crop form part with updated functionality
                        ?>
                        <form id="cropForm" method="POST" action="">
                                <h2>Add crop</h2>
                                <label for="managerIDcrop">Manager ID:</label>
                                <input type="number" id="managerIDcrop" name="managerIDcrop" value="<?= $selectedManagerID; ?>" readonly required>

                                <label for="crop_id">Product ID:</label>
                                <select name="crop_id" id="crop_id" required>
                                        <option value="">Select crop</option>
                                        <?php
                                        // Fetch product IDs and categories from the crops table
                                        $product_query = "SELECT crop_id, crop_name, category FROM crops";
                                        $product_result = $conn->query($product_query);
                                        if ($product_result->num_rows > 0) {
                                                while ($row = $product_result->fetch_assoc()) {
                                                        echo "<option value='" . $row['crop_id'] . "' data-category='" . $row['category'] . "'>" . $row['crop_id'] . " - " . $row['crop_name'] . "</option>";
                                                }
                                        }
                                        ?>
                                </select><br><br>

                                <!-- <label for="cropCategory">Category:</label>
                                <select id="cropCategory" name="cropCategory" required>
                                        <option value="">Select</option> -->
                                <!-- Categories will be populated based on selected crop -->
                                <!-- </select><br> -->

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
                                </select>

                                <label for="cropQuantity">Quantity</label>
                                <input type="number" id="cropQuantity" name="cropQuantity" required>

                                <label for="cropPrice">Price per Quantity</label>
                                <input type="number" id="cropPrice" name="cropPrice" required>

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

                                <button type="submit" name="add_crop" class="btn">Submit crop</button>
                        </form>

                        <!-- crop Table -->
                        <?php if ($crops && $crops->num_rows > 0): ?>
                                <h2>crop Inventory for Manager: <?= htmlspecialchars($managerInfo['employee_name']); ?></h2>
                                <table>
                                        <thead>
                                                <tr>
                                                        <th>crop Name</th>
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
                                                <?php while ($row = $crops->fetch_assoc()): ?>
                                                        <tr>
                                                                <td><?= htmlspecialchars($row['crop_name']); ?></td>
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
                                <p>No crops found for the selected manager.</p>
                        <?php endif; ?>


                        <script>
                                // JavaScript to update the category field automatically when a crop is selected
                                document.getElementById('crop_id').addEventListener('change', function() {
                                        var selectedcrop = this.options[this.selectedIndex];
                                        var category = selectedcrop.getAttribute('data-category');

                                        // Update category field
                                        var categoryField = document.getElementById('cropCategory');
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
                                        var selectedcropId = localStorage.getItem('crop_id');
                                        if (selectedcropId) {
                                                var cropSelect = document.getElementById('crop_id');
                                                cropSelect.value = selectedcropId;

                                                // Update category based on saved crop ID
                                                var selectedOption = cropSelect.options[cropSelect.selectedIndex];
                                                var category = selectedOption.getAttribute('data-category');
                                                var categoryField = document.getElementById('cropCategory');
                                                categoryField.innerHTML = "";
                                                var newOption = document.createElement("option");
                                                newOption.value = category;
                                                newOption.textContent = category;
                                                categoryField.appendChild(newOption);
                                                categoryField.disabled = false;
                                        }
                                });

                                // Save the crop_id to localStorage when it changes
                                document.getElementById('crop_id').addEventListener('change', function() {
                                        localStorage.setItem('crop_id', this.value);
                                });
                        </script>

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


                </div>
        </div>


</body>

</html>