<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriculture_product_data";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// Get data from the POST request
$id = isset($_POST['id']) ? $_POST['id'] : '';
$type = isset($_POST['type']) ? $_POST['type'] : '';

// Validate the data
if ($id && $type) {
 if ($type == 'historical') {
  // Delete the historical data record based on id
  $sql = "DELETE FROM historical_data WHERE id = ?";
 } elseif ($type == 'current') {
  // Delete the current data record based on id
  $sql = "DELETE FROM product_info WHERE id = ?";
 }

 $stmt = $conn->prepare($sql);
 $stmt->bind_param('i', $id);  // 'i' denotes integer type for id
 if ($stmt->execute()) {
 
 } else {
  echo "Error: " . $stmt->error;
 }
} else {
 echo "Invalid data.";
}

$conn->close();
