<?php
// Include the database connection file
include('../config/connect.php');

// Enable error reporting for debugging
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Get the JSON data sent from the frontend
$data = json_decode(file_get_contents('php://input'), true);

// Extract data
$cartItems = $data['cartItems'];
$consumer_id = $data['consumer_id'];
$purchase_date = $data['purchase_date']; // The current date sent from JS

// Start transaction
$conn->begin_transaction();

try {
 // Loop through each cart item and insert into purchase tables
 foreach ($cartItems as $item) {
  $product_id = $item['product_id'];
  $product_name = $item['product_name'];
  $quantity = $item['quantity'];
  $price = $item['price'];
  $total_price = $item['total_price'];

  // Prepare the INSERT query
  $sql = "INSERT INTO customer_purchase_history (consumer_id, product_id, product_name, quantity, price, total_price, purchase_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

  if ($stmt = $conn->prepare($sql)) {
   $stmt->bind_param("iisidss", $consumer_id, $product_id, $product_name, $quantity, $price, $total_price, $purchase_date);

   // Execute the statement
   $stmt->execute();
  } else {
   // Output error if the statement fails to prepare
   throw new Exception("Failed to prepare SQL statement: " . $conn->error);
  }

  // Update product quantity in the product_info table
  $update_sql = "UPDATE product_info SET quantity = quantity - ? WHERE product_id = ?";
  if ($update_stmt = $conn->prepare($update_sql)) {
   $update_stmt->bind_param("ii", $quantity, $product_id);
   $update_stmt->execute();
  } else {
   throw new Exception("Failed to prepare update query: " . $conn->error);
  }
 }

 // Commit transaction
 $conn->commit();

 // Return success response
 echo json_encode(['status' => 'success']);
} catch (Exception $e) {
 // Rollback in case of an error
 $conn->rollback();
 echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// Close connection
$conn->close();
