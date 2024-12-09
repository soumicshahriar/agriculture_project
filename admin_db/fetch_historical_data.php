<?php
// Include database connection
include('../config/connect.php');

// Get start and end dates from the query parameters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

// Initialize response arrays
$tableData = [];
$chartData = ['labels' => [], 'data' => []];

// Build query to fetch historical data
$query = "
    SELECT hd.product_id, p.product_name, hd.quantity, hd.price, 
           (hd.quantity * hd.price) AS total_price, hd.date
    FROM historical_data hd
    JOIN product p ON hd.product_id = p.product_id
";

// Add date filter if provided
if ($startDate && $endDate) {
    $query .= " WHERE hd.date BETWEEN '$startDate' AND '$endDate'";
}

$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Add row to table data
        $tableData[] = $row;

        // Populate chart data
        if (!in_array($row['product_name'], $chartData['labels'])) {
            $chartData['labels'][] = $row['product_name'];
            $chartData['data'][] = $row['quantity'];
        } else {
            $index = array_search($row['product_name'], $chartData['labels']);
            $chartData['data'][$index] += $row['quantity'];
        }
    }
}

// Return data as JSON
echo json_encode(['tableData' => $tableData, 'chartData' => $chartData]);
