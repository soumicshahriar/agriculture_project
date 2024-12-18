<?php
// Step 1: Database connection
$servername = "localhost"; // Change to your database server
$username = "root";        // Change to your database username
$password = "";            // Change to your database password
$dbname = "agriculture_product_data"; // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
}

// Step 2: Fetch the count of rows from the employee table
$employeeQuery = "SELECT COUNT(*) AS employee_count FROM employee";
$employeeResult = $conn->query($employeeQuery);
$employeeCount = 0;
if ($employeeResult->num_rows > 0) {
        $row = $employeeResult->fetch_assoc();
        $employeeCount = $row['employee_count'];
}

// Fetch the count of rows from the customers table
$customerQuery = "SELECT COUNT(*) AS customer_count FROM customers";
$customerResult = $conn->query($customerQuery);
$customerCount = 0;
if ($customerResult->num_rows > 0) {
        $row = $customerResult->fetch_assoc();
        $customerCount = $row['customer_count'];
}

// Step 3: Handle the revenue calculation based on day, month, or year
$period = isset($_POST['period']) ? $_POST['period'] : 'day'; // Default to day
$month = isset($_POST['month']) ? $_POST['month'] : date('m'); // Default to current month
$year = isset($_POST['year']) ? $_POST['year'] : date('Y'); // Default to current year

$date = new DateTime();
$start_date = '';
$end_date = '';

// Handle period (Day, Month, Year)
if ($period == 'day') {
        $start_date = $date->format('Y-m-d') . ' 00:00:00';
        $end_date = $date->format('Y-m-d') . ' 23:59:59';
} elseif ($period == 'month') {
        $start_date = "$year-$month-01 00:00:00";
        $end_date = date("Y-m-t", strtotime("$year-$month-01")) . ' 23:59:59';
} elseif ($period == 'year') {
        $start_date = $year . '-01-01 00:00:00';
        $end_date = $year . '-12-31 23:59:59';
}

// SQL query to fetch revenue from customer_purchase_history
$sql = "SELECT SUM(total_price) AS revenue
        FROM customer_purchase_history
        WHERE purchase_date BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$stmt->bind_result($revenue);
$stmt->fetch();

// Default to 0 if no revenue is found
$revenue = $revenue ? $revenue : 0;

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Employee, Customer Count & Revenue</title>
        <style>
                /* Global reset for better cross-browser consistency */
                * {
                        margin: 0;
                        padding: 0;
                        box-sizing: border-box;
                }

                /* Body styling */
                body {
                        font-family: 'Arial', sans-serif;
                        background-color: #f4f7fb;
                        color: #333;
                        line-height: 1.6;
                        padding: 20px;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                }

                /* Container to center the cards and give space */
                .container {
                        display: flex;
                        justify-content: center;
                        gap: 30px;
                        flex-wrap: wrap;
                        margin-top: 20px;
                }

                /* Form styling */
                form {
                        background-color: #fff;
                        border-radius: 8px;
                        padding: 20px;
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                        margin-bottom: 20px;
                        width: 100%;
                        max-width: 600px;
                        text-align: center;
                }

                form select,
                form button {
                        padding: 10px;
                        margin: 10px 5px;
                        border-radius: 5px;
                        border: 1px solid #ccc;
                        font-size: 16px;
                        width: 120px;
                        cursor: pointer;
                        background-color: #f9f9f9;
                        transition: all 0.3s ease;
                }

                form select:focus,
                form button:focus {
                        outline: none;
                        border-color: #007bff;
                }

                /* Button hover effect */
                form button:hover {
                        background-color: #007bff;
                        color: #fff;
                }

                /* Card styling */
                .card {
                        background-color: white;
                        border-radius: 12px;
                        padding: 15px;
                        /* width: 250px; */
                        min-height: 200px;
                        text-align: center;
                        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                        transition: all 0.3s ease;
                        margin: 20px;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                }

                .card:hover {
                        transform: translateY(-5px);
                        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
                }

                .card h2 {
                        font-size: 2.5rem;
                        color: #333;
                        margin-bottom: 10px;
                }

                .card p {
                        font-size: 1.2rem;
                        color: #777;
                }

                .revenue-card h3 {
                        font-size: 2.2rem;
                        color: #4CAF50;
                        margin-top: 10px;
                }

                .revenue-card .card-text {
                        font-size: 1.1rem;
                        color: #555;
                }

                .revenue-card .card-title {
                        font-size: 1.5rem;
                        font-weight: bold;
                        color: #007bff;
                }

                /* Flexbox alignment to keep cards aligned in a row */
                .card-container {
                        display: flex;
                        justify-content: center;
                        gap: 20px;
                        flex-wrap: wrap;
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                        .container {
                                flex-direction: column;
                                align-items: center;
                        }

                        .card-container {
                                flex-direction: column;
                                align-items: center;
                        }

                        form {
                                width: 100%;
                                max-width: 90%;
                        }
                }

                /* Drop shadow effect for the cards */
                .card {
                        background-color: white;
                        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                }

                .card:hover {
                        background-color: white;
                }

                /* Enhanced select box and input fields */
                select,
                input,
                button {
                        font-family: inherit;
                        font-size: 16px;
                        background-color: #fafafa;
                        border: 1px solid #ddd;
                        padding: 12px;
                        border-radius: 6px;
                        transition: border-color 0.3s;
                }

                select:focus,
                input:focus,
                button:focus {
                        border-color: #007bff;
                        outline: none;
                }

                select option {
                        font-size: 16px;
                }
        </style>
