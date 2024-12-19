<?php
// Database connection
$servername = "localhost";  // Change as per your server details
$username = "root";         // Your MySQL username
$password = "";             // Your MySQL password
$dbname = "agriculture_product_data";  // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Insert data into database
if (isset($_POST['insert'])) {
    $stored_crops = $_POST['stored_crops'];
    $total_quantity = $_POST['total_quantity'];
    $quantity_available = $_POST['quantity_available'];
    $storage_location = $_POST['storage_location'];

    $insert_sql = "INSERT INTO warehouse (stored_crops, total_quantity, quantity_available, storage_location) 
                   VALUES ('$stored_crops', $total_quantity, $quantity_available, '$storage_location')";

    if ($conn->query($insert_sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Update record
if (isset($_POST['update'])) {
    $warehouse_id = $_POST['warehouse_id'];
    $stored_crops = $_POST['stored_crops'];
    $total_quantity = $_POST['total_quantity'];
    $quantity_available = $_POST['quantity_available'];
    $storage_location = $_POST['storage_location'];

    $update_sql = "UPDATE warehouse SET stored_crops='$stored_crops', total_quantity=$total_quantity, 
                   quantity_available=$quantity_available, storage_location='$storage_location' WHERE warehouse_id=$warehouse_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Record updated successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Delete record
if (isset($_POST['delete'])) {
    $warehouse_id = $_POST['warehouse_id'];

    $delete_sql = "DELETE FROM warehouse WHERE warehouse_id=$warehouse_id";
    if ($conn->query($delete_sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error: " . $conn->error;
    }
}

// Fetch all records from the database
$sql = "SELECT * FROM warehouse";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse Dashboard</title>
</head>
<body>
    <h1>Warehouse Dashboard</h1>

    <!-- Form to Insert New Data -->
    <h2>Insert New Warehouse Data</h2>
    <form method="POST">
        <label for="stored_crops">Stored Crops:</label>
        <input type="text" name="stored_crops" required><br>

        <label for="total_quantity">Total Quantity:</label>
        <input type="number" name="total_quantity" required><br>

        <label for="quantity_available">Quantity Available:</label>
        <input type="number" name="quantity_available" required><br>

        <label for="storage_location">Storage Location:</label>
        <input type="text" name="storage_location" required><br>

        <button type="submit" name="insert">Insert Data</button>
    </form>

    <h2>Warehouse Data</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Warehouse ID</th>
                <th>Stored Crops</th>
                <th>Total Quantity</th>
                <th>Quantity Available</th>
                <th>Storage Location</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                        <td>" . $row["warehouse_id"] . "</td>
                        <td>" . $row["stored_crops"] . "</td>
                        <td>" . $row["total_quantity"] . "</td>
                        <td>" . $row["quantity_available"] . "</td>
                        <td>" . $row["storage_location"] . "</td>
                        <td>
                            <button onclick=\"editWarehouse(" . $row['warehouse_id'] . ")\">Edit</button>
                            <button onclick=\"deleteWarehouse(" . $row['warehouse_id'] . ")\">Delete</button>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No data available</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Edit Form (this will be filled with the row data when "Edit" is clicked) -->
    <h2>Edit Warehouse</h2>
    <form id="editForm" method="POST">
        <input type="hidden" name="warehouse_id" id="warehouse_id">
        <label for="stored_crops">Stored Crops:</label>
        <input type="text" name="stored_crops" id="stored_crops" required><br>

        <label for="total_quantity">Total Quantity:</label>
        <input type="number" name="total_quantity" id="total_quantity" required><br>

        <label for="quantity_available">Quantity Available:</label>
        <input type="number" name="quantity_available" id="quantity_available" required><br>

        <label for="storage_location">Storage Location:</label>
        <input type="text" name="storage_location" id="storage_location" required><br>

        <button type="submit" name="update">Update</button>
    </form>

    <script>
        // Function to populate the form with selected row data for editing
        function editWarehouse(warehouse_id) {
            const row = document.querySelector(`tr td:first-child[textContent='${warehouse_id}']`).parentNode;

            document.getElementById('warehouse_id').value = warehouse_id;
            document.getElementById('stored_crops').value = row.cells[1].textContent;
            document.getElementById('total_quantity').value = row.cells[2].textContent;
            document.getElementById('quantity_available').value = row.cells[3].textContent;
            document.getElementById('storage_location').value = row.cells[4].textContent;
        }

        // Function to handle delete action
        function deleteWarehouse(warehouse_id) {
            if (confirm('Are you sure you want to delete this warehouse?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'warehouse_id';
                input.value = warehouse_id;
                form.appendChild(input);
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete';
                form.appendChild(deleteInput);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>

</body>
</html>

<?php
// Close the connection
$conn->close();
?>
