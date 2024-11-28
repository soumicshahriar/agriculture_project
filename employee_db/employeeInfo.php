<?php
// Database credentials
$host = "localhost"; // Replace with your database host
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "agriculture_product_data"; // Replace with your database name

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize filters
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$office_id_filter = isset($_GET['office_id']) ? $_GET['office_id'] : '';

// Fetch distinct roles for the filter dropdown
$roles_query = "SELECT DISTINCT role FROM employee";
$roles_result = $conn->query($roles_query);

// Fetch distinct office IDs for the filter dropdown
$office_query = "SELECT DISTINCT office_id FROM employee";
$office_result = $conn->query($office_query);

// Build the SQL query based on filters
$sql = "SELECT * FROM employee WHERE 1=1";
if (!empty($role_filter)) {
    $sql .= " AND role = '" . $conn->real_escape_string($role_filter) . "'";
}
if (!empty($office_id_filter)) {
    $sql .= " AND office_id = '" . $conn->real_escape_string($office_id_filter) . "'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Employee Table with Filters</title>
</head>

<body>
    <link rel="stylesheet" href="">
    <!-- Include the navbar -->
    <?php include '../navbar/nav.html'; ?>


    <h1>Employee Information</h1>

    <!-- Filter Form -->
    <form method="GET" action="">
        <label for="role">Filter by Role:</label>
        <select name="role" id="role">
            <option value="">All Roles</option>
            <?php
            if ($roles_result->num_rows > 0) {
                while ($row = $roles_result->fetch_assoc()) {
                    $selected = ($row['role'] == $role_filter) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($row['role']) . "' $selected>" . htmlspecialchars($row['role']) . "</option>";
                }
            }
            ?>
        </select>

        <label for="office_id">Filter by Office ID:</label>
        <select name="office_id" id="office_id">
            <option value="">All Offices</option>
            <?php
            if ($office_result->num_rows > 0) {
                while ($row = $office_result->fetch_assoc()) {
                    $selected = ($row['office_id'] == $office_id_filter) ? "selected" : "";
                    echo "<option value='" . htmlspecialchars($row['office_id']) . "' $selected>" . htmlspecialchars($row['office_id']) . "</option>";
                }
            }
            ?>
        </select>

        <button type="submit">Apply Filters</button>
    </form>

    <!-- Employee Table -->
    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr>";
        // Fetch column names
        $columns = $result->fetch_fields();
        foreach ($columns as $column) {
            echo "<th>" . $column->name . "</th>";
        }
        echo "</tr>";

        // Fetch rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $value) {
                echo "<td>" . htmlspecialchars($value) . "</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No records found.";
    }

    // Close the connection
    $conn->close();
    ?>
</body>

</html>