</head>

<body>

        <!-- Filter Form -->
        <form method="POST">
                <label for="period">Select Period: </label>
                <select name="period" id="period">
                        <option value="day" <?php echo ($period == 'day' ? 'selected' : ''); ?>>Day</option>
                        <option value="month" <?php echo ($period == 'month' ? 'selected' : ''); ?>>Month</option>
                        <option value="year" <?php echo ($period == 'year' ? 'selected' : ''); ?>>Year</option>
                </select>

                <!-- Additional filter for Month and Year -->
                <?php if ($period == 'month'): ?>
                        <label for="month">Select Month: </label>
                        <select name="month" id="month">
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                        <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"
                                                <?php echo ($month == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : ''); ?>>
                                                <?php echo date("F", mktime(0, 0, 0, $i, 1)); ?>
                                        </option>
                                <?php endfor; ?>
                        </select>
                <?php endif; ?>

                <label for="year">Select Year: </label>
                <select name="year" id="year">
                        <?php for ($i = 2020; $i <= date("Y"); $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo ($year == $i ? 'selected' : ''); ?>>
                                        <?php echo $i; ?>
                                </option>
                        <?php endfor; ?>
                </select>

                <button type="submit">Revenue</button>
        </form>

        <div class="container">
                <!-- Card for employee count -->
                <a class="card" href="../employee_db/employeeInfo.php">
                        <div class="card">
                                <h2><?php echo $employeeCount; ?></h2>
                                <p>Employees</p>
                        </div>
                </a>

                <!-- Card for customer count -->
                <div class="card">
                        <div class="card">
                                <h2><?php echo $customerCount; ?></h2>
                                <p>Customers</p>
                        </div>
                </div>

                <!-- Card for revenue -->
                <div class="card revenue-card">
                        <h5 class="card-title">Revenue Calculation (<?php echo ucfirst($period); ?>)</h5>
                        <p class="card-text">Total revenue for the selected period:</p>
                        <h3>$<?php echo number_format($revenue, 2); ?></h3>
                        <p class="card-text"><small class="text-muted">Calculated for the period: <?php echo date('F j, Y', strtotime($start_date)) . ' to ' . date('F j, Y', strtotime($end_date)); ?></small></p>
                </div>
        </div>

</body>

</html>