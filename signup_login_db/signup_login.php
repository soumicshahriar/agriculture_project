<?php

// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check request type
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sign-up functionality
    if (isset($_POST['f_name'], $_POST['l_name'], $_POST['phone'], $_POST['email'], $_POST['password'])) {
        $f_name = trim($_POST['f_name']);
        $l_name = trim($_POST['l_name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']); // Storing password as plain text (NOT RECOMMENDED)

        // Check if email already exists
        $stmt = $conn->prepare("SELECT email FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo json_encode(["message" => "Error: The email address is already registered!"]);
        } else {
            // Insert the new user into the database
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO customers (f_name, l_name, phone, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $f_name, $l_name, $phone, $email, $password);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Sign-up successful!"]);
            } else {
                echo json_encode(["message" => "Error: " . $stmt->error]);
            }
        }

        $stmt->close();
    } elseif (isset($_POST['email'], $_POST['password'])) {
        // Login functionality
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        $stmt = $conn->prepare("SELECT id, f_name, l_name, password FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->bind_result($db_id, $db_f_name, $db_l_name, $db_password);

        if ($stmt->fetch() && $password === $db_password) {
            // Corrected JSON response formatting
            echo json_encode([
                "message" => "Login successful!",
                "id" => (int)$db_id, // Ensure ID is an integer
                "f_name" => $db_f_name,
                "l_name" => $db_l_name
            ]);
        } else {
            echo json_encode(["message" => "Invalid email or password!"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["message" => "Invalid request!"]);
    }
}

$conn->close();
?>
