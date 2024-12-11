<?php
include('../config/connect.php'); // Include the database connection

// Read incoming data
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];

// Validate product_id
if (empty($product_id) || !is_numeric($product_id)) {
 echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
 exit;
}

// Check if the demand count record exists for this product_id
$sql = "SELECT * FROM product_demand_table WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
 // Demand record exists, increment the count
 $row = $result->fetch_assoc();
 $new_count = $row['count'] + 1;

 // Update the demand count
 $update_sql = "UPDATE product_demand_table SET count = ? WHERE product_id = ?";
 $update_stmt = $conn->prepare($update_sql);
 $update_stmt->bind_param("ii", $new_count, $product_id);
 $update_stmt->execute();

 echo json_encode(['status' => 'success', 'message' => 'Demand count updated']);
} else {
 // No record exists, create a new one with count = 1
 $insert_sql = "INSERT INTO product_demand_table (product_id, count) VALUES (?, 1)";
 $insert_stmt = $conn->prepare($insert_sql);
 $insert_stmt->bind_param("i", $product_id);
 $insert_stmt->execute();

 echo json_encode(['status' => 'success', 'message' => 'Demand added']);
}

// Close the prepared statement and database connection
$stmt->close();
$conn->close();
