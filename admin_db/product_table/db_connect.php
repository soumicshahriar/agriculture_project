<?php
// Database Connection Settings
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "agriculture_product_data";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

