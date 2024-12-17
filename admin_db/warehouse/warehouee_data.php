<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$database = "agricultural_product_data";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the warehouse table
$sql = "SELECT * FROM warehouse";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agricultural Warehouse Data</title>
    <style>
        /* General styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            margin-top: 20px;
            font-size: 2em;
        }

        /* Table styles */
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        th, td {
            padding: 12px;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: #27ae60;
            color: white;
            font-size: 1.1em;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e0e0e0;
        }

        td {
            color: #555;
        }

        /* Footer styles */
        footer {
            text-align: center;
            margin-top: 20px;
            background-color: #27ae60;
            color: white;
            padding: 10px 0;
        }
    </style>
</head>
<body>
    <h1>Agricultural Warehouse Inventory</h1>

    <table>
        <tr>
            <th>Warehouse ID</th>
            <th>Stored Crops</th>
            <th>Total Quantity</th>
            <th>Quantity Available</th>
            <th>Storage Location</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . htmlspecialchars($row['warehouse_id']) . "</td>
                        <td>" . htmlspecialchars($row['stored_crops']) . "</td>
                        <td>" . htmlspecialchars($row['total_quantity']) . "</td>
                        <td>" . htmlspecialchars($row['quantity_available']) . "</td>
                        <td>" . htmlspecialchars($row['storage_location']) . "</td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No records found</td></tr>";
        }
        $conn->close();
        ?>
    </table>

    <footer>
        <p>&copy; 2024 Agricultural Product Data | Warehouse Management</p>
    </footer>
</body>
</html>
