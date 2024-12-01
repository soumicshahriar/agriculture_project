<?php
// Database connection
include('config/connect.php');

// SQL query to fetch product data with JOIN - simplified without image path
$query = "SELECT p.product_name, pi.quantity 
          FROM product p
          LEFT JOIN product_info pi ON p.product_id = pi.product_id";
$result = $conn->query($query);

// Check if we have results
if ($result->num_rows > 0) {
    $products = [];
    
    while ($row = $result->fetch_assoc()) {
        // Change image path construction to match the HTML structure
        $imagePath = $row['product_name'] . '.jpg';
        $row['image'] = file_exists('Images/' . $imagePath) ? $imagePath : 'default.jpg';
        $products[] = $row;
    }

    // Return the products as a JSON response
    echo json_encode($products);
} else {
    echo json_encode([]);
}

$conn->close();
?>
