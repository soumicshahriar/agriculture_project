<?php
// Database connection
include('../config/connect.php');


// Fetch customer data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $customer_id = $_GET['id'] ?? $_SESSION['userId']; // Get customer ID from session or query parameter

    $stmt = $conn->prepare("SELECT id, f_name, l_name, phone, email, password FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        echo json_encode($customer); // Send data as JSON
    } else {
        echo "No customer found.";
    }
    $stmt->close();
}

// Fetch user name
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_name'])) {
    $customer_id = $_SESSION['userId']; // Get customer ID from session

    $stmt = $conn->prepare("SELECT f_name, l_name FROM customers WHERE id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    if ($customer) {
        $full_name = $customer['f_name'] . ' ' . $customer['l_name'];
        echo json_encode(['name' => $full_name]); // Send full name as JSON
    } else {
        echo json_encode(['name' => 'Guest']); // Fallback name
    }
    $stmt->close();
}

// Update customer data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['id'];
    $f_name = $_POST['f_name'];
    $l_name = $_POST['l_name'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("UPDATE customers SET f_name = ?, l_name = ?, phone = ?, password = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $f_name, $l_name, $phone, $password, $id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile: " . $conn->error;
    }
    $stmt->close();
}

// Fetch customer purchase history
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['get_purchase_history'])) {
    $customer_id = $_SESSION['userId']; // Get customer ID from session

    $stmt = $conn->prepare("SELECT product_name, quantity, price, total_price, purchase_date FROM customer_purchase_history WHERE consumer_id = ?");
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $purchase_history = $result->fetch_all(MYSQLI_ASSOC); // Fetch all purchase history

    echo json_encode($purchase_history); // Send data as JSON
    $stmt->close();
}

$conn->close();
?>