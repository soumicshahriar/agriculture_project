<?php

// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for adding new data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $new_price = empty($_POST['new_price']) ? null : $_POST['new_price'];
    $old_price = empty($_POST['old_price']) ? null : $_POST['old_price'];
    $production_cost = $_POST['production_cost'];
    $production_date = $_POST['production_date'];
    $expiration_date = $_POST['expiration_date'];

    // Check if the product_id already exists in product_info
    $check_sql = "SELECT * FROM product_info WHERE product_id = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("i", $product_id);
    $stmt_check->execute();
    $stmt_check_result = $stmt_check->get_result();


    if ($stmt_check_result->num_rows > 0) {
        // If product exists in product_info table, show the message in a pop-up
        echo "<script>alert('Product already exists in the product_info table. You can edit the product instead of adding a new one.');</script>";
    } else {
        // If product doesn't exist, proceed to insert into product_info table
        $sql = "INSERT INTO product_info (product_id, quantity, new_price, old_price, production_cost, production_date, expiration_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iidddss", $product_id, $quantity, $new_price, $old_price, $production_cost, $production_date, $expiration_date);
        if ($stmt->execute()) {
            // After successfully inserting into product_info, insert into product_info_all table
            $sql_all = "INSERT INTO product_info_all (product_id, new_price, old_price, quantity, production_date) 
                        VALUES (?, ?, ?, ?, ?)";
            $stmt_all = $conn->prepare($sql_all);
            $stmt_all->bind_param("dddis", $product_id, $new_price, $old_price, $quantity, $production_date);
            if ($stmt_all->execute()) {
                echo "<script>alert('Data added successfully!');</script>";
            } else {
                echo "<p>Error inserting into product_info_all: " . $stmt_all->error . "</p>";
            }
            $stmt_all->close();
        } else {
            echo "<p>Error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    }
    $stmt_check->close();
}

// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $new_price = empty($_POST['new_price']) ? null : $_POST['new_price'];
    $production_cost = $_POST['production_cost'];
    $production_date = $_POST['production_date'];
    $expiration_date = $_POST['expiration_date'];

    // Get the current price before update
    $get_current_price = "SELECT new_price, old_price FROM product_info WHERE id = ?";
    $stmt = $conn->prepare($get_current_price);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($current_price, $old_price);
    $stmt->fetch();
    $stmt->close();

    // Update the product in product_info table (we are just updating here)
    $sql = "UPDATE product_info SET old_price = ?, new_price = ?, quantity = ?, production_cost = ?, production_date = ?, expiration_date = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ddiissi", $current_price, $new_price, $quantity, $production_cost, $production_date, $expiration_date, $id);
    if ($stmt->execute()) {

        // Insert a new record into product_info_all to keep track of the change
        $sql_all_insert = "INSERT INTO product_info_all (product_id, new_price, old_price, quantity, production_date) 
                           VALUES (?, ?, ?, ?, ?)";
        $stmt_all = $conn->prepare($sql_all_insert);
        $stmt_all->bind_param("dddis", $product_id, $new_price, $current_price, $quantity, $production_date);

        if ($stmt_all->execute()) {
            // Redirect to reset the form

            // Success message alert, page will refresh after clicking "OK"
            echo "
            <script>
                // Show success alert
                // alert('Data updated successfully!');

                // Refresh the page after the user clicks OK
                alert('Data updated successfully!');
setTimeout(function() {
    window.location.href = '../product_db/product_info.php'; // Redirect to another page
}, 0);
           
            </script>";

            // header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
            // exit(); // Stop further code execution after redirect
        } else {
            echo "<p>Error inserting into product_info_all: " . $stmt_all->error . "</p>";
        }
        $stmt_all->close();
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Delete the record from product_info table
    $sql = "DELETE FROM product_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<script>alert('Data deleted successfully!');</script>";
    } else {
        echo "<p>Error: " . $stmt->error . "</p>";
    }
    $stmt->close();
}

// Fetch data from PRODUCT and product_info tables
$sql = "SELECT 
            pi.id,
            p.product_id, 
            p.product_name, 
            p.category, 
            pi.quantity, 
            pi.new_price, 
            pi.old_price, 
            pi.production_date, 
            pi.expiration_date 
        FROM 
            PRODUCT p
        LEFT JOIN 
            product_info pi ON p.product_id = pi.product_id
            ORDER BY p.product_id";
