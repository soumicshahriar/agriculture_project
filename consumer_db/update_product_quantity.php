<?php
include('../config/connect.php');

// Get the incoming request data
$data = json_decode(file_get_contents("php://input"));

$product_id = $data->product_id;
$quantity_changed = $data->quantity_changed; // Positive for adding to the cart, negative for removing

// Check if quantity_changed is valid
if (!is_numeric($quantity_changed) || !is_numeric($product_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

// Update the product quantity in the database
$sql = "UPDATE product_info 
        SET quantity = quantity - ? 
        WHERE product_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $quantity_changed, $product_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update product quantity']);
}

// Close the database connection
$conn->close();
