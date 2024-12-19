<?php
session_start(); // Start the session
include('../config/connect.php'); // Include the database connection

// Fetch customer data
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id']) && !isset($_GET['history'])) {
    $customer_id = $_GET['id'] ?? $_SESSION['userId']; // Get customer ID from query parameter or session

    // Validate the customer ID
    if (!is_numeric($customer_id)) {
        echo json_encode(["error" => "Invalid customer ID."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT id, f_name, l_name, phone, email FROM customers WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["error" => "Database prepare error."]);
        exit;
    }

    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        header('Content-Type: application/json');
        echo json_encode($customer); // Send customer data as JSON
    } else {
        echo json_encode(["error" => "No customer found."]);
    }

    $stmt->close();
    exit;
}

// Fetch purchase history
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['history']) && isset($_GET['id'])) {
    $consumer_id = $_GET['id']; // Use ID passed in the URL

    // Validate the consumer ID
    if (!is_numeric($consumer_id)) {
        echo json_encode(["error" => "Invalid consumer ID."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT product_name, quantity, price, total_price, purchase_date 
                            FROM customer_purchase_history 
                            WHERE consumer_id = ? 
                            ORDER BY purchase_date DESC");
    if (!$stmt) {
        echo json_encode(["error" => "Database prepare error."]);
        exit;
    }

    $stmt->bind_param("i", $consumer_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $purchaseHistory = [];
    while ($row = $result->fetch_assoc()) {
        $purchaseHistory[] = $row;
    }

    if (empty($purchaseHistory)) {
        echo json_encode(["error" => "No purchase history found."]);
    } else {
        header('Content-Type: application/json');
        echo json_encode($purchaseHistory); // Send purchase history as JSON
    }

    $stmt->close();
    exit;
}

// Update customer data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $phone = $_POST['phone'];
    $password = $_POST['password']; // Storing password as plain text 


    $stmt = $conn->prepare("UPDATE customers SET f_name = ?, l_name = ?, phone = ?, password = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(["error" => "Database prepare error."]);
        exit;
    }

    $stmt->bind_param("ssssi", $f_name, $l_name, $phone, $password, $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Profile updated successfully!"]);
    } else {
        echo json_encode(["error" => "Error updating profile: " . $conn->error]);
    }

    $stmt->close();
    exit;
}

// Process purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase'])) {
    $consumer_id = $_SESSION['userId']; // Get consumer_id from session
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $total_price = $quantity * $price;
    $purchase_date = date('Y-m-d'); // Use the current date for purchase

    $stmt = $conn->prepare("INSERT INTO customer_purchase_history 
                            (consumer_id, product_id, product_name, quantity, price, total_price, purchase_date) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(["error" => "Database prepare error."]);
        exit;
    }

    $stmt->bind_param("iissdds", $consumer_id, $product_id, $product_name, $quantity, $price, $total_price, $purchase_date);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Purchase processed successfully!"]);
    } else {
        echo json_encode(["error" => "Error processing purchase: " . $conn->error]);
    }

    $stmt->close();
    exit;
}

// If no valid endpoint was matched
http_response_code(400);
echo json_encode(["error" => "Invalid request."]);
$conn->close();
?>
