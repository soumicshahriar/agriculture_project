<?php

// Include the database connection file
include('../config/connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check request type
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sign-up functionality (for customers only)
    if (isset($_POST['f_name'], $_POST['l_name'], $_POST['phone'], $_POST['email'], $_POST['password'])) {
        $f_name = trim($_POST['f_name']);
        $l_name = trim($_POST['l_name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']); 

        // Check if email already exists in customers
        $stmt = $conn->prepare("SELECT email FROM customers WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // Check if email already exists in employee
        $stmt_employee = $conn->prepare("SELECT email FROM employee WHERE email = ?");
        $stmt_employee->bind_param("s", $email);
        $stmt_employee->execute();
        $stmt_employee->store_result();

        if ($stmt->num_rows > 0 || $stmt_employee->num_rows > 0) {
            echo json_encode(["message" => "Error: The email address is already registered!"]);
        } else {
            // Insert the new user into the database
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO customers (f_name, l_name, phone, email, password) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $f_name, $l_name, $phone, $email, $password);

            if ($stmt->execute()) {
                echo "Sign-up successful!"; 
            } else {
                echo json_encode(["message" => "Error: " . $stmt->error]);
            }
        }

        $stmt->close();
    } elseif (isset($_POST['email'], $_POST['password'])) {
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Check for customers
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
                "l_name" => $db_l_name,
                "consumer_id" => $db_id // Add consumer_id to the response
            ]);
        } else {
            $stmt->close();

            // Check for employees
            $stmt = $conn->prepare("SELECT employee_id, employee_name, role, password FROM employee WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($db_employee_id, $db_employee_name, $db_role, $db_password);

            if ($stmt->fetch() && $password === $db_password) {
                echo json_encode([
                    "message" => "Login successful!",
                    "employee_id" => (int)$db_employee_id,
                    "employee_name" => $db_employee_name,
                    "role" => $db_role
                ]);
            } else {
                echo json_encode(["message" => "Invalid email or password!"]);
            }
        }

        $stmt->close();
    } else {
        echo json_encode(["message" => "Invalid request!"]);
    }
}

$conn->close();
