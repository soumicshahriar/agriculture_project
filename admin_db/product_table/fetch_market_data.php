<?php
// Include the database connection
include 'db_connect.php';

// SQL Query to fetch market price data
$sql = "SELECT p.product_name, mp.price, mp.date
        FROM products p
        JOIN market_prices mp ON p.product_id = mp.product_id
        ORDER BY mp.date DESC"; // Get most recent prices first

// Execute the query
$result = $conn->query($sql);

// Initialize an array to hold the result data
$data = [];

// Check if data exists
if ($result->num_rows > 0) {
    // Fetch each row and add to data array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Return the result as a JSON response
echo json_encode($data);

// Close the connection
$conn->close();
?>
