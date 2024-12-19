<?php
// Include the database connection file
include('../config/connect.php');

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

// Handle delete request
// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Delete from warehouse_manager table first
    $delete_manager_query = "DELETE FROM warehouse_manager WHERE warehouse_manager_employee_id = ?";
    $stmt_manager = $conn->prepare($delete_manager_query);
    $stmt_manager->bind_param("i", $delete_id);

    if ($stmt_manager->execute()) {
        // Now delete from employee table
        $delete_query = "DELETE FROM employee WHERE employee_id = ?";
        $stmt_employee = $conn->prepare($delete_query);
        $stmt_employee->bind_param("i", $delete_id);

        if ($stmt_employee->execute()) {
            echo "Employee and related warehouse manager record deleted successfully.";
            // Redirect back to the same page to refresh the table
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error deleting employee: " . $conn->error;
        }
    } else {
        echo "Error deleting warehouse manager record: " . $conn->error;
    }

    // Close the statements
    $stmt_manager->close();
    $stmt_employee->close();
}

// Handle add employee form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Get form values
    $employee_name = $_POST['employee_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password=$_POST['password'];
    $road_no = $_POST['road_no'];
    $house_no = $_POST['house_no'];
    $hire_date = $_POST['hire_date'];
    $role = $_POST['role'];
    $office_id = $_POST['office_id'];
    $check_quality = $_POST['check_quality'];  // New field for check quality
    $add_barcode_tag = $_POST['add_barcode_tag'];  // New field for barcode tag

    

    // Check if an employee with the same email or phone exists
    $check_query = "SELECT * FROM employee WHERE email = ? OR phone = ?";
    $check_query = "SELECT * FROM customers WHERE email = ? OR phone = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "An employee with the same email or phone already exists.";
    } else {
        // Insert the new employee into the database
        $insert_query = "INSERT INTO employee (employee_name, phone, email, road_no, house_no, hire_date, role, office_id,password)
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("sssssssss", $employee_name, $phone, $email, $road_no, $house_no, $hire_date, $role, $office_id,$password);

        if ($stmt->execute()) {
            // Insert into warehouse_managers table (for check_quality and add_barcode_tag)
            $insert_manager_query = "INSERT INTO warehouse_managers (employee_id, check_quality, add_barcode_tag) 
                                     VALUES (LAST_INSERT_ID(), ?, ?)";
            $stmt_manager = $conn->prepare($insert_manager_query);
            $stmt_manager->bind_param("ss", $check_quality, $add_barcode_tag);
            $stmt_manager->execute();

            echo "Employee added successfully.";
        } else {
            echo "Error adding employee: " . $conn->error;
        }
    }

    // Close the statement
    $stmt->close();
    // $stmt_manager->close();
}

// Build the SQL query to join employee and warehouse_managers table
$sql = "SELECT DISTINCT e.*, wm.check_quality, wm.add_barcode_tag 
        FROM employee e
        LEFT JOIN warehouse_managers wm ON e.employee_id = wm.employee_id
        WHERE 1=1";

if (!empty($role_filter)) {
    $sql .= " AND e.role = '" . $conn->real_escape_string($role_filter) . "'";
}
if (!empty($office_id_filter)) {
    $sql .= " AND e.office_id = '" . $conn->real_escape_string($office_id_filter) . "'";
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


    <h1>Employee Information</h1>
    <!-- Include the navbar -->
    <button><a style="text-decoration: none; color:white;" href="../admin_db2/index.php">Back</a></button>
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

    <!-- Add Employee Button -->
    <button id="addEmployeeBtn">Add Employee</button>

    <!-- Add Employee Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Employee</h2>
            <form method="POST" action="">
                <label for="employee_name">Employee Name:</label>
                <input type="text" name="employee_name" required>

                <label for="phone">Phone:</label>
                <input type="text" name="phone" required>

                <label for="email">Email:</label>
                <input type="email" name="email" required>

                <label for="password">Password:</label>
                <input type="password" name="password" required>


                <label for="road_no">Road No:</label>
                <input type="text" name="road_no" required>

                <label for="house_no">House No:</label>
                <input type="text" name="house_no" required>

                <label for="hire_date">Hire Date:</label>
                <input type="date" name="hire_date" required>

                <label for="role">Role:</label>
                <select name="role" required>
                    <option value="warehouse manager">Warehouse Manager</option>
                    <option value="Market Manager">Market Manager</option>
                    <?php
                    if ($roles_result->num_rows > 0) {
                        while ($row = $roles_result->fetch_assoc()) {
                            echo "<option value='" . $row['role'] . "'>" . $row['role'] . "</option>";
                        }
                    }
                    ?>
                </select>

                <label for="office_id">Office ID:</label>
                <select name="office_id" required>
                    <option value="1001">1001</option>
                    <option value="1002">1002</option>
                    <?php
                    if ($office_result->num_rows > 0) {
                        while ($row = $office_result->fetch_assoc()) {
                            echo "<option value='" . $row['office_id'] . "'>" . $row['office_id'] . "</option>";
                        }
                    }
                    ?>
                </select>

                <label for="check_quality">Check Quality:</label>
                <select name="check_quality" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>

                <label for="add_barcode_tag">Barcode Tag:</label>
                <select name="add_barcode_tag" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>

                <button type="submit" name="submit">Add Employee</button>
            </form>
        </div>
    </div>

    <!-- Employee Table -->
    <?php
    if ($result->num_rows > 0) {
        echo "<table border='1'>";
        echo "<tr>";
        // Fetch column names
        $columns = $result->fetch_fields();
        foreach ($columns as $column) {
            // Skip the check_quality and add_barcode_tag columns
            if ($column->name !== 'check_quality' && $column->name !== 'add_barcode_tag') {
                echo "<th>" . htmlspecialchars($column->name) . "</th>";
            }
        }

        echo "<th>Action</th>"; // Add action column for delete button
        echo "</tr>";

        // Fetch rows
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $key => $value) {
                // Skip the check_quality and add_barcode_tag columns
                if ($key !== 'check_quality' && $key !== 'add_barcode_tag') {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
            }

            // Add delete button
            echo "<td>";
            echo "<form method='GET' action=''>";
            echo "<input type='hidden' name='delete_id' value='" . htmlspecialchars($row['employee_id']) . "'>";
            echo "<button type='submit' onclick='return confirm(\"Are you sure you want to delete this record?\")'>Delete</button>";
            echo "</form>";
            echo "</td>";

            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No records found.";
    }

    // Close the connection
    $conn->close();
    ?>

    <script>
        // Modal functionality
        var modal = document.getElementById("myModal");
        var btn = document.getElementById("addEmployeeBtn");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>

</html>