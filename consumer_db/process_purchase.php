<?php
// Database credentials
$host = "localhost";
$username = "root";
$password = "";
$database = "agriculture_product_data";

// Connect to the database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}

// Get the raw POST data
$data = json_decode(file_get_contents("php://input"), true);

// Check if cartItems are present in the data
if (isset($data['cartItems'])) {
 $cartItems = $data['cartItems'];

 // Start a transaction
 $conn->begin_transaction();

 try {
  // Prepare SQL statement for inserting purchase history
  $stmt = $conn->prepare("INSERT INTO customer_purchase_history (consumer_id, product_id, product_name, quantity, price, total_price, purchase_date) VALUES (?, ?, ?, ?, ?, ?, ?)");

  // Loop through each item in the cart and insert it into the purchase history
  foreach ($cartItems as $item) {
   // Assuming the consumer_id is available (you can modify this to match your logic)
   $consumer_id = 1;  // Replace with actual consumer ID logic

   // Generate the current purchase date and time
   $purchase_date = date('Y-m-d H:i:s'); // Current date and time in "Y-m-d H:i:s" format

   // Insert into the customer_purchase_history_list table
   $stmt->bind_param(
    "iisidss",  // Data types for the query (i: int, s: string, d: decimal)
    $consumer_id,
    $item['product_id'],
    $item['product_name'],
    $item['quantity'],
    $item['price'],
    $item['total_price'],
    $purchase_date  // Set the purchase date here
   );

   if (!$stmt->execute()) {
    // Debugging: Output execution errors
    echo "Execution failed: " . $stmt->error;
    exit;
   }

   // Update product quantity in the product_info table
   $updateStmt = $conn->prepare("UPDATE product_info SET quantity = quantity - ? WHERE product_id = ?");
   $updateStmt->bind_param("ii", $item['quantity'], $item['product_id']);
   $updateStmt->execute();  // Execute the query to update the product quantity
   $updateStmt->close();
  }

  // Commit the transaction
  $conn->commit();

  // Close the statement
  $stmt->close();

  // Return a success response
  echo json_encode(['status' => 'success']);
 } catch (Exception $e) {
  // If there's an error, rollback the transaction
  $conn->rollback();
  echo json_encode(['status' => 'error', 'message' => 'Purchase failed. Please try again.']);
 }
} else {
 echo json_encode(['status' => 'error', 'message' => 'No cart items found']);
}

// Close the database connection
$conn->close();
