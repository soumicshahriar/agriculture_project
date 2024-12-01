<?php
// Database connection
$host = 'localhost';
$dbname = 'agriculture_product_data';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to fetch data
    $stmt = $pdo->query("SELECT product_name AS product, seasonality AS season, yield_data AS yield, price_per_kg AS price FROM products");

    // Fetch data as an associative array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data as JSON
    header('Content-Type: application/json');
    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