$result = $conn->query($sql);

// Fetch the data for the specific product to update
$product_data = null;
if (isset($_GET['update'])) {
    $id = $_GET['update'];
    $sql = "SELECT * FROM product_info WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product_data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Info Form</title>
    <link rel="stylesheet" href="../product_db/product_info_style.css">
    <style>
        /* Add this CSS to position the logout button */
        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: transparent;
            border: none;
            padding: 10px;
            cursor: pointer;
        }

        .logout-btn {
            text-decoration: none;
            color: white;
            background-color: #007bff;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <h1 class="heading">Add/Update Product Info</h1>
    <!-- Logout Button -->
    <button type="button" class="logout-button">
        <a href="../index.html" class="logout-btn">Logout</a>
    </button>
    
    <!-- Product Info Form -->
    <form method="POST" action="">
        <input type="hidden" name="id" value="<?= $product_data ? htmlspecialchars($product_data['id']) : '' ?>">

        <label for="product_id">Product ID:</label>
        <select name="product_id" id="product_id" required>
            <option value="">Select Product</option>
            <?php
            // Fetch product IDs from the PRODUCT table
            $product_query = "SELECT product_id, product_name FROM PRODUCT";
            $product_result = $conn->query($product_query);
            if ($product_result->num_rows > 0) {
                while ($row = $product_result->fetch_assoc()) {
                    $selected = ($product_data && $product_data['product_id'] == $row['product_id']) ? 'selected' : '';
                    echo "<option value='" . $row['product_id'] . "' $selected>" . $row['product_id'] . " - " . $row['product_name'] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" value="<?= $product_data ? htmlspecialchars($product_data['quantity']) : '' ?>" required><br><br>

        <label for="new_price">New Price:</label>
        <input type="number" step="0.01" name="new_price" id="new_price" value="<?= $product_data ? htmlspecialchars($product_data['new_price']) : '' ?>"><br><br>

        <label for="old_price">Old Price (optional):</label>
        <input type="number" step="0.01" name="old_price" id="old_price" value="<?= $product_data ? htmlspecialchars($product_data['old_price']) : '' ?>" readonly><br><br>

        <label for="production_cost">Production Cost:</label>
        <input type="number" step="0.01" name="production_cost" id="production_cost" value="<?= $product_data ? htmlspecialchars($product_data['production_cost']) : '' ?>" required><br><br>

        <label for="production_date">Production Date:</label>
        <input type="date" name="production_date" id="production_date" value="<?= $product_data ? htmlspecialchars($product_data['production_date']) : '' ?>" required><br><br>

        <label for="expiration_date">Expiration Date:</label>
        <input type="date" name="expiration_date" id="expiration_date" value="<?= $product_data ? htmlspecialchars($product_data['expiration_date']) : '' ?>" required><br><br>

        <?php if ($product_data): ?>
            <button type="submit" name="update">Update</button>
        <?php else: ?>
            <button type="submit" name="submit">Add</button>
        <?php endif; ?>
    </form>

    <h2 class="heading">Product and Product Info Data</h2>

    <!-- Display Data from PRODUCT and product_info Tables -->
    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th class='quantity'>Quantity</th>
                    <th class='new-price'>New Price</th>
                    <th>Old Price</th>
                    <th>Production Date</th>
                    <th>Expiration Date</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row['product_id']) . "</td>
                    <td>" . htmlspecialchars($row['product_name']) . "</td>
                    <td>" . htmlspecialchars($row['category']) . "</td>
                    <td class='quantity'>" . htmlspecialchars($row['quantity']) . "</td>
                    <td class='new-price'>" . (is_null($row['new_price']) ? "NULL" : htmlspecialchars($row['new_price'])) . "</td>
                    <td>" . (is_null($row['old_price']) ? "NULL" : htmlspecialchars($row['old_price'])) . "</td>
                    <td>" . htmlspecialchars($row['production_date']) . "</td>
                    <td>" . htmlspecialchars($row['expiration_date']) . "</td>
                    <td>
                        <a href='?update=" . $row['id'] . "'>Edit</a> 
                    </td>
                    <td>
                        <a href='?delete=" . $row['id'] . "' onclick=\"return confirm('Are you sure?');\">Delete</a>
                    </td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No data found.</p>";
    }
    ?>
</body>

</html>

<?php
// Close connection
$conn->close();
?